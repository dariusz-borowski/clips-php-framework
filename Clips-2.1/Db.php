<?php

class Clips_Db {

    const DISTINCT      = 'distinct';
    const COLUMNS       = 'columns';
    const FROM          = 'from';
    const UNION         = 'union';
    const WHERE         = 'where';
    const GROUP         = 'group';
    const HAVING        = 'having';
    const ORDER        	= 'order';
    const LIMIT			= 'limit';
    const OFFSET  		= 'offset';
	const QUERY  		= 'query';

    const INNER_JOIN     = 'inner join';
    const LEFT_JOIN      = 'left join';
    const RIGHT_JOIN     = 'right join';
    const FULL_JOIN      = 'full join';
    const CROSS_JOIN     = 'cross join';
    const NATURAL_JOIN   = 'natural join';

	const SQL_SEPARATOR  = ' ';
    const SQL_WILDCARD   = '*';
    const SQL_SELECT     = 'SELECT';
    const SQL_UNION      = 'UNION';
    const SQL_UNION_ALL  = 'UNION ALL';
    const SQL_FROM       = 'FROM';
    const SQL_WHERE      = 'WHERE';
    const SQL_DISTINCT   = 'DISTINCT';
    const SQL_GROUP   	 = 'GROUP BY';
    const SQL_ORDER   	 = 'ORDER BY';
    const SQL_HAVING     = 'HAVING';
    const SQL_AND        = 'AND';
    const SQL_AS         = 'AS';
    const SQL_OR         = 'OR';
    const SQL_ON         = 'ON';
    const SQL_ASC        = 'ASC';
    const SQL_DESC       = 'DESC';
	const SQL_LIMIT      = 'LIMIT';
	const SQL_OFFSET     = 'OFFSET';
	const SQL_INSERT     = 'INSERT';
	const SQL_UPDATE     = 'UPDATE';
	const SQL_DELETE     = 'DELETE';
	const SQL_IN 		 = 'IN';


	protected $_config = array(
		'id_column' => 'id',
		'error_mode' => PDO::ERRMODE_EXCEPTION,
		'host' => null,
		'dbname' => null,
		'engine' => null,
		'username' => null,
		'password' => null,
		'driver_options' => null,
		'logging' => false,
		'schema' => null,
	);

	protected $_parts = array();

	protected $_db;








	public static function factory($config) {		

		$existing = Clips_Registry::get('Clips_Db_'.md5(strtolower(json_encode($config))));

		if ($existing) {

			return $existing;

		}

		$instance = new Clips_Db();

		if (!isset($config['engine']))
			throw new Exception('cannot find engine definition (pgsql, mysql etc...)');

		if (!isset($config['host']))
			throw new Exception('cannot find host definition');

		if (!isset($config['dbname']))
			throw new Exception('cannot find dbname definition');

		$instance -> _config['engine'] = $config['engine'];
		$instance -> _config['host'] = $config['host'];
		$instance -> _config['dbname'] = $config['dbname'];
		$instance -> _config['username'] = $config['username'];
		$instance -> _config['password'] = $config['password'];

		if (isset($config['logging']))
			$instance -> _config['logging'] = $config['logging'];

		if (isset($config['schema']))
			$instance -> _config['schema'] = $config['schema'];

		if (isset($config['id_column']))
			$instance -> _config['id_column'] = $config['id_column'];

		if (isset($config['error_mode']))
			$instance -> _config['error_mode'] = $config['error_mode'];

		if (isset($config['driver_options']))
			$instance -> _config['driver_options'] = $config['driver_options'];

        if (!empty($config['charset'])) {
            $instance -> _config['driver_options'][1002] = "SET NAMES '" . $config['charset'] . "'"; // 1002 = PDO::MYSQL_ATTR_INIT_COMMAND
        }

		$instance -> _db = new PDO($instance -> _config['engine'].':host='.$instance -> _config['host'].';dbname='.$instance -> _config['dbname'], $instance -> _config['username'], $instance -> _config['password'], $instance -> _config['driver_options']);
		
		$instance -> _db -> setAttribute(PDO::ATTR_ERRMODE, $instance -> _config['error_mode']);

		switch($instance -> _db -> getAttribute(PDO::ATTR_DRIVER_NAME)) {

			case 'pgsql':
			case 'sqlsrv':
			case 'dblib':
			case 'mssql':
			case 'sybase':
					$instance -> _config['_quote_character'] = '"';
				break;
			case 'mysql':
			case 'sqlite':
			case 'sqlite2':
			default:
					$instance -> _config['_quote_character'] = '`';
				break;

		}

		Clips_Registry::set('Clips_Db_'.md5(strtolower(json_encode($config))), $instance);

		return $instance;

	}














	public function joinLeft($table, $condition, $columns = self::SQL_WILDCARD, $schema = NULL) {

		return $this -> _join(self::LEFT_JOIN, $table, $condition, $columns, $schema);

	}

	public function joinRight($table, $condition, $columns = self::SQL_WILDCARD, $schema = NULL) {

		return $this -> _join(self::RIGHT_JOIN, $table, $condition, $columns, $schema);

	}

	public function joinInner($table, $condition, $columns = self::SQL_WILDCARD, $schema = NULL) {

		return $this -> _join(self::INNER_JOIN, $table, $condition, $columns, $schema);

	}

	public function joinFull($table, $condition, $columns = self::SQL_WILDCARD, $schema = NULL) {

		return $this -> _join(self::FULL_JOIN, $table, $condition, $columns, $schema);

	}

	public function joinCross($table, $columns = self::SQL_WILDCARD, $schema = NULL) {

		return $this -> _join(self::CROSS_JOIN, $table, NULL, $columns, $schema);

	}

	public function joinNatural($table, $columns = self::SQL_WILDCARD, $schema = NULL) {

		return $this -> _join(self::NATURAL_JOIN, $table, NULL, $columns, $schema);

	}


    public function select() {

        return $this;

    }


    public function distinct($flag = true) {
	
		$this -> _init();
	
        $this -> _parts[self::DISTINCT] = (bool) $flag;

        return $this;

    }


	public function andWhere($condition, $value = NULL) {

		$this -> _where($condition, $value);

		return this;

	}

	public function orWhere($condition, $value = NULL) {

		$this -> _where($condition, $value, true);

		return this;

	}

    public function where($condition, $value = NULL) {

		$this -> _where($condition, $value);

        return $this;

    }


	public function quoteInto($condition, $value) {
	
		return preg_replace('/\?/', self::quote($value), $condition);
	
	}
	
	
	
	public function quote($value) {

		return $this -> _quote_value($value);

	}


    public function group($condition) {

        if (!is_array($condition)) {

            $condition = array($condition);

		}

		foreach($condition as $c) {
		
			$this -> _parts[self::GROUP][] = $c;
			
			if (is_object($c)) {
			
				$this -> _parts[self::GROUP][] = $c -> expr;
				
			} else {
			
				$this -> _parts[self::GROUP][] = $this -> _quote_identifier($c);
			
			}
			
		}			

        return $this;

    }


    public function order($order)
    {
        if (!is_array($order)) {

            $order = array($order);

        }

        foreach ($order as $o) {

			if (is_object($o)) {

				$this->_parts[self::ORDER][] = $o -> expr;

			} else {

				list($order, $direction) = explode(' ', $o);

				if (!$direction)
					$direction = self::SQL_ASC;

				$this->_parts[self::ORDER][] = $this -> _quote_identifier($order) . self::SQL_SEPARATOR . $direction;

			}

        }

        return $this;
    }




	public function from($table, $columns = self::SQL_WILDCARD, $schema = NULL) {

		if ($this -> _parts[self::FROM] != NULL)
			$this -> _init();

		if (!is_array($table))
			$table = array($table);

		foreach($table as $k => $t) {

			if ($k)
				$prefix = $k;
			else
				$prefix = $t;

			$this -> _parts[self::FROM] = $this->_quote_identifier(($schema ? $schema.'.' : NULL).$t) . self::SQL_SEPARATOR . $this -> _quote_identifier($k);

		}

		$this -> _add_column($columns, ($prefix ? $prefix.'.' : NULL));

		return $this;

	}



    public function having($cond) {

        $this->_parts[self::HAVING][] = (count($this->_parts[self::HAVING]) > 0 ? SQL_AND.self::SQL_SEPARATOR : NULL).$cond;

        return $this;

    }



    public function limit($limit = NULL, $offset = NULL) {

        $this->_parts[self::LIMIT] = abs((int)$limit);

		if ($offset !== NULL)
			$this->_parts[self::OFFSET] = abs((int)$offset);

        return $this;
    }


    public function offset($offset = NULL) {

        $this->_parts[self::OFFSET] = abs((int)$offset);

        return $this;
    }



	public static function expr($sql) {

		$obj = (object)NULL;
		$obj -> expr = $sql;

		return $obj;

	}


	public function insertDelayed($table, $data, $schema = NULL) {

		return $this -> insert($table, $data, $schema, true);

	}
	
	public function beginTransaction() {

		$this -> _db -> beginTransaction();

	}	

	public function begin() {

		$this -> _db -> beginTransaction();

	}

	public function commit() {

		$this -> _db -> commit();

	}

	public function rollBack() {

		$this -> _db -> rollBack();

	}

	public function lastInsertId($name = NULL) {

		$id = $this -> _db -> lastInsertId($name);

		return $id;

	}


	public function insert($table, $data, $schema = NULL, $delayed = false) {

		$this -> _init();

		foreach($data as $key => $value) {

			$keys[] = $this -> _quote_identifier($key);
			$values[] = $this -> _quote_value($value);

		}

		$count = $this -> _db -> exec(self::SQL_INSERT . self::SQL_SEPARATOR . ($delayed ? 'DELAYED' . self::SQL_SEPARATOR : '') . 'INTO' . self::SQL_SEPARATOR . $this -> _quote_identifier((isset($schema) ? $schema . '.' : '').$table) .
					self::SQL_SEPARATOR . '('.implode(',', $keys).')' . self::SQL_SEPARATOR . 'VALUES(' . implode(',', $values) . ')');

		return $count;

	}



	public function update($table, $data, $where = NULL, $schema = NULL) {

		$this -> _init();

		foreach($data as $key => $value) {

			$parts[] = $this -> _quote_identifier($key) . '=' . $this -> _quote_value($value);

		}

		$statement = self::SQL_UPDATE . self::SQL_SEPARATOR . $this -> _quote_identifier((isset($schema) ? $schema . '.' : '').$table) .
			' SET ' . implode(', ', $parts);

		if (!is_array($where)) {

			$statement .= self::SQL_SEPARATOR . self::SQL_WHERE . self::SQL_SEPARATOR . $where;

		} else {

			foreach($where as $key => $value) {

				$wheres[] = preg_replace('/\?/', $this -> _quote_value($value), $key);

			}

			$statement .= self::SQL_SEPARATOR . self::SQL_WHERE . self::SQL_SEPARATOR . implode(' AND ', $wheres);


		}

		$count = $this -> _db -> exec($statement);

		return $count;

	}


	public function delete($table, $where = NULL, $schema = NULL) {

		$this -> _init();

		$statement = self::SQL_DELETE . self::SQL_SEPARATOR . self::SQL_FROM . self::SQL_SEPARATOR . $this -> _quote_identifier((isset($schema) ? $schema . '.' : '').$table);

		if (!is_array($where)) {

			$statement .= self::SQL_SEPARATOR . self::SQL_WHERE . self::SQL_SEPARATOR . $where;

		} else {

			foreach($where as $key => $value) {

				$wheres[] = preg_replace('/\?/', $this -> _quote_value($value), $key);

			}

			$statement .= self::SQL_SEPARATOR . self::SQL_WHERE . self::SQL_SEPARATOR . implode(' AND ', $wheres);

		}

		$count = $this -> _db -> exec($statement);

		return $count;

	}


	public function execute($query) {

		$this -> _init();

		$count = $this -> _db -> exec($query);

		return $count;

	}

	public function fetch() {

		$statement = $this -> _db -> query($this -> _parts[self::QUERY]);

		$rows = $statement -> fetch(PDO::FETCH_ASSOC);

		return $rows;

	}

	public function fetchColumn($column = 0) {

		$statement = $this -> _db -> query($this -> _parts[self::QUERY]);

		$value = $statement -> fetchColumn($column);

		return $value;

	}


	public function fetchAll() {

        $statement = $this -> _db -> query($this -> _parts[self::QUERY]);

		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

		return $rows;

	}















	protected function _init() {

		$this -> _parts[self::INNER_JOIN] = array();
		$this -> _parts[self::LEFT_JOIN] = array();
		$this -> _parts[self::RIGHT_JOIN] = array();
		$this -> _parts[self::FULL_JOIN] = array();
		$this -> _parts[self::CROSS_JOIN] = array();
		$this -> _parts[self::NATURAL_JOIN] = array();
		$this -> _parts[self::WHERE] = array();
		$this -> _parts[self::LIMIT] = NULL;
		$this -> _parts[self::FROM] = NULL;
		$this -> _parts[self::OFFSET] = NULL;
		$this -> _parts[self::DISTINCT] = NULL;
		$this -> _parts[self::HAVING] = array();
		$this -> _parts[self::COLUMNS] = array();
		$this -> _parts[self::ORDER] = array();
		$this -> _parts[self::QUERY] = NULL;
		$this -> _parts[self::UNION] = NULL;
		$this -> _parts[self::GROUP] = NULL;

		return $this;

	}


	protected function _quote_value($value) {

		if (is_object($value)) {

			return $value -> expr;

		} else {

			return '\''.addslashes($value).'\'';

		}

	}


	protected function _quote_identifier($identifier) {

		if (!$identifier) return;

		$parts = explode('.', $identifier);
		$parts = array_map(array($this, '_quote_part'), $parts);
		return join('.', $parts);

	}

	protected function _quote_part($part) {

		if ($part === self::SQL_WILDCARD) {

			return $part;

		}

		$quote_character = $this -> _config['_quote_character'];

		return $quote_character . $part . $quote_character;

	}



    public function _where($condition, $values, $or = false) {

		if (!is_array($values))
			$values = array($values);

		foreach($values as $value) {

			if (is_array($value)) {

				$condition = preg_replace('/\?/', '\''.implode('\',\'', $value).'\'', $condition);

			} else {

				$condition = preg_replace('/\?/', $this -> _quote_value($value), $condition);

			}

		}

		$this -> _parts[self::WHERE][] = self::SQL_SEPARATOR . ((count($this -> _parts[self::WHERE]) === 0) ?  self::SQL_WHERE : ($or ? self::SQL_OR : self::SQL_AND)) . self::SQL_SEPARATOR . $condition;

        return $this;

    }

	protected function _join($type, $table, $condition = NULL, $columns = self::SQL_WILDCARD, $schema = NULL) {

		if (!is_array($table))
			$table = array($table);

		$prefix = NULL;

		foreach($table as $k => $t) {

			if (is_string($k))
				$prefix = $k;
				else {
					$prefix = $t;
				}

			if (!empty($condition)) {
			
				if (is_object($condition)) {
				
					$condition = $condition -> expr;
				
				} else {

					list($left_condition, $center_condition, $right_condition) = sscanf($condition, '%s %s %s');

					$condition = $this -> _quote_identifier($left_condition) . self::SQL_SEPARATOR . $center_condition . self::SQL_SEPARATOR . $this -> _quote_identifier($right_condition) . self::SQL_SEPARATOR;

				}

			}

			$this -> _parts[$type][] = $this->_quote_identifier(($schema ? $schema . '.' : NULL) . $t) . self::SQL_SEPARATOR . $this -> _quote_identifier($prefix) . ($condition ? self::SQL_SEPARATOR . self::SQL_ON . self::SQL_SEPARATOR . $condition : NULL);

		}

		$this -> _add_column($columns, ($prefix ? $prefix.'.' : NULL), ($schema ? $schema.'.' : NULL));

		return $this;

	}


	protected function _add_column($columns, $prefix = NULL) {

		if (!is_array($columns))
			$columns = array($columns);

		foreach($columns as $keycolumn => $column) {

			if (is_object($column)) {

				$this -> _parts[self::COLUMNS][] = $column -> expr. ($keycolumn ? (self::SQL_SEPARATOR . self::SQL_AS . self::SQL_SEPARATOR . $this -> _quote_identifier($keycolumn)) : NULL);

			} else {

				$this -> _parts[self::COLUMNS][] = $this -> _quote_identifier(($prefix && (strpos($column, '.') === FALSE) ? $prefix : NULL).$column).(is_string($keycolumn) ? (self::SQL_SEPARATOR . self::SQL_AS . self::SQL_SEPARATOR . $this -> _quote_identifier($keycolumn)) : NULL);

			}

		}

	}



	public function query($query = NULL) {

		if (!$query) {

			$this -> _parts[self::QUERY] = $this -> _build_query();

		} else {

			$this -> _parts[self::QUERY] = $query;

		}

		return $this;

	}






	protected function _build_select() {

		if (empty($this -> _parts[self::COLUMNS]))
			$this -> _parts[self::COLUMNS][] = self::SQL_WILDCARD;

		$columns = implode(', ', $this -> _parts[self::COLUMNS]);

		$fragment = self::SQL_SELECT . ($this -> _parts[self::DISTINCT] ? self::SQL_SEPARATOR . self::SQL_DISTINCT : ''). self::SQL_SEPARATOR . $columns . self::SQL_SEPARATOR . self::SQL_FROM . self::SQL_SEPARATOR . $this -> _parts[self::FROM];

        return $fragment;

	}

	protected function _build_join() {

		$joins = array(self::LEFT_JOIN, self::RIGHT_JOIN, self::INNER_JOIN, self::FULL_JOIN, self::CROSS_JOIN, self::NATURAL_JOIN);

		foreach($joins as $jointype) {

			if (is_array($this -> _parts[$jointype])) {

				foreach($this -> _parts[$jointype] as $table) {

					$fragment .= self::SQL_SEPARATOR . $jointype . self::SQL_SEPARATOR . $table;

				}

			}

		}

		return $fragment;

	}

	protected function _build_where() {

		if ($this -> _parts[self::WHERE]) {

			foreach($this -> _parts[self::WHERE] as $where) {

				$fragment .= $where;

			}

		}

		return $fragment;

	}

    protected function _build_group() {

        if ($this->_parts[self::FROM] && $this->_parts[self::GROUP]) {

            $group = array();

            foreach ($this->_parts[self::GROUP] as $term) {

                $group[] = $term;

            }

            $fragment .= ' ' . self::SQL_GROUP . ' ' . implode(",\n\t", $group);
        }

        return $fragment;

    }

    protected function _build_order() {

		$fragment = '';

		if ($this->_parts[self::ORDER])
			$fragment = self::SQL_SEPARATOR . self::SQL_ORDER . self::SQL_SEPARATOR . implode(', ', $this->_parts[self::ORDER]);

        return $fragment;

    }


    protected function _build_having() {

		$fragment = '';

		if ($this->_parts[self::HAVING])
			$fragment = self::SQL_SEPARATOR . self::SQL_HAVING . self::SQL_SEPARATOR . implode(', ', $this->_parts[self::HAVING]);

        return $fragment;

    }

    protected function _build_limit() {

		$fragment = '';

		if ($this->_parts[self::LIMIT])
			$fragment = self::SQL_SEPARATOR . self::SQL_LIMIT . self::SQL_SEPARATOR . $this->_parts[self::LIMIT];

        return $fragment;

    }

    protected function _build_offset() {

		$fragment = '';

		if ($this->_parts[self::OFFSET] !== NULL)
			$fragment = self::SQL_SEPARATOR . self::SQL_OFFSET . self::SQL_SEPARATOR . $this->_parts[self::OFFSET];

        return $fragment;

    }

	protected function _build_query() {

		$fragment  = $this -> _build_select();
		$fragment .= $this -> _build_join();
		$fragment .= $this -> _build_where();
		$fragment .= $this -> _build_group();
		$fragment .= $this -> _build_having();
		$fragment .= $this -> _build_order();
		$fragment .= $this -> _build_limit();
		$fragment .= $this -> _build_offset();

		return $fragment;

	}

	public function __toString() {

		return $this -> _build_query();
		$this -> _parts[self::QUERY];

	}

}

?>