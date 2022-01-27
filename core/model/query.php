<?php

class Query
{
    private $db;
    private $table;
    private $query;
    private $result;

    public function __construct(&$db, $table, $query = null)
    {
        $this->db = $db;
        $this->table = $table;
        if ($query !== null) {
            $this->query = $query;
        } else {
            $this->query = "SELECT * FROM {$table}";
        }
    }

    // public function update() {}
    // public function remove() {}

    public function where($where)
    {
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

    public function fetch()
    {
        if (empty($this->result)) {
            $this->result = $this->db->query($this->query);
        }
		return mysqli_fetch_assoc($this->result);
    }

    public function fetchAll()
    {
        $result = $this->db->query($this->query);
		return mysqli_fetch_all($result);
    }

    public function entity()
    {
        $data = $this->fetch();
        return new Entity($this->db, $this->table, $data);
    }

    public function entityAll()
    {

    }

}