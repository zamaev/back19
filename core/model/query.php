<?php

namespace Core\Model;

/**
 * $query = $model->query($query)
 * 
 * $query->fetch()
 * $query->fetchAll()
 * $query->entity()
 * $query->entityAll()
 * 
 */
 
class Query
{
    private $table;
    private $fields = '*';
    private $join = '';
    private $joined_tables = [];

    private $where = '';
    private $group = '';
    private $order = '';
    private $limit = '';

    private $query;
    private $result;

    public function __construct($table, $query = null)
    {
        $this->table = $table;
        $this->joined_tables[] = $table;
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

    public function update($where, $data)
    {
        $sets = [];
        foreach ($data as $f => $v) {
            $sets[] = "`".$f."` = '".$v."'";
        }
        $update = implode(', ', $sets);

        $this->where($where);
        $query = "UPDATE {$this->table} SET {$update} {$this->where}";
        return model()->db->query($query);
    }

    public function delete($where)
    {
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

        } else if (!$fields && count($this->joined_tables) > 1) {
            $fields_arr = [];
            foreach ($this->joined_tables as $table) {
                $table_fields = model()->getEntityFieldsByTable($table);
                $table_entity_name = model()->getEntityByTable($table);
                foreach ($table_fields as $field) {
                    if (preg_match('#(?<entity>.+)__id$#', $field, $matches)) {
                        $entity_table = model()->getTableByEntity($matches['entity']);
                        if (in_array($entity_table, $this->joined_tables)) {
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
    public function join($join_table, $type = 'LEFT')
    {
        foreach ($this->joined_tables as $table) {
            $entity_fields = model()->getEntityFieldsByTable($table);
            $entity = model()->getEntityByTable($table);
            $join_entity = model()->getEntityByTable($join_table);
            $entity_join_field = $join_entity.'__id';
            if (in_array($entity_join_field, $entity_fields)) {
                $join_table_new_name = $join_entity;
                $table_new_name = $entity;
                $this->join .= " {$type} JOIN {$join_table} {$join_table_new_name} ON {$table_new_name}.{$entity_join_field} = {$join_table_new_name}.{$join_entity}";                
                $this->joined_tables[] = $join_table;
                $this->fields();
                return $this;
            }
        }
        throw new Exception("Wrong table order '{$table}->{$join_table}' or invalid join table");
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
            $entity = model()->getEntityByTable($this->table);
            $this->where .= "`".$entity."` = '".$where."'";
        
        } else {
            $this->where .= $where;
        }
        return $this;
    }

    // beta - подумать как лучше использовать с having
    public function group($group)
    {
        $this->group = "GROUP BY {$group}";
        return $this;
    }
    public function order($fields, $type)
    {
        $this->order = 'ORDER BY '.$fields.' '.$type;
        return $this;
    }
    public function limit($offset, $count = null)
    {
        $this->limit = "LIMIT {$offset}";
        if ($count) {
            $this->limit .= ", {$count}";
        }
        return $this;
    }
    public function pageCount($page, $count)
    {
        $offset = ($page - 1) * $count;
        $this->limit($offset, $count);
        return $this;
    }


    // так же используется для выполнения model()->query() без select
    public function build()
    {
        if (!$this->result) {
            if (!$this->query) {
                $entity = model()->getEntityByTable($this->table);
                $this->query = "SELECT {$this->fields} FROM {$this->table} {$entity} {$this->join} {$this->where} {$this->group} {$this->order} {$this->limit}";
            }
            $this->result = model()->db->query($this->query);
        }
    }
    public function count() {
        $this->fields = 'COUNT(*) as count';
        $this->build();
		return $this->result->fetch_assoc()['count'];
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