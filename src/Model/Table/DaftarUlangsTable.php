<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class DaftarUlangsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('daftar_ulangs');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id', 'User wajib ada.');

        // path files
        $validator->scalar('formulir_pendaftaran_pdf')->maxLength('formulir_pendaftaran_pdf', 255)->allowEmptyString('formulir_pendaftaran_pdf');
        $validator->scalar('bukti_pembayaran_img')->maxLength('bukti_pembayaran_img', 255)->allowEmptyString('bukti_pembayaran_img');
        $validator->scalar('surat_perjanjian_pdf')->maxLength('surat_perjanjian_pdf', 255)->allowEmptyString('surat_perjanjian_pdf');
        $validator->scalar('surat_persetujuan_orangtua_pdf')->maxLength('surat_persetujuan_orangtua_pdf', 255)->allowEmptyString('surat_persetujuan_orangtua_pdf');

        $validator
            ->scalar('status')
            ->inList('status', ['draft', 'submitted', 'verified'], 'Status tidak valid.')
            ->notEmptyString('status');

        return $validator;
    }
}
