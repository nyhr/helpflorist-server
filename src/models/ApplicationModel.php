<?php

class ApplicationModel {
    public $id;
    public $name;
    public $version;
    public $type;
    public $download_url;
    public $created_by;
    public $created_at;
    public $updated_by;
    public $updated_at;

    
    /**
     * Constructor for the ApplicationModel class.
     *
     * @param int $id The id of the application.
     * @param string $name The name of the application.
     * @param string $version The version of the application.
     * @param string $type The type of the application.
     * @param string $download_url The download URL of the application.
     * @param int $created_by The ID of the user who created the application.
     * @param string $created_at The creation date of the application.
     * @param int $updated_by The ID of the user who updated the application.
     * @param string $updated_at The update date of the application.
     */
    public function __construct($id, $name, $version, $type, $download_url, $created_by, $created_at, $updated_by, $updated_at) {
        $this->id = $id;
        $this->name = $name;
        $this->version = $version;
        $this->type = $type;
        $this->download_url = $download_url;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
        $this->updated_by = $updated_by;
        $this->updated_at = $updated_at;
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