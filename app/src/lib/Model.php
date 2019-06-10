<?php

/**  */
namespace Test\lib;

/**
 * Class Model
 */
abstract class Model
{
    /** @var \PDO */
    public static $connection;

    protected $object;

    abstract public function getTableName();

    /**
     * @param object $object
     */
    public function set($object)
    {
        $this->object = $object;
    }

    /**
     * @return object
     */
    public function get()
    {
        return $this->object;
    }

    public function store($object = null)
    {
        if (null === $object) {
            if (!$this->object) {
                return;
            }
            $object = $this->object;
        }

        $tableName = $this->getTableName();
        $values = [];
        if (isset($object->id)) {
            $sets = [];
            foreach (\array_keys((array) $object) as $varName) {
                if ($varName !== 'id') {
                    $sets[] = sprintf('%s = :%s', $varName, $varName);
                }
                $values[':'.$varName] = $object->$varName;
            }
            $set = \implode(',', $sets);
            $stmt = self::$connection->prepare("UPDATE `$tableName` SET $set WHERE id = :id");
        } else {
            $places = [];
            $valuesPlaces = [];
            foreach (\array_keys((array) $object) as $varName) {
                $places[] = sprintf(':%s', $varName);
                $valuesPlaces[] = $varName;
                $values[':'.$varName] = $object->$varName;
            }
            $placesStr = \implode(',', $places);
            $valuesPlacesStr = implode(',', $valuesPlaces);
            $stmt = self::$connection->prepare("INSERT INTO `$tableName` ($valuesPlacesStr) VALUES ($placesStr)");
        }

        foreach ($values as $name => $value) {
            $stmt->bindValue($name, $value);
        }
        $stmt->execute();

        if (!isset($object->id)) {
            $object->id = self::$connection->lastInsertId();
        }
        if (!$this->object) {
            $this->object = $object;
        }
    }

    /**
     * @param integer $objId
     *
     * @return null|array
     */
    public function find($objId)
    {
        $tableName = $this->getTableName();
        $stmt = self::$connection->prepare("SELECT * FROM `$tableName` WHERE id = :id");
        $stmt->bindValue(':id', $objId);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * @param array $fields
     *
     * @return null|array
     */
    public function findBy($fields, $limit = 200, $offset = 0)
    {
        $tableName = $this->getTableName();
        $whereData = [];
        foreach ($fields as $name => $value) {
            $whereData[] = \sprintf('%s = :%s', $name, $name);
        }
        $whereClause = \join(',', $whereData);
        $stmt = self::$connection->prepare("SELECT * FROM `$tableName` WHERE $whereClause  LIMIT $limit OFFSET $offset");
        $stmt->execute($fields);

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * @param array $fields
     *
     * @return object|null
     */
    public function findOneBy($fields)
    {
        $result = $this->findBy($fields);
        if (\count($result)) {
            return $result[0];
        }

        return null;
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return null|array
     */
    public function findAll($limit = 200, $offset = 0)
    {
        $tableName = $this->getTableName();
        $stmt = self::$connection->prepare("SELECT * FROM `$tableName` LIMIT $limit OFFSET $offset");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function create()
    {

    }
}
