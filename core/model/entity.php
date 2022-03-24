<?php

namespace Core\Model;

/**
 * создать механизм выдачи уже полученного entity а не формировать его заново
 * потому что возможно что будут неоднозначные данные
 * 
 */
class Entity
{
    // private $db;
    private $table;

    private $entity_name;
    private $entity_id;

    private $data;
    private $changed = [];

    private $deleted = false;

    private $joins = [];


    // избавиться от получения $db и $table и использовать Entity не как обертку, а получать в нем данные
    public function __construct($data)
    {
        $this->data = $data;

        $this->entity_name = array_keys($data)[0];
        $this->entity_id = $data[$this->entity_name];
        
        $this->table = model()->getTableByEntity($this->entity_name);
    }

    /**
     * дополнить связанной таблицей многие ко многим
     */
    public function __get($name)
    {
        if (in_array($name, array_keys($this->data))) {
            return $this->data[$name];

        } else if (in_array($name.'__id', array_keys($this->data))) { // если есть связанная таблица
            if ($this->data[$name.'__id']) {
                if (isset($this->joins[$name.'__id'])) {
                    return $this->joins[$name.'__id'];
                } else {
                    $table = model()->getTableByEntity($name);
                    $entity = model()->{$table}->where([$name => $this->data[$name.'__id']])->entity();
                    $this->joins[$name.'__id'] = $entity;
                    return $entity;
                }
            } else {
                return null;
            }

        } else if ($name == 'id') {
            return $this->data[$this->entity_name];
        
        } else if (model()->isTable($name)) {
            // TODO реализовать получение данных многие ко многим

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

    // может лучше использовать insert или update, хотя тогда придется отдельно получать id
    public function setData($data)
    {
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($this->data)) && $field !== $this->entity_name) {
                $this->changed[$field] = $value;
            }
        }
        return $this;
    }

    public function data()
    {
        return $this->data;
    }

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
            model()->db->query($query);

            // возможно это плохая реализация, потому что перед получением id может записаться несколько строк в многопоточном режиме (возможно)
            $this->entity_id = model()->query('SELECT LAST_INSERT_ID() as id;')->fetch()['id'];
            $this->data[$this->entity_name] = $this->entity_id;

        } else if (!empty($this->changed)) {
            $updates = [];
            foreach ($this->changed as $k => $v) {
                $updates[] = "`{$k}` = '{$v}'";
            }
            $updates = implode(', ', $updates);
            model()->db->query("UPDATE `{$this->table}` SET {$updates} WHERE `{$this->entity_name}` = {$this->entity_id};");
        }

        foreach ($this->changed as $k => $v) {
            $this->data[$k] = $v;
        }
        $this->changed = []; // очистка для изменений при последующем сохранении
        
        return $this;
    }

    public function delete()
    {
        model()->db->query("DELETE FROM `{$this->table}` WHERE `{$this->entity_name}` = {$this->entity_id}");
        $this->deleted = true;
    }

    public function __destruct()
    {
        $this->save();
    }
}
