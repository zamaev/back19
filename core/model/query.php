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

    private $type = 'SELECT';
    private $fields = '*';
    private $where = '';
    private $order = '';
    
    private $insert;
    private $update;

    private $query;
    private $result;

    public function __construct(&$db, $table, $query = null)
    {
        $this->db = $db;
        $this->table = $table;
        $this->query = $query;
    }

    // beta протестить
    // возможно сразе же вызвать fetch()
    public function insert($data)
    {
        $this->type = 'INSERT';
        $fileds = [];
        $values = [];
        foreach ($data as $f => $v) {
            $fileds[] = $f;
            $values[] = $v;
        }
        $this->insert = '('. implode(', ', $fileds) .') VALUES ('. implode(', ', $values) .')';
        return $this;
    }

    // beta протестить
    // возможно сразе же вызвать fetch()
    public function update($data) {
        $this->type = 'UPDATE';
        $sets = [];
        foreach ($data as $f => $v) {
            $sets[] = $f.' = '.$v;
        }
        $this->update = implode(', ', $sets);
        return $this;
    }

    // beta протестить
    // возможно сразе же вызвать fetch()
    public function delete() {
        $this->type = 'DELETE';
        return $this;
    }

    
    /**
     * в таком случае не выдавать entity наверное
     */
    // public function join() {}
    

    public function where($where)
    {
        $this->where .= " WHERE ";
        if (is_array($where)) {
            $where_temp = [];
            foreach ($where as $k => $v) {
                $where_temp[] = '`'.$k.'` = \''.$v.'\'';
            }
            $this->where .= implode(' AND ', $where_temp);
        } else {
            $this->where .= $where;
        }
        return $this;
    }

    public function order($fields, $type) {
        $this->order = 'ORDER BY '.$fields.' '.$type;
        return $this;
    }


    private function buildQuery()
    {
        if ($this->query) {
            return;
        }
        if ($this->type = 'SELECT') {
            $this->query = $this->type.' '.$this->fields.' FROM '.$this->table;
        } else if ($this->type = 'INSERT') {
            $this->query = $this->type.' INTO '.$this->table.' '.$this->insert;
        } else if ($this->type = 'UPDATE') {
            $this->query = $this->type.' '.$this->table.' SET '.$this->update;
        } else if ($this->type = 'DELETE') {
            $this->query = $this->type.' FROM '.$this->table;
        }
        $this->query .= ' '.$this->where;
        $this->query .= ' '.$this->order;
    }


    public function fetch()
    {
        if (empty($this->result)) {
            $this->buildQuery();
            $this->result = $this->db->query($this->query);
        }
		return $this->result->fetch_assoc();
    }

    public function fetchAll()
    {
        $this->buildQuery();
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