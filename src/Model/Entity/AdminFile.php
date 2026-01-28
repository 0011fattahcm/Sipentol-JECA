<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AdminFile extends Entity
{
    protected array $_accessible = [
        'key_name' => true,
        'file_path' => true,
        'file_name' => true,
        'mime_type' => true,
        'created' => true,
        'modified' => true,
    ];
}
