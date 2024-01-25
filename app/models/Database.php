<?php

class Database {
    private $pdo;

    public function __construct($_CONFIG) {
        try {
            $dsn = "mysql:host={$_CONFIG['db']['host']};dbname={$_CONFIG['db']['name']};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $_CONFIG['db']['user'], $_CONFIG['db']['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
    
            foreach ($params as $key => &$val) {
                if ($key == ':limit') {
                    $stmt->bindValue($key, (int)$val, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $val);
                }
            }
    
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function select($columns, $table, $conditions = [], $limit = null, $order = null) {
        $sql = "SELECT $columns FROM $table";
        $params = [];

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = "$key = :$key";
                $params[":$key"] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
    
        if (!empty($limit)) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }

        if (!empty($order)) {
            $sql .= " ORDER BY $order";
        }

        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function simpleSelect($columns, $table, $conditions = []) {
        $sql = "SELECT $columns FROM $table";
        $params = [];
    
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = "$key = :$key";
                $params[":$key"] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }
    
        return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key=:$key";
        }
        $set = implode(', ', $setParts);

        // Préparation des conditions where
        $whereParts = [];
        $whereParams = [];
        foreach ($where as $key => $value) {
            $whereKey = "where_$key";
            $whereParts[] = "$key=:$whereKey";
            $whereParams[":$whereKey"] = $value;
        }
        $whereSQL = implode(' AND ', $whereParts);

        // Construction de la requête SQL
        $sql = "UPDATE $table SET $set WHERE $whereSQL";

        // Fusion des paramètres de mise à jour et des conditions where
        $params = array_merge($data, $whereParams);

        // Exécution de la requête
        $this->query($sql, $params);
    }

    public function delete($table, $where) {
        $whereParts = [];
        $params = [];
        foreach ($where as $key => $value) {
            $whereParts[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        $whereSQL = implode(' AND ', $whereParts);
        $sql = "DELETE FROM $table WHERE $whereSQL";
        $this->query($sql, $params);
    }
}
?>