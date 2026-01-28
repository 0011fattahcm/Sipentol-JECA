<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;

class OnlineTestsController extends AppController
{
    public function index()
    {
        $this->request->allowMethod(['get']);

        $OnlineTests = FactoryLocator::get('Table')->get('OnlineTests');
        $Users = FactoryLocator::get('Table')->get('Users');

        $q = trim((string)$this->request->getQuery('q', ''));
        $limit = (int)$this->request->getQuery('limit', 10);
        if (!in_array($limit, [10, 20, 50, 100], true)) {
            $limit = 10;
        }

        // Pastikan setiap user punya row online_tests
        // (biar admin bisa set jadwal/ID walaupun user belum pernah buka /tes-online)
        $missingUserIds = $Users->find()
            ->select(['Users.id'])
            ->leftJoinWith('OnlineTests')
            ->where(['OnlineTests.id IS' => null])
            ->enableHydration(false)
            ->all()
            ->extract('id')
            ->toList();

        if (!empty($missingUserIds)) {
            $rows = [];
            foreach ($missingUserIds as $uid) {
                $rows[] = $OnlineTests->newEntity([
                    'user_id' => (int)$uid,
                    'status' => 'waiting',
                    'test_url' => 'https://test.jecaid.com',
                    'schedule_start' => null,
                    'schedule_end' => null,
                    'test_access_id' => null,
                    'admin_note' => null,
                    'test_location_type' => 'online',
                    'test_location_detail' => null,
                ]);
            }
            $OnlineTests->saveMany($rows);
        }

        // include data pendaftaran (NIK/WA) untuk kebutuhan search admin
        // - contain() untuk hydrate entity
        // - leftJoinWith() agar Pendaftarans.* bisa dipakai di WHERE (search)
        $query = $OnlineTests->find()
            ->contain(['Users', 'Users.Pendaftarans'])
            ->leftJoinWith('Users.Pendaftarans')
            ->orderDesc('OnlineTests.id');

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'Users.nama_lengkap LIKE' => '%' . $q . '%',
                    'Users.email LIKE' => '%' . $q . '%',
                    'OnlineTests.test_access_id LIKE' => '%' . $q . '%',
                    'Pendaftarans.nik LIKE' => '%' . $q . '%',
                    'Pendaftarans.whatsapp LIKE' => '%' . $q . '%',
                ]
            ]);
        }

        $items = $this->paginate($query, [
            'limit' => $limit,
            'maxLimit' => 100,
        ]);

        $this->set(compact('items', 'q', 'limit'));
    }

    public function edit($id)
    {
        $this->request->allowMethod(['get', 'post', 'put', 'patch']);

        $OnlineTests = FactoryLocator::get('Table')->get('OnlineTests');
        $item = $OnlineTests->get((int)$id, ['contain' => ['Users']]);

        if ($this->request->is(['post', 'put', 'patch'])) {
            $data = $this->request->getData();

            // support input datetime-local: "YYYY-MM-DDTHH:MM"
            // atau input text: "YYYY-MM-DD HH:MM"
            $start = trim((string)($data['schedule_start'] ?? ''));
            $end   = trim((string)($data['schedule_end'] ?? ''));

            $start = str_replace('T', ' ', $start);
            $end   = str_replace('T', ' ', $end);

            $payload = [
                'test_access_id' => trim((string)($data['test_access_id'] ?? '')),
                'test_url' => trim((string)($data['test_url'] ?? 'https://test.jecaid.com')),
                'status' => (string)($data['status'] ?? 'waiting'), // waiting|open|closed
                'test_location_type' => (string)($data['test_location_type'] ?? 'online'),
                'test_location_detail' => trim((string)($data['test_location_detail'] ?? '')),
            ];

            $payload['schedule_start'] = ($start !== '') ? new FrozenTime($start) : null;
            $payload['schedule_end']   = ($end !== '') ? new FrozenTime($end) : null;

            $item = $OnlineTests->patchEntity($item, $payload);

            if ($OnlineTests->save($item)) {
                $this->Flash->success('Tes online berhasil diperbarui.');
                return $this->redirect(['prefix' => 'Admin', 'controller' => 'OnlineTests', 'action' => 'index']);
            }

            $this->Flash->error('Gagal menyimpan data tes online.');
        }

        $this->set(compact('item'));
    }
}
