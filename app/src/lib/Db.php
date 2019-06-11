<?php

/**  */
namespace Test\lib;

/**
 * Class Db
 */
class Db
{
    const CONNECTION_STRING_FORMAT = 'mysql:host=%s;port=%d;dbname=%s';

    /** @var \PDO  */
    private $connection;

    /**
     * Db constructor.
     *
     * @param string  $host
     * @param integer $port
     * @param string  $dbName
     * @param string  $user
     * @param string  $psw
     */
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

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
