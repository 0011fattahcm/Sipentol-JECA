<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Http\Exception\NotFoundException;

class AdminFilesController extends AppController
{
    public function initialize(): void
{
    parent::initialize();
    $this->viewBuilder()->setLayout('admin');
}

    public function daftarUlangDrafts()
    {
        $this->request->allowMethod(['get','post','put','patch']);

        $AdminFiles = $this->fetchTable('AdminFiles');

        $keys = [
            'daftar_ulang_formulir' => 'Draft Formulir Pendaftaran (PDF)',
            'daftar_ulang_perjanjian' => 'Draft Surat Perjanjian (PDF)',
            'daftar_ulang_persetujuan_ortu' => 'Draft Persetujuan Orang Tua (PDF)',
        ];

        $existing = $AdminFiles->find()
            ->where(['key_name IN' => array_keys($keys)])
            ->all()
            ->indexBy('key_name')
            ->toArray();

        if ($this->request->is(['post','put','patch'])) {
            foreach ($keys as $key => $label) {
                $file = $this->request->getData($key);
                if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) continue;
                if ($file->getError() !== UPLOAD_ERR_OK) continue;
                if ($file->getClientMediaType() !== 'application/pdf') continue;

                $dirRel = 'uploads/admin/drafts/';
                $dirAbs = WWW_ROOT . $dirRel;
                if (!is_dir($dirAbs)) mkdir($dirAbs, 0775, true);

                $name = $key . '_' . time() . '.pdf';
                $file->moveTo($dirAbs . $name);

                $row = $existing[$key] ?? $AdminFiles->newEmptyEntity();
                $row->set([
                    'key_name' => $key,
                    'file_path' => '/' . $dirRel . $name,
                    'file_name' => $name,
                    'mime_type' => 'application/pdf'
                ]);

                $AdminFiles->saveOrFail($row);
            }

            $this->Flash->success('Draft Daftar Ulang berhasil disimpan.');
            return $this->redirect(['action'=>'daftarUlangDrafts']);
        }

        $this->set(compact('keys','existing'));
    }

    public function download(string $key)
    {
        $AdminFiles = $this->fetchTable('AdminFiles');
        $row = $AdminFiles->find()->where(['key_name'=>$key])->first();

        if (!$row || empty($row->file_path)) {
            throw new NotFoundException('File tidak ditemukan.');
        }

        $abs = WWW_ROOT . ltrim($row->file_path,'/');
        return $this->response->withFile($abs, [
            'download'=>true,
            'name'=>$row->file_name
        ]);
    }
}
