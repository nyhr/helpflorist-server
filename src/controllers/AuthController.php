<?php

require_once(__DIR__ . "/../lib/jwt/index.php");
require_once(__DIR__ . "/UserController.php");
require_once(__DIR__ . "/../models/UserModel.php");

class AuthController {
    private $db;
    private $jwt;

    /**
     * Constructor for the AuthController class.
     *
     * @param DatabaseManager $db The database manager instance.
     */
    public function __construct($db) {
        $this->db = $db;
        $this->jwt = new JWT();
    }

    /**
     * Login a user.
     *
     * @param string $username
     * @param string $password
     * @return string
     * @throws Exception
     */
    public function login($username, $password) {
        $userController = new UserController($this->db);    
        $user = $userController->getByUsername($username);
        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "User not found"]);
            exit;
        } 

        if (!password_verify($password, $user["password"])) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid password"]);
            exit;
        }

        unset($user["password"]);

        return $this->jwt->createToken($user);
    }
}
?>