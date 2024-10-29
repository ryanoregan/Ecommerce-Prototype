<?php

namespace Agora\Controller;

use Agora\Database\IContext;
use Agora\Model\ItemModel;
use Agora\View\BuyerView;

class BuyerController extends AbstractController
{
    private IContext $context;

    public function __construct(IContext $context)
    {
        parent::__construct($context);
        $this->context = $context;
    }

    public function getAllItems()
    {
        try {
            // Access the database through the context
            $db = $this->context->getDB();
            if (!$db->isConnected()) {
                error_log("Database connection is not established.");
                return false;
            }

            // Fetch all items using ItemModel
            $itemModel = new ItemModel(0, '', '', 0.0, 0); // Adjust constructor as needed
            $allItemsData = $itemModel->getAllItems($db);

            // Convert fetched data into ItemModel objects
            $items = [];
            foreach ($allItemsData as $itemData) {
                $items[] = new ItemModel(
                    $itemData['ItemID'],
                    $itemData['ItemName'],
                    $itemData['Description'],
                    (float)$itemData['Price'],
                    $itemData['SellerID'],
                    $itemData['ImagePath'] ?? null
                );
            }

            // Render the items page with the fetched items
            $buyerView = new BuyerView();
            $buyerView->setTemplate('./html/Buyer.html'); // Path to buyer items template
            $buyerView->setItems($items); // Assuming BuyerView has a setItems method
            echo $buyerView->render();

        } catch (\Exception $e) {
            // Handle exceptions during item retrieval
            echo 'Error: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function viewItemDetail()
    {
        $itemID = $_GET['itemID'] ?? null;
    
        // Check if itemID is provided
        if (!$itemID) {
            echo "<p>Item ID not provided.</p>";
            return;
        }
    
        // Access the database through the context
        $db = $this->context->getDB();
        if (!$db->isConnected()) {
            error_log("Database connection is not established.");
            echo "<p>Database connection error.</p>";
            return;
        }
    
        // Fetch the item by ID
        $itemModel = new ItemModel(0, '', '', 0.0, 0);
        $itemData = $itemModel->getItemById($db, $itemID);
    
        // If item was not found
        if (!$itemData) {
            echo "<p>Item not found.</p>";
            return;
        }
    
        // Create an ItemModel instance based on fetched data
        $item = new ItemModel(
            $itemData[0]['ItemID'],
            $itemData[0]['ItemName'],
            $itemData[0]['Description'],
            (float)$itemData[0]['Price'],
            $itemData[0]['SellerID'],
            $itemData[0]['ImagePath'] ?? null
        );
    
        // Render the item detail page
        $buyerView = new BuyerView();
        $buyerView->setTemplate('./html/Buyer.html'); // Path to buyer items template
        $buyerView->setItems([$item]); // Pass as array if view expects it
        echo $buyerView->render();
    }
}