<?php

namespace Agora\View;

class BuyerView extends AbstractView
{
    // Optional: You can define properties specific to the BuyerView if needed

    // Method to prepare data for rendering the buyer's dashboard or homepage
    public function prepare(): void
    {
        // Set any template fields or data needed for the buyer's page
        // For example, buyer-specific messages or data
        $this->setTemplateField('welcomeMessage', 'Welcome to your Buyer Dashboard!');
        $this->setTemplateField('errorMessage', null); // Set errorMessage field, if necessary
    }

    // Method to render the buyer's view
    public function render(): string
    {
        // Prepare the data needed for rendering
        $this->prepare();

        // Call the parent render method to output the template
        return parent::render();
    }
}