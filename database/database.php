<?php
class Database {
    private $connection;

    public function __construct($host, $user, $password, $database) {
        set_error_handler(array($this, 'handleWarningAsException')); // Set custom error handler for warnings
        $errors = $this->checkDatabaseParameters($host, $user, $database);
        restore_error_handler(); // Restore the default error handler

        if ($errors) {
            $this->returnJsonError($errors);
        } else {
            try {
                $this->tryMysqliConnection($host, $user, $password, $database);
            } catch (mysqli_sql_exception $e) {
                $this->returnJsonError('Mysqli connection failed: ' . $e->getMessage());
            }
        }
    }

    private function checkDatabaseParameters($host, $user, $database) {
        $errors = [];

        if (empty($host)) {
            $errors[] = 'Database host is empty.';
        }

        if (empty($user)) {
            $errors[] = 'Database username is empty.';
        }

        if (empty($database)) {
            $errors[] = 'Database name is empty.';
        }

        return $errors;
    }

    private function tryMysqliConnection($host, $user, $password, $database) {
        $this->connection = @new mysqli($host, $user, $password, $database); // Use @ to suppress warnings

        if ($this->connection->connect_error) {
            $errorMessage = $this->connection->connect_error;

            if (strpos($errorMessage, 'Unknown database') !== false) {
                throw new mysqli_sql_exception('Database name does not exist: ' . $errorMessage);
            } elseif (strpos($errorMessage, 'Access denied for user') !== false) {
                throw new mysqli_sql_exception('Access denied for the given username and password: ' . $errorMessage);
            } else {
                throw new mysqli_sql_exception('Mysqli connection failed: ' . $errorMessage);
            }
        }
    }

    public function query($sql) {
        if ($this->connection instanceof mysqli) {
            // Use mysqli for queries
            $result = $this->connection->query($sql);
            if ($result === false) {
                $errorMessage = strip_tags($this->connection->error);
                $this->returnJsonError('Mysqli query failed: ' . $errorMessage);
            }
            return $result;
        } else {
            $this->returnJsonError('No valid database connection available for query');
        }
    }

    private function returnJsonError($messages) {
        $errorData = array(
            'error' => 'Error',
            'messages' => $messages
        );

        // Encode the error data as JSON and return it
        echo json_encode($errorData);
        exit();
    }

    public function handleWarningAsException($errno, $errstr) {
        $this->returnJsonError('Warning: ' . $errstr);
    }
}
