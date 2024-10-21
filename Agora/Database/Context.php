<?php

namespace Agora\Database;

class Context implements IContext
{
    private $db;      // Database instance
    private $uri;     // URI
    private $config;  // Configuration settings
    private $session; // Session data
    private $user;    // Current user

    // Constructor to initialize the properties
    public function __construct($db, string $uri, array $config, Session $session) // Make sure to use the Session type
    {
        $this->db = $db;
        $this->uri = $uri;
        $this->config = $config;
        $this->session = $session;
    }

    // Method to get the database instance
    public function getDB()
    {
        return $this->db;
    }

    // Method to get the URI
    public function getURI(): string
    {
        return $this->uri;
    }

    // Method to get configuration settings
    public function getConfig(): array
    {
        return $this->config;
    }

    // Method to get the current user
    public function getUser()
    {
        return $this->user;
    }

    // Method to set the current user
    public function setUser($user): void
    {
        $this->user = $user;
    }

    // Method to get the session instance
    public function getSession(): Session // Add this method
    {
        return $this->session;
    }

    // Method to create a context from a configuration file
    public function createFromConfigFile($configFile)
    {
        // Example logic to read from a config file and set properties
        if (file_exists($configFile)) {
            $config = parse_ini_file($configFile);
            
            // Create a new Database instance
            $this->db = new Database($config['host'], $config['user'], $config['password'], $config['database']);
            
            $this->uri = $config['uri'];
            $this->config = $config;
        } else {
            throw new \Exception("Configuration file not found: $configFile");
        }
    }
}