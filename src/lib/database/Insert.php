<?php
require_once(__DIR__ . "/DatabaseManager.php");

class Insert {
    private $db;
    private $table;
    private $columns = array();
    private $values = array();
    private $params = array();

    /**
     * Constructor for the Insert class.
     *
     * @param DatabaseManager $db The database manager instance.
     */
    public function __construct(DatabaseManager $db) {
        $this->db = $db->getDatabase();
    }

    /**
     * Specifies the table to insert into.
     *
     * @param string $table
     * @return $this
     */
    public function into($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * Specifies the columns to insert values into.
     *
     * @param array $columns
     * @return $this
     */
    public function columns($columns) {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Specifies the values to insert.
     *
     * @param array $values
     * @return $this
     */
    public function values($values) {
        $this->values = $values;
        return $this;
    }

    public function getQuery() {
        return "INSERT INTO " . $this->table . " (" . implode(", ", $this->columns) . ") VALUES (" . implode(", ", $this->values) . ")";
    }

    /**
     * Executes the INSERT query.
     *
     * @return bool True on success, false on failure.
     */
    public function execute() {
        if (empty($this->table) || empty($this->columns) || empty($this->values)) {
            throw new Exception("Table, columns, and values must be specified.");
        }

        if (count($this->columns) !== count($this->values)) {
            throw new Exception("Number of columns and values must match.");
        }

        // Escape column names to prevent SQL injection
        $escapedColumns = array_map([$this, 'escapeIdentifier'], $this->columns);
        $placeholders = array_fill(0, count($this->values), '?');
        $query = "INSERT INTO " . $this->escapeIdentifier($this->table) . " (" . implode(", ", $escapedColumns) . ") VALUES (" . implode(", ", $placeholders) . ")";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->lastErrorMsg());
        }

        // Bind values with appropriate data types
        foreach ($this->values as $index => $value) {
            $paramIndex = $index + 1; // SQLite3 uses 1-based indexing for parameters
            $stmt->bindValue($paramIndex, $value, $this->getSQLiteType($value));
        }

        $result = $stmt->execute();
        if ($result === false) {
            throw new Exception("Insert failed: " . $this->db->lastErrorMsg());
        }

        // Optionally, return the last inserted row ID
        return $this->db->lastInsertRowID();
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

    /**
     * Escapes an identifier (column name or table name) to prevent SQL injection.
     *
     * @param string $identifier
     * @return string
     */
    private function escapeIdentifier($identifier) {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
}
?>
