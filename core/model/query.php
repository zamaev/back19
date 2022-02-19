<?php

/**
 * $query = $model->query($query)
 * 
 * $query->fetch()
 * $query->fetchAll()
 * $query->entity()
 * $query->entityAll()
 * 
 */

/**
 * пока не знаю стоит ли в конструкторе формировать select
 * вдург я добавлю insert и update
 * хотя эти задачи и решает entity
 * 
 * возможно я отдельно вынесу select и не буду через конструктор его определять
 */
 
class Query
{
    private $db;
    private $table;
    private $query;
    private $result;

    public function __construct(&$db, $table, $query = null)
    {
        $this->db = $db;
        if ($query !== null) {
            $this->query = $query;
        } else {
            $this->table = $table;
            $this->query = "SELECT * FROM {$table}";
        }
    }

    // public function update() {}
    // public function remove() {}

    public function where($where)
    {
        // если нет table то использовался query и не выполнять это
        $this->query .= " WHERE ";
        if (is_array($where)) {
            $where_temp = [];
            foreach ($where as $k => $v) {
                $where_temp[] = '`'.$k.'` = \''.$v.'\'';
            }
            $this->query .= implode(' AND ', $where_temp);
        } else {
            $this->query .= $where;
        }
        return $this;
    }

    /**
     * в таком случае не выдавать entity наверное
     */
    // public function join() {}

    public function fetch()
    {
        if (empty($this->result)) {
            $this->result = $this->db->query($this->query);
        }
		return $this->result->fetch_assoc();
		// return mysqli_fetch_assoc($this->result);
    }

    public function fetchAll()
    {
        $result = $this->db->query($this->query);
		return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function entity()
    {
        if ($data = $this->fetch()) {
            return new Entity($this->db, $this->table, $data);
        }
        return null;
    }

    public function entities()
    {
        for ($entities = null; $e = $this->entity(); $entities[] = $e);
        return $entities;
    }

}