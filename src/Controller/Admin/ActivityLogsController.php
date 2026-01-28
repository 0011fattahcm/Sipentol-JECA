<?php
declare(strict_types=1);

namespace App\Controller\Admin;

class ActivityLogsController extends AppController
{
    public function index()
    {
        $q = (string)$this->request->getQuery('q', '');
        $limit = (int)$this->request->getQuery('limit', 20);
        if (!in_array($limit, [10, 20, 50, 100], true)) $limit = 20;

        $query = $this->ActivityLogs->find()
            ->contain(['Users'])
            ->orderDesc('ActivityLogs.id');

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'ActivityLogs.aktivitas LIKE' => '%' . $q . '%',
                    'Users.email LIKE' => '%' . $q . '%',
                    'Users.nama_lengkap LIKE' => '%' . $q . '%', // FIX
                    'ActivityLogs.user_id' => ctype_digit($q) ? (int)$q : null, // optional: search by user_id if numeric
                ]
            ]);
        }

        $logs = $this->paginate($query, ['limit' => $limit]);

        $this->set(compact('logs', 'q', 'limit'));
    }
}
