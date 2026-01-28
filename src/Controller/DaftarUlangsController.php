<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;

class DaftarUlangsController extends AppController
{
    public function form()
    {
        $this->request->allowMethod(['get','post','put','patch']);

        $identity = $this->Authentication->getIdentity();
        $userId = (int)$identity->get('id');

        // RULE: form daftar ulang hanya aktif jika status user sudah lulus tes
        // (boleh tambahin status lanjutan kalau kamu mau)
$user = $this->fetchTable('Users')->get($userId, [
    'fields' => ['id', 'status']
]);

$userStatus = (string)$user->status;
$canDaftarUlang = in_array($userStatus, ['lulus_tes', 'daftar_ulang', 'verifikasi_ulang', 'aktif'], true);


        // Ambil / create row daftar ulang untuk user
        $daftarUlang = $this->DaftarUlangs->find()
            ->where(['user_id' => $userId])
            ->first();

        if (!$daftarUlang) {
            $daftarUlang = $this->DaftarUlangs->newEntity([
                'user_id' => $userId,
                'status'  => 'draft',
            ]);
            $this->DaftarUlangs->saveOrFail($daftarUlang);
        }

        // Kalau belum lulus tes, matikan aksi submit (GET boleh render)
        if (!$canDaftarUlang && $this->request->is(['post','put','patch'])) {
            $this->Flash->error('Form Daftar Ulang belum dibuka. Status tes Anda belum lulus.');
            return $this->redirect(['action' => 'form']);
        }

        if ($this->request->is(['post','put','patch'])) {

    $prevStatus = (string)($daftarUlang->status ?? 'draft'); // SIMPAN STATUS SEBELUM SAVE

    $daftarUlang = $this->DaftarUlangs->patchEntity($daftarUlang, $this->request->getData());

    $ok = true;

            // upload PDF (optional / boleh satu-satu)
            $ok = $this->handleUpload($daftarUlang, 'formulir_pendaftaran_pdf', $userId, ['application/pdf']) && $ok;
            $ok = $this->handleUpload($daftarUlang, 'surat_perjanjian_pdf', $userId, ['application/pdf']) && $ok;
            $ok = $this->handleUpload($daftarUlang, 'surat_persetujuan_orangtua_pdf', $userId, ['application/pdf']) && $ok;

            // upload image (optional / boleh satu-satu)
            $ok = $this->handleUpload($daftarUlang, 'bukti_pembayaran_img', $userId, ['image/jpeg','image/png','image/webp']) && $ok;

            if ($ok && $this->DaftarUlangs->save($daftarUlang)) {

    // RECORD DI SINI (HANYA SAAT SAVE SUKSES)
    // bedakan submit pertama vs update
    $newStatus = (string)($daftarUlang->status ?? 'draft');

    $aksi = ($prevStatus === 'draft' && $newStatus !== 'draft')
        ? 'Submit Daftar Ulang'
        : 'Update Daftar Ulang';

    $this->ActivityLogger->record($aksi, (int)$userId);

    $this->Flash->success('Daftar ulang berhasil disimpan.');
    return $this->redirect(['action' => 'form']);
}

            $this->Flash->error('Gagal menyimpan. Periksa file yang diupload atau coba lagi.');
        }

        $this->set(compact('daftarUlang', 'identity', 'canDaftarUlang'));
        $reviewStatus = (string)($daftarUlang->status ?? 'draft');
        $this->set(compact('reviewStatus'));
    }

public function downloadDraft(string $type)
{
    $this->request->allowMethod(['get']);

    $map = [
        'formulir'         => 'daftar_ulang_formulir',
        'perjanjian'       => 'daftar_ulang_perjanjian',
        'persetujuan-ortu' => 'daftar_ulang_persetujuan_ortu',
    ];

    if (!isset($map[$type])) {
        throw new NotFoundException('Draft tidak ditemukan.');
    }

    $key = $map[$type];

    $AdminFiles = $this->fetchTable('AdminFiles');
    $row = $AdminFiles->find()->where(['key_name' => $key])->first();

    if (!$row || empty($row->file_path)) {
        $this->Flash->error('Draft belum tersedia. Silakan hubungi admin.');
        return $this->redirect('/daftar-ulang');
    }

    $abs = WWW_ROOT . ltrim((string)$row->file_path, '/');
    if (!file_exists($abs)) {
        $this->Flash->error('Draft belum tersedia di server. Silakan hubungi admin.');
        return $this->redirect('/daftar-ulang');
    }

    return $this->response->withFile($abs, [
        'download' => true,
        'name' => $row->file_name ?: basename($abs),
    ]);
}


    private function handleUpload($entity, string $field, int $userId, array $allowedMime): bool
    {
        $file = $this->request->getData($field);

        if (!$file || !($file instanceof \Psr\Http\Message\UploadedFileInterface)) {
            return true; // tidak upload -> skip
        }

        if ($file->getError() === UPLOAD_ERR_NO_FILE) return true;
        if ($file->getError() !== UPLOAD_ERR_OK) return false;

        $mime = (string)$file->getClientMediaType();
        if (!in_array($mime, $allowedMime, true)) {
            return false;
        }

        // limit size 10MB
        if ($file->getSize() > 10 * 1024 * 1024) {
            return false;
        }

        $ext = strtolower(pathinfo((string)$file->getClientFilename(), PATHINFO_EXTENSION));
        if ($ext === '') {
            $ext = ($mime === 'application/pdf') ? 'pdf' : 'jpg';
        }

        $dirRel = "uploads/daftar-ulang/{$userId}/";
        $dirAbs = WWW_ROOT . $dirRel;

        if (!is_dir($dirAbs)) {
            mkdir($dirAbs, 0775, true);
        }

        $safeName  = $field . '_' . time() . '.' . $ext;
        $targetAbs = $dirAbs . $safeName;

        $file->moveTo($targetAbs);

        // simpan relative path untuk akses public
        $entity->set($field, '/' . $dirRel . $safeName);

        return true;
    }
}
