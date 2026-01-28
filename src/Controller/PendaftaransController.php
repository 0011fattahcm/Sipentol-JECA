<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\FrozenDate;
use Cake\Utility\Text;

class PendaftaransController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // Pastikan sudah pakai Authentication middleware
        // dan halaman ini hanya untuk user login
    }

public function form()
{
    $this->request->allowMethod(['get', 'post', 'put', 'patch']);

    $identity = $this->Authentication->getIdentity();
    if (!$identity) {
        // harusnya tidak terjadi kalau halaman ini protected
        throw new \RuntimeException('Identity not found');
    }

    // PAKAI SATU SUMBER SAJA: field id
    $userId = (int)$identity->get('id');
    $email  = (string)$identity->get('email');

    // 1) Ambil existing pendaftaran
    $pendaftaran = $this->Pendaftarans
        ->find()
        ->where(['user_id' => $userId])
        ->first();

    if (!$pendaftaran) {
        $pendaftaran = $this->Pendaftarans->newEmptyEntity();
        $pendaftaran->user_id = $userId;
        $pendaftaran->email   = $email; // jika kolom email NOT NULL
    }

    if ($this->request->is(['post', 'put', 'patch'])) {
        $data = $this->request->getData();

        // 2) Paksa email & user_id mengikuti akun login
        $data['user_id'] = $userId;
        $data['email']   = $email;

        // 3) Map field form -> kolom DB yang benar
        // form kamu pakai "instagram" & "facebook", sedangkan DB instagram_url & facebook_url
        if (isset($data['instagram'])) {
            $data['instagram_url'] = $data['instagram'];
            unset($data['instagram']);
        }
        if (isset($data['facebook'])) {
            $data['facebook_url'] = $data['facebook'];
            unset($data['facebook']);
        }

        // ===== Sumber Informasi Pendaftaran =====
$src = (string)($data['info_sumber'] ?? '');

// reset field tambahan supaya tidak nyangkut dari pilihan sebelumnya
$data['info_referral_code'] = null;
$data['info_instansi_nama'] = null;
$data['info_sumber_lain']   = null;

// validasi kondisional (server-side)
if ($src === 'jeca_group' || $src === 'jeca_relations') {
    $ref = trim((string)($data['info_referral_code_input'] ?? ''));
    if ($ref === '') {
        $this->Flash->error('Kode referral wajib diisi untuk pilihan JECA Group/JECA Relations.');
        $this->set(compact('pendaftaran', 'identity', 'email'));
        return;
    }
    $data['info_referral_code'] = $ref;
} elseif ($src === 'instansi') {
    $inst = trim((string)($data['info_instansi_nama_input'] ?? ''));
    if ($inst === '') {
        $this->Flash->error('Nama instansi wajib diisi untuk pilihan Instansi.');
        $this->set(compact('pendaftaran', 'identity', 'email'));
        return;
    }
    $data['info_instansi_nama'] = $inst;
} elseif ($src === 'lain') {
    $lain = trim((string)($data['info_sumber_lain_input'] ?? ''));
    if ($lain === '') {
        $this->Flash->error('Keterangan sumber lain wajib diisi.');
        $this->set(compact('pendaftaran', 'identity', 'email'));
        return;
    }
    $data['info_sumber_lain'] = $lain;
}

// hapus field input dummy (biar tidak ikut patchEntity)
unset($data['info_referral_code_input'], $data['info_instansi_nama_input'], $data['info_sumber_lain_input']);


        // 4) Patch
        $pendaftaran = $this->Pendaftarans->patchEntity($pendaftaran, $data);

        // 5) Handle upload (punyamu tetap dipakai)
        $uploadBase = WWW_ROOT . 'uploads' . DS . 'pendaftaran' . DS . $userId . DS;
        if (!is_dir($uploadBase)) {
            mkdir($uploadBase, 0775, true);
        }

        $pasFoto = $this->request->getData('pas_foto');
        if ($pasFoto && $pasFoto->getError() === UPLOAD_ERR_OK) {
            $ext = pathinfo($pasFoto->getClientFilename(), PATHINFO_EXTENSION);
            $filename = 'pas_foto_' . time() . '.' . strtolower($ext);
            $pasFoto->moveTo($uploadBase . $filename);
            $pendaftaran->pas_foto_path = 'uploads/pendaftaran/' . $userId . '/' . $filename;
        }

        $ktp = $this->request->getData('ktp_pdf');
        if ($ktp && $ktp->getError() === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($ktp->getClientFilename(), PATHINFO_EXTENSION));
            $filename = 'ktp_' . time() . '.' . $ext;
            $ktp->moveTo($uploadBase . $filename);
            $pendaftaran->ktp_pdf_path = 'uploads/pendaftaran/' . $userId . '/' . $filename;
        }

        $ijazah = $this->request->getData('ijazah_pdf');
        if ($ijazah && $ijazah->getError() === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($ijazah->getClientFilename(), PATHINFO_EXTENSION));
            $filename = 'ijazah_' . time() . '.' . $ext;
            $ijazah->moveTo($uploadBase . $filename);
            $pendaftaran->ijazah_pdf_path = 'uploads/pendaftaran/' . $userId . '/' . $filename;
        }

        $transkrip = $this->request->getData('transkrip_pdf');
        if ($transkrip && $transkrip->getError() === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($transkrip->getClientFilename(), PATHINFO_EXTENSION));
            $filename = 'transkrip_' . time() . '.' . $ext;
            $transkrip->moveTo($uploadBase . $filename);
            $pendaftaran->transkrip_pdf_path = 'uploads/pendaftaran/' . $userId . '/' . $filename;
        }

        // 6) Save
   // 6) Save
$isCreate = empty($pendaftaran->id); // sebelum save

if ($this->Pendaftarans->save($pendaftaran)) {

    $aksi = $isCreate ? 'Submit Form Pendaftaran' : 'Update Form Pendaftaran';
    $this->ActivityLogger->record($aksi, (int)$userId);

    // === SET STATUS: setelah submit pendaftaran => menunggu tes
    $Users = $this->fetchTable('Users');
    $u = $Users->get($userId);
    $u->status = 'menunggu_tes';
    $Users->save($u);

    $this->Flash->success('Data pendaftaran berhasil disimpan.');
    return $this->redirect(['action' => 'form']);
}


// kalau gagal, jangan record
$errors = $pendaftaran->getErrors();
$this->Flash->error('Gagal menyimpan. Cek validasi data.');



        // DEBUG: ini wajib sementara supaya ketahuan validasi field mana yang fail
        $errors = $pendaftaran->getErrors();
        $this->Flash->error('Gagal menyimpan. Cek validasi data.');
        // uncomment sementara:
        // debug($errors); die;
    }

    $this->set(compact('pendaftaran', 'identity', 'email'));
}


    private function handleUpload(array $data, string $field, string $uploadBase, array $allowedMime, string $targetColumn): array
    {
        $file = $this->request->getData($field);

        if (!$file || !method_exists($file, 'getError')) {
            return $data;
        }

        if ($file->getError() !== UPLOAD_ERR_OK) {
            return $data; // tidak upload / error upload
        }

        $mime = $file->getClientMediaType() ?: '';
        if (!in_array($mime, $allowedMime, true)) {
            // NOTE: biar user dapat error, kita set field invalid via Flash saja
            $this->Flash->error("File {$field} tidak valid. Format tidak sesuai.");
            return $data;
        }

        $ext = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
        $safeName = Text::uuid() . '.' . $ext;
        $dest = $uploadBase . $safeName;

        $file->moveTo($dest);

        // simpan relative path untuk akses dari web
        $relative = 'uploads/pendaftaran/' . $data['user_id'] . '/' . $safeName;
        $data[$targetColumn] = $relative;

        return $data;
    }
}
