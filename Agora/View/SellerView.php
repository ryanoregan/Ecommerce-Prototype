<?php

namespace Agora\View;

class SellerView extends AbstractView
{
    // Optional: You can define properties specific to the SellerView if needed

    // Method to prepare data for rendering the seller's dashboard or homepage
    public function prepare(): void
    {
        // Set any template fields or data needed for the seller's page
        // For example, seller-specific messages or data
        $this->setTemplateField('welcomeMessage', 'Welcome to your Seller Dashboard!');
        $this->setTemplateField('errorMessage', null); // Set errorMessage field, if necessary
    }

    // Method to render the seller's view
    public function render(): string
    {
        // Prepare the data needed for rendering
        $this->prepare();
        // Call the parent render method to output the template
        return parent::render();
    }
}
