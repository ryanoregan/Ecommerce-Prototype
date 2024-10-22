<?php

namespace Agora\Model;

class ItemModel
{
    private int $itemID;
    private string $itemName;
    private string $description;
    private float $price;
    private int $sellerID;

    // Constructor to initialize the properties
    public function __construct(int $itemID, string $itemName, string $description, float $price, int $sellerID, ?string $imagePath = null)
    {
        $this->itemID = $itemID;
        $this->itemName = $itemName;
        $this->description = $description;
        $this->price = $price;
        $this->sellerID = $sellerID;
        $this->imagePath = $imagePath;
    }

    // Getter for itemID
    public function getItemID(): int
    {
        return $this->itemID;
    }

    // Getter for itemName
    public function getItemName(): string
    {
        return $this->itemName;
    }

    // Getter for description
    public function getDescription(): string
    {
        return $this->description;
    }

    // Getter for price
    public function getPrice(): float
    {
        return $this->price;
    }

    // Getter for sellerID
    public function getSellerID(): int
    {
        return $this->sellerID;
    }
        // Function to create an item and insert it into the database
        public function createItem(\Agora\Database\Database $db): bool
        {
            // SQL query to insert a new item
            $sql = "INSERT INTO Items (ItemName, Description, Price, SellerID, ImagePath) VALUES (?, ?, ?, ?, ?)";
            
            // Prepare parameters for binding
            $fields = [$this->itemName, $this->description, $this->price, $this->sellerID, $this->imagePath];
        
            // Debugging: Log the SQL and parameters
            error_log("Executing SQL: $sql");
            error_log("Parameters: itemName={$this->itemName}, description={$this->description}, price={$this->price}, sellerID={$this->sellerID}, imagePath={$this->imagePath}");
            
            // Execute the prepared statement
            if (!$db->executePrepared($sql, $fields)) {
                error_log("Item creation failed for item: {$this->itemName}");
                return false;
            }
        
            return true; // Return true on successful insertion
        }
}