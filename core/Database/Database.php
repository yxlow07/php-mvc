<?php

namespace core\Database;

use app\Models\LoginModel;
use app\Models\UserModel;
use core\Models\BaseModel;

class Database
{
    public \PDO $pdo;

    public function __construct($config = [])
    {
        $dsn = $config['DSN'] ?? '';
        $username = $config['USERNAME'] ?? 'root';
        $password = $config['PASSWORD'] ?? '';

        $this->pdo = new \PDO($dsn, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    public function prepare(string $sql) : \PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    public function insert(string $table, array $attributes, array|object $values): bool
    {
        $params = implode(',', array_map(fn($attr) => ":$attr", $attributes));
        $attributes = implode(',', $attributes);
        $counter = 0;

        $statement = $this->prepare("INSERT INTO $table ($attributes) VALUES ($params)");

        foreach (explode(',', $attributes) as $attribute) {
            // For anyone that don't understand, basically I check if it is an object, if it is then straight query the attribute of the object
            // If not, then I check if values is an associative array, if it is I straight access it with [] operation, if not then I use integer accessing and ++ (CP Knowledge)
            $value = !(is_object($values)) ? (array_key_exists($attribute, $values) ? $values[$attribute] : $values[$counter++]) : $values->{$attribute};
            $statement->bindValue(":$attribute", $value);
        }

        return $statement->execute();
    }

    /**
     * This function differs from upsert function. Using this function, updated ids still will update the row using old row data
     * @param string $table
     * @param array $attributes
     * @param array|object $values If object is passed, must be instanceof BaseModel
     * @param array $conditions
     * @return bool
     */
    public function update(string $table, array $attributes, array|object $values, array $conditions, bool $needCheckIfExists = false): bool
    {
        $updateFields = implode(', ', array_map(fn($attr) => "$attr = :$attr", $attributes));
        $selectConditions = implode(' AND ', array_map(fn($attr) => "$attr = :$attr", array_keys($conditions)));

        $statement = $this->prepare("UPDATE $table SET $updateFields WHERE $selectConditions");

        // Bind values
        if (is_array($values)) {
            foreach ($values as $attribute => &$value) {
                $statement->bindValue(":$attribute", $value);
            }
        } else {
            foreach ($attributes as $attribute) {
                $statement->bindValue(":$attribute", $values->{$attribute} ?? '');
            }
        }

        // Bind conditions
        foreach ($conditions as $attribute => &$condition) {
            $statement->bindValue(":$attribute", $condition);
        }

        if ($needCheckIfExists) {
            if ($this->findOne($table, $conditions)) {
                return $statement->execute();
            } else {
                return $this->insert($table, $attributes, $values);
            }
        } else {
            return $statement->execute();
        }
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param array $selectAttributes
     * @param $class
     * @param bool $fetchObject
     * @param bool $isSearch
     * @return false|mixed|object|\stdClass|null
     */
    public function findOne($table, $conditions = [], $selectAttributes = ['*'], $class = null, $fetchObject = true, $isSearch = false)
    {
        $selectAttributes = implode(',', $selectAttributes);
        if (!$isSearch) {
            $selectConditions = implode(' OR ', array_map(fn($attr) => "$attr = :$attr", array_keys($conditions)));
        } else {
            $conditionsArray = [];
            foreach ($conditions as $attribute => $searchPattern) {
                $conditionsArray[] = "$attribute LIKE :$attribute";
            }
            $selectConditions = implode(' OR ', $conditionsArray);
        }


        $sql = "SELECT $selectAttributes FROM $table WHERE $selectConditions";
        $statement = $this->prepare($sql);

        foreach ($conditions as $attribute => $condition) {
            $statement->bindValue(":$attribute", $condition);
        }

        $statement->execute();

        return $fetchObject ? $statement->fetchObject($class) : $statement->fetchColumn();
    }

    /**
     * @param string $table
     * @param array $selectAttributes
     * @param array $conditions
     * @param $class
     * @param bool $fetchObject
     * @param bool $isSearch
     * @return false|array
     */
    public function findAll($table, $selectAttributes = ['*'], $conditions = [], $class = null, $fetchObject = false, $isSearch = false)
    {
        $selectAttributes = implode(',', $selectAttributes);

        if (!$isSearch) {
            // Build the WHERE clause based on conditions
            $selectConditions = '';
            if (!empty($conditions)) {
                $selectConditions = 'WHERE ' . implode(' AND ', array_map(fn($attr) => "$attr = :$attr", array_keys($conditions)));
            }
        } else {
            $conditionsArray = [];
            foreach ($conditions as $attribute => $searchPattern) {
                $conditionsArray[] = "$attribute LIKE :$attribute";
            }
            $selectConditions = 'WHERE ' . implode(' OR ', $conditionsArray);
        }

        $sql = "SELECT $selectAttributes FROM $table $selectConditions";
        $statement = $this->prepare($sql);

        // Bind values for conditions
        foreach ($conditions as $attribute => $condition) {
            $statement->bindValue(":$attribute", $condition);
        }

        $statement->execute();

        // Fetch results based on the fetchObject parameter
        if ($fetchObject) {
            return $statement->fetchAll(\PDO::FETCH_CLASS, $class);
        } else {
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function delete(string $table, array $conditions = []): bool
    {
        // Build the WHERE clause
        $whereClause = implode(' AND ', array_map(fn($attr) => "$attr = :$attr", array_keys($conditions)));

        // Construct the SQL query
        $sql = "DELETE FROM $table WHERE $whereClause";

        // Prepare and execute the delete query
        $statement = $this->prepare($sql);

        // Bind values
        foreach ($conditions as $attribute => $condition) {
            $statement->bindValue(":$attribute", $condition);
        }

        // Execute the delete query and return the result
        return $statement->execute();
    }
}