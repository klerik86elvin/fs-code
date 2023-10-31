<?php

namespace app\database;
class QueryBuilder
{
    private string $sql = '';
    private string $select = "*";
    public array $binds = [];
    private string $table = '';
    private bool $where = false;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function where(string $field, string $operation , mixed $value = null) : QueryBuilder
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
    public function orWhere(callable $callback) : QueryBuilder
    {
        $queryBuilder = new QueryBuilder($this->table);
        $callback($queryBuilder);
        $this->sql .= " OR (". str_replace(' WHERE ','',$queryBuilder->sql) . ")";
        $this->binds = [...$this->binds, ...$queryBuilder->binds];
        return $this;
    }
    public function orderBy(string $field, $sort = 'asc') : QueryBuilder
    {
        $this->sql .= " ORDER BY {$field} ".strtoupper($sort);
        return $this;
    }

    public function select() : QueryBuilder
    {
        $this->select = implode(', ', func_get_args());
        return $this;

    }
    public function limit(int $limit) : QueryBuilder
    {
        $this->sql .= " LIMIT {$limit}";
        return $this;
    }
    public function toSql() : string
    {
        $this->sql = "SELECT {$this->select}"." FROM " .$this->table.$this->sql;
        return $this->sql;
    }
    public function get() : array
    {
        $this->toSql();
        $connection = Connection::getConnection();
        $prepare = $connection->prepare($this->sql);
        $prepare->execute($this->binds);
        return $prepare->fetchAll();
    }

}