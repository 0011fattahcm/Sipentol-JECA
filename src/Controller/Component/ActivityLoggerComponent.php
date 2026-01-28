<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\FrozenTime;

class ActivityLoggerComponent extends Component
{
    /**
     * Record aktivitas user ke tabel logs
     * Aman: gagal simpan tidak mematikan flow aplikasi
     */
    public function record(string $aktivitas, ?int $userId = null): void
    {
        try {
            $controller = $this->getController();

            // Ambil user ID dari Authentication jika tidak dikirim
            if ($userId === null && $controller->components()->has('Authentication')) {
                $identity = $controller->Authentication->getIdentity();
                if ($identity) {
                    $userId = (int)$identity->get('id');
                }
            }

            // user_id WAJIB, kalau tidak ada → skip
            if (!$userId) {
                return;
            }

            $Logs = $controller->fetchTable('ActivityLogs');

            $entity = $Logs->newEntity([
                'user_id'   => $userId,
                'aktivitas' => mb_substr(trim($aktivitas), 0, 255),
                'created'   => FrozenTime::now(),
            ]);

            $Logs->save($entity);
        } catch (\Throwable $e) {
            // DI-SILENT SENGAJA
        }
    }
}
