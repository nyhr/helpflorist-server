<?php
require_once(__DIR__ . "/DatabaseManager.php");

class Update {
    private $db;
    private $table;
    private $values = array();
    private $conditions = array();
    private $params = array();

    public function __construct(DatabaseManager $db) {
        $this->db = $db->getDatabase();
    }

    /**
     * Specifies the table to update.
     *
     * @param string $table
     * @return $this
     */
    public function table($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * Sets the column values to update.
     *
     * @param array $values Associative array of column => value
     * @return $this
     */
    public function set($values) {
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    /**
     * Adds a WHERE condition with parameter binding.
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

    public function getQuery() {
        $setClauses = [];
        foreach ($this->values as $column => $value) {
            $setClauses[] = $column . ' = ?';
            $this->params[] = $value;
        }

        $query = "UPDATE " . $this->table . " SET " . implode(', ', $setClauses);

        if (!empty($this->conditions)) {
            $query .= " WHERE " . implode(' AND ', $this->conditions);
        }

        return nl2br($query . "\n\n" . "Params: " . json_encode($this->params));
    }

    /**
     * Executes the UPDATE query.
     *
     * @return bool True on success, false on failure.
     */
    public function execute() {
        if (empty($this->table)) {
            throw new Exception("Table name is required.");
        }
    
        if (empty($this->values)) {
            throw new Exception("Values to update are required.");
        }
    
        $setClauses = [];
        foreach ($this->values as $column => $value) {
            $setClauses[] = "$column = ?";
        }
    
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $setClauses);
    
        if (!empty($this->conditions)) {
            $query .= " WHERE " . implode(' AND ', $this->conditions);
        }
    
        $stmt = $this->db->prepare($query);
    
        // Bind parameters in the correct order
        $paramIndex = 1;
        foreach ($this->values as $value) {
            $stmt->bindValue($paramIndex, $value, $this->getSQLiteType($value));
            $paramIndex++;
        }
        foreach ($this->params as $value) {
            $stmt->bindValue($paramIndex, $value, $this->getSQLiteType($value));
            $paramIndex++;
        }
    
        $result = $stmt->execute();
        if ($result === false) {
            throw new Exception("Update failed: " . $this->db->lastErrorMsg());
        }
    
        // Check if any rows were affected
        if ($this->db->changes() === 0) {
            throw new Exception("No rows were updated. Please check the provided ID.");
        }
    
        return true;
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
