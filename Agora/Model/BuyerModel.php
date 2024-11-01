<?php

namespace Agora\Model;

class BuyerModel
{

    // Method to search for items by name or category
    public function searchItem(string $query): array
    {
        // Simulate searching for items in a database
        // For now, returning a static example array. In a real system, this would query a database.
        $items = [
            ['itemID' => 1, 'itemName' => 'Laptop', 'price' => 999.99],
            ['itemID' => 2, 'itemName' => 'Smartphone', 'price' => 499.99],
        ];

        // Logic to filter items based on the search query
        return array_filter($items, function ($item) use ($query) {
            return stripos($item['itemName'], $query) !== false;
        });
    }

    // Method to add an item to the buyer's cart
    public function addItem(int $itemID, int $quantity): bool
    {
        // Simulate adding an item to a cart (e.g., adding to a session or database)
        // For now, assume the item is successfully added to the cart and return true.
        echo "Item with ID $itemID and quantity $quantity added to cart.\n";
        return true;
    }

    // Method to simulate making a payment
    public function makePayment(float $amount, string $paymentMethod): bool
    {
        // Simulate the payment process (e.g., interacting with a payment gateway)
        // In a real system, this would involve API calls to process the payment.
        echo "Payment of $amount made using $paymentMethod.\n";

        // Return true if payment is successful
        return true;
    }
}