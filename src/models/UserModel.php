<?php

class UserModel {

    public $id;
    public $username;
    public $email;
    public $password;
    public $role_id;
    public $created_at;
    public $updated_at;


    /**
     * Constructor for the UserModel class.
     *
     * @param string $username The username of the user.
     * @param string $email The email of the user.
     * @param string $password The password of the user.
     * @param int $role_id The ID of the role of the user.
     */
    public function __construct($id, $username, $email, $password, $role_id) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->created_at = date("m-d-Y H:i:s");
        $this->updated_at = date("m-d-Y H:i:s");
    }

    /**
     * Converts the object to an array.
     *
     * @return array The object properties as an array.
     */
    public function toArray() {
        return get_object_vars($this);
    }
}

?>