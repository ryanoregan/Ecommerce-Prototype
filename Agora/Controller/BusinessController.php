<?php

namespace Agora\Controller;

use Agora\Database\IContext;
use Agora\View\BusinessView;
use Agora\Model\BusinessModel;



class BusinessController extends AbstractController
{
    private IContext $context;

    public function __construct(IContext $context)
    {
        parent::__construct($context);
        $this->context = $context;
    }

    public function getAdminHome($errorMessage = null)
    {
        try {
            $businessView = new BusinessView(); // Create an instance of SellerView
            $businessView->setTemplate('./html/BusinessAdminHome.html'); // Set the template path
            $businessView->setTemplateField('username', $this->context->getUser()); 
    
            // Optionally set an error message
            if ($errorMessage) {
                $businessView->setTemplateField('errorMessage', $errorMessage);
            }
    
            echo $businessView->render(); // Render the view
        } catch (\Exception $e) {
            // Handle exceptions during rendering
            echo 'Error: ' . htmlspecialchars($e->getMessage());
        }
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
    
                // Save the business account to the database (assuming BusinessModel has a createBusiness method)
                $businessModel->createBusiness($db);

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
}