<?php

/**  */
namespace Test\Model;

use Test\lib\Model;

/**
 * Class UserModel
 */
class UserModel extends Model
{
    public function getTableName()
    {
        return 'user';
    }
}
