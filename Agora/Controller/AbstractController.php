<?php

namespace Agora\Controller;

use Agora\Database\IContext;

abstract class AbstractController
{
    private $context; // The context instance
    private $model;   // The model associated with this controller

    // Constructor to initialize the controller with a context
    public function __construct(IContext $context)
    {
        $this->context = $context;
                // Ensure the user is set in the context from the session
                $loggedInUser = $this->context->getSession()->get('loggedInUser');
                if ($loggedInUser !== null) {
                    // Set the user in the context
                    $this->context->setUser($loggedInUser);
                }
    }

    // Protected method to get the context
    protected function getContext(): IContext
    {
        return $this->context;
    }

    // Protected method to get the database from the context
    protected function getDB()
    {
        return $this->context->getDB();
    }

    // Protected method to get the URI from the context
    protected function getURI()
    {
        return $this->context->getURI();
    }

    // Protected method to get the configuration from the context
    protected function getConfig()
    {
        return $this->context->getConfig();
    }

    // Public method to process the request (to be implemented in subclasses)
    public function process()
    {
        // Default implementation can be empty; subclasses should provide specific logic
        throw new \Exception("Process method not implemented in subclass.");
    }

    // Protected method to get the view (to be implemented in subclasses)
    protected function getView()
    {
        // Default implementation can be empty; subclasses should provide specific logic
        throw new \Exception("getView method not implemented in subclass.");
    }

    // Public method to get the model
    public function getModel()
    {
        return $this->model;
    }

    // Public method to set the model
    public function setModel($model): void
    {
        $this->model = $model;
    }
}