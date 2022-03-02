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
    private $changed = [];

    private $deleted = false;


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
     * если нет такого поля, то искать связанные таблицы
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

    public function setData($data)
    {
        foreach ($data as $k => $v) {
            if (in_array($k, array_keys($this->data)) && $k !== $this->entity_name) {
                $this->changed[$k] = $v;
            }
        }
        return $this;
    }

    public function data()
    {
        return $this->data;
    }

    // вернуть data, чтобы в дальнейшем с этим работать
    /**
     * проверить что объект не удалялся (если будет единый механизм одного объекта)
     * 
     * user 
     * { user->remove() }
     * user 
     */
    public function save()
    {
        if ($this->deleted) {
            return null;
        }
        if (empty($this->entity_id) && !empty($this->changed)) {
            $columns = [];
            $values = [];
            foreach ($this->changed as $k => $v) {
                $columns[] = "`{$k}`";
                if ($v === '') {
                    $values[] = 'null';
                } else {
                    $values[] = "'{$v}'";
                }
            }
            $columns = implode(', ', $columns);
            $values = implode(', ', $values);
            $query = "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$values});";
            $this->db->query($query);
            $this->entity_id = $this->db->query('SELECT LAST_INSERT_ID() as id;')->fetch_assoc()['id'];
            $this->data[$this->entity_name] = $this->entity_id;

        } else if (!empty($this->changed)) {
            $updates = [];
            foreach ($this->changed as $k => $v) {
                $updates[] = "`{$k}` = '{$v}'";
            }
            $updates = implode(', ', $updates);
            $this->db->query("UPDATE `{$this->table}` SET {$updates} WHERE `{$this->entity_name}` = {$this->entity_id};");
        }

        foreach ($this->changed as $k => $v) {
            $this->data[$k] = $v;
        }
        $this->changed = []; // очистка для изменений при последующем сохранении
        
        return $this;
    }

    public function delete()
    {
        $this->db->query("DELETE FROM `{$this->table}` WHERE `{$this->entity_name}` = {$this->entity_id}");
        $this->deleted = true;
    }

    public function __destruct()
    {
        $this->save();
    }
}
