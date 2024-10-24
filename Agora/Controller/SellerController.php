<?php

namespace Agora\Controller;

use Agora\Database\IContext;
use Agora\Model\ItemModel; // Use ItemModel to represent the item being sold
use Agora\Model\UserModel;
use Agora\View\SellerView; // Assuming you have a SellerView to render seller-related pages
use Agora\Model\SellerModel;

class SellerController extends AbstractController
{
    private IContext $context;

    public function __construct(IContext $context)
    {
        parent::__construct($context);
        $this->context = $context;
    }

    public function handleAddSaleItem()
    {
            // Check if the user is logged in
        $user = $this->context->getUser();
        if ($user === null) {
            echo "<script>alert('You must be logged in to add an item.'); window.history.back();</script>";
            return;
    }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve item data from the form submission with updated keys
            $itemName = $_POST['item_name'] ?? ''; // Changed to item_name
            $itemDescription = $_POST['item_description'] ?? ''; // Changed to item_description
            $itemPrice = $_POST['item_price'] ?? ''; // Changed to item_price
    
            // Validate inputs (ensure none of the fields are empty)
            if (empty($itemName) || empty($itemDescription) || empty($itemPrice)) {
                echo "<script>alert('All fields are required.'); window.history.back();</script>";
                return;
            }


        // Handle image upload
        $imagePath = null; // Initialize image path
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploads/"; // Make sure this directory is writable
            $targetFile = $targetDir . basename($_FILES["item_image"]["name"]);

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile; // Store the path for the database
            } else {
                // Handle error (e.g., file move failed)
                echo "<script>alert('Error uploading the image.'); window.history.back();</script>";
                return;
            }
        } else {
            echo "<script>alert('No image was uploaded.'); window.history.back();</script>";
            return;
        }
    
            try {
                // Create an ItemModel instance to represent the sale item
                $itemModel = new ItemModel(
                    0, // ItemID will be auto-generated
                    $itemName,
                    $itemDescription,
                    $itemPrice,
                    $this->context->getUser()->getUserID(), // Get current seller's ID
                    $imagePath
                );
    
                // Access the database through Context
                $db = $this->context->getDB();
                if (!$db->isConnected()) {
                    error_log("Database connection is not established.");
                    return false;
                }
    
                // Save the item to the database (assuming ItemModel has a createItem method)
                $itemModel->createItem($db);
    
                // Redirect to the seller dashboard after successful creation
                header("Location: /MyWebsite/Assessment%203/index.php/dashboard");
                exit();
            } catch (\Exception $e) {
                // Handle exceptions during item creation
                echo 'Error: ' . htmlspecialchars($e->getMessage());
            }
        } else {
            // Render the add item form if not a POST request
            try {
                $sellerView = new SellerView();
                $sellerView->setTemplate('./html/seller.html'); // Adjust path to the add item template
                echo $sellerView->render();
            } catch (\Exception $e) {
                echo 'Error: ' . htmlspecialchars($e->getMessage());
            }
        }
    }

    public function getListings()
    {
        // Check if the user is logged in
        $user = $this->context->getUser();
        if ($user === null) {
            echo "<script>alert('You must be logged in to view your listings.'); window.history.back();</script>";
            return;
        }
    
        // Get the current seller's ID
        $sellerID = $user->getUserID();
    
        try {
            // Access the database through Context
            $db = $this->context->getDB();
            if (!$db->isConnected()) {
                error_log("Database connection is not established.");
                return false;
            }
    
            // Fetch all listings for the current seller
            // Assuming `getItemsBySellerID` returns an array of associative arrays
            $itemModel = new ItemModel(0, '', '', 0.0, 0); // Placeholder constructor to access method
            $listingsData = $itemModel->getItemsBySellerID($db, $sellerID); // Fetch items
    
            // Convert fetched data into `ItemModel` objects
            $listings = [];
            foreach ($listingsData as $itemData) {
                $listings[] = new ItemModel(
                    $itemData['ItemID'],
                    $itemData['ItemName'],
                    $itemData['Description'],
                    (float)$itemData['Price'],
                    $itemData['SellerID'],
                    $itemData['ImagePath'] ?? null // Assuming ImagePath is optional
                );
            }
    
            // Render the listings page with the fetched listings
            $sellerView = new SellerView();
            $sellerView->setTemplate('./html/listings.html'); // Adjust path to the listings template
    
            // Pass the listings to the view (assuming SellerView has a method for this)
            $sellerView->setListings($listings); // Assuming you will implement this in the view

    
            echo $sellerView->render();
    
        } catch (\Exception $e) {
            // Handle exceptions during the retrieval of listings
            echo 'Error: ' . htmlspecialchars($e->getMessage());
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
        $sellerID = $user->getUserID();
    
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
            $profileData = $userModel->getProfilebyUserID($db, $sellerID); // Fetch items
    
        // Check if profileData is an array and contains data
        if (!is_array($profileData) || empty($profileData)) {
            throw new \Exception("No profile data found for user ID: $sellerID.");
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

        // Create an instance of SellerModel to get the location
        $sellerModel = new SellerModel(''); // Assuming a constructor that sets necessary properties
        $location = $sellerModel->getLocationByUserID($db, $sellerID); // Fetch location


        // Check if the user is attempting to edit the profile
        if (isset($_GET['action']) && $_GET['action'] === 'edit') {
            // Render the edit form with the existing user and location data
            // If no edit action, just render the profile page
            $sellerView = new SellerView();
            $sellerView->setTemplate('./html/SellerProfile.html');
            $sellerView->setProfile(['user' => $Profile, 'location' => $location]); // Pass associative array
            echo $sellerView->render();
            
        } else {
            // If no edit action, just render the profile page
            $sellerView = new SellerView();
            $sellerView->setTemplate('./html/SellerProfile.html');
            $sellerView->setProfile(['user' => $Profile, 'location' => $location]); // Pass associative array
            echo $sellerView->render();
        }

    } catch (\Exception $e) {
        // Handle exceptions during the retrieval of profile data
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
public function submitEdit()
{

    $db = $this->context->getDB();
    if (!$db->isConnected()) {
        error_log("Database connection is not established.");
        return false;
    }
    // Check if request is POST for form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userID = $_POST['userID'] ?? null;

        if ($userID) {
            // Retrieve posted data
            $newUserName = $_POST['username'] ?? null;
            $newEmail = $_POST['email'] ?? null;
            $newPassword = $_POST['password'] ?? null;
            $newLocation = $_POST['location'] ?? null;

            // Update user details
            $userModel = new userModel(0, '', '', 0.0, 0);
            if ($newUserName) {
                $userModel->updateUserName($db, $userID, $newUserName);
            }
            if ($newEmail) {
                $userModel->updateEmail($db, $userID, $newEmail);
            }
            if ($newPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $userModel->updatePassword($db, $userID, $hashedPassword);
            }
            if ($newLocation) {
                $sellerModel = new SellerModel('');
                $sellerModel->updateLocation($db, $userID, $newLocation);
            }
            
            // Redirect to the profile page after the update
            header("Location: ./index.php/profile");
            exit();
        }
    } 

}

}