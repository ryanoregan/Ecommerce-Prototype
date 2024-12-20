<?php

namespace Agora\Controller;

use Agora\Database\IContext;
use Agora\Model\ItemModel;
use Agora\Model\UserModel;
use Agora\View\SellerView;
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
            $itemName = $_POST['item_name'] ?? '';
            $itemDescription = $_POST['item_description'] ?? '';
            $itemPrice = $_POST['item_price'] ?? '';

            // Validate inputs
            if (empty($itemName) || empty($itemDescription) || empty($itemPrice)) {
                echo "<script>alert('All fields are required.'); window.history.back();</script>";
                return;
            }


            // Handle image upload
            $imagePath = null; // Initialize image path
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == UPLOAD_ERR_OK) {
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($_FILES["item_image"]["name"]);

                // Get the file type
                $fileType = mime_content_type($_FILES["item_image"]["tmp_name"]); // Get MIME type of the uploaded file
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                // Validate file type
                if (!in_array($fileType, $allowedTypes)) {
                    echo "<script>alert('Invalid file type. Please upload an image (JPEG, PNG, GIF).'); window.history.back();</script>";
                    return;
                }

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile; // Store the path for the database
                } else {
                    // Handle error (e.g., file move failed)
                    echo "<script>alert('Error uploading the image.'); window.history.back();</script>";
                    return;
                }
            } else {
                echo "<script>alert('No image was uploaded or there was an upload error.'); window.history.back();</script>";
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

                // Save the item to the database
                $itemModel->createItem($db);
                
                // Redirect to the seller dashboard after successful creation
                echo "<script>alert('Item successfully listed!.'); window.location.href = '/MyWebsite/Assessment%203/index.php/dashboard';</script>";
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
            $itemModel = new ItemModel(0, '', '', 0.0, 0); // Placeholder constructor to access method
            $listingsData = $itemModel->getItemsBySellerID($db, $sellerID); // Fetch items

            // Convert fetched data into `ItemModel` objects
            $listings = [];
            foreach ($listingsData as $itemData) {
                $listings[] = new ItemModel(
                    $itemData['ItemID'],
                    $itemData['ItemName'],
                    $itemData['Description'],
                    (float) $itemData['Price'],
                    $itemData['SellerID'],
                    $itemData['ImagePath'] ?? null
                );
            }

            // Render the listings page with the fetched listings
            $sellerView = new SellerView();
            $sellerView->setTemplate('./html/listings.html');

            // Pass the listings to the view
            $sellerView->setListings($listings);

            echo $sellerView->render();

        } catch (\Exception $e) {
            // Handle exceptions during the retrieval of listings
            echo 'Error: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function getProfile($action = null, $userID = null)
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
            $userModel = new UserModel(0, '', '', '', ''); // Placeholder constructor to access method
            $profileData = $userModel->getProfilebyUserID($db, $sellerID); // Fetch items

            // Check if profileData is an array and contains data
            if (!is_array($profileData) || empty($profileData)) {
                throw new \Exception("No profile data found for user ID: $sellerID.");
            }

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
                $userModel = new userModel(0, '', '', '', '');
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

    public function showConnections()
    {
        // Check if the user is logged in
        $user = $this->context->getUser();
        if ($user === null) {
            echo "<script>alert('You must be logged in to view your connections.'); window.history.back();</script>";
            return;
        }

        // Get the current seller's ID
        $sellerID = $user->getUserID();

        // Access the database through Context
        $db = $this->context->getDB();
        if (!$db->isConnected()) {
            echo "<script>alert('Database connection is not established. Please try again later.'); window.history.back();</script>";
            return;
        }

        // Instantiate the BusinessModel and fetch businesses
        $businessModel = new \Agora\Model\BusinessModel(0, '', '', '', [], '');
        $businesses = $businessModel->getBusinessAccountsByUserID($db, $sellerID);

        // Render the connections view
        $sellerView = new SellerView();
        $sellerView->setTemplate('./html/SellerConnections.html');
        $sellerView->setBusinesses($businesses);
        echo $sellerView->render();
    }
}