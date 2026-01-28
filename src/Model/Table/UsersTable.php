<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Query;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        // Relasi 1-1: users.id -> pendaftarans.user_id
        // Dipakai untuk kebutuhan admin (search NIK/WA)
        $this->hasOne('Pendaftarans', [
            'foreignKey' => 'user_id',
        ]);

        // Relasi balik untuk online_tests (buat leftJoinWith di admin)
        $this->hasOne('OnlineTests', [
            'foreignKey' => 'user_id',
        ]);

        $this->hasOne('DaftarUlangs', [
            'foreignKey' => 'user_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('nama_lengkap', 'Nama wajib diisi.')
            ->notEmptyString('email', 'Email wajib diisi.')
            ->email('email', false, 'Format email tidak valid.')
            ->notEmptyString('password', 'Password wajib diisi.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email'], 'Email sudah digunakan'));
        return $rules;
    }

    public function findAuth(Query $query, array $options)
    {
        return $query->where([
            'Users.is_verified' => 1,
            'Users.is_active' => 1,
        ]);
    }

    public function hashPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
}
