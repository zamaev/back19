<?php

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
		if (!empty($tables) || !empty($entities)) {
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
		return in_array($name, $tables);
	}

	public function isEntity($name)
	{
		$entities = array_keys($this->entities);
		return in_array($name, $entities);
	}

	public function __call($name, $arguments)
	{
		$options = $arguments[0];
		if ($options === null) {
			throw new Exception('need options');
		} else if (!is_array($options)) {
			$options = [$name => $options];
		}

		if ($this->isEntity($name)) {
			$table = $this->entities[$name];
			return $this->{$table}->where($options)->entity();

		} else if ($this->isTable($name)) {
			$query = new Query($this->db, $name);
			$query->where($options);
			return $query;

		} else {
			throw new Exception("don't have entity");
		}

	}

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


	// create table with array ??
	// create new object with array or entity
	// public function __set() {}

}


$model = Model::getInstance();

// $user = $model->user;
$user = $model->user(5);


// $user->name = 'test';
$user->password = 'password3';


// print_r($user);


// $model->user(2);					// return Entity object
// $model->user(['name' => 'user1']);  // return Entity object
// $model->user    					// return empty Entity for create new table row

// $model->users;						// return Query object with table
// $model->users([...])				// return Query with Where




// $query = $model->query("SELECT * FROM users");
// print_r($query->fetch());
// print_r($query->fetch());

// print_r($model->table('users')->where(['user' => 1])->fetch());
