<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\Datasource\EntityInterface;

class SiteSettingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('site_settings');
        $this->setDisplayField('key_name');
        $this->setPrimaryKey('id');

        // created / modified
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('key_name')
            ->maxLength('key_name', 100, 'Key terlalu panjang (maks 100 karakter).')
            ->requirePresence('key_name', 'create', 'Key wajib diisi.')
            ->notEmptyString('key_name', 'Key wajib diisi.');

        $validator
            ->scalar('value_text')
            ->allowEmptyString('value_text'); // boleh kosong

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // key_name harus unik
        $rules->add($rules->isUnique(
            ['key_name'],
            'Key ini sudah ada.'
        ));

        return $rules;
    }

    /**
     * Helper opsional: ambil value berdasarkan key (return string|null)
     */
    public function getValue(string $keyName): ?string
    {
        $row = $this->find()
            ->select(['value_text'])
            ->where(['key_name' => $keyName])
            ->first();

        return $row ? (string)$row->value_text : null;
    }

    /**
     * Helper opsional: set value (upsert) berdasarkan key
     */
    public function setValue(string $keyName, ?string $valueText): EntityInterface
    {
        $entity = $this->find()
            ->where(['key_name' => $keyName])
            ->first();

        if (!$entity) {
            $entity = $this->newEmptyEntity();
            $entity->key_name = $keyName;
        }

        $entity->value_text = $valueText ?? '';

        return $this->saveOrFail($entity);
    }
}
