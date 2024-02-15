<?php

namespace App\Models;

use App\Plugins\Di\Injectable;
use App\Plugins\Di\Factory;

abstract class BaseModel extends Injectable {
    protected $db;

    public function __construct() {
        $di = Factory::getDi();
        $this->db = $di->getShared("db");
    }

    abstract public function create($params);
    abstract public function getById($id);
    abstract public function getAll();
    abstract public function update($id, $params);
    abstract public function delete($id);
    abstract public function search($params);
    
}