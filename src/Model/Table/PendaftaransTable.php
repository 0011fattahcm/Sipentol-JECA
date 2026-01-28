<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class PendaftaransTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pendaftarans');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        // REQUIRED
        $validator
            ->requirePresence('nik', 'create')
            ->notEmptyString('nik', 'NIK wajib diisi.')

            ->requirePresence('nama_lengkap', 'create')
            ->notEmptyString('nama_lengkap', 'Nama lengkap wajib diisi.')

            ->requirePresence('jenis_kelamin', 'create')
            ->notEmptyString('jenis_kelamin', 'Jenis kelamin wajib diisi.')

            ->requirePresence('tanggal_lahir', 'create')
            ->notEmptyDate('tanggal_lahir', 'Tanggal lahir wajib diisi.')

            ->requirePresence('tinggi_badan', 'create')
            ->notEmptyString('tinggi_badan', 'Tinggi badan wajib diisi.')
            ->numeric('tinggi_badan', 'Tinggi badan harus angka.')

            ->requirePresence('berat_badan', 'create')
            ->notEmptyString('berat_badan', 'Berat badan wajib diisi.')
            ->numeric('berat_badan', 'Berat badan harus angka.')

            ->requirePresence('alamat_lengkap', 'create')
            ->notEmptyString('alamat_lengkap', 'Alamat lengkap wajib diisi.')

            ->requirePresence('domisili_saat_ini', 'create')
            ->notEmptyString('domisili_saat_ini', 'Domisili saat ini wajib diisi.')

            // Pendidikan
            ->requirePresence('pendidikan_jenjang', 'create')
            ->notEmptyString('pendidikan_jenjang', 'Jenjang terakhir wajib diisi.')

            ->requirePresence('pendidikan_instansi', 'create')
            ->notEmptyString('pendidikan_instansi', 'Nama instansi wajib diisi.')

            ->requirePresence('pendidikan_jurusan', 'create')
            ->notEmptyString('pendidikan_jurusan', 'Jurusan wajib diisi.')

            ->requirePresence('pendidikan_tahun_kelulusan', 'create')
            ->notEmptyString('pendidikan_tahun_kelulusan', 'Tahun kelulusan wajib diisi.')
            ->numeric('pendidikan_tahun_kelulusan', 'Tahun kelulusan harus angka.')

            // Keluarga
            ->requirePresence('ayah_nama', 'create')
            ->notEmptyString('ayah_nama', 'Nama ayah wajib diisi.')
            ->requirePresence('ayah_usia', 'create')
            ->notEmptyString('ayah_usia', 'Usia ayah wajib diisi.')
            ->numeric('ayah_usia')

            ->requirePresence('ayah_pekerjaan', 'create')
            ->notEmptyString('ayah_pekerjaan', 'Pekerjaan ayah wajib diisi.')

            ->requirePresence('ibu_nama', 'create')
            ->notEmptyString('ibu_nama', 'Nama ibu wajib diisi.')
            ->requirePresence('ibu_usia', 'create')
            ->notEmptyString('ibu_usia', 'Usia ibu wajib diisi.')
            ->numeric('ibu_usia')

            ->requirePresence('ibu_pekerjaan', 'create')
            ->notEmptyString('ibu_pekerjaan', 'Pekerjaan ibu wajib diisi.')

            // Kontak
            ->requirePresence('email', 'create')
            ->notEmptyString('email', 'Email wajib diisi.')
            ->email('email', false, 'Format email tidak valid.')

            ->requirePresence('whatsapp', 'create')
            ->notEmptyString('whatsapp', 'Whatsapp wajib diisi.');

        // Optional URL
        $validator
            ->allowEmptyString('instagram_url')
            ->allowEmptyString('facebook_url');

        $validator
        ->requirePresence('info_sumber', 'create')
        ->notEmptyString('info_sumber', 'Sumber informasi pendaftaran wajib dipilih.');

    // optional fields (akan divalidasi kondisional di controller)
    $validator
        ->allowEmptyString('info_referral_code')
        ->maxLength('info_referral_code', 100);

    $validator
        ->allowEmptyString('info_instansi_nama')
        ->maxLength('info_instansi_nama', 150);

    $validator
        ->allowEmptyString('info_sumber_lain')
        ->maxLength('info_sumber_lain', 150);    

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['user_id'], 'Data pendaftaran untuk user ini sudah ada.'));
        $rules->add($rules->isUnique(['nik'], 'NIK sudah digunakan.'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }
}
