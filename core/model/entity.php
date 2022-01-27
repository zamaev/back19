<?php

class Entity
{
    private $db;
    private $table;
    private $entity;
    private $entity_id;
    private $data;
    private $changed;

    public function __construct(&$db, $table, $data)
    {
        $this->db = $db;
        $this->table = $table;
        $this->data = $data;
        $this->entity = array_keys($data)[0];
        $this->entity_id = $data[$this->entity];
    }

    public function __get($name)
    {
        if (in_array($name, array_keys($this->data))) {
            return $this->data[$name];
        } else {
            throw new Exception('entity has not option');
        }
    }

    // запретить менять id
    public function __set($name, $value)
    {
        if (in_array($name, array_keys($this->data)) && $name !== $this->entity) {
            $this->changed[$name] = $value;
        } else {
            throw new Exception('invalid entity option');
        }
    }


    public function __destruct()
    {
        // сделать в одном, создать или обновить при существовании

        if (empty($this->entity_id)) {
            echo 'create';

            
            $query = "INSERT INTO `users` (`user`, `name`, `password`, `job`) VALUES (NULL, 'judy', 'manager', '3');";

        } else if (!empty($this->changed)) {
            $updates = [];
            foreach ($this->changed as $k => $v) {
                $updates[] = "`{$k}` = '{$v}'";
            }
            $updates = implode(', ', $updates);
            $query = "UPDATE `{$this->table}` SET {$updates} WHERE `{$this->entity}` = {$this->entity_id}";
            $this->db->query($query);
        }
    }

    // delete changed for remove function
    public function remove() {
        $this->changed = null;
        //...
    }

}
