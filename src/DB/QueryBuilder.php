<?php

namespace HongXunPan\Tools\DB;

class QueryBuilder
{
    /** @var string $table */
    private $table;
    /** @var string $db */
    private $db;

    private $mode;
    /** @var array|mixed|string[] $fields */
    private $fields;
    /** @var array $where */
    private $where = [];
    /** @var array $order */
    private $order = [];
    /** @var int $limit */
    private $limit;
    /** @var int $offset */
    private $offset;

    public function __construct($table = '')
    {
        if ($table) {
            $this->setTable($table);
        }
    }

    public function setTable($table)
    {
        if (strpos($table, '.') !== false) {
            list($table, $db) = explode('.', $table);
            $this->setDB($db);
        }
        $this->table = "`$table`";
        return $this;
    }

    public function setDB($db)
    {
        $this->db = $db;
        return $this;
    }

    public function select($fields = ['*'])
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }
        $this->fields = $fields;
        $this->mode = 'select';
        return $this;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column) && !empty($column)) {
            $this->where[] = [$boolean => '(' . $this->whereArray($column) . ') '];
        }
        return $this;
//        func_num_args() === 2;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    private function whereArray($array)
    {
        if (!$array) {
            return $this;
        }
        $where = [];
        foreach ($array as $key => $value) {
            $where[] = "`$key` = $value";
        }
        return implode(' and ', $where);
    }

    private function buildWhere()
    {
        if (!$this->where) {
            return '';
        }
        if (count($this->where) === 1) {
            return $this->where[0];
        }
        $return = '';
        foreach ($this->where as $item) {
            $boolean = key($item);
            $value = $item[$boolean];
            if (!$return) {
                $boolean = ' where';
            }
            $return .= "$boolean $value";
        }
        return rtrim($return, 'and');
    }

    private function buildLimit($useOffset = false)
    {
        $limit = '';
        if ($this->limit) {
            $limit = $this->limit;
        }
        if ($useOffset && $this->offset) {
            $limit = $this->offset . ',' . $limit;
        }
        if ($limit) {
            $limit = " limit $limit";
        }
        return $limit;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function orderBy($order, $desc = 'asc')
    {
        if (is_array($order)) {
            foreach ($order as $key => $value) {
                if (is_int($key)) {
                    $this->order[] = "`$value` $desc";
                    continue;
                }
                if (is_string($key) && !in_array(strtolower($value), ['asc', 'desc'])) {
                    throw new \Exception("can not deal order $key $desc");
                }
                $this->order[] = "`$key` $desc";
            }
        } else {
            $this->order[] = "`$order` $desc";
        }
        return $this;
    }

    private function buildOrderBy()
    {
        if (!$this->order) {
            return '';
        }
        return ' order by' . implode(',', $this->order);
    }

    public function toSql()
    {
        $sql = '';
        $table = $this->db ? $this->db . '.' . $this->table : $this->table;
        switch (strtolower($this->mode)) {
            case 'select':
                $sql = "select " . implode(',', $this->fields) . " from $table " . $this->buildWhere() . $this->buildOrderBy() . $this->buildLimit(true);
                break;
        }
        return $sql;
    }
}