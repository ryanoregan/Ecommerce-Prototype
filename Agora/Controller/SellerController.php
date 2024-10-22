<?php

namespace Agora\Controller;

use Agora\Database\IContext;
use Agora\Model\ItemModel; // Use ItemModel to represent the item being sold
use Agora\View\SellerView; // Assuming you have a SellerView to render seller-related pages

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
                $sellerView->setTemplate('./html/add_item.html'); // Adjust path to the add item template
                echo $sellerView->render();
            } catch (\Exception $e) {
                echo 'Error: ' . htmlspecialchars($e->getMessage());
            }
        }
    }
}