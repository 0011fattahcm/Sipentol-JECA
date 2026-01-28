<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Http\Exception\NotFoundException;

class UsersController extends AppController
{
    public function index()
    {
        $q = trim((string)$this->request->getQuery('q', ''));

        $limit = (int)$this->request->getQuery('limit', 20);
        if (!in_array($limit, [10, 20, 50, 100], true)) {
            $limit = 20;
        }

        $Users = $this->fetchTable('Users');
        $query = $Users->find();

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'Users.nama_lengkap LIKE' => '%' . $q . '%',
                    'Users.email LIKE'        => '%' . $q . '%',
                ]
            ]);
        }

        $users = $this->paginate($query->orderDesc('Users.id'), [
            'limit' => $limit,
        ]);

        $this->set(compact('users', 'q', 'limit'));
    }

    public function view($id = null)
    {
        $Users = $this->fetchTable('Users');

        $user = $Users->find()
            ->where(['Users.id' => (int)$id])
            ->first();

        if (!$user) {
            throw new NotFoundException('User tidak ditemukan.');
        }

        $this->set(compact('user'));
    }

    public function edit($id = null)
    {
        $Users = $this->fetchTable('Users');

        $user = $Users->find()
            ->where(['Users.id' => (int)$id])
            ->first();

        if (!$user) {
            throw new NotFoundException('User tidak ditemukan.');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = (array)$this->request->getData();
            unset($data['password'], $data['new_password'], $data['password_confirm'], $data['confirm_password']);
            // Optional: normalisasi status biar konsisten
            if (isset($data['status'])) {
                $data['status'] = strtolower(trim((string)$data['status']));
            }

            $user = $Users->patchEntity($user, $data);

            if ($Users->save($user)) {
                $this->Flash->success('Data user berhasil diperbarui.');
                return $this->redirect([
                    'action' => 'view',
                    $user->id,
                    '?' => $this->request->getQueryParams(),
                ]);
            }

            $this->Flash->error('Gagal menyimpan perubahan. Cek input.');
        }

        $this->set(compact('user'));
    }

    public function toggle($id = null)
{
    $this->request->allowMethod(['post']);

    $Users = $this->fetchTable('Users');

    $user = $Users->find()
        ->where(['Users.id' => (int)$id])
        ->first();

    if (!$user) {
        throw new NotFoundException('User tidak ditemukan.');
    }

    $user->is_active = ((int)$user->is_active === 1) ? 0 : 1;

    if ($Users->save($user, ['validate' => false])) {
        $this->Flash->success('Status akun user diperbarui.');
    } else {
        $this->Flash->error('Gagal memperbarui status akun user.');
    }

    return $this->redirect(['action' => 'index', '?' => $this->request->getQueryParams()]);
}


    public function delete($id = null)
    {
        $this->request->allowMethod(['post']);

        $Users = $this->fetchTable('Users');

        $user = $Users->find()
            ->where(['Users.id' => (int)$id])
            ->first();

        if (!$user) {
            throw new NotFoundException('User tidak ditemukan.');
        }

        if ($Users->delete($user)) {
            $this->Flash->success('User berhasil dihapus.');
        } else {
            $this->Flash->error('User gagal dihapus.');
        }

        return $this->redirect(['action' => 'index', '?' => $this->request->getQueryParams()]);
    }
}
