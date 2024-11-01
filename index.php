<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Manually require classes
require_once './Agora/Database/IContext.php';
require_once './Agora/Database/Context.php';
require_once './Agora/Database/IDatabase.php';
require_once './Agora/Database/Database.php';
require_once './Agora/Controller/AbstractController.php';
require_once './Agora/Controller/UserController.php';
require_once './Agora/Controller/SellerController.php';
require_once './Agora/Controller/BuyerController.php';
require_once './Agora/Controller/BusinessController.php';
require_once './Agora/View/AbstractView.php';
require_once './Agora/View/LoginView.php';
require_once './Agora/View/SignUpView.php';
require_once __DIR__ . '/Agora/View/BuyerView.php';
require_once __DIR__ . '/Agora/View/SellerView.php';
require_once __DIR__ . '/Agora/View/BusinessView.php';
require_once './Agora/Database/ISession.php';
require_once './Agora/Database/Session.php';
require_once './Agora/Model/ItemModel.php';
require_once './Agora/Model/SellerModel.php';
require_once './Agora/Model/BusinessModel.php';
require_once './Agora/Database/IURI.php';
require_once './Agora/Database/URI.php';

use Agora\Database\Context;
use Agora\Database\Session;
use Agora\Controller\UserController; 
use Agora\Controller\SellerController;
use Agora\Controller\BuyerController;
use Agora\Controller\BusinessController;
use Agora\Database\URI;

session_start(); // Start the session

// Define the path to the config file
$configFile = __DIR__ . '/Agora/config/config.ini';

// Create a new Session instance
$session = new Session(); 

// Initialize the database context
try {
    // Create a new Context instance
    $context = new Context(null, '/MyWebsite/Assessment 3', [], $session); // Pass null for db initially
    $context->createFromConfigFile($configFile); // Load the config and create the DB connection
} catch (\Exception $e) {
    // Handle the error
    echo "Error: " . $e->getMessage();
    exit; // Stop further execution
}

// After initializing the database context
$database = $context->getDB(); // Get the database instance from the context

// Initialize the URI object
$uri = new URI($_SERVER['HTTP_HOST']); // Create URI object with the host
$uri->createFromRequest(); // Populate the parts of the URI from the request

//uri to handle actions
$action = $_GET['action'] ?? null;
$userID = $_GET['userID'] ?? null;
$itemID = $_GET['itemID'] ?? null;
$businessID = $_GET['businessID'] ?? null;

// Routing logic
if ($action === 'edit' && $userID !== null) {
    $sellerController = new SellerController($context);
    $sellerController->getProfile($action, $userID);
    exit();
}
if ($action === 'viewItem' && $itemID !== null) {
    $buyerController = new BuyerController($context);
    $buyerController->viewItemDetail($action, $itemID);
    exit();
}
if ($action === 'edit' && $businessID !== null) {
    $businessController = new BusinessController($context);
    $businessController->getBusinessAccounts($action, $businessID);
    exit();
}

if ($businessID !== null) {
    $businessController = new BusinessController($context);
    $businessController->getConnections($businessID);
    exit();
}


// Routing based on the last part of the URI
$lastPart = end($uri->parts); // Get the last part of the URI

switch ($lastPart) {
    case 'index.php':
        // Show the login page
        $userController = new UserController($context);
        $userController->handleLogin();
        break;

    case 'signup':
        // Check if it's a POST request for signup
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle the signup form submission
            $userController = new UserController($context);
            $userController->handleSignUp(); // Call handleSignUp to process the signup
        } else {
            // Show the signup page (GET request)
            $userController = new UserController($context);
            $userController->renderSignUpView(); // Render the signup form
        }
        break;

    case 'logout':
        // Handle logout
        $userController = new UserController($context);
        $userController->handleLogout();
        break;

    case 'dashboard':
        // Check if user is stored in the session
        $loggedInUser = $session->get('loggedInUser');

        if ($loggedInUser) {
            // Set the user in the context
            $context->setUser($loggedInUser);

            // Now you can continue processing based on the user's role
            $user = $context->getUser();

            if ($user) {
                // Redirect based on user role
                $role = $user->getRole();

                switch ($role) {
                    case 'Buyer':
                        $buyerController = new BuyerController($context);
                        $buyerController->getAllItems();
                        break;
                    case 'Seller':
                        $userController = new UserController($context);
                        $userController->renderSellerView();
                        break;
                    case 'Business Account Administrator':
                        $businessController = new BusinessController($context);
                        $businessController->getBusinessAccounts();
                        break;
                    default:
                        echo "Invalid user role!";
                        break;
                }
            }
        } else {
            // If no user is logged in, redirect to login page
            header("Location: /MyWebsite/Assessment%203/index.php");
            exit();
        }
        break;

    case 'addSaleItem':
        $sellerController = new SellerController($context);
        $sellerController->handleAddSaleItem();
        break;

    case 'listings':
        $sellerController = new SellerController($context);
        $sellerController->getListings();
        break;


    case 'profile':
        $sellerController = new SellerController($context);
        $sellerController->getProfile();
        // Check if there are any query parameters
        $action = $_GET['action'] ?? null;
        $userID = $_GET['userID'] ?? null;

        break;

    case 'buyerProfile':
        $userController = new UserController($context);
        $userController->getProfile();
        break;

    case 'submitEdit':
        $sellerController = new Agora\Controller\SellerController($context);
        $sellerController->submitEdit();
        break;

    case 'createBusinessAccount':
        $businessController = new Agora\Controller\BusinessController($context);
        $businessController->handleCreateBusinessAccount();
        break;

    case 'submitEditAccounts':
        $businessController = new Agora\Controller\BusinessController($context);
        $businessController->submitEditAccounts();
        break;


    case 'connections':
        $businessController = new Agora\Controller\BusinessController($context);
        $businessController->handleAddConnection();
        break;

    case 'sellerConnections':
        $sellerController = new Agora\Controller\SellerController($context);
        $sellerController->showConnections();
        break;

    case 'buyerConnections':
        $buyerController = new Agora\Controller\BuyerController($context);
        $buyerController->showConnections();
        break;


    default:
        // Handle 404 error or redirect to home
        http_response_code(404);
        echo '404 Not Found';
}

