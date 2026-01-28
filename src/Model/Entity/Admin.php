<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Admin extends Entity
{
    protected array $_accessible = [
        '*' => true,
        'id' => false,
    ];

    // jangan expose password
    protected array $_hidden = ['password'];
}
