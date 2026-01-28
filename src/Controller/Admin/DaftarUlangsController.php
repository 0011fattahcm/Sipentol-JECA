<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Datasource\FactoryLocator;
use Cake\Http\Exception\NotFoundException;

class DaftarUlangsController extends AppController
{
    public function index()
    {
        $this->request->allowMethod(['get']);

        $DaftarUlangs = FactoryLocator::get('Table')->get('DaftarUlangs');

        $q = trim((string)$this->request->getQuery('q', ''));
        $limit = (int)$this->request->getQuery('limit', 10);
        if (!in_array($limit, [10, 20, 50, 100], true)) $limit = 10;

        $query = $DaftarUlangs->find()
            ->contain(['Users', 'Users.Pendaftarans'])
            ->leftJoinWith('Users.Pendaftarans')
            ->orderDesc('DaftarUlangs.modified');

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'Users.nama_lengkap LIKE' => "%$q%",
                    'Users.email LIKE' => "%$q%",
                    'Pendaftarans.nik LIKE' => "%$q%",
                    'Pendaftarans.whatsapp LIKE' => "%$q%",
                    'DaftarUlangs.status LIKE' => "%$q%",
                ]
            ]);
        }

        $items = $this->paginate($query, [
            'limit' => $limit,
            'maxLimit' => 100,
        ]);

        $this->set(compact('items', 'q', 'limit'));
    }

    public function view($id)
    {
        $this->request->allowMethod(['get']);

        $DaftarUlangs = FactoryLocator::get('Table')->get('DaftarUlangs');

        $item = $DaftarUlangs->get((int)$id, [
            'contain' => ['Users', 'Users.Pendaftarans']
        ]);

        // cek kelengkapan upload (field wajib)
        $required = [
            'formulir_pendaftaran_pdf' => 'Formulir Pendaftaran (PDF)',
            'surat_perjanjian_pdf' => 'Surat Perjanjian (PDF)',
            'surat_persetujuan_orangtua_pdf' => 'Surat Persetujuan Orang Tua (PDF)',
            'bukti_pembayaran_img' => 'Bukti Pembayaran (Gambar)',
        ];

        $missing = [];
        foreach ($required as $field => $label) {
            $val = trim((string)($item->get($field) ?? ''));
            if ($val === '') $missing[] = $label;
        }

        $isComplete = (count($missing) === 0);

        $this->set(compact('item', 'missing', 'isComplete'));
    }

public function verify($id)
{
    $this->request->allowMethod(['post']);

    $DaftarUlangs = $this->fetchTable('DaftarUlangs');
    $Users = $this->fetchTable('Users');

    $item = $DaftarUlangs->get((int)$id);
    $item->set('status', 'verified');

    // optional: bersihkan note
    // $item->set('admin_note', null);

    $DaftarUlangs->saveOrFail($item);

    // user jadi aktif
    $user = $Users->get((int)$item->user_id);
    $user->set('status', 'aktif');
    $Users->saveOrFail($user);

    $this->Flash->success('Berkas valid. User diubah menjadi AKTIF.');
    return $this->redirect(['action' => 'view', $id, '?' => $this->request->getQueryParams()]);
}


public function needFix($id)
{
    $this->request->allowMethod(['post']);

    $DaftarUlangs = $this->fetchTable('DaftarUlangs');
    $Users = $this->fetchTable('Users');

    $item = $DaftarUlangs->get((int)$id);
    $item->set('status', 'need_fix');

    // kalau pakai admin_note:
    $note = trim((string)$this->request->getData('admin_note'));
    if ($note !== '') $item->set('admin_note', $note);

    $DaftarUlangs->saveOrFail($item);

    // user tetap boleh akses daftar ulang
    $user = $Users->get((int)$item->user_id);
    $user->set('status', 'daftar_ulang');
    $Users->saveOrFail($user);

    $this->Flash->success('User diminta perbaiki berkas (Need Fix).');
    return $this->redirect(['action' => 'view', $id, '?' => $this->request->getQueryParams()]);
}


   public function openAccess($id)
{
    $this->request->allowMethod(['post']);

    $DaftarUlangs = $this->fetchTable('DaftarUlangs');
    $Users = $this->fetchTable('Users');
    $Pendaftarans = $this->fetchTable('Pendaftarans');

    $item = $DaftarUlangs->get((int)$id);
    $user = $Users->get((int)$item->user_id);

    // 1) buka akses dari sisi users
    $user->set('status', 'lulus_tes'); // atau 'daftar_ulang' kalau user-side ngijinin itu
    $Users->saveOrFail($user);

    // 2) buka akses dari sisi pendaftarans (ini yang biasanya dicek oleh user-page)
    $pendaftaran = $Pendaftarans->find()
        ->where(['user_id' => (int)$item->user_id])
        ->first();

    if ($pendaftaran) {
        // ✅ SESUAIKAN NAMA FIELD DI DB KAMU:
        // pilihan umum: status_tes / status / is_lulus / lulus
        if ($pendaftaran->has('status_tes')) {
            $pendaftaran->set('status_tes', 'lulus');
        } elseif ($pendaftaran->has('status')) {
            $pendaftaran->set('status', 'lulus');
        } elseif ($pendaftaran->has('is_lulus')) {
            $pendaftaran->set('is_lulus', 1);
        }

        $Pendaftarans->save($pendaftaran);
    }

    $this->Flash->success('Akses Daftar Ulang dibuka untuk user.');
    return $this->redirect(['action' => 'view', $id, '?' => $this->request->getQueryParams()]);
}


}
