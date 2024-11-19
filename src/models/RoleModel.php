<?php

class RoleModel {
    public $id;
    public $name;

    
    /**
     * Constructor for the RoleModel class.
     *
     * @param int $id The ID of the role.
     * @param string $name The name of the role.
     */
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
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