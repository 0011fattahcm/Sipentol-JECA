<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class OnlineTest extends Entity
{
    // Mass assignment
    protected array $_accessible = [
        'user_id' => true,
        'status' => true,
        'test_url' => true,
        'test_access_id' => true,
        'schedule_start' => true,
        'schedule_end' => true,

        // ✅ tambahin ini
        'test_location_type' => true,
        'test_location_detail' => true,

        'admin_note' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
    ];
}
