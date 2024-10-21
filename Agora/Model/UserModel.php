<?php

namespace Agora\Model;

class UserModel
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

    // Method to hash the user's password (assuming you're using bcrypt for hashing)
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
    
        // Debugging: Log the SQL and parameters
        error_log("Executing SQL: $sql");
        error_log("Parameters: username=$username, email=$email, password=******, role=user");
        
        // Execute the prepared statement
        if (!$db->executePrepared($sql, $fields)) {
            error_log("User creation failed for username: $username, email: $email");
            return false;
        }
    
        echo "<script>alert('User created successfully: username=$username, email=$email');</script>"; // Log success
        return true;
    }
}