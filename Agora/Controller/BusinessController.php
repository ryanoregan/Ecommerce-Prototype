<?php

namespace Agora\Controller;

use Agora\Database\IContext;
use Agora\View\BusinessView;
use Agora\Model\BusinessModel;
use Agora\Model\UserModel;


class BusinessController extends AbstractController
{
    private IContext $context;

    public function __construct(IContext $context)
    {
        parent::__construct($context);
        $this->context = $context;
    }

    public function handleCreateBusinessAccount() {
        // Check if the user is logged in
        $user = $this->context->getUser();
        if ($user === null) {
            echo "<script>alert('You must be logged in to create a business account.'); window.history.back();</script>";
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve business data from the form submission
            $businessName = $_POST['businessName'] ?? '';
            $legalDetails = $_POST['legalDetails'] ?? '';
            $hqLocation = $_POST['hqLocation'] ?? '';
            $additionalLocations = $_POST['additionalLocations'] ?? ''; // This will be a string

            // Convert the string to an array (if there are multiple locations)
            $additionalLocationsArray = !empty($additionalLocations) ? array_map('trim', explode(',', $additionalLocations)) : []; // Split by comma and trim spaces
    
            // Handle image upload
            $imagePath = null; // Initialize image path
            if (isset($_FILES['businessLogo']) && $_FILES['businessLogo']['error'] == UPLOAD_ERR_OK) {
                $targetDir = "uploads/"; // Make sure this directory is writable
                $targetFile = $targetDir . basename($_FILES["businessLogo"]["name"]);
    
                // Get the file type
                $fileType = mime_content_type($_FILES["businessLogo"]["tmp_name"]); // Get MIME type of the uploaded file
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
                // Validate file type
                if (!in_array($fileType, $allowedTypes)) {
                    echo "<script>alert('Invalid file type. Please upload an image (JPEG, PNG, GIF).'); window.history.back();</script>";
                    return;
                }
    
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["businessLogo"]["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile; // Store the path for the database
                } else {
                    echo "<script>alert('Error uploading the image.'); window.history.back();</script>";
                    return;
                }
            }
    
            try {
                // Create a BusinessModel instance to represent the business account
                $businessModel = new BusinessModel(
                    0, // BusinessID will be auto-generated
                    $businessName,
                    $legalDetails,
                    $hqLocation,
                    $additionalLocationsArray, // Pass the array here
                    $imagePath
                );
    
                // Access the database through Context
                $db = $this->context->getDB();
                if (!$db->isConnected()) {
                    error_log("Database connection is not established.");
                    return false;
                }

                $userID = $user->getUserID(); // Get the UserID as an integer

    
                // Save the business account to the database (assuming BusinessModel has a createBusiness method)
                $businessModel->createBusiness($db, $userID);

            // Show success alert and redirect to dashboard
            echo "<script>alert('Business Account created successfully!'); window.location.href='/MyWebsite/Assessment%203/index.php/dashboard';</script>";
            exit();
            } catch (\Exception $e) {
                echo 'Error: ' . htmlspecialchars($e->getMessage());
            }
        } else {
            // Render the create business account form if not a POST request
            try {
                $businessView = new BusinessView();
                $businessView->setTemplate('./html/createBusinessAccount.html');
                echo $businessView->render();
            } catch (\Exception $e) {
                echo 'Error: ' . htmlspecialchars($e->getMessage());
            }
        }
    }

    public function getBusinessAccounts()
    {
        // Check if the user is logged in
        $user = $this->context->getUser();
        if ($user === null) {
            echo "<script>alert('You must be logged in to view your business accounts.'); window.history.back();</script>";
            return;
        }
    
        // Get the current user's ID
        $userID = $user->getUserID();
    
        try {
            // Access the database through Context
            $db = $this->context->getDB();
            if (!$db->isConnected()) {
                error_log("Database connection is not established.");
                return false;
            }
    
            // Fetch all business accounts for the current user
            $businessModel = new BusinessModel(0, '', '', '', [], ''); // Placeholder constructor to access method
            $businessAccountsData = $businessModel->getBusinessAccountsByUserID($db, $userID); // Fetch business accounts
    
            // Ensure businessAccountsData is an array
            if (!is_array($businessAccountsData)) {
                throw new \Exception("Failed to fetch business accounts.");
            }
    
            // Convert fetched data into `BusinessModel` objects
            $businessAccounts = [];
            foreach ($businessAccountsData as $businessData) {
                $businessAccounts[] = new BusinessModel(
                    $businessData['BusinessID'],
                    $businessData['BusinessName'],
                    $businessData['LegalBusinessDetails'],
                    $businessData['HQLocation'],
                    !empty($businessData['AdditionalLocations']) ? explode(',', $businessData['AdditionalLocations']) : [], // Convert string to array
                    $businessData['ImagePath'] ?? null // Assuming ImagePath is optional
                );
            }
    
 
                // Render the business accounts page
                $businessView = new BusinessView();
                $businessView->setTemplate('./html/BusinessAdminHome.html'); // Adjust path to the business accounts template
                $businessView->setBusinessAccounts($businessAccounts); // Pass the business accounts to the view
                echo $businessView->render();
            
    
        } catch (\Exception $e) {
            // Handle exceptions during the process
            echo 'Error: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function submitEditAccounts()
{
    $db = $this->context->getDB();
    if (!$db->isConnected()) {
        error_log("Database connection is not established.");
        return false;
    }
    
    // Check if request is POST for form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $businessID = $_POST['businessID'] ?? null;

        if ($businessID) {
            // Retrieve posted data
            $newBusinessName = $_POST['businessName'] ?? null;
            $newLegalDetails = $_POST['legalBusinessDetails'] ?? null;
            $newHQLocation = $_POST['hqLocation'] ?? null;
            $newAdditionalLocations = $_POST['additionalLocations'] ?? null; // Comma-separated list
            $logoImage = $_FILES['logoImage'] ?? null;

            // Update business details
            $businessModel = new BusinessModel(0, '', '', '', [], '');
            
            if ($newBusinessName) {
                $businessModel->updateBusinessName($db, $businessID, $newBusinessName);
            }
            if ($newLegalDetails) {
                $businessModel->updateLegalBusinessDetails($db, $businessID, $newLegalDetails);
            }
            if ($newHQLocation) {
                $businessModel->updateHQLocation($db, $businessID, $newHQLocation);
            }
            if ($newAdditionalLocations) {
                // Convert comma-separated string to array and update
                $locationsArray = array_map('trim', explode(',', $newAdditionalLocations));
                $businessModel->updateAdditionalLocations($db, $businessID, $locationsArray);
            }
            if ($logoImage && $logoImage['error'] === UPLOAD_ERR_OK) {
                // Handle logo image upload
                $targetDir = '/path/to/uploads/'; // Define your actual upload path
                $targetFile = $targetDir . basename($logoImage['name']);
                if (move_uploaded_file($logoImage['tmp_name'], $targetFile)) {
                    $businessModel->updateLogoPath($db, $businessID, $targetFile);
                } else {
                    error_log("Failed to upload logo image.");
                }
            }
            
            // Redirect to the business accounts page after the update
            header("Location: ./index.php/dashboard");
            exit();
        }
    }
}

public function getConnections(int $businessID)
{
    // Ensure the user is logged in
    $user = $this->context->getUser();
    if ($user === null) {
        echo "<script>alert('You must be logged in to view connections.'); window.history.back();</script>";
        return;
    }

    try {
        // Access the database through Context
        $db = $this->context->getDB();
        if (!$db->isConnected()) {
            error_log("Database connection is not established.");
            return false;
        }

        // Fetch connections for the specified business ID
        $businessModel = new BusinessModel(0, '', '', '', [], '');
        $connectionsData = $businessModel->getConnectionsByBusinessID($db, $businessID);

        // Fetch business name based on businessID
        $businessName = $businessModel->getBusinessNameByID($db, $businessID);

        // Create view and set data
        $businessView = new BusinessView();
        $businessView->setTemplate('./html/connections.html');
        $businessView->setConnections($connectionsData);
        $businessView->setBusinessName($businessName); // Pass the business name to the view
        $businessView->setBusinessID($businessID);
        echo $businessView->render();

    } catch (\Exception $e) {
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}

public function handleAddConnection() {
    // Ensure the user is logged in
    $user = $this->context->getUser();
    if ($user === null) {
        echo "<script>alert('You must be logged in to add a connection.'); window.history.back();</script>";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $businessID = $_POST['businessID'] ?? null;
        $userID = $_POST['userID'] ?? null;

        // Validate input
        if ($businessID && $userID) {
            try {
                // Access the database through Context
                $db = $this->context->getDB();
                if (!$db->isConnected()) {
                    error_log("Database connection is not established.");
                    return false;
                }

                // Check if userID exists and retrieve role
                $userModel = new UserModel(0, '', '', 0.0, 0);
                $userRole = $userModel->getRoleByUserID($db, $userID);

                if ($userRole) {
                    // Add connection to Users_Business table
                    $businessModel = new BusinessModel(0, '', '', '', [], '');
                    $businessModel->addConnection($db, $businessID, $userID, $userRole);

                    // Show success alert
                    echo "<script>alert('Connection added successfully!'); window.history.back();</script>";
                } else {
                    echo "<script>alert('User ID does not exist.'); window.history.back();</script>";
                }
            } catch (\Exception $e) {
                echo 'Error: ' . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "<script>alert('Business ID and User ID are required.'); window.history.back();</script>";
        }
    }
}
}