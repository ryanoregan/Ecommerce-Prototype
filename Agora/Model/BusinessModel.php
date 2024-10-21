<?php

namespace Agora\Model;

class BusinessModel
{
    private int $businessID;
    private string $businessName;
    private string $legalBusinessDetails;
    private string $HQLocation;
    private array $additionalLocations;  // Storing multiple locations as an array

    // Constructor to initialize the business data
    public function __construct(
        int $businessID,
        string $businessName,
        string $legalBusinessDetails,
        string $HQLocation,
        array $additionalLocations = []
    ) {
        $this->businessID = $businessID;
        $this->businessName = $businessName;
        $this->legalBusinessDetails = $legalBusinessDetails;
        $this->HQLocation = $HQLocation;
        $this->additionalLocations = $additionalLocations;
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
}