<?php

require_once(__DIR__ . "/../models/index.php");
require_once(__DIR__ . "/../lib/database/index.php");

class RoleController {
    private $db;

    /**
     * Constructor for the RoleController class.    
     *
     * @param DatabaseManager $db The database manager instance.
     */
    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    } 
    
    /**
     * Creates a new role.
     *
     * @param RoleModel $role The role data to create.
     * @return RoleModel The created role.
     */
    public function create(RoleModel $role) {
        try {
            if($this->roleExists($role->name)) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Role already exists"]);
                exit;
            }

            $insert = new Insert($this->db);
            $insert->into("roles")
                ->columns(["name"])
                ->values([$role->name])
                ->execute();

            header("HTTP/1.1 201 Created");
            echo json_encode(["message" => "Role created successfully"]);
            return $role;
        } catch(Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => "An unexpected error occurred. Please try again later.", "data" => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Checks if a role exists by name.
     *
     * @param string $name The name of the role to check.
     * @return bool True if the role exists, false otherwise.
     */
    private function roleExists(string $name) {
        $select = new Select($this->db);
        return $select->from("roles")
        ->where("name = ?", [$name])
        ->fetch() !== null;
    }

    /**
     * Retrieves all roles.
     *
     * @return array An array of RoleModel objects.
     */
    public function getAll() {
        $select = new Select($this->db);
        return $select->from("roles")
        ->fetchAll();
    }

    /**
     * Retrieves a role by its ID.
     *
     * @param int $id The ID of the role to retrieve.
     * @return array | bool The retrieved role.
     */
    public function getById(int $id) {
        $select = new Select($this->db);
        return $select->from("roles")
        ->where("id = ?", [$id])
        ->fetch();
    }

    /**
     * Updates a role.
     *
     * @param RoleModel $role The role data to update.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(RoleModel $role) {
        $update = new Update($this->db);
        return $update->table("roles")
        ->set($role->toArray())
        ->where("id = ?", [$role->id])
        ->execute();
    }

    /**
     * Deletes a role by its ID.
     *
     * @param int $id The ID of the role to delete.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function delete(int $id) {
        $delete = new Delete($this->db);
        return $delete->from("roles")
        ->where("id = ?", [$id])
        ->execute();
    }
}
?>