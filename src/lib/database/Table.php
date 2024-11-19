<?php

require_once(__DIR__ . "/DatabaseManager.php");

class Table {
    private $db;
    private $table;
    private $ifNotExists = false;
    private $columns = array();
    private $primaryKey = null;
    private $constraints = array();

    /**
     * Constructor for the Table class.
     *
     * @param DatabaseManager $db The database manager instance.
     */
    public function __construct(DatabaseManager $db) {
        $this->db = $db->getDatabase();
    }

    /**
     * Sets the name of the table to create.
     *
     * @param string $name The name of the table.
     * @return $this
     */
    public function create($name) {
        $this->table = $name;
        return $this;
    }

    /**
     * Adds IF NOT EXISTS clause to the CREATE TABLE statement.
     *
     * @return $this
     */
    public function ifNotExists() {
        $this->ifNotExists = true;
        return $this;
    }

    /**
     * Adds a column to the table.
     *
     * @param string $name        The name of the column.
     * @param string $type        The data type of the column.
     * @param string $constraints Optional constraints (e.g., 'NOT NULL').
     * @return $this
     */
    public function addColumn($name, $type, $constraints = '') {
        $this->columns[] = array(
            'name' => $name,
            'type' => $type,
            'constraints' => $constraints
        );
        return $this;
    }

    /**
     * Sets the primary key for the table.
     *
     * @param string|array $columns The column(s) to set as primary key.
     * @return $this
     */
    public function primaryKey($columns) {
        if (is_string($columns)) {
            $columns = array($columns);
        }
        $this->primaryKey = $columns;
        return $this;
    }

    /**
     * Adds a table-level constraint.
     *
     * @param string $constraint The constraint to add.
     * @return $this
     */
    public function addConstraint($constraint) {
        $this->constraints[] = $constraint;
        return $this;
    }

    /**
     * Executes the CREATE TABLE statement.
     *
     * @return bool True on success, false on failure.
     */
    public function execute() {
        $sql = 'CREATE TABLE ';
        if ($this->ifNotExists) {
            $sql .= 'IF NOT EXISTS ';
        }
        $sql .= $this->table . ' (';

        $columnDefs = array();
        foreach ($this->columns as $column) {
            $colDef = $column['name'] . ' ' . $column['type'];
            if (!empty($column['constraints'])) {
                $colDef .= ' ' . $column['constraints'];
            }
            $columnDefs[] = $colDef;
        }

        if (!empty($this->primaryKey)) {
            $pk = 'PRIMARY KEY (' . implode(', ', $this->primaryKey) . ')';
            $columnDefs[] = $pk;
        }

        if (!empty($this->constraints)) {
            foreach ($this->constraints as $constraint) {
                $columnDefs[] = $constraint;
            }
        }

        $sql .= implode(', ', $columnDefs);
        $sql .= ');';

        // Execute the SQL
        try {
            $this->db->exec($sql);
        } catch (Exception $e) {
            // Handle exception
            echo 'Error creating table: ' . $e->getMessage();
            return false;
        }

        // Reset the builder
        $this->reset();

        return true;
    }

    /**
     * Resets the builder to its initial state.
     */
    private function reset() {
        $this->table = null;
        $this->ifNotExists = false;
        $this->columns = array();
        $this->primaryKey = null;
        $this->constraints = array();
    }
}

?>
