<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class AdminsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('admins');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('username')->maxLength('username', 50)->requirePresence('username', 'create')->notEmptyString('username')
            ->scalar('password')->maxLength('password', 255)->requirePresence('password', 'create')->notEmptyString('password')
            ->scalar('name')->maxLength('name', 100)->requirePresence('name', 'create')->notEmptyString('name')
            ->boolean('is_active')->allowEmptyString('is_active');

        return $validator;
    }
}
