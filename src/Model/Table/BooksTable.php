<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Database\Type;

class User extends Entity
{
  
    protected $_accessible = [
        'id' => true,
        'book_name' => true,
        'category' => true,
        'image' => true,

    ];
}