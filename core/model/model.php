<?php

class Model extends Singleton
{
    private $db;

	private $tables = [];
	private $entities = [];

    protected function __construct()
    {
		list('host' => $host, 'user' => $user, 'pass' => $pass, 'name' => $name) = require(__DIR__.'/../../config/db.php');
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->db = new mysqli($host, $user, $pass, $name) or die('not connect to db');
        } catch (Exception $e) {
            throw new Exception('not connect to db');
        }
		$this->initTables();
    }

	private function initTables()
	{
		$tables = Cache::get('tables');
		$entities = Cache::get('entities');
		if (!empty($tables) && !empty($entities)) {
			$this->tables = $tables;
			$this->entities = $entities;

		} else {
			$db_tables = $this->db->query('SHOW tables')->fetch_all(MYSQLI_ASSOC);
			if (empty($db_tables)) {
				throw new Exception('empty db');
			}
			$tables = [];
			foreach ($db_tables as $table) {
				$tables[] = array_values($table)[0];
			}
			foreach($tables as $table) {
				$table_data = $this->db->query('DESCRIBE '.$table)->fetch_all(MYSQLI_ASSOC);

				$table_columns = array_column($table_data, 'Field');
				$this->tables[$table] = $table_columns;

				$entity = $table_columns[0];
				$this->entities[$entity] = $table;
			}
			Cache::set('tables', $this->tables);
			Cache::set('entities', $this->entities);
		}
	}


	public function getEntityByTable($name)
	{
		$tables = array_keys($this->tables);
		return in_array($name, $tables) ? $this->tables[$name][0] : null;
	}
	public function getEntityFieldsByTable($name)
	{
		$tables = array_keys($this->tables);
		return in_array($name, $tables) ? $this->tables[$name] : null;
	}
	public function getTableByEntity($name)
	{
		$entities = array_keys($this->entities);
		return in_array($name, $entities) ? $this->entities[$name] : null;
	}
	public function isTable($name) { return $this->getEntityFieldsByTable($name) ? true : false; }
	public function isEntity($name) { return $this->getTableByEntity($name) ? true : false; }


	public function __call($name, $arguments)
	{
		$where = null;
		if (!empty($arguments)) {
			if (is_array($arguments[0])) {
				$where = $arguments[0];
			} else if ($this->isEntity($name)) {
				$where = [$name => $arguments[0]];
			} else if ($this->isTable($name)) {
				$entity = $this->tables[$name][0];
				$where = [$entity => $arguments[0]];
			}
		}

		if ($this->isEntity($name)) {
			$table = $this->getTableByEntity($name);
			if ($where) {
				return $this->{$table}->where($where)->entity();
			} else {
				$entity_feilds = $this->tables[$table];
				$data = array_fill_keys($entity_feilds, null);
				return new Entity($data);
			}

		} else if ($this->isTable($name)) {
			return $this->{$name}->where($where);
		}

		throw new Exception('invalid entity or table - '. $name);
	}

	public function __get($name)
	{
		if ($name == 'db') {
			return $this->db;
		} else if ($this->isTable($name)) {
			return new Query($name);
		}
		return null;
	}

    public function query($query)
    {
        return new Query(null, $query);
    }
}

function model($table = null) { 
	if ($table) {
		return Model::getInstance()->{$table};
	} else {
		return Model::getInstance(); 
	}
}
