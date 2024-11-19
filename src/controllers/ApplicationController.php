<?php

require_once(__DIR__ . "/../controllers/index.php");
require_once(__DIR__ . "/../lib/database/index.php");
require_once(__DIR__ . "/../models/index.php");

class ApplicationController {
    private $db;

    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    }

    /**
     * Creates a new application.
     *
     * @param ApplicationModel $application The ApplicationModel object to create.
     * @return ApplicationModel The created application.
     */
    public function create(ApplicationModel $application) {
        try {
            if($this->applicationExists($application->name)) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Application already exists"]);
                exit;
            }

            $insert = new Insert($this->db);
            $insert->into("applications")
            ->columns(["name", "version", "type", "download_url", "created_by", "created_at", "updated_by", "updated_at"])
            ->values([
                $application->name,
                $application->version,
                $application->type,
                $application->download_url,
                $application->created_by,
                $application->created_at,
                $application->updated_by,
                $application->updated_at
            ]);
            $insert->execute();

            header("HTTP/1.1 201 Created");
            echo json_encode(["message" => "Application created successfully"]);
            
            return $application;
        } catch (Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => "An unexpected error occurred. Please try again later.", "data" => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Checks if an application exists by name.
     *
     * @param string $name The name of the application to check.
     * @return bool True if the application exists, false otherwise.
     */
    private function applicationExists(string $name) {
        $select = new Select($this->db);
        return $select->from("applications")
        ->where("name = ?", [$name])
        ->fetch() !== null;
    }

    /**
     * Retrieves all applications.
     *
     * @return array An array of ApplicationModel objects.
     */
    public function getAll() {
        $select = new Select($this->db);
        return $select->from("applications")
        ->fetchAll();
    }

    /**
     * Retrieves an application by its ID.
     *
     * @param int $id The ID of the application to retrieve.
     * @return array | null The ApplicationModel object or null if not found.
     */
    public function getById(int $id) {
        $select = new Select($this->db);
        return $select->from("applications")
        ->where("id = ?", [$id])
        ->fetch();
    }

    /**
     * Updates an application.
     *
     * @param ApplicationModel $application The ApplicationModel object to update.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(ApplicationModel $application) {
        $update = new Update($this->db);
        return $update->table("applications")
        ->set($application->toArray())
        ->where("id = ?", [$application->id])
        ->execute();
    }

    /**
     * Deletes an application by its ID.
     *
     * @param int $id The ID of the application to delete.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function delete(int $id) {
        $delete = new Delete($this->db);
        return $delete->from("applications")
        ->where("id = ?", [$id])
        ->execute();
    }
}
?>