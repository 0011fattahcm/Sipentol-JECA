<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\Datasource\EntityInterface;

class OnlineTestsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('online_tests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // created, modified
        $this->addBehavior('Timestamp');

        // Relasi: online_tests.user_id -> users.id
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

    $validator
        ->scalar('status')
        ->inList('status', ['waiting', 'open', 'closed'], 'Status tidak valid.')
        ->requirePresence('status', 'create')
        ->notEmptyString('status');

    $validator
        ->scalar('test_url')
        ->maxLength('test_url', 255)
        ->requirePresence('test_url', 'create')
        ->notEmptyString('test_url')
        ->url('test_url', 'Format URL test tidak valid.');

    $validator
        ->scalar('test_access_id')
        ->maxLength('test_access_id', 50)
        ->allowEmptyString('test_access_id');

    $validator
        ->dateTime('schedule_start')
        ->allowEmptyDateTime('schedule_start');

    $validator
        ->dateTime('schedule_end')
        ->allowEmptyDateTime('schedule_end');

    // ===== Lokasi Tes =====
    $validator
        ->scalar('test_location_type')
        ->maxLength('test_location_type', 20)
        ->allowEmptyString('test_location_type') // biar aman utk row lama; DB default bisa handle
        ->inList('test_location_type', ['lpk_jeca', 'onsite', 'online', 'custom'], 'Tipe lokasi tidak valid.');

    $validator
        ->scalar('test_location_detail')
        ->maxLength('test_location_detail', 255)
        ->allowEmptyString('test_location_detail');

    // wajib isi detail jika custom
    $validator->add('test_location_detail', 'requiredIfCustom', [
        'rule' => function ($value, $context) {
            $type = $context['data']['test_location_type'] ?? null;
            if ($type === 'custom') {
                return trim((string)$value) !== '';
            }
            return true;
        },
        'message' => 'Lokasi luar wajib diisi jika tipe lokasi = Lokasi Luar.',
    ]);

    // optional: pastikan end >= start kalau dua-duanya diisi
    $validator->add('schedule_end', 'endAfterStart', [
        'rule' => function ($value, $context) {
            $start = $context['data']['schedule_start'] ?? null;
            if (empty($start) || empty($value)) {
                return true;
            }
            // Cake biasanya kasih FrozenTime, tapi aman kalau string:
            try {
                $startTs = is_object($start) ? $start->getTimestamp() : strtotime((string)$start);
                $endTs   = is_object($value) ? $value->getTimestamp() : strtotime((string)$value);
                return $endTs >= $startTs;
            } catch (\Throwable $e) {
                return false;
            }
        },
        'message' => 'Jadwal selesai harus setelah jadwal mulai.',
    ]);

    // optional: kalau status open, wajib ada access id
    $validator->add('test_access_id', 'requiredIfOpen', [
        'rule' => function ($value, $context) {
            $status = $context['data']['status'] ?? null;
            if ($status === 'open') {
                return trim((string)$value) !== '';
            }
            return true;
        },
        'message' => 'ID Tes wajib diisi jika status = open.',
    ]);

    $validator
        ->scalar('admin_note')
        ->allowEmptyString('admin_note');

    return $validator;
}


    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // user_id harus valid
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        // unik: 1 user hanya 1 row online_tests
        $rules->add($rules->isUnique(['user_id'], 'Online test untuk user ini sudah ada.'), [
            'errorField' => 'user_id',
        ]);

        return $rules;
    }
}
