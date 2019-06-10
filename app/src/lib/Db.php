<?php
/**
 * Project app.
 * User: ds40a
 * Date: 26.05.19
 * Time: 13:14
 */

namespace Test\lib;

/**
 * Class Db
 */
class Db
{
    const CONNECTION_STRING_FORMAT = 'mysql:host=%s;port=%d;dbname=%s';

    private $connection;

    public function __construct($host, $port, $dbName, $user, $psw)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->psw  = $psw;

        $this->connection = new \PDO(
            sprintf(
                self::CONNECTION_STRING_FORMAT,
                $host,
                $port,
                $dbName
            ),
            $user,
            $psw
        );
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
