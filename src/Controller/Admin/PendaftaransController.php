<?php
declare(strict_types=1);

namespace App\Controller\Admin;

class PendaftaransController extends AppController
{
    public function index()
    {
        $q = (string)$this->request->getQuery('q', '');
        $limit = (int)$this->request->getQuery('limit', 10);
        if (!in_array($limit, [10, 20, 50, 100], true)) {
            $limit = 10;
        }

        // Aman (tanpa loadModel)
        $Pendaftarans = $this->fetchTable('Pendaftarans');

        $query = $Pendaftarans->find()
            ->orderDesc('Pendaftarans.created');

        if ($q !== '') {
            $like = '%' . trim($q) . '%';
            $query->where([
                'OR' => [
                    'Pendaftarans.nama_lengkap LIKE' => $like,
                    'Pendaftarans.nik LIKE' => $like,
                    'Pendaftarans.email LIKE' => $like,
                    'Pendaftarans.whatsapp LIKE' => $like,
                ]
            ]);
        }

        $pendaftarans = $this->paginate($query, [
            'limit' => $limit,
        ]);

        $this->set(compact('pendaftarans', 'q', 'limit'));
    }

    public function view($id = null)
{
    $Pendaftarans = $this->fetchTable('Pendaftarans');

    $pendaftaran = $Pendaftarans->get((int)$id, [
        'contain' => ['Users'],
    ]);

    $this->set(compact('pendaftaran'));
}
}
