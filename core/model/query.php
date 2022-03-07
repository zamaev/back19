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
    private $fields = '*';
    private $join = '';
    private $join_tables = [];

    private $where = '';
    private $order = '';

    // private $group = '';

    private $query;
    private $result;

    public function __construct($table, $query = null)
    {
        $this->table = $table;
        $this->join_tables[] = $table;
        $this->query = $query;
    }

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

    public function delete($where) {
        $this->where($where);
        $query = "DELETE FROM {$this->table} {$this->where}";
        return model()->db->query($query);
    }

    public function fields($fields = null)
    {
        if (is_string($fields)) {
            $this->fields = $fields;

        } else if (is_array($fields)) {
            $this->fields = implode(', ', $fields);

        } else if (!$fields && count($this->join_tables) > 1) {
            $fields_arr = [];
            foreach ($this->join_tables as $table) {
                $table_fields = model()->getEntityByTable($table);
                $table_entity_name = model()->getEntityNameByTable($table);
                foreach ($table_fields as $field) {
                    if (preg_match('#(?<entity>.+)__id$#', $field, $matches)) {
                        $entity_table = model()->getTableByEntity($matches['entity']);
                        if (in_array($entity_table, $this->join_tables)) {
                            continue;
                        }
                        $fields_arr[] = "{$table_entity_name}.{$field} as `{$field}`";
                    } else if ($field == $table_entity_name) {
                        $fields_arr[] = "{$table_entity_name}.{$field} as `{$table_entity_name}`";
                    } else {
                        $fields_arr[] = "{$table_entity_name}.{$field} as `{$table_entity_name}.{$field}`";
                    }
                }
            }
            $this->fields = implode(", ", $fields_arr);
        }
        return $this;
    }

    /**
     * model()->users->jobs
     * model('users')->jobs
     * model()->posts->categories->users->jobs->fetchAll()
     * 
     * таблица имеет название entity, чтобы в where использовать 'user.name' как entity, которые возвращается в fields
     */
    public function join($table, $type = 'LEFT')
    {
        foreach ($this->join_tables as $first_table) {
            $second_table = $table;
            $first_entity = model()->getEntityByTable($first_table);
            $first_entity_name = model()->getEntityNameByTable($first_table);
            $second_entity_name = model()->getEntityNameByTable($second_table);
    
            if (in_array($second_entity_name.'__id', $first_entity)) {
                $second_entity_name_id = $second_entity_name.'__id';
                $this->join .= " {$type} JOIN {$second_table} {$second_entity_name} ON {$first_entity_name}.{$second_entity_name_id} = {$second_entity_name}.{$second_entity_name}";                
                $this->join_tables[] = $table;
                $this->fields();
                return $this;
            }
        }
        throw new Exception("wrong table ordr: {$first_table}->{$second_table}");
    }

    /**
     * model()->users->inner('jobs')
     * model('users')->inner('jobs')
     */
    public function inner($table) 
    {
        return $this->join($table, 'INNER');
    }

    public function __get($name)
    {
        if (model()->isTable($name)) {
            return $this->join($name);
        }
        throw new Exception($name . ' - is not a table');
    }
    
    // group by
    // public function group() {}

    /**
     * $model->post(['category.slug' => 'test']) - сделать под это обработку
     */
    public function where($where)
    {
        if (!$where) {
            return $this;
        }
        $this->where .= 'WHERE ';
        if (is_array($where)) {
            $where_temp = [];
            foreach ($where as $k => $v) {
                $where_temp[] = $k." = '".$v."'";
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
        $this->order = 'ORDER BY '.$fields.' '.$type;
        return $this;
    }

    // так же используется для выполнения model()->query() без select
    public function build()
    {
        if (!$this->result) {
            if (!$this->query) {
                $entity = model()->getEntityNameByTable($this->table);
                $this->query = "SELECT {$this->fields} FROM {$this->table} {$entity} {$this->join} {$this->where} {$this->order}";
            }
            $this->result = model()->db->query($this->query);
        }
    }
    public function fetch()
    {
        $this->build();
		return $this->result->fetch_assoc();
    }
    public function fetchAll()
    {
        $this->build();
		return $this->result->fetch_all(MYSQLI_ASSOC);
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