<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class PengumumanTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pengumuman');
        $this->setDisplayField('judul');
        $this->setPrimaryKey('id');

        // kalau kamu belum pakai Timestamp di table ini, biarkan saja
        // $this->addBehavior('Timestamp', [
        //     'events' => ['Model.beforeSave' => ['created_at' => 'new']]
        // ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('judul')->maxLength('judul', 255)->requirePresence('judul', 'create')->notEmptyString('judul')
            ->scalar('isi')->requirePresence('isi', 'create')->notEmptyString('isi')
            ->inList('target', ['semua', 'tertentu'], 'Target harus semua atau tertentu')
            ->boolean('is_active');

        // target_user_ids boleh kosong kalau target = semua
        $validator->allowEmptyString('target_user_ids');

        return $validator;
    }
}
