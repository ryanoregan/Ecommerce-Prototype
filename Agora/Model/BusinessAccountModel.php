<?php

namespace Agora\Model;

class BusinessAccountModel
{
    private string $HQLocation;
    private string $legalBusinessDetails;

    // Constructor to initialize the HQ location and legal business details
    public function __construct(string $HQLocation, string $legalBusinessDetails)
    {
        $this->HQLocation = $HQLocation;
        $this->legalBusinessDetails = $legalBusinessDetails;
    }

    // Method to update HQ Location
    public function updateHQLocation(string $newHQLocation): void
    {
        $this->HQLocation = $newHQLocation;
    }

    // Method to update legal business details
    public function updateLegalBusinessDetails(string $newLegalDetails): void
    {
        $this->legalBusinessDetails = $newLegalDetails;
    }

    // Getter for HQ Location
    public function getHQLocation(): string
    {
        return $this->HQLocation;
    }

    // Getter for legal business details
    public function getLegalBusinessDetails(): string
    {
        return $this->legalBusinessDetails;
    }

}