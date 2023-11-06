<?php

function createQuery($data, $keyword) {
    
    if (checkFormat($data)) {
        // Determine the number of tables in the JSON data
        $numTables = count($data['tables']);

        if ($numTables > 1) {
            // Find common columns (the first column from each table)
            $commonColumns = [];
            foreach ($data['tables'] as $table => $tableColumns) {
                $commonColumns[] = "$table." . $tableColumns[0];
            }

            // Build the JOIN query
            $joinConditions = [];

            foreach ($data['tables'] as $table => $tableColumns) {
                $joinConditions[] = "$table." . $tableColumns[0] . " = " . reset($commonColumns);
            }

            $selectColumns = [];
            foreach ($data['tables'] as $table => $tableColumns) {
                foreach ($tableColumns as $column) {
                    $selectColumns[] = "$table.$column";
                }
            }

            $joinQuery = "SELECT " . implode(', ', $selectColumns) . " FROM " . implode(' JOIN ', array_keys($data['tables']));

            if (!empty($joinConditions)) {
                $joinQuery .= " ON " . implode(' AND ', $joinConditions);
            }

            $whereConditions = [];

            // Add conditions for the WHERE clause using the LIKE operator
            foreach ($data['tables'] as $table => $tableColumns) {
                foreach ($tableColumns as $column) {
                    $whereConditions[] = "$table.$column LIKE '%$keyword%'";
                }
            }

            if (!empty($whereConditions)) {
                $joinQuery .= " WHERE " . implode(' OR ', $whereConditions);
            }

            return $joinQuery;
        } elseif ($numTables === 1) {
            // If there is only one table, create a normal query
            $table = key($data['tables']);
            $columns = $data['tables'][$table];

            // Initialize an array to store conditions for each column
            $conditions = [];

            // Loop through columns and create a condition for each one using the LIKE operator
            foreach ($columns as $column) {
                $conditions[] = "$table.$column LIKE '%$keyword%'";
            }

            // Combine conditions using "OR" to search for the keyword in any column
            $query = "SELECT " . implode(', ', $columns) . " FROM $table WHERE " . implode(' OR ', $conditions);

           
        }
    } 
    return $query;
}

function findCommonColumns($tables) {
    if (count($tables) < 2) {
        return array();
    }

    $commonColumns = array();

    // Iterate through the columns of the first table to check for commonality
    $firstTable = key($tables); // Get the table name of the first table
    $firstTableColumns = reset($tables);

    foreach ($firstTableColumns as $column) {
        $isCommon = true;

        // Check if the column exists in all tables and record the table names
        $tableNames = array($firstTable); // Include the first table
        foreach ($tables as $tableName => $tableColumns) {
            if (!in_array($column, $tableColumns)) {
                $isCommon = false;
                break;
            } else {
                $tableNames[] = $tableName;
            }
        }

        if ($isCommon) {
            // Store the common column with its corresponding table names
            $commonColumns[$column] = $tableNames;
        }
    }

    return $commonColumns;
}


function checkFormat($data) {
    $errorData = array();
    $success = true;
    
    if (is_array($data) && isset($data['tables']) && is_array($data['tables'])) {
        $tables = $data['tables'];

        if (count($tables) > 0) {
            foreach ($tables as $tableName => $tableColumns) {
                if (is_string($tableName) && is_array($tableColumns) && count($tableColumns) > 0) {
                    continue;
                } else {
                    $success = false;
                    $errorData = array(
                        'error' => 'Error',
                        'messages' => "Tables provided should have an array of columns. Check our documentation for correct format."
                    );
                    echo json_encode($errorData);
                    exit();
                }
            }
        } else {
            $success = false;
            $errorData = array(
                'error' => 'Error',
                'messages' => "No tables provided. Check our documentation for correct format."
            );
            echo json_encode($errorData);
            exit();
        }
    } else {
        $success = false;
        $errorData = array(
            'error' => 'Error',
            'messages' => "Incorrect JSON format provided. Check our documentation for correct format."
        );

        echo json_encode($errorData);
        exit();
    }

    return $success;

}

