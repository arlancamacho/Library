<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property bool $status
 */
class User extends Entity
{

    protected $_accessible = [
        'id' => true,
        'book_name' => true,
        'category' => true,
        'image' => true,
    ];
}