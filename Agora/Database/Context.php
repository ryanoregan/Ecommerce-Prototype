<?php
namespace Agora\Database;

class Context implements IContext
{
    private ?IDatabase $db;       // Specify type for the database instance
    private string $uri;          // Specify type for the URI
    private array $config;        // Specify type for the configuration settings
    private Session $session;     // Specify type for the session data
    private mixed $user;          // Specify type for the current user, allowing null

    // Constructor to initialize the properties
    public function __construct(?IDatabase $db, string $uri, array $config, Session $session)
    {
        $this->db = $db;
        $this->uri = $uri;
        $this->config = $config;  // Keep the config as it is for now
        $this->session = $session;
        $this->user = null; // Initialize user as null
    }

    // Method to get the database instance
    public function getDB(): ?IDatabase
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
    public function getUser(): mixed
    {
        return $this->user;
    }

    // Method to set the current user
    public function setUser($user): void
    {
        $this->user = $user;
    }

    // Method to get the session instance
    public function getSession(): Session
    {
        return $this->session;
    }

    // Method to create a context from a configuration file
    public function createFromConfigFile($configFile): void
    {
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
