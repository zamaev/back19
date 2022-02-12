<?php

/**
 * создать механизм выдачи уже полученного entity а не формировать его заново
 * потому что возможно что будут неоднозначные данные
 * 
 */
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

        $this->entity_name = array_keys($data)[0];
        $this->entity_id = $data[$this->entity_name];
    }

    /**
     * дополнить получением связанных данных из join таблиц
     */
    public function __get($name)
    {
        if (in_array($name, array_keys($this->data))) {
            return $this->data[$name];
        } else {
            throw new Exception('entity has no filed');
        }
    }

    public function __set($name, $value)
    {
        if (in_array($name, array_keys($this->data)) && $name !== $this->entity_name) {
            if ($this->data[$name] !== $value) {
                $this->changed[$name] = $value;
            }
        } else {
            throw new Exception('invalid entity field');
        }
    }


    public function __destruct()
    {
        $this->save();
    }

    /**
     * проверить что объект не удалялся (если будет единый механизм одного объекта)
     * 
     * user 
     * { user->remove() }
     * user 
     */
    public function save()
    {
        // 
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

        } else if (!empty($this->changed)) {
            $updates = [];
            foreach ($this->changed as $k => $v) {
                $updates[] = "`{$k}` = '{$v}'";
            }
            $updates = implode(', ', $updates);
            $this->db->query("UPDATE `{$this->table}` SET {$updates} WHERE `{$this->entity_name}` = {$this->entity_id}");
        }

    }

    public function remove() {
        $this->changed = null;
        $this->db->query("DELETE FROM `{$this->table}` WHERE `{$this->entity_name}` = {$this->entity_id}");
    }

}
