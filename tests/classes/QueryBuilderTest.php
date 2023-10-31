<?php


namespace classes;
use app\database\DB;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    public function test_get_simple_select() : void
    {
        $query = DB::table('users')->toSql();
        $this->assertEquals("SELECT * FROM users",$query);
    }

    public function test_select_get_conditional() : void
    {
        $query = DB::table('users')->where('id', '>',10)->toSql();
        $this->assertEquals("SELECT * FROM users WHERE id > :id", $query);
    }

    public function test_get_select_with_more_than_one_conditional() : void
    {
        $query = DB::table('users')
            ->where('id', 10)
            ->where('firstname', '=', 'John')
            ->toSql();
        $this->assertEquals("SELECT * FROM users WHERE id = :id AND firstname = :firstname", $query);
    }

    public function test_get_select_with_more_than_one_conditional_and_use_type_conditional() : void
    {
        $query = DB::table('users')
            ->where('id', 10)
            ->where('name','Tom')
            ->orWhere(function ($query) {
                return $query->where('name', '=', 'John')->where('age', '>', 20);
                })
            ->toSql();
        $this->assertEquals("SELECT * FROM users WHERE id = :id AND name = :name OR (name = :name AND age > :age)", $query);
    }

    public function test_select_get_order_by()
    {
        $query = DB::table('users')->orderBy('name')->toSql();
        $this->assertEquals("SELECT * FROM users ORDER BY name ASC", $query);
    }
    public function test_select_with_conditions_get_order_by()
    {
        $query = DB::table('users')
            ->where('age','>', 10)
            ->orderBy('name','desc')->toSql();
        $this->assertEquals("SELECT * FROM users WHERE age > :age ORDER BY name DESC", $query);
    }

    public function test_select_limit_and_conditional_and_order()
    {
        $query = DB::table('users')
            ->where('age' ,'>', 10)
            ->orderBy('name', 'desc')
            ->limit(10)
            ->toSql();
        $this->assertEquals("SELECT * FROM users WHERE age > :age ORDER BY name DESC LIMIT 10", $query);
    }

    public function test_select_fields()
    {
        $query = DB::table('users')->select('name','age')->toSql();
        $this->assertEquals("SELECT name, age FROM users", $query);
    }
}