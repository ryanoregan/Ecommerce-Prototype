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
                    0,
                    $itemData['ItemName'],
                    $itemData['Description'],
                    (float)$itemData['Price'],
                    0,
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
}