<?php

/**
 * $model->user(2);	- return Entity object
 * $model->user(['name' => 'user1']); - return Entity object
 * 
 * $model->user() - return empty Entity for create new table row
 * 
 * 
 * $model->query($query) - return Query object with query
 * 
 * $model->users; - return Query object with table 'users'
 * $model->users([...]) - return Query object with `table` 'users' and `where` params
 * 
 */


class Model extends Singleton
{
    private $db;

	// напрямую не давать доступ, возвращать данные по запросу 
	private $tables = [];	# table => columns
	private $entities = [];  # entity => table

    protected function __construct()
    {
		extract(require('config/db.php'));
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
			$data = $this->query('SHOW tables')->fetchAll();
			if (empty($data)) {
				throw new Exception('empty db');
			}
			$tables = array_column($data, 0);

			foreach($tables as $table) {
				$table_data = $this->query('DESCRIBE '.$table)->fetchAll();

				$table_columns = array_column($table_data, 0);
				$this->tables[$table] = $table_columns;

				$entity = $table_columns[0];
				$this->entities[$entity] = $table;
			}
			Cache::set('tables', $this->tables);
			Cache::set('entities', $this->entities);
		}
	}

    public function query($query)
    {
        return new Query($this->db, null, $query);
    }

	public function isTable($name)
	{
		$tables = array_keys($this->tables);
		return in_array($name, $tables) ? $this->tables[$name] : null;
	}

	public function isEntity($name)
	{
		$entities = array_keys($this->entities);
		return in_array($name, $entities) ? $this->entities[$name] : null;
	}


	/**
	 * $model->user(2);	- return Entity object by `id`
	 * $model->user(['name' => 'user1']); - return Entity object by WHERE
	 * $model->user() - return empty Entity for create new table row if changed this
	 * 
	 * $model->users([...]) - return Query object with `table` and `where` params
	 */
	public function __call($name, $arguments)
	{
		if ($table = $this->isEntity($name)) {

			$where = null;
			if (!empty($arguments)) {
				if (is_array($arguments[0])) {
					$where = $arguments[0];
				} else {
					$where = [$name => $arguments[0]];
				}
			}

			if ($where === null) {
				$entity_feilds = $this->tables[$table];
				$data = array_fill_keys($entity_feilds, null);
				return new Entity($this->db, $table, $data);
			} else {
				return $this->{$table}->where($where)->entity();
			}

		} else if ($this->isTable($name) && !empty($arguments) && is_array($arguments[0])) {
			return $this->{$name}->where($arguments[0]);
		}

		return null;
	}


	/**
	 * 
	 */
	public function __get($name)
	{
		if ($this->isEntity($name)) {
			$table = $this->entities[$name];
			$table_keys = $this->tables[$table];
			$data = array_fill_keys($table_keys, null);
			return new Entity($this->db, $table, $data);

		} else if ($this->isTable($name)) {
			return new Query($this->db, $name);

		} else {
			throw new Exception("don't have table");
		}
	}


	// создание новой записи в таблице с использованием либо существующей entity либо через data array
	// public function __set() {}

}


$model = Model::getInstance();

// $user = $model->user(5);
// $user->password = 'pas0ds00ord33';

$user = $model->user(2);

debug($user);


