<?php

namespace Agora\Model;

class SellerModel
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
    public function updateLocation(string $newLocation): void
    {
        $this->location = $newLocation;
    }

    // Method to add an item for sale
    public function addSaleItem(string $itemName, string $description, float $price, int $quantity): bool
    {
        // Simulate adding an item to the seller's inventory
        // In a real application, this would interact with a database to save the sale item
        echo "Adding item for sale:\n";
        echo "Item: $itemName\nDescription: $description\nPrice: $$price\nQuantity: $quantity\n";
        
        // Assuming the item is successfully added and returning true
        return true;
    }
}