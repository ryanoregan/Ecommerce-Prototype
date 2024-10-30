<?php

namespace Agora\Database;

class Database implements IDatabase
{
    private $conn; // Database connection resource
    private bool $isInTransaction = false; // Flag to track if a transaction is in progress

    // Constructor to initialize the database connection
    public function __construct($host, $user, $password, $database)
    {
        $this->conn = new \mysqli($host, $user, $password, $database);

        // Check for connection errors
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to execute a query and return results
    public function query(string $sql)
    {
        $result = $this->conn->query($sql);
        if (!$result) {
            $this->sqlError($sql);
        }
        return $result;
    }

    // Method to execute a query that modifies data (e.g., INSERT, UPDATE, DELETE)
    public function execute(string $sql): bool
    {
        error_log("Executing SQL: $sql"); // Log the SQL query being executed
        $success = $this->conn->query($sql);
        if (!$success) {
            error_log("SQL Error: " . $this->conn->error . " in query: $sql");
            return false;
        }
        return true;
    }
    

    // Method to execute a batch of SQL queries
    public function executeBatch(array $list): bool
    {
        foreach ($list as $sql) {
            if (!$this->execute($sql)) {
                return false; // Stop at the first error
            }
        }
        return true;
    }

    // Method to get the last inserted ID
    public function getInsertID(): int
    {
        return $this->conn->insert_id;
    }

    // Method to close the database connection
    public function close(): void
    {
        $this->conn->close();
    }

    // Begin a transaction
    public function beginTransaction(): void
    {
        $this->conn->begin_transaction();
        $this->isInTransaction = true;
    }

    // Commit the current transaction
    public function commitTransaction(): void
    {
        if ($this->isInTransaction) {
            $this->conn->commit();
            $this->isInTransaction = false;
        }
    }

    // Roll back the current transaction
    public function rollbackTransaction(): void
    {
        if ($this->isInTransaction) {
            $this->conn->rollback();
            $this->isInTransaction = false;
        }
    }

    // Method to execute a prepared query and return results
    public function queryPrepared(string $parameterisedSQL, array $fields)
    {
        $stmt = $this->conn->prepare($parameterisedSQL);
        if ($stmt === false) {
            $this->sqlError($parameterisedSQL);
            return false;
        }

        // Bind parameters
        $stmt->bind_param(...$this->getBindParams($fields));

        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all results as an associative array
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : null; // Return results or null if no results
    }

    // Method to execute a prepared query that modifies data
    public function executePrepared(string $parameterisedSQL, array $fields): bool
    {
        // Get binding parameters
        $bindParams = $this->getBindParams($fields);
        
        // Prepare the SQL statement
        $stmt = $this->conn->prepare($parameterisedSQL);
        if ($stmt === false) {
            error_log("Failed to prepare statement: " . $this->conn->error);
            return false;
        }
    
        // Bind parameters
        if (!$stmt->bind_param(...$bindParams)) {
            error_log("Failed to bind parameters: " . $stmt->error);
            return false;
        }
        
        error_log("Preparing to execute statement..."); // Log before execution
        if (!$stmt->execute()) {
            error_log("Failed to execute prepared statement: " . $stmt->error);
            return false;
        }
        error_log("Statement executed successfully."); // Log on successful execution
    
        // Check the affected rows to confirm insertion
        if ($stmt->affected_rows === 0) {
            error_log("No rows were inserted. SQL: $parameterisedSQL");
            return false;
        }
    
        // If everything is successful, log the successful execution
        error_log("User created successfully. SQL: $parameterisedSQL, Params: " . implode(', ', $fields));
    
        // Close the statement and return true
        $stmt->close();
        return true;
    }

    // Method to find a user by username
    public function findUserByUsername(string $username)
    {
        // Use a prepared statement to prevent SQL injection
        $stmt = $this->conn->prepare("SELECT UserID, UserName, Email, Password, Role FROM Users WHERE UserName = ? LIMIT 1");
        if ($stmt === false) {
            $this->sqlError("Preparing statement failed");
            return null; // Return null if preparation fails
        }
    
        // Bind the username parameter
        $stmt->bind_param('s', $username);
        $stmt->execute();
    
        // Get the result
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Return user data as an associative array
    }

    // Private method to handle SQL errors
    private function sqlError($source): void
    {
        echo "SQL Error: " . $this->conn->error . " in query: $source\n";
    }

    // Helper method to prepare bind parameters for prepared statements
    private function getBindParams(array $fields): array
    {
        // Log the fields received for binding
        error_log("getBindParams called with fields: " . implode(', ', $fields));
    
        // Assume all fields are strings, but you can adjust the logic if necessary
        $types = str_repeat('s', count($fields)); // 's' for string
    
        // Log the type string that will be used for binding
        error_log("Binding types: " . $types);

        return array_merge([$types], $fields);
    }

        // Database.php
    public function isConnected(): bool
    {
        return $this->conn instanceof \mysqli && $this->conn->ping();
    }

// Method to find a user by UserID and return their role
public function getUserRoleByUserID(int $userID): ?string
{
    // Use a prepared statement to prevent SQL injection
    $stmt = $this->conn->prepare("SELECT Role FROM Users WHERE UserID = ? LIMIT 1");
    if ($stmt === false) {
        $this->sqlError("Preparing statement failed");
        return null; // Return null if preparation fails
    }

    // Bind the userID parameter
    $stmt->bind_param('i', $userID);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    
    // Fetch the role
    $role = $result->fetch_assoc()['Role'] ?? null; // Return the role or null if not found

    // Close the statement
    $stmt->close();
    
    return $role; // Return the user role
}

public function testQuery()
{
    // Example query to test the connection
    $result = $this->query("SELECT COUNT(*) AS count FROM users"); // Change 'users' to your actual table name
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    return false; // or throw an exception if needed
}
}