<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController as BaseAppController;

class AppController extends BaseAppController
{
    public function initialize(): void
    {
        parent::initialize();

        // Layout admin default untuk semua halaman prefix Admin
        $this->viewBuilder()->setLayout('admin');
         if ($this->components()->has('Authentication')) {
        $this->components()->unload('Authentication'); }
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $session = $this->request->getSession();
        $adminId = $session->read('Admin.id');

        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');

        // allow login page
        if ($controller === 'Auth' && $action === 'login') {
            return;
        }

        // protect all other admin routes
        if (!$adminId) {
            return $this->redirect(['prefix' => 'Admin', 'controller' => 'Auth', 'action' => 'login']);
        }

        $this->set('adminName', (string)$session->read('Admin.name'));
    }
}
