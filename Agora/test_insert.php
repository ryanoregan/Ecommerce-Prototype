<?php
// Basic example to test database connection and insertion

$host = "localhost"; // Replace with your host
$user = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "Agora"; // Replace with your database name

// Create a new mysqli connection
$conn = new mysqli($host, $user, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the user data
$username = 'testuser';
$email = 'test@test.com';
$plainPassword = 'password123'; // Replace with the desired plain text password
$role = 'user';

// Hash the password
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

// Prepare the insert statement
$sql = "INSERT INTO Users (UserName, Email, Password, Role) VALUES ('$username', '$email', '$hashedPassword', '$role')";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "User inserted successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>