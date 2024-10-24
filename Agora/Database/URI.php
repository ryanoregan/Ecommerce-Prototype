<?php

namespace Agora\Database;

class URI implements IURI
{
    private string $site;       // The base site URL
    public array $parts;       // Array to store parts of the URI

    // Constructor to initialize the URI components
    public function __construct(string $site, array $parts = [])
    {
        $this->site = $site;
        $this->parts = $parts;
    }

    // Method to get the site information
    public function getSite(): string
    {
        return $this->site;
    }

    // Method to get a specific part of the URI
    public function getPart(): string
    {
        return !empty($this->parts) ? $this->parts[0] : ''; // Return the first part or empty string
    }

    // Method to get the ID associated with the URI
    public function getID(): int
    {
        // Assuming the last part is the ID; adapt as needed
        return !empty($this->parts) ? (int) end($this->parts) : 0; // Return the last part as ID or 0
    }

    // Method to get the raw URI as a string
    public function getRawUri(): string
    {
        return  implode('/', $this->parts);
    }

    // Method to get the remaining parts of the URI
    public function getRemainingParts(): array
    {
        return array_slice($this->parts, 1); // Return all parts except the first one
    }

    // Method to prepend a part to the URI
    public function prependParts(string $part): void
    {
        array_unshift($this->parts, $part); // Prepend the new part to the array
    }

    // Method to create a URI from the current request
    public function createFromRequest(): void
    {
        // Assuming you want to use the current request's URI
        $this->site = $_SERVER['HTTP_HOST'];
        $requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $this->parts = $requestUri;
    }
}