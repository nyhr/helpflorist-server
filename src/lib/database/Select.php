<?php
require_once(__DIR__ . "/DatabaseManager.php");

class Select {
    private $db;
    private $columns = '*';
    private $table;
    private $joins = array();
    private $conditions = array();
    private $params = array();
    private $orderBy = '';
    private $groupBy = '';
    private $limit = '';
    private $offset = '';

    /**
     * Constructor for the Select class.
     *
     * @param DatabaseManager $db The database manager instance.
     */
    public function __construct(DatabaseManager $db) {
        $this->db = $db->getDatabase();
    }

    /**
     * Specifies the columns to select.
     *
     * @param string|array $columns
     * @return $this
     */
    public function select($columns) {
        if (is_array($columns)) {
            $this->columns = implode(', ', $columns);
        } else {
            $this->columns = $columns;
        }
        return $this;
    }

    /**
     * Specifies the table to select from.
     *
     * @param string $table
     * @return $this
     */
    public function from($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * Adds a JOIN clause to the query.
     *
     * @param string $joinType
     * @param string $table
     * @param string $onCondition
     * @return $this
     */
    public function join($joinType, $table, $onCondition) {
        $this->joins[] = strtoupper($joinType) . " JOIN " . $table . " ON " . $onCondition;
        return $this;
    }

    /**
     * Adds a WHERE condition to the query with parameter binding.
     *
     * @param string $condition
     * @param array $params
     * @return $this
     */
    public function where($condition, $params = array()) {
        $this->conditions[] = $condition;
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Adds an ORDER BY clause to the query.
     *
     * @param string $column
     * @return $this
     */
    public function orderBy($column) {
        $this->orderBy = " ORDER BY " . $column;
        return $this;
    }

    /**
     * Adds a GROUP BY clause to the query.
     *
     * @param string $column
     * @return $this
     */
    public function groupBy($column) {
        $this->groupBy = " GROUP BY " . $column;
        return $this;
    }

    /**
     * Adds a LIMIT clause to the query.
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit) {
        $this->limit = " LIMIT " . intval($limit);
        return $this;
    }

    /**
     * Adds an OFFSET clause to the query.
     *
     * @param int $offset
     * @return $this
     */
    public function offset($offset) {
        $this->offset = " OFFSET " . intval($offset);
        return $this;
    }

    /**
     * Executes the query and returns all results.
     *
     * @return array
     */
    public function fetchAll() {
        $stmt = $this->prepareStatement();
        $result = $stmt->execute();
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Executes the query and returns a single result.
     *
     * @return array|null
     */
    public function fetch() {
        $stmt = $this->prepareStatement();
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) ?: null;
    }

    /**
     * Builds and prepares the SQL statement.
     *
     * @return SQLite3Stmt
     */
    private function prepareStatement() {
        $query = $this->buildQuery();
        $stmt = $this->db->prepare($query);

        // Bind parameters
        foreach ($this->params as $index => $value) {
            $paramIndex = $index + 1; // SQLite3 uses 1-based indexing for parameters
            $stmt->bindValue($paramIndex, $value, $this->getSQLiteType($value));
        }

        return $stmt;
    }


    /**
     * Returns the built query string.
     *
     * @return string
     */
    public function getQuery() {
        return $this->buildQuery();
    }

    /**
     * Builds the SQL query string.
     *
     * @return string
     */
    private function buildQuery() {
        $query = "SELECT " . $this->columns . " FROM " . $this->table;

        if (!empty($this->joins)) {
            $query .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->conditions)) {
            $query .= " WHERE " . implode(' AND ', $this->conditions);
        }

        if (!empty($this->groupBy)) {
            $query .= $this->groupBy;
        }

        if (!empty($this->orderBy)) {
            $query .= $this->orderBy;
        }

        if (!empty($this->limit)) {
            $query .= $this->limit;
        }

        if (!empty($this->offset)) {
            $query .= $this->offset;
        }

        return $query;
    }

    /**
     * Determines the SQLite3 data type for binding.
     *
     * @param mixed $value
     * @return int
     */
    private function getSQLiteType($value) {
        if (is_int($value)) {
            return SQLITE3_INTEGER;
        } elseif (is_float($value)) {
            return SQLITE3_FLOAT;
        } elseif (is_null($value)) {
            return SQLITE3_NULL;
        } else {
            return SQLITE3_TEXT;
        }
    }
}
?>
