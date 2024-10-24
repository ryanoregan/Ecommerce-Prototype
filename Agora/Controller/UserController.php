<?php

namespace Agora\Controller;

require_once __DIR__ . '/../Model/UserModel.php';

use Agora\Database\IContext;
use Agora\Model\UserModel; // Assuming you have a UserModel for user data
use Agora\View\LoginView; // Assuming you have a LoginView for rendering the login form
use Agora\View\SignUpView; // Assuming you have a SignUpView for rendering the signup form
use Agora\View\BuyerView;
use Agora\View\SellerView;

class UserController extends AbstractController
{
    private IContext $context; // Define context as a property

    public function __construct(IContext $context)
    {
        parent::__construct($context);
        $this->context = $context; // Assign the context to the property
    }

    public function handleLogin()
    {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
    
            // Validate inputs
            if (empty($username) || empty($password)) {
                // Show an error message if fields are empty
                $loginView = new LoginView();
                $loginView->setTemplate('./html/index.html');
                $loginView->setTemplateField('errorMessage', 'Please enter both username and password.');
                echo $loginView->render();
                return; // Stop further execution
            }
    
            // Access the database through Context
            $db = $this->context->getDB();
    
            try {
                // Logic to validate user credentials
                $userData = $db->findUserByUsername($username); // Fetch user data based on the username
    
                if ($userData) {
                    // Create a UserModel instance with the retrieved data
                    $userModel = new UserModel(
                        $userData['UserID'], // Assuming the data has 'userID' key
                        $userData['UserName'], // Assuming the data has 'username' key
                        $userData['Email'], // Assuming the data has 'email' key
                        $userData['Password'], // Assuming the data has 'password' key
                        $userData['Role'], // Assuming the data has 'role' key
                        true
                    );
    
            // Verify the password using the raw password from the POST data
            if (password_verify($password, $userData['Password'])) { // Compare with the hashed password
                // Login successful
                $this->context->setUser($userModel); // Store user in Context
                $this->context->getSession()->set('loggedInUser', $userModel); // Store user in session
                header("Location: ./index.php/dashboard");
                    // Redirect user based on their role
                    // switch ($userModel->getRole()) {
                    //     case 'Buyer':
                    //         header("Location: ./index.php/dashboard");
                    //         break;
                    //     case 'Seller':
                    //         header("Location: ./index.php");
                    //         break;
                    //     case 'Master Admin':
                    //         header("Location: /admin_dashboard");
                    //         break;
                    //     case 'Business Account Administrator':
                    //         header("Location: /business_dashboard");
                    //         break;
                    //     default:
                    //         header("Location: /homepage"); // Fallback if role is not matched
                    // }
                exit();
            } else {
                // Password did not match
                echo "<script>alert('Invalid username or password. Entered: $password, Stored: {$userData['Password']}'); window.history.back();</script>";
                return; // Stop further execution
            }
            } else {
                // User not found
                echo "<script>alert('Invalid username or password.'); window.history.back();</script>";
                return; // Stop further execution
            }
    
                // If we reach here, login failed
                $loginView = new LoginView();
                $loginView->setTemplate('./html/index.html');
                $loginView->setTemplateField('errorMessage', $errorMessage);
                echo $loginView->render();
            } catch (\Exception $e) {
                // Handle exceptions, e.g., rendering issues
                echo 'Error: ' . htmlspecialchars($e->getMessage()); // Display the error message safely
            }
        } else {
            // If not a POST request, show the login form
            try {
                $loginView = new LoginView();
                $loginView->setTemplate('./html/index.html');
                echo $loginView->render();
            } catch (\Exception $e) {
                // Handle exceptions during rendering
                echo 'Error: ' . htmlspecialchars($e->getMessage()); // Display the error message safely
            }
        }
    }

    public function handleSignUp()
    {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'Buyer'; // Default role if not provided

            if ($password !== $confirmPassword) {
                echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
                return;
            }

            // Logic to create a new user account
            try {
                // Create a UserModel instance with default values for userID and isLoggedIn
                $userModel = new UserModel(0, $username, $email, '', $role, false); // Password will be set later
                // Hash the password before saving
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Access the database through Context
                $db = $this->context->getDB();
                if (!$db->isConnected()) {
                    error_log("Database connection is not established.");
                    return false; // Or handle it appropriately
                }

                // Call createUser with the Database instance
                $userModel->createUser($db, $username, $email, $hashedPassword);

            // Show alert for successful signup
            echo "<script>alert('User registered successfully! Please log in.'); window.location.href = '/MyWebsite/Assessment%203/index.php';</script>";
            exit();
        } catch (\Exception $e) {
            // Handle exceptions during user creation
            $this->renderSignUpView('Error: ' . htmlspecialchars($e->getMessage()));
        }
    } else {
        // If not a POST request, show the signup form
        $this->renderSignUpView();
    }
    }

    public function renderSignUpView($errorMessage = null)
    {
        try {
            $signUpView = new SignUpView();
            $signUpView->setTemplate('./html/signup.html'); // Adjust path to your signup template
            if ($errorMessage) {
                $signUpView->setTemplateField('errorMessage', $errorMessage);
            }
            echo $signUpView->render();
        } catch (\Exception $e) {
            // Handle exceptions during rendering
            echo 'Error: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function renderBuyerView($errorMessage = null)
{
    try {
        $buyerView = new BuyerView(); // Create an instance of BuyerView
        $buyerView->setTemplate('./html/buyer.html'); // Set the template path
        $buyerView->setTemplateField('username', $this->context->getUser()); 

        // Optionally set an error message
        if ($errorMessage) {
            $buyerView->setTemplateField('errorMessage', $errorMessage);
        }

        echo $buyerView->render(); // Render the view
    } catch (\Exception $e) {
        // Handle exceptions during rendering
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}

public function renderSellerView($errorMessage = null)
{
    try {
        $sellerView = new SellerView(); // Create an instance of SellerView
        $sellerView->setTemplate('./html/seller.html'); // Set the template path
        $sellerView->setTemplateField('username', $this->context->getUser()); 

        // Optionally set an error message
        if ($errorMessage) {
            $sellerView->setTemplateField('errorMessage', $errorMessage);
        }

        echo $sellerView->render(); // Render the view
    } catch (\Exception $e) {
        // Handle exceptions during rendering
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}

    public function handleLogout()
    {
        // Clear the session to log out the user
        try {
            $session = $this->context->getSession();
            $session->clear(); // Clear session using your Session class

            // Redirect to the login page or homepage
            header("Location: /MyWebsite/Assessment%203/index.php"); // Adjust path as necessary
            exit();
        } catch (\Exception $e) {
            // Handle exceptions during logout
            echo 'Error: ' . htmlspecialchars($e->getMessage()); // Display the error message safely
        }
    }

    public function getProfile()
    {
        // Check if the user is logged in
        $user = $this->context->getUser();
        if ($user === null) {
            echo "<script>alert('You must be logged in to view your listings.'); window.history.back();</script>";
            return;
        }
    
        // Get the current seller's ID
        $userID = $user->getUserID();
    
        try {
            // Access the database through Context
            $db = $this->context->getDB();
            if (!$db->isConnected()) {
                error_log("Database connection is not established.");
                return false;
            }
    
            // Fetch all listings for the current seller
            // Assuming `getItemsBySellerID` returns an array of associative arrays
            $userModel = new UserModel(0, '', '', 0.0, 0); // Placeholder constructor to access method
            $profileData = $userModel->getProfilebyUserID($db, $userID); // Fetch items
    
        // Check if profileData is an array and contains data
        if (!is_array($profileData) || empty($profileData)) {
            throw new \Exception("No profile data found for user ID: $userID.");
        }

        var_dump($profileData); // Check what you get here
        var_dump($profileData[0]['UserID']); // Debug output
        // Create UserModel instance with the fetched data
        $Profile[] = new UserModel(
            $profileData[0]['UserID'],
            $profileData[0]['UserName'],
            $profileData[0]['Email'],
            $profileData[0]['Password'],
            $profileData[0]['Role']
        );

        $buyerView = new buyerView();
        $buyerView->setTemplate('./html/buyerProfile.html');
        $buyerView->setProfile(['user' => $Profile,]); // Pass associative array
        echo $buyerView->render();

    } catch (\Exception $e) {
        // Handle exceptions during the retrieval of profile data
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
}