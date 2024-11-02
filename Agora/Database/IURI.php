<?php

namespace Agora\Database;

interface IURI
{
    // Method to get the site information
    public function getSite(): string;

    // Method to get a specific part of the URI
    public function getPart(): string;

    // Method to get the ID associated with the URI
    public function getID(): int;

    // Method to get the raw URI as a string
    public function getRawUri(): string;
    
    // Method to prepend a part to the URI
    public function prependParts(string $part): void;

    // Method to create a URI from the current request
    public function createFromRequest(): void;
}
