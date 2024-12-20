<?php

namespace Agora\Model;

class UserModel extends AbstractModel
{
    private int $userID;
    private string $userName;
    private string $email;
    private string $password;
    private string $role;
    private bool $isLoggedIn;

    public function __construct(int $userID, string $userName, string $email, string $password, string $role, bool $isLoggedIn = false)
    {
        $this->userID = $userID;
        $this->userName = $userName;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->isLoggedIn = $isLoggedIn;
    }

    // Getter for userID
    public function getUserID(): int
    {
        return $this->userID;
    }

    // Getter for userName
    public function getUserName(): string
    {
        return $this->userName;
    }

    // Getter for user role
    public function getRole(): string
    {
        return $this->role;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    // Method to hash the user's password
    public function passwordHash(): string
    {
        return password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Method to check if the user is logged in
    public function isLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    // Update account settings (for example, email or password)
    public function updateAccountSettings(string $newEmail = null, string $newPassword = null): void
    {
        if ($newEmail) {
            $this->email = $newEmail;
        }

        if ($newPassword) {
            // Store the hashed password
            $this->password = password_hash($newPassword, PASSWORD_BCRYPT);
        }
    }

    public function createUser(\Agora\Database\Database $db, string $username, string $email, string $hashedPassword): bool
    {
        // SQL query with placeholders
        $sql = "INSERT INTO Users (UserName, Email, Password, Role) VALUES (?, ?, ?, ?)";

        // Prepare parameters for binding, including role
        $fields = [$username, $email, $hashedPassword, $this->role];

        // Execute the prepared statement
        if (!$db->executePrepared($sql, $fields)) {
            error_log("User creation failed for username: $username, email: $email");
            return false;
        }

        // Get the newly inserted UserID
        $userId = $db->getInsertId();

        // Insert into the appropriate role table based on the user's role
        switch ($this->role) {
            case 'Seller':
                $sqlRole = "INSERT INTO Sellers (UserID, Location) VALUES (?, 'Default Location')";
                break;
            case 'Business Account Administrator':
                $sqlRole = "INSERT INTO BusinessAccountAdministrators (UserID, HQLocation, LegalBusinessDetails) VALUES (?, 'Default HQ', 'Default Legal Details')";
                break;
            default:
                error_log("Role assignment failed for username: $username, invalid role: {$this->role}");
                return false;
        }

        // Execute the prepared statement for role table
        if (!$db->executePrepared($sqlRole, [$userId])) {
            error_log("Role assignment failed for username: $username, UserID: $userId");
            return false;
        }

        echo "<script>alert('User created successfully: username=$username, email=$email');</script>"; // Log success
        return true;
    }

    public function getProfileByUserID($db, $userID)
    {
        $sql = "SELECT * FROM Users WHERE userID = ?";
        $fields = [$userID];

        // Log the SQL query and parameters
        error_log("Executing query: " . $sql . " with parameters: " . json_encode($fields));

        // Make sure to return an empty array if the execution fails
        $result = $db->queryPrepared($sql, $fields);

        // Check if $result is false
        if ($result === false) {
            error_log("Failed to retrieve items for seller ID: " . $userID);
            return []; // Return an empty array on failure
        }

        // Log the retrieved results
        error_log("Retrieved items: " . print_r($result, true));

        return $result; // Return the result set
    }

    public function updateUserName($db, $userID, $newUserName)
    {
        $sql = "UPDATE Users SET userName = ? WHERE userID = ?";
        $fields = [$newUserName, $userID]; // Include userID in fields array

        // Log the SQL query and parameters
        error_log("Executing query: " . $sql . " with parameters: " . json_encode($fields));

        // Execute the prepared statement
        $result = $db->queryPrepared($sql, $fields);

        // Check if the query execution was successful
        if ($result === false) {
            error_log("Failed to update username for user ID: " . $userID);
            return false; // Return false on failure
        }

        // Log success message
        error_log("Successfully updated username for user ID: " . $userID);

        return true; // Return true to indicate success
    }

    public function updateEmail($db, $userID, $newEmail)
    {
        $sql = "UPDATE Users SET Email = ? WHERE userID = ?";
        $fields = [$newEmail, $userID]; // Include userID in fields array

        // Log the SQL query and parameters
        error_log("Executing query: " . $sql . " with parameters: " . json_encode($fields));

        // Execute the prepared statement
        $result = $db->queryPrepared($sql, $fields);

        // Check if the query execution was successful
        if ($result === false) {
            error_log("Failed to update username for user ID: " . $userID);
            return false; // Return false on failure
        }

        // Log success message
        error_log("Successfully updated username for user ID: " . $userID);

        return true; // Return true to indicate success
    }

    public function updatePassword($db, $userID, $hashedPassword)
    {
        $sql = "UPDATE Users SET Password = ? WHERE userID = ?";
        $fields = [$hashedPassword, $userID]; // Include userID in fields array

        // Log the SQL query and parameters
        error_log("Executing query: " . $sql . " with parameters: " . json_encode($fields));

        // Execute the prepared statement
        $result = $db->queryPrepared($sql, $fields);

        // Check if the query execution was successful
        if ($result === false) {
            error_log("Failed to update username for user ID: " . $userID);
            return false; // Return false on failure
        }

        // Log success message
        error_log("Successfully updated username for user ID: " . $userID);

        return true; // Return true to indicate success
    }

    public function getRoleByUserID($db, int $userID): ?string
    {
        // SQL query to retrieve the role
        $sql = "SELECT Role FROM Users WHERE UserID = ?";
        $fields = [$userID];

        // Log the SQL query and parameters
        error_log("Executing query: " . $sql . " with parameters: " . json_encode($fields));

        // Execute the prepared statement
        $result = $db->queryPrepared($sql, $fields);

        // Check if $result is false or empty
        if ($result === false || empty($result)) {
            error_log("Failed to retrieve role for user ID: " . $userID);
            return null; // Return null if the query fails or no role is found
        }

        // Log the retrieved role
        error_log("Retrieved role for user ID {$userID}: " . $result[0]['Role']);

        // Return the role from the result set
        return $result[0]['Role'];
    }

    public function usernameExists($db, $username): bool
    {
        $sql = "SELECT COUNT(*) AS count FROM Users WHERE UserName = ?";
        $fields = [$username];

        // Execute the prepared statement
        $result = $db->queryPrepared($sql, $fields);

        // Check if query execution was successful and retrieve count
        if ($result === false || empty($result)) {
            error_log("Failed to check username existence for: " . $username);
            return false;
        }

        // Return true if count is greater than 0, indicating the username exists
        return $result[0]['count'] > 0;
    }
    
}