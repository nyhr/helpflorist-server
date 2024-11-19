<?php
// SQLITE3 database manager
class DatabaseManager {
    private $db;
    private $db_path;

    public function __construct() {
        $settings_path = dirname(__DIR__, 3) . "/settings.ini";
        $settings = parse_ini_file($settings_path, true);
        
        if(!$settings["database"]["path"]) {
            throw new Exception("DATABASE_PATH is not set");
        } else {
            $this->db_path = dirname(__DIR__, 3) . "/" . $settings["database"]["path"];
        }
        
        $this->db = new SQLite3($this->db_path);
    }

    public function query($query) {
        return $this->db->query($query);
    }

    public function getDatabase() {
        return $this->db;
    }

    public function exec($query) {
        return $this->db->exec($query);
    }
}

?>
