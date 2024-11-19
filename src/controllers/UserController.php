<?php

require_once(__DIR__ . "/../models/index.php");
require_once(__DIR__ . "/../lib/database/index.php");

class UserController {

    private $db;

    /**
     * Constructor for the UserController class.
     *
     * @param DatabaseManager $db The database manager instance.
     */
    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    }

    /**
     * Creates a new user.
     *
     * @param UserModel $user The user data to create.
     * @return UserModel The created user.
     */
    public function create(UserModel $user) {
        try {
            // Optional: Validate input data before attempting to insert
            $this->validateUser($user);

            // Optional: Check if email already exists to provide immediate feedback
            if ($this->emailExists($user->email)) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Email already exists"]);
                exit;
            }

            $insert = new Insert($this->db);
            $insert->into("users")
                ->columns(["username", "password", "email", "role_id", "created_at", "updated_at"])
                ->values([
                    $user->username,
                    password_hash($user->password, PASSWORD_DEFAULT),
                    $user->email,
                    $user->role_id,
                    $user->created_at,
                    $user->updated_at
                ])
                ->execute();

            header("HTTP/1.1 201 Created");
            echo json_encode(["message" => "User created successfully"]);
            return $user;
        } catch (Exception $e) {
            // Log the exception for debugging purposes
            error_log($e->getMessage());

            if (strpos($e->getMessage(), "UNIQUE constraint failed") !== false) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Username or email already exists"]);
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(["error" => "An unexpected error occurred. Please try again later."]);
            }
            exit;
        }
    }

    /**
     * Checks if an email exists in the database.
     *
     * @param string $email The email to check.
     * @return bool | array True if the email exists, false otherwise.
     */
    public function emailExists($email) {
        $select = new Select($this->db);
        return $select->from("users")
        ->where("email = ?", [$email])
        ->fetch();
    }

    /**
     * Checks if a username exists in the database.
     *
     * @param string $username The username to check.
     * @return bool | array True if the username exists, false otherwise.
     */
    public function usernameExists($username) {
        $select = new Select($this->db);
        return $select->from("users")
        ->where("username = ?", [$username])
        ->fetch();
    }

    /**
     * Validates the user data.
     *
     * @param UserModel $user The user data to validate.
     * @throws Exception If the user data is invalid.
     */
    public function validateUser(UserModel $user) {
        if(empty($user->username) || empty($user->password) || empty($user->email)) {
            throw new Exception("Invalid user data");
        }
    }

    /**
     * Gets a user by their ID.
     *
     * @param int $id The ID of the user to get.
     * @return array | bool The user data, or false if the user does not exist.
     */
    public function getOne($id) {
        $select = new Select($this->db);
        return $select->from("users")
        ->where("id = ?", [$id])
        ->fetch();
    }

    /**
     * Gets all users.
     *
     * @return array | bool The user data, or false if the user does not exist.
     */
    public function getAll() {
        $select = new Select($this->db);
        return $select->from("users")
        ->fetchAll();
    }

    /**
     * Gets a user by their username.
     *
     * @param string $username The username to get.
     * @return array | bool The user data, or false if the user does not exist.
     */
    public function getByUsername($username) {
        $select = new Select($this->db);
        return $select->from("users")
        ->where("username = ?", [$username])
        ->fetch();
    }

    /**
     * Summary of getById
     * @param mixed $id
     * @return array|null
     */
    public function getById($id) {
        $select = new Select($this->db);
        return $select->from("users")
        ->where("id = ?", [$id])
        ->fetch();
    }

    /**
     * Updates a user.
     *
     * @param UserModel $user The user data to update.
     * @return bool True if the user was updated, false otherwise.
     */
    public function update(UserModel $user) {
        $updateFields = array_filter($user->toArray(), function($value) {
            return $value !== null;
        });

        unset($updateFields['id']);
        unset($updateFields['created_at']);
        
        if (isset($updateFields['password'])) {
            $updateFields['password'] = password_hash($updateFields['password'], PASSWORD_DEFAULT);
        }

        $updateFields['updated_at'] = date('m-d-Y H:i:s');

        $update = new Update($this->db);
        return $update->table("users")
            ->set($updateFields)
            ->where("id = ?", [$user->id])
            ->execute();
    }

    /**
     * Deletes a user.
     *
     * @param int $id The ID of the user to delete.
     * @return bool True if the user was deleted, false otherwise.
     */
    public function delete($id) {
        $delete = new Delete($this->db);
        return $delete->from("users")
        ->where("id = ?", [$id])
        ->execute();
    }
}

?>