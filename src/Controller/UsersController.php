<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Mailer;
use Cake\Utility\Security;
use Authentication\PasswordHasher\DefaultPasswordHasher;


class UsersController extends AppController
{

public function beforeFilter(\Cake\Event\EventInterface $event)
{
    parent::beforeFilter($event);

    // Halaman yang boleh diakses TANPA login
    $this->Authentication->addUnauthenticatedActions([
        'register',
        'login',
        'verifyOtp',
        'resendOtp',
        'forgotPassword',
        'resetPassword',
    ]);
}



    /**
     * REGISTER
     */
public function register()
{
    $user = $this->Users->newEmptyEntity();

    

    if ($this->request->is('post')) {

        $data = $this->request->getData();

           // WAJIB setuju S&K
    $agree = (string)($data['agree_terms'] ?? '');
    if ($agree !== '1') {
        $this->Flash->error('Anda wajib menyetujui Syarat & Ketentuan untuk mendaftar.');
        $this->set('agreeError', 'Anda wajib menyetujui Syarat & Ketentuan untuk mendaftar.');
        // pastikan form tetap tampil dengan data yang sudah diketik
        $user = $this->Users->patchEntity($user, $data);
        $this->set(compact('user'));
        return;
    }

        // Hapus field konfirmasi dari data sebelum patch
        unset($data['confirm_password']);

        // Hash password
        $data['password'] = (new DefaultPasswordHasher())->hash($data['password']);

        // Generate OTP 6 digit
        $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $data['otp_code']      = $otp;
        $data['otp_expiry']    = FrozenTime::now()->addMinutes(10);
        $data['is_verified']   = 0;
        $data['status']        = 'pendaftaran';
        $data['otp_resend_count'] = 0;

        $user = $this->Users->patchEntity($user, $data);

        // Kalau gagal save (misal email sudah dipakai), tetap di halaman register
        if (!$this->Users->save($user)) {
            $this->Flash->error('Gagal membuat akun. Silakan periksa kembali data Anda.');
            $this->set(compact('user'));
            return;
        }

        $this->ActivityLogger->record('Register', (int)$user->id);

        // --- Sampai sini artinya SAVE BERHASIL ---

        // Kirim OTP
        $this->_sendOtpEmail($user->email, $otp);

        $this->Flash->success('Akun berhasil dibuat. Silakan verifikasi OTP.');

        // Redirect pakai URL string langsung (pasti ke verify-otp)
        return $this->redirect('/users/verify-otp/' . $user->id);
    }

    $this->set(compact('user'));
}

    /**
     * VERIFY OTP
     */
public function verifyOtp($id = null)
{
    $this->request->allowMethod(['get', 'post']);

    if (!$id) {
        $this->Flash->error('User tidak ditemukan.');
        return $this->redirect(['action' => 'register']);
    }

    $user = $this->Users->get($id);

    if ($this->request->is('post')) {

        $inputOtp = trim((string)$this->request->getData('otp_code'));

        // Jika OTP tidak ada
        if (empty($user->otp_code) || empty($user->otp_expiry)) {
            $this->Flash->error('OTP tidak ditemukan. Silakan daftar ulang.');
            return $this->redirect(['action' => 'register']);
        }

        $now = FrozenTime::now();

        $isOtpValid = (
            hash_equals((string)$user->otp_code, (string)$inputOtp) &&
            $user->otp_expiry instanceof \DateTimeInterface &&
            $now < $user->otp_expiry
        );

        if (!$isOtpValid) {
            $this->Flash->error('Kode OTP salah atau sudah kedaluwarsa.');
            $this->set(compact('user'));
            return;
        }

        // OTP valid -> update status verifikasi
        $user->is_verified = 1;
        $user->otp_code = null;
        $user->otp_expiry = null;
        $user->otp_resend_count = 0;

        if (!$this->Users->save($user)) {
            $this->Flash->error('Gagal menyimpan verifikasi. Silakan coba lagi.');
            $this->set(compact('user'));
            return;
        }

        // Record log (pakai userId manual karena belum login)
        $this->ActivityLogger->record('Verify OTP', (int)$user->id);

        // PENTING: bersihkan session auth biar tidak “nyangkut login”
        $this->Authentication->logout();

        $this->Flash->success('Verifikasi berhasil. Silakan login.');
        return $this->redirect(['action' => 'login']);
    }

    $this->set(compact('user'));
}


    /**
 * RESEND OTP
 */
public function resendOtp($id = null)
{
    $this->request->allowMethod(['post', 'get']);

    if (!$id) {
        $this->Flash->error('User tidak ditemukan.');
        return $this->redirect(['action' => 'register']);
    }

    $user = $this->Users->get($id);

    // Cek jumlah resend OTP
    if ($user->otp_resend_count >= 3) {
        $this->Flash->error('Anda sudah mencapai batas maksimal pengiriman ulang OTP (3 kali).');
        return $this->redirect(['action' => 'verifyOtp', $id]);
    }

    // Generate OTP baru
    $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

    $user->otp_code = $otp;
    $user->otp_expiry = FrozenTime::now()->addMinutes(10);
    $user->otp_resend_count += 1;

    if ($this->Users->save($user)) {
        $this->ActivityLogger->record('Resend OTP', (int)$user->id);
        $this->_sendOtpEmail($user->email, $otp);
        $this->Flash->success(
            'OTP baru telah dikirim (' . $user->otp_resend_count . '/3).'
        );
    } else {
        $this->Flash->error('Gagal mengirim ulang OTP. Silakan coba lagi.');
    }

    return $this->redirect(['action' => 'verifyOtp', $id]);
}


    /**
     * LOGIN
     */
public function login()
{
    $this->request->allowMethod(['get', 'post']);

    $switch = (string)$this->request->getQuery('switch');
    $result = $this->Authentication->getResult();

    // Jika sudah login dan akses GET /users/login
    if ($this->request->is('get') && $result && $result->isValid()) {
        if ($switch === '1') {
            // ganti akun: bersihkan total
            $this->Authentication->logout();
            $this->request->getSession()->destroy();
        } else {
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    if ($this->request->is('post')) {
        if ($result && $result->isValid()) {

            /** @var \App\Model\Entity\User $user */
            $user = $result->getData();

            // Pastikan akun aktif
            if ((int)$user->is_active !== 1) {
                $this->Authentication->logout();
                $this->request->getSession()->destroy();
                $this->Flash->error('Akun Anda sedang dinonaktifkan. Silakan hubungi admin.');
                return $this->redirect(['action' => 'login']);
            }

            // Pastikan akun sudah terverifikasi
            if ((int)$user->is_verified !== 1) {
                $this->Authentication->logout();
                $this->request->getSession()->destroy();
                $this->Flash->error('Akun Anda belum diverifikasi. Silakan cek email untuk kode OTP.');
                return $this->redirect(['action' => 'login']);
            }

            // Set identity (session)
            $this->Authentication->setIdentity($user);

            // Record login
            $this->ActivityLogger->record('Login', (int)$user->id);

            // Redirect prioritas
            $redirect = (string)$this->request->getQuery('redirect');
            if ($redirect !== '') {
                return $this->redirect($redirect);
            }

            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        $this->Flash->error('Email atau password salah.');
    }

    // render view login.php (Cake auto-render)
}

    /**
     * LOGOUT
     */
public function logout()
{
    $this->request->allowMethod(['post']); // penting (hindari CSRF error)

    $identity = $this->Authentication->getIdentity();
    if ($identity) {
        $this->ActivityLogger->record('Logout', (int)$identity->get('id'));
    }

    $this->Authentication->logout();
    $this->Flash->success('Anda telah logout.');

    return $this->redirect(['action' => 'login']);
}


    /**
     * SEND OTP EMAIL
     */
  private function _sendOtpEmail(string $email, string $otp): void
{
    $mailer = new Mailer('default');

    $html = "
    <div style='font-family: Arial; padding:20px;'>
        <h2 style='color:#4f46e5;'>Kode OTP Verifikasi Akun SiPentol LPK JECA</h2>

        <p>Halo,</p>
        <p>Berikut adalah kode OTP Anda:</p>

        <div style='
            background:#f3f4f6;
            padding:15px;
            font-size:24px;
            font-weight:bold;
            border-radius:8px;
            width:fit-content;
            margin:15px 0;
            border-left:4px solid #4f46e5;
        '>
            $otp
        </div>

        <p>Kode berlaku selama <b>10 menit</b>. Mohon untuk tidak membalas email ini</p>
        <br>
        <p>Terima kasih,<br>Tim SiPentol JECA</p>
    </div>";

    $mailer->setTo($email)
        ->setSubject('Kode OTP Verifikasi Akun SiPentol JECA')
        ->setEmailFormat('html')
        ->deliver($html);
}

public function forgotPassword()
{
    $this->request->allowMethod(['get', 'post']);

    if ($this->request->is('post')) {
        $email = strtolower(trim((string)$this->request->getData('email')));

        // Selalu kasih pesan sukses (hindari email enumeration)
        $this->Flash->success('Jika email terdaftar, link reset password akan dikirim ke email Anda.');

        $user = $this->Users->find()
            ->where(['email' => $email])
            ->first();

        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);

            $user->reset_password_token_hash = $tokenHash;
            $user->reset_password_expiry = FrozenTime::now()->addMinutes(30);

            if ($this->Users->save($user)) {
                $this->ActivityLogger->record('Request Reset Password', (int)$user->id);
                $resetUrl = $this->request->getUri()->getScheme() . '://' .
                    $this->request->getUri()->getHost();

                // Kalau kamu pakai port lokal (cake server), host biasanya sudah include port.
                // Tapi getHost() bisa tidak include port. Jadi kita ambil authority saja:
                $resetUrl = $this->request->getUri()->getScheme() . '://' . $this->request->getUri()->getAuthority();

                $link = $resetUrl . '/reset-password/' . $token;

                $this->_sendResetPasswordEmail($user->email, $link);
            }
        }

return $this->redirect(['action' => 'forgotPassword']);
    }
}

public function resetPassword($token = null)
{
    $this->request->allowMethod(['get', 'post']);

    $token = (string)$token;
    if (!$token) {
        $this->Flash->error('Link reset password tidak valid.');
        return $this->redirect(['action' => 'forgotPassword']);
    }

    $tokenHash = hash('sha256', $token);

    $user = $this->Users->find()
        ->where([
            'reset_password_token_hash' => $tokenHash,
            'reset_password_expiry >' => FrozenTime::now()
        ])
        ->first();

    if (!$user) {
        $this->Flash->error('Link reset password tidak valid atau sudah kedaluwarsa.');
        return $this->redirect(['action' => 'forgotPassword']);
    }

    if ($this->request->is('post')) {
        $password = (string)$this->request->getData('password');
        $confirm  = (string)$this->request->getData('confirm_password');

        if (strlen($password) < 6) {
            $this->Flash->error('Password minimal 6 karakter.');
            return;
        }

        if ($password !== $confirm) {
            $this->Flash->error('Konfirmasi password tidak sama.');
            return;
        }

        $hashed = (new DefaultPasswordHasher())->hash($password);

$affected = $this->Users->updateAll(
    [
        'password' => $hashed,
        'reset_password_token_hash' => null,
        'reset_password_expiry' => null,
        'modified' => FrozenTime::now(),
    ],
    ['id' => $user->id]
);

if ($affected) {
      $this->ActivityLogger->record('Reset Password', (int)$user->id);
    $this->Flash->success('Password berhasil diubah. Silakan login.');
    return $this->redirect(['action' => 'login']);
}

$this->Flash->error('Gagal mengubah password. Silakan coba lagi.');
return;


        $this->Flash->error('Gagal mengubah password. Silakan coba lagi.');
    }

    $this->set(compact('token'));
}

private function _sendResetPasswordEmail(string $email, string $link): void
{
    $mailer = new Mailer('default');

    $html = "
    <div style='font-family: Arial; padding:20px;'>
        <h2 style='color:#4f46e5;'>Reset Password Akun SiPentol</h2>
        <p>Halo,</p>
        <p>Klik tombol di bawah untuk reset password Anda:</p>

        <p style='margin:20px 0;'>
            <a href='{$link}' style='background:#4f46e5;color:white;padding:12px 18px;border-radius:10px;text-decoration:none;display:inline-block;'>
                Reset Password
            </a>
        </p>

        <p>Link ini berlaku selama <b>30 menit</b>.</p>
        <p>Jika Anda tidak merasa meminta reset password, abaikan email ini.</p>
        <br>
        <p>Terima kasih,<br>Tim SiPentol</p>
    </div>";

    $mailer->setTo($email)
        ->setSubject('Reset Password Akun SiPentol')
        ->setEmailFormat('html')
        ->deliver($html);
}
}
