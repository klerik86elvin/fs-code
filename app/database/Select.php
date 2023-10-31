<?php

namespace app\database;

final class Select
{
    private string $sql = '';
    private bool $where = false;
    public function query(string $query)
    {
        $this->sql = $query;
        return $this;
    }

    public function where(string $field, string $operation , mixed $value = null)
    {
        if ($value === null){
            $value = $operation;
            $operation = "=";
        }
        if (!$this->where){
            $this->sql .= " WHERE {$field} {$operation} :{$field}";
            $this->where = true;
        }
        else {
            $this->sql .= " AND {$field} {$operation} :{$field}";
        }
        $this->binds[$field] = $value;
        return $this;
    }
    public function orWhere(callable $callback){
        $queryBuilder = new Select();
        $callback($queryBuilder);
        $this->sql .= " OR (". str_replace(' WHERE ','',$queryBuilder->sql) . ")";
        return $this;
    }
    public function orderBy(string $field, $sort = 'asc')
    {
        $this->sql .= " ORDER BY {$field} ".strtoupper($sort);
        return $this;
    }

    public function select(array $fields = ["*"])
    {
        return $fields;
    }
    public function limit(int $limit)
    {
        $this->sql .= " LIMIT {$limit}";
        return $this;
    }
    public function get()
    {
        return $this->sql;
    }
}