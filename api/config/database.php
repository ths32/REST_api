<?php
$path = getcwd();
require_once($path.'/config/config.php');

class Database
{
    private $connection;

    public function __construct()
    {
        $this->initializeConnection();
    }

    private function initializeConnection()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);
            if ($this->connection->connect_error) {
                throw new Exception("Could not connect to the database: " . $this->connection->connect_error);
            }
        } catch (Exception $e) {
            throw new Exception("Database connection error: " . $e->getMessage());
        }
    }

    // public function query($query, $params = [])
    // {
    //     try {
    //         $stmt = $this->prepareStatement($query, $params);
    //         $result = $this->executeStatement($stmt);
    //         $data = $result->fetch_all(MYSQLI_ASSOC);
    //         $stmt->close();
    //         return $data;
    //     } catch (Exception $e) {
    //         throw new Exception("Database query error: " . $e->getMessage());
    //     }
    // }

    public function query($query, $params = [])
    {  
        try {
            $stmt = $this->prepareStatement($query, $params);
            if (stripos($query, 'SELECT') !== false) {
                // For SELECT queries, bind the parameters, execute the statement, and return the result set
                $this->bindParams($stmt, $params);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result === false) {
                    throw new Exception("Error retrieving result set: " . $stmt->error);
                }    
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $data;
            } elseif (stripos($query, 'INSERT') !== false) {
                // For INSERT statements, execute the statement and return the inserted ID
                $stmt->execute();
                $insertedId = $stmt->insert_id;
                $stmt->close();
                return $insertedId;
            } elseif (stripos($query, 'UPDATE') !== false || stripos($query, 'DELETE') !== false) {
                // For UPDATE or DELETE statements, execute the statement and return the affected rows
                $stmt->execute();
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
                return $affectedRows;
            } else {
                // For other queries, fetch data
                // SELECT queries
                //$this->bindParams($stmt, $params);
                //$stmt->execute();
                //$result = $stmt->get_result();
                //if ($result === false) {
                    throw new Exception("Error retrieving result set: " . $stmt->error);
                //}
                //$result = $this->executeStatement($stmt);
                //$data = $result->fetch_all(MYSQLI_ASSOC);
                //$stmt->close();
                //return $data;
            }
        } catch (Exception $e) {
            throw new Exception("Database query error: " . $e->getMessage());
        }
    }


    private function prepareStatement($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            throw new Exception("Unable to prepare statement: " . $this->connection->error);
        }
        $this->bindParams($stmt, $params);
        return $stmt;
    }

    private function bindParams($stmt, $params)
    {
        if (!empty($params)) {
            $paramTypes = $params[0];
            $paramValues = array_slice($params, 1);
            $stmt->bind_param($paramTypes, ...$paramValues);
        }
    }

    private function executeStatement($stmt)
    {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            throw new Exception("Error retrieving result set: " . $stmt->error);
        }
        return $result;
    }

    // Add more methods as needed
    // public function closeConnection()
    // {
    //     if ($this->connection !== null && $this->connection->ping()) {
    //         $this->connection->close();
    //     }
    // }
    public function closeConnection()
    {
        try {
            if ($this->connection !== null) {
                $this->connection->close();
                $this->connection = null;
            }
        } catch (Exception $e) {
            echo "Error in closeConnection: " . $e->getMessage();
        }
    }
    
    public function __destruct()
    {
        try {
            if ($this->connection !== null) {
                //$this->closeConnection();
                $this->connection->close();
                //$this->connection = null;
            }
        } catch (Exception $e) {
            echo "Error in __destruct: " . $e->getMessage();
        }
    }
}