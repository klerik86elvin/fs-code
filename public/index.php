<?php
require '../vendor/autoload.php';

use app\database\DB;

$data = DB::table('users')->get();
var_dump($data);