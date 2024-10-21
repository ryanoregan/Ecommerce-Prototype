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
    public function __construct(int $itemID, string $itemName, string $description, float $price, int $sellerID)
    {
        $this->itemID = $itemID;
        $this->itemName = $itemName;
        $this->description = $description;
        $this->price = $price;
        $this->sellerID = $sellerID;
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
}