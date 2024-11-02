<?php

namespace Agora\Database;

class Session implements ISession
{
    private string $context;  // Current session context

    // Constructor to initialize session context and start PHP session
    public function __construct(string $context = 'default')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();  // Start the PHP session if it hasn't already been started
        }
        $this->context = $context;
    }

    // Method to get a value from the session by key
    public function get(string $key): mixed
    {
        $contextKey = $this->getContextKey($key);
        return $this->isKeySet($key) ? $_SESSION[$contextKey] : null;
    }

    // Method to set a value in the session by key
    public function set(string $key, $value): void
    {
        $contextKey = $this->getContextKey($key);
        $_SESSION[$contextKey] = $value;
    }

    // Method to check if a key is set in the session
    public function isKeySet(string $key): bool
    {
        $contextKey = $this->getContextKey($key);
        return isset($_SESSION[$contextKey]);
    }

    // Method to unset a key in the session
    public function unsetKey(string $key): void
    {
        $contextKey = $this->getContextKey($key);
        unset($_SESSION[$contextKey]);
    }

    // Method to change the current session context
    public function changeContext(string $context): void
    {
        $this->context = $context;
      
    }

    // Method to clear the entire session
    public function clear(): void
    {
        session_unset();  // Unset all session variables
        session_destroy();  // Destroy the session completely
            // Remove the session cookie from the browser
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, 
            $params["path"], $params["domain"], 
            $params["secure"], $params["httponly"]
        );
    }
    }
    // Private method to append the current context to the key
    private function getContextKey(string $key): string
    {
        return $this->context . '_' . $key;
    }
}