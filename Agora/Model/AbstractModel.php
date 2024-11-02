<?php

namespace Agora\Model;

use Agora\Database\IDatabase;

abstract class AbstractModel
{
    protected $db;
    protected $originalData = [];  // Stores the original data when the model is loaded
    protected $currentData = [];   // Stores the current data (after modifications)

    public function __construct(IDatabase $db, array $data = [])
    {
        $this->db = $db;
        $this->originalData = $data;
        $this->currentData = $data; // At first, current data is the same as original data
    }

    // Returns the current database instance
    public function getDB()
    {
        return $this->db;
    }

    // Detect if any fields have been modified compared to the original data
    public function hasChanges()
    {
        return $this->originalData !== $this->currentData;
    }

    // Set a value in the current data
    public function setAttribute($key, $value)
    {
        $this->currentData[$key] = $value;
    }

    // Save the current state of the model to the database
    public function save()
    {
        if ($this->hasChanges()) {
            // Logic to save the currentData to the database.
            // You might call $this->db->update or $this->db->insert depending on the logic.
            echo "Saving changes to the database...\n";

            // After saving, the current data is now considered the original
            $this->originalData = $this->currentData;
        } else {
            echo "No changes detected.\n";
        }
    }
}