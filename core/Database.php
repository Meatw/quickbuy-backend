<?php

class Database
{
    private $connection;
    
    public function __construct(array $config)
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function query($sql, $params = [])
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        
        return $statement;
    }
    
    public function find($table, $id)
    {
        $statement = $this->query("SELECT * FROM {$table} WHERE id = ?", [$id]);
        return $statement->fetch();
    }
    
    public function findBy($table, $column, $value)
    {
        $statement = $this->query("SELECT * FROM {$table} WHERE {$column} = ?", [$value]);
        return $statement->fetch();
    }
    
    public function findAll($table, $conditions = [], $orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$table}";
        $params = [];
        
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $clauses = [];
            
            foreach ($conditions as $column => $value) {
                $clauses[] = "{$column} = ?";
                $params[] = $value;
            }
            
            $sql .= implode(' AND ', $clauses);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $statement = $this->query($sql, $params);
        return $statement->fetchAll();
    }
    
    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, array_values($data));
        
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $id, $data)
    {
        $sets = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE id = ?";
        
        return $this->query($sql, $params)->rowCount();
    }
    
    public function delete($table, $id)
    {
        $sql = "DELETE FROM {$table} WHERE id = ?";
        return $this->query($sql, [$id])->rowCount();
    }
    
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }
    
    public function commit()
    {
        $this->connection->commit();
    }
    
    public function rollback()
    {
        $this->connection->rollBack();
    }
}
