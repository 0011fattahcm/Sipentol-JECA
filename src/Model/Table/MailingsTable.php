<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class MailingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mailings');
        $this->setPrimaryKey('id');
    }
}
