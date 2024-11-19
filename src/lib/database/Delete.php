<?php
require_once(__DIR__ . "/DatabaseManager.php");

class Delete {
    private $db;
    private $table;
    private $conditions = array();
    private $params = array();

    /**
     * Constructor for the Delete class.
     *
     * @param DatabaseManager $db The database manager instance.
     */
    public function __construct(DatabaseManager $db) {
        $this->db = $db->getDatabase();
    }

    /**
     * Specifies the table to delete from.
     *
     * @param string $table
     * @return $this
     */
    public function from($table) {
        $this->table = $table;
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

    /**
     * Executes the DELETE query.
     *
     * @return bool True on success, false on failure.
     */
    public function execute() {
        if (empty($this->table)) {
            throw new Exception("Table name is required.");
        }

        $query = "DELETE FROM " . $this->table;

        if (!empty($this->conditions)) {
            $query .= " WHERE " . implode(' AND ', $this->conditions);
        }

        $stmt = $this->db->prepare($query);

        // Bind parameters
        foreach ($this->params as $index => $value) {
            $paramIndex = $index + 1; // SQLite3 uses 1-based indexing for parameters
            $stmt->bindValue($paramIndex, $value, $this->getSQLiteType($value));
        }

        $result = $stmt->execute();
        if ($result === false) {
            throw new Exception("Delete failed: " . $this->db->lastErrorMsg());
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
