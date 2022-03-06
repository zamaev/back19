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
    private $table;

    private $type = 'SELECT';

    private $fields = '*';

    private $where = '';
    private $order = '';

    private $query;
    private $result;

    public function __construct($table, $query = null)
    {
        $this->table = $table;
        $this->query = $query;
    }

    // beta протестить
    public function insert($data)
    {
        $fileds = [];
        $values = [];
        foreach ($data as $f => $v) {
            $fileds[] = '`'.$f.'`';
            $values[] = "'".$v."'";
        }
        $insert = '('. implode(', ', $fileds) .') VALUES ('. implode(', ', $values) .');';
        $query = "INSERT INTO {$this->table} {$insert}";
        return model()->db->query($query);
    }

    public function update($where, $data) {
        $sets = [];
        foreach ($data as $f => $v) {
            $sets[] = "`".$f."` = '".$v."'";
        }
        $update = implode(', ', $sets);

        $this->where($where);
        $query = "UPDATE {$this->table} SET {$update} {$this->where}";
        return model()->db->query($query);
    }

    // beta протестить
    public function delete() {
        $this->type = 'DELETE';
        return $this;
    }

    
    /**
     * в таком случае не выдавать entity наверное
     */
    // public function join() {}
    

    /**
     * $model->post(['category.slug' => 'test']) - сделать под это обработку
     */
    public function where($where)
    {
        if (!$where) {
            return $this;
        }
        $this->where .= ' WHERE ';
        if (is_array($where)) {
            $where_temp = [];
            foreach ($where as $k => $v) {
                $where_temp[] = "`".$k."` = '".$v."'";
            }
            $this->where .= implode(' AND ', $where_temp);

        } else if (is_numeric($where)) {
            $entity = model()->getEntityNameByTable($this->table);
            $this->where .= "`".$entity."` = '".$where."'";
        
        } else {
            $this->where .= $where;
        }
        return $this;
    }

    public function order($fields, $type) {
        $this->order = ' ORDER BY '.$fields.' '.$type;
        return $this;
    }


    private function buildQuery()
    {
        if ($this->query) {
            return;

        } else if ($this->type == 'SELECT') {
            $this->query = "SELECT {$this->fields} FROM {$this->table}";

        } else if ($this->type == 'DELETE') {
            $this->query = "DELETE FROM {$this->table}";
        }
        $this->query .= $this->where . $this->order;
    }


    public function fetch()
    {
        if (empty($this->result)) {
            $this->buildQuery();
            $this->result = model()->db->query($this->query);
        }
		return $this->result->fetch_assoc();
    }

    public function fetchAll()
    {
        $this->buildQuery();
        $result = model()->db->query($this->query);
		return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function entity()
    {
        if ($data = $this->fetch()) {
            return new Entity($data);
        }
        return null;
    }

    public function entities()
    {
        for ($entities = []; $e = $this->entity(); $entities[] = $e);
        return $entities;
    }

}