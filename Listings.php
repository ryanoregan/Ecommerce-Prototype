<?php

// Include necessary files
require_once './Agora/Database/IContext.php';
require_once './Agora/Database/Context.php';
require_once './Agora/Database/IDatabase.php';
require_once './Agora/Database/Database.php';
require_once './Agora/Controller/AbstractController.php';
require_once './Agora/Controller/UserController.php';
require_once './Agora/View/AbstractView.php';
require_once './Agora/View/LoginView.php';
require_once './Agora/Database/ISession.php';
require_once './Agora/Database/Session.php';
require_once './Agora/Model/UserModel.php';

// Start a session to manage user session data
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /MyWebsite/Assessment%203/index.php'); // Redirect to login if not logged in
    exit();
}

// Create a new context or database instance (assuming it's already configured)
$context = new \Agora\Database\Context(/* pass config or use factory method */);
$db = $context->getDB();

// Get the user's ID from the session
$sellerID = $_SESSION['user']['userID'];

// Fetch listings for the logged-in user
$items = $db->getItemsBySellerID($db, $sellerID);

?>