<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ActivityLogsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('logs');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT', // FIX
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id')

            ->scalar('aktivitas')
            ->maxLength('aktivitas', 255)
            ->requirePresence('aktivitas', 'create')
            ->notEmptyString('aktivitas')

            ->dateTime('created')
            ->allowEmptyDateTime('created');
    }
}
