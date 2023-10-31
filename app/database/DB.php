<?php

namespace app\database;


class DB
{
    public static function table($table): QueryBuilder
    {
        $queryBuilder = new QueryBuilder($table);
        return $queryBuilder;
    }
}