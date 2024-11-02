<?php

namespace Agora\Database;

interface ISession
{
    // Method to get a value from the session by key
    public function get(string $key): mixed;

    // Method to set a value in the session by key
    public function set(string $key, $value): void;

    // Method to check if a key is set in the session
    public function isKeySet(string $key): bool;

    // Method to unset a key in the session
    public function unsetKey(string $key): void;

    // Method to change the current session context
    public function changeContext(string $context): void;

    // Method to clear the entire session
    public function clear(): void;
}