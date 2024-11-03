<?php

namespace Agora\Model;

class SellerModel extends AbstractModel
{
    private string $location;

    // Constructor to initialize the seller's location
    public function __construct(string $location)
    {
        $this->location = $location;
    }

    // Getter for location
    public function getLocation(): string
    {
        return $this->location;
    }

    // Method to update the seller's location
    public function updateLocation($db, $userID, $newLocation)
    {
        $sql = "UPDATE Sellers SET Location = ? WHERE UserID = ?";

        $fields = [$newLocation, $userID]; // Include userID in fields array

        // Log the SQL query and parameters
        error_log("Executing query: " . $sql . " with parameters: " . json_encode($fields));

        // Execute the prepared statement
        $result = $db->queryPrepared($sql, $fields);

        // Check if the query execution was successful
        if ($result === false) {
            error_log("Failed to update username for user ID: " . $userID);
            return false; // Return false on failure
        }

        // Log success message
        error_log("Successfully updated username for user ID: " . $userID);

        return true; // Return true to indicate success

    }


    public function getLocationByUserID($db, $userID)
    {
        $sql = "SELECT Location FROM Sellers WHERE UserID = ?";
        $fields = [$userID];

        $result = $db->queryPrepared($sql, $fields);

        if ($result === false || empty($result)) {
            return null; // Return null if not found
        }

        return $result[0]['Location']; // Assuming the result is an associative array
    }
}