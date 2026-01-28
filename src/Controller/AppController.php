<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');

        // Authentication dipakai untuk USER area
        // (Admin prefix akan di-unload di Admin\AppController)
        $this->loadComponent('Authentication.Authentication');

        $this->loadComponent('ActivityLogger');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Kalau Authentication di-unload (misal prefix Admin), jangan panggil methodnya
        if (!$this->components()->has('Authentication')) {
            return;
        }

        $this->Authentication->allowUnauthenticated([
            'login',
            'register',
            'verifyOtp',
        ]);

        // ==== REFRESH DATA USER UNTUK KONSISTENSI UI ====
        // Bug: Dashboard = onboarding, pindah menu = daftar ulang
        // terjadi karena Dashboard pakai entity Users terbaru, sedangkan halaman lain pakai identity session lama.
        // Solusi: ambil user terbaru dari DB di setiap request user-side, expose sebagai `authUser`.
        $prefix = (string)($this->request->getParam('prefix') ?? '');
        if (strtolower($prefix) === 'admin') {
            return;
        }

        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            return;
        }

        try {
            $userId = (int)$identity->getIdentifier();

            $Users = $this->fetchTable('Users');
            $freshUser = $Users->find()
                ->where(['Users.id' => $userId])
                ->first();

            if ($freshUser) {
                // sumber kebenaran UI
                $this->set('authUser', $freshUser);

                // OPTIONAL: refresh identity untuk request ini (kalau plugin support)
                try {
                    $this->Authentication->setIdentity($freshUser);
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        } catch (\Throwable $e) {
            // silent fail
        }
    }
}
