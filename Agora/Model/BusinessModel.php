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
        return $this->hqLocation;
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

    // Getter for imagePath
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    // Method to update business name
    public function updateBusinessName($db, $businessID, $newBusinessName)
    {
        $query = "UPDATE Business SET BusinessName = ? WHERE BusinessID = ?";
        $params = [$newBusinessName, $businessID];
        return $db->executePrepared($query, $params);
    }

    // Method to update legal business details
    public function updateLegalBusinessDetails($db, $businessID, $newLegalBusinessDetails)
    {
        $query = "UPDATE Business SET LegalBusinessDetails = ? WHERE BusinessID = ?";
        $params = [$newLegalBusinessDetails, $businessID];
        return $db->executePrepared($query, $params);
    }

    // Method to update HQ location
    public function updateHQLocation($db, $businessID, $newHQLocation)
    {
        $query = "UPDATE Business SET HQLocation = ? WHERE BusinessID = ?";
        $params = [$newHQLocation, $businessID];
        return $db->executePrepared($query, $params);
    }

    // Method to update additional locations
    public function updateAdditionalLocations($db, $businessID, $newAdditionalLocations)
    {
        $locations = implode(',', $newAdditionalLocations); // Convert array to string
        $query = "UPDATE Business SET AdditionalLocations = ? WHERE BusinessID = ?";
        $params = [$locations, $businessID];
        return $db->executePrepared($query, $params);
    }

    // Method to update image path
    public function updateImagePath($db, $businessID, $newImagePath)
    {
        $query = "UPDATE Business SET ImagePath = ? WHERE BusinessID = ?";
        $params = [$newImagePath, $businessID];
        return $db->executePrepared($query, $params);
    }

    // Method to create a new business account
    public function createBusiness(\Agora\Database\Database $db, int $userID): bool
    {
        $this->additionalLocations = is_array($this->additionalLocations) 
            ? implode(',', $this->additionalLocations) 
            : $this->additionalLocations;

        $sql = "INSERT INTO Business (BusinessName, LegalBusinessDetails, HQLocation, AdditionalLocations, ImagePath) VALUES (?, ?, ?, ?, ?)";
        $fields = [
            $this->businessName,
            $this->legalBusinessDetails,
            $this->hqLocation,
            $this->additionalLocations,
            $this->imagePath
        ];

        if (!$db->executePrepared($sql, $fields)) {
            error_log("Business creation failed for business: {$this->businessName}");
            return false;
        }

        $businessID = $db->getInsertID();
        $userRole = $db->getUserRoleByUserID($userID);

        $sqlUserBusiness = "INSERT INTO Users_Business (UserID, BusinessID, Role) VALUES (?, ?, ?)";
        $fieldsUserBusiness = [$userID, $businessID, $userRole];
        
        if (!$db->executePrepared($sqlUserBusiness, $fieldsUserBusiness)) {
            error_log("Failed to associate UserID $userID with BusinessID $businessID and Role $userRole");
            return false;
        }

        return true;
    }

    // Method to get business accounts by user ID
    public function getBusinessAccountsByUserID(\Agora\Database\Database $db, int $userID): array
    {
        $sql = "
            SELECT b.BusinessID, b.BusinessName, b.LegalBusinessDetails, b.HQLocation, b.AdditionalLocations, b.ImagePath
            FROM Business b
            INNER JOIN Users_Business ub ON b.BusinessID = ub.BusinessID
            WHERE ub.UserID = ?
        ";
        $params = [$userID];
        error_log("Executing SQL: $sql");
        error_log("Parameters: userID={$userID}");

        $result = $db->queryPrepared($sql, $params);

        if ($result === false) {
            error_log("Failed to fetch business accounts for UserID: $userID");
            return [];
        }

        return $result ?: [];
    }
}