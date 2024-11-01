<?php

require_once './Agora/Database/IContext.php';
require_once './Agora/Database/Context.php';
require_once './Agora/Database/IDatabase.php';
require_once './Agora/Database/Database.php';
require_once './Agora/Controller/AbstractController.php';
require_once './Agora/Controller/UserController.php';
require_once './Agora/View/AbstractView.php';
require_once './Agora/View/LoginView.php';
require_once './Agora/View/SignUpView.php'; // Include SignUpView
require_once './Agora/Database/ISession.php';
require_once './Agora/Database/Session.php';
require_once './Agora/Model/UserModel.php';

// Start a session to manage user session data
session_start();

// Create a new context or database instance (assuming it's already configured)
$context = new \Agora\Database\Context(/* pass config or use factory method */);
$db = $context->getDB();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Find the user by username
    $user = $db->findUserByUsername($username);

    // Check if user exists and password matches
    if ($user && password_verify($password, $user['Password'])) { // Make sure to use the correct column name
        // Store user data in session
        $_SESSION['user'] = [
            'userID' => $user['UserID'], // Adjust based on your user table
            'username' => $user['UserName'], // Adjust based on your user table
            'email' => $user['Email'], // Adjust based on your user table
            'role' => $user['Role'] // Adjust based on your user table
        ];

        // Redirect to a logged-in page (e.g., dashboard)
        header('Location: /MyWebsite/Assessment%203/dashboard.php'); // Change to your dashboard page
        exit();
    } else {
        // Invalid login
        $errorMessage = "Invalid username or password.";
        // You might want to redirect back with an error message or show it on the same page
        echo $errorMessage; // Replace this with your error handling logic
    }
}