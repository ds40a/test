<?php

/**  */
namespace Test\Model;

use Test\lib\Model;

/**
 * Class UserModel
 */
class UserModel extends Model
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'user';
    }
}
