<?php
declare(strict_types=1);

namespace App\Controller\Admin;

class PengumumanController extends AppController
{
    /** @var \App\Model\Table\PengumumanTable */
    public $Pengumuman;

    /** @var \App\Model\Table\UsersTable */
    public $Users;

    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('admin');

        // WAJIB: assign table objects
        $this->Pengumuman = $this->fetchTable('Pengumuman');
        $this->Users = $this->fetchTable('Users');
    }

    public function index()
    {
        $this->request->allowMethod(['get']);

        $q    = (string)$this->request->getQuery('q', '');
        $from = (string)$this->request->getQuery('from', '');
        $to   = (string)$this->request->getQuery('to', '');

        $query = $this->Pengumuman->find()
            ->orderDesc('Pengumuman.id');

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'Pengumuman.judul LIKE' => "%{$q}%",
                    'Pengumuman.isi LIKE'   => "%{$q}%",
                ]
            ]);
        }

        // filter rentang tanggal (created_at) format: YYYY-MM-DD
        if ($from !== '') {
            $query->where(['Pengumuman.created_at >=' => $from . ' 00:00:00']);
        }
        if ($to !== '') {
            $query->where(['Pengumuman.created_at <=' => $to . ' 23:59:59']);
        }

        $pengumuman = $this->paginate($query, ['limit' => 10]);

        $this->set(compact('pengumuman', 'q', 'from', 'to'));
        $this->set('title', 'Pengumuman');
    }

    public function add()
    {
        $this->request->allowMethod(['get', 'post']);

        $pengumuman = $this->Pengumuman->newEmptyEntity();

        // list user untuk checkbox
        $users = $this->Users->find()
            ->select(['id', 'nama_lengkap', 'email'])
            ->orderAsc('nama_lengkap')
            ->all();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $target = (string)($data['target'] ?? 'semua');

            // normalize target_user_ids
            if ($target === 'semua') {
                $data['target_user_ids'] = '[]';
            } else {
                $ids = $data['target_user_ids'] ?? [];
                if (!is_array($ids)) $ids = [];
                $ids = array_values(array_unique(array_map('intval', $ids)));
                $data['target_user_ids'] = json_encode($ids, JSON_UNESCAPED_UNICODE);
            }

            $data['is_active'] = !empty($data['is_active']) ? 1 : 0;

            // set created_at kalau tabel kamu belum auto
            if (empty($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            $pengumuman = $this->Pengumuman->patchEntity($pengumuman, $data);

            if ($this->Pengumuman->save($pengumuman)) {
                $this->Flash->success('Pengumuman berhasil disimpan.');
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('Gagal menyimpan pengumuman. Cek inputnya.');
        }

        $this->set(compact('pengumuman', 'users'));
        $this->set('title', 'Tambah Pengumuman');
    }

    public function edit($id = null)
    {
        $this->request->allowMethod(['get', 'post', 'put', 'patch']);

        $pengumuman = $this->Pengumuman->get($id);

        $users = $this->Users->find()
            ->select(['id', 'nama_lengkap', 'email'])
            ->orderAsc('nama_lengkap')
            ->all();

        $selectedUserIds = [];
        if (!empty($pengumuman->target_user_ids)) {
            $decoded = json_decode((string)$pengumuman->target_user_ids, true);
            if (is_array($decoded)) {
                $selectedUserIds = array_values(array_unique(array_map('intval', $decoded)));
            }
        }

        if ($this->request->is(['post', 'put', 'patch'])) {
            $data = $this->request->getData();

            $target = (string)($data['target'] ?? 'semua');

            if ($target === 'semua') {
                $data['target_user_ids'] = '[]';
                $selectedUserIds = [];
            } else {
                $ids = $data['target_user_ids'] ?? [];
                if (!is_array($ids)) $ids = [];
                $ids = array_values(array_unique(array_map('intval', $ids)));
                $data['target_user_ids'] = json_encode($ids, JSON_UNESCAPED_UNICODE);
                $selectedUserIds = $ids;
            }

            $data['is_active'] = !empty($data['is_active']) ? 1 : 0;

            $pengumuman = $this->Pengumuman->patchEntity($pengumuman, $data);

            if ($this->Pengumuman->save($pengumuman)) {
                $this->Flash->success('Pengumuman berhasil diupdate.');
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('Gagal update pengumuman.');
        }

        $this->set(compact('pengumuman', 'users', 'selectedUserIds'));
        $this->set('title', 'Edit Pengumuman');
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $pengumuman = $this->Pengumuman->get($id);

        if ($this->Pengumuman->delete($pengumuman)) {
            $this->Flash->success('Pengumuman berhasil dihapus.');
        } else {
            $this->Flash->error('Gagal menghapus pengumuman.');
        }

        return $this->redirect(['action' => 'index']);
    }
}
