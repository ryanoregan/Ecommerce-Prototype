<?php

namespace Agora\Model;

class BusinessModel {
    private $businessID;
    private $businessName;
    private $legalBusinessDetails;
    private $hqLocation;
    private $additionalLocations;
    private $imagePath;

    public function __construct(int $businessID, string $businessName, string $legalBusinessDetails, string $hqLocation, array $additionalLocations, ?string $imagePath) {
        $this->businessID = $businessID;
        $this->businessName = $businessName;
        $this->legalBusinessDetails = $legalBusinessDetails;
        $this->hqLocation = $hqLocation;
        $this->additionalLocations = $additionalLocations;
        $this->imagePath = $imagePath;
    }


    // Getter for businessID
    public function getBusinessID(): int
    {
        return $this->businessID;
    }

    // Getter for businessName
    public function getBusinessName(): string
    {
        return $this->businessName;
    }

    // Getter for legalBusinessDetails
    public function getLegalBusinessDetails(): string
    {
        return $this->legalBusinessDetails;
    }

    // Getter for HQLocation (Headquarters Location)
    public function getHQLocation(): string
    {
        return $this->HQLocation;
    }

    // Getter for additionalLocations (can return an array of additional locations)
    public function getAdditionalLocations(): array
    {
        return $this->additionalLocations;
    }

    // Optional: Add a method to display additional locations in a comma-separated string
    public function getAdditionalLocationsAsString(): string
    {
        return implode(', ', $this->additionalLocations);
    }

    public function createBusiness(\Agora\Database\Database $db): bool
{
    // SQL query to insert a new business
    $sql = "INSERT INTO Business (BusinessName, LegalBusinessDetails, HQLocation, AdditionalLocations, ImagePath) VALUES (?, ?, ?, ?, ?)";
    
    // Prepare parameters for binding
    $fields = [
        $this->businessName,
        $this->legalBusinessDetails,
        $this->hqLocation,
        $this->additionalLocations,
        $this->imagePath
    ];

    // Debugging: Log the SQL and parameters
    error_log("Executing SQL: $sql");
    error_log("Parameters: businessName={$this->businessName}, legalBusinessDetails={$this->legalBusinessDetails}, hqLocation={$this->hqLocation}, additionalLocations={$this->additionalLocations}, imagePath={$this->imagePath}");
    
    // Execute the prepared statement
    if (!$db->executePrepared($sql, $fields)) {
        error_log("Business creation failed for business: {$this->businessName}");
        return false;
    }

    return true; // Return true if the business was created successfully
}
}