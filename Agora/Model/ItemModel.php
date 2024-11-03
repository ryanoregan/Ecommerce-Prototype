<?php

namespace Agora\Model;

class ItemModel extends AbstractModel
{
    private int $itemID;
    private string $itemName;
    private string $description;
    private float $price;
    private int $sellerID;
    private ?string $imagePath;

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

    public function getImagePath(): ?string
    {
        return $this->imagePath;
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
    public function getItemsBySellerID($db, $sellerID)
    {
        $sql = "SELECT * FROM Items WHERE SellerID = ?";
        $fields = [$sellerID];

        // Log the SQL query and parameters
        error_log("Executing query: " . $sql . " with parameters: " . json_encode($fields));

        // Make sure to return an empty array if the execution fails
        $result = $db->queryPrepared($sql, $fields);

        // Check if $result is false
        if ($result === false) {
            error_log("Failed to retrieve items for seller ID: " . $sellerID);
            return []; // Return an empty array on failure
        }

        // Log the retrieved results
        error_log("Retrieved items: " . print_r($result, true));

        return $result; // Return the result set
    }

    public function getItemById($db, $itemID)
    {
        $sql = "SELECT * FROM Items WHERE ItemID = ?";
        $fields = [$itemID];

        $result = $db->queryPrepared($sql, $fields);

        // Check if $result is false or empty
        if ($result === false || empty($result)) {
            return null; // Return null if not found
        }
        // Log the retrieved results
        error_log("Retrieved items: " . print_r($result, true));
        return $result; // Return the first item

    }

    public function getAllItems($db)
    {
        $sql = "SELECT * FROM Items";

        // Log the SQL query
        error_log("Executing query: " . $sql);

        // Execute the query without any parameters
        $result = $db->query($sql);

        // Check if $result is false
        if ($result === false) {
            error_log("Failed to retrieve all items.");
            return []; // Return an empty array on failure
        }

        // Log the retrieved results
        error_log("Retrieved all items: " . print_r($result, true));

        return $result; // Return the result set
    }
}