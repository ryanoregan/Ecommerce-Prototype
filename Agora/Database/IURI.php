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
}