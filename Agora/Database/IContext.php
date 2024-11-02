<?php

namespace Agora\Database;

interface IContext
{
    // Method to get the database instance
    public function getDB();

    // Method to get the URI
    public function getURI(): string;

    // Method to get configuration settings
    public function getConfig(): array;

    // Method to get the current user
    public function getUser();

    // Method to set the current user
    public function setUser($user): void;

    // Method to get the session instance, returning ISession for flexibility
    public function getSession(): ISession;
}
