<?php

class Entity
{
    private $db;
    private $table;
    private $entity_name;
    private $entity_id;
    private $data;
    private $changed;

    public function __construct(&$db, $table, $data)
    {
        $this->db = $db;
        $this->table = $table;
        $this->data = $data;
        var_dump($data);

        $this->entity_name = array_keys($data)[0];
        $this->entity_id = $data[$this->entity_name];
    }

    public function __get($name)
    {
        if (in_array($name, array_keys($this->data))) {
            return $this->data[$name];
        } else {
            throw new Exception('entity has no option');
        }
    }

    // TODO запретить менять id
    public function __set($name, $value)
    {
        if (in_array($name, array_keys($this->data)) && $name !== $this->entity_name) {
            if ($this->data[$name] !== $value) {
                $this->changed[$name] = $value;
            }
        } else {
            throw new Exception('invalid entity option');
        }
    }


    public function __destruct()
    {
        $this->save();
    }


    // перенести сюда
    public function save()
    {
        // сделать в одном, создать или обновить при существовании
        // проверить что объект не удалялся

        if (empty($this->entity_id) && !empty($this->changed)) {
            $columns = [];
            $values = [];
            foreach ($this->changed as $k => $v) {
                $columns[] = "`{$k}`";
                $values[] = "'{$v}'";
            }
            $columns = implode(', ', $columns);
            $values = implode(', ', $values);
            $query = "INSERT INTO `users` ({$columns}) VALUES ({$values});";
            $this->db->query($query);
            debug('создание объекта');

        } else if (!empty($this->changed)) {
            $updates = [];
            foreach ($this->changed as $k => $v) {
                $updates[] = "`{$k}` = '{$v}'";
            }
            $updates = implode(', ', $updates);
            $query = "UPDATE `{$this->table}` SET {$updates} WHERE `{$this->entity_name}` = {$this->entity_id}";
            $this->db->query($query);
            debug('обновление объекта');
        }

    }


    // delete changed for remove function
    public function remove() {
        $this->changed = null;
        //...
    }

}
