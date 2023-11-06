<?php
require_once 'database/database.php';
require_once 'database/queries.php';

class Search {
    private $db;

    public function setDatabaseConfig($configFile) {
        include $configFile;
        $this->db = new Database($db_host, $db_user, $db_password, $db_name);
    }

    public function search($queryKeyword, $tables) {
        $result = [];

        try {
            $sql = createQuery($tables, $queryKeyword);
            $result = $this->db->query($sql);
        } catch (Exception $e) {
            $errorData = array(
                'error' => 'Error',
                'message' => $e->getMessage()
            );
            
            // Encode the error data as JSON
            echo json_encode($errorData);
            exit();
        }

        return $result;
    }
}
