<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Datasource\FactoryLocator;
use Authentication\PasswordHasher\DefaultPasswordHasher;

class AuthController extends AppController
{
    // login tidak perlu auth gate (sudah di-allow di Admin\AppController)
    public function login()
    {
        $this->viewBuilder()->setLayout('admin_auth');
        $this->request->allowMethod(['get', 'post']);

        $session = $this->request->getSession();
        if ($session->read('Admin.id')) {
            return $this->redirect(['prefix' => 'Admin', 'controller' => 'Dashboard', 'action' => 'index']);
        }

        if ($this->request->is('post')) {
            $username = trim((string)$this->request->getData('username'));
            $password = (string)$this->request->getData('password');

            $Admins = FactoryLocator::get('Table')->get('Admins');
            $admin = $Admins->find()
                ->where(['username' => $username, 'is_active' => 1])
                ->first();

            if ($admin) {
                $hasher = new DefaultPasswordHasher();
                if ($hasher->check($password, (string)$admin->password)) {
                    $session->write('Admin.id', (int)$admin->id);
                    $session->write('Admin.name', (string)$admin->name);
                    return $this->redirect(['prefix' => 'Admin', 'controller' => 'Dashboard', 'action' => 'index']);
                }
            }

            $this->Flash->error('Username atau password salah.');
        }
    }

public function logout()
{
    $this->request->allowMethod(['post']); // CSRF aman

    // Ambil identity dengan cara yang paling aman
    $identity = $this->request->getAttribute('identity');


    // Logout: coba lewat Authentication component kalau ada
    if (property_exists($this, 'Authentication') && $this->Authentication) {
        $this->Authentication->logout();
    } else {
        // fallback: hapus session auth manual kalau sistem kamu pakai session sendiri
        $this->request->getSession()->destroy();
    }

    $this->Flash->success('Anda telah logout.');

    return $this->redirect(['prefix' => 'Admin', 'controller' => 'Auth', 'action' => 'login']);
}


}
