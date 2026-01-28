<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ActivityLog extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     * Set '*' to false to prevent mass assignment of unknown fields.
     */
    protected array $_accessible = [
        'user_id' => true,
        'aktivitas' => true,
        'created' => true,
        'user' => true,
    ];
}
