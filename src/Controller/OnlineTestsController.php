<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\FrozenTime;
use Cake\Utility\Security;

class OnlineTestsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // kalau kamu sudah pakai Authentication middleware, biarkan
    }

    public function index()
    {
        $this->request->allowMethod(['get']);

        $identity = $this->Authentication->getIdentity();
        $userId = (int)$identity->get('id');

        $onlineTest = $this->OnlineTests
            ->find()
            ->where(['user_id' => $userId])
            ->first();

        if (!$onlineTest) {
            $onlineTest = $this->OnlineTests->newEntity([
                'user_id' => $userId,
                'status' => 'waiting',
                'test_url' => 'https://test.jecaid.com',
                'schedule_start' => null,
                'schedule_end' => null,
                'test_access_id' => null,
                'admin_note' => null,
            ]);

            $this->OnlineTests->saveOrFail($onlineTest);
        }

        // PENTING: kirim identity ke view biar sidebar aman
        $this->set(compact('onlineTest', 'identity'));
    }
}
