<?php

namespace Agora\Controller;

use Agora\Database\IContext;

abstract class AbstractController
{
    private IContext $context; // The context instance

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

}