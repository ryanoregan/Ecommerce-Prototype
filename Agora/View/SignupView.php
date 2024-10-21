<?php

namespace Agora\View;

class SignUpView extends AbstractView
{
    // Optional: You can define properties specific to the SignUpView if needed

    // Method to prepare data for rendering the sign-up view
    public function prepare(): void
    {
        // Here you might want to set any template fields if needed
        // For example, error messages for failed sign-up attempts
        $this->setTemplateField('errorMessage', null);
    }

    // Method to render the sign-up view
    public function render(): string
    {
        // Prepare the data needed for rendering
        $this->prepare();

        // Call the parent render method to output the template
        return parent::render();
    }
}