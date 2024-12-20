<?php

namespace Agora\View;

class LoginView extends AbstractView
{
    // Method to prepare data for rendering the login view
    public function prepare(): void
    {
        // Here you might want to set any template fields if needed
        // For example, error messages for failed login attempts
        $this->setTemplateField('errorMessage', null);
    }

    // Method to render the login view
    public function render(): string
    {
        // Prepare the data needed for rendering
        $this->prepare();

        // Call the parent render method to output the template
        return parent::render();
    }
}