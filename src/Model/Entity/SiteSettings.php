<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class SiteSettings extends Entity
{
    /**
     * Mass assignment rules.
     * - id biasanya tidak boleh di-mass assign.
     */
    protected array $_accessible = [
        'key_name' => true,
        'value_text' => true,
        'created' => true,
        'modified' => true,
    ];
}
