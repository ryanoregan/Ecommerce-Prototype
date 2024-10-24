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

    public function setProfile(array $Profile)
    {
        $this->Profile = $Profile['user'];
    }

    // Method to render the buyer's view
    public function render(): string
    {
        // Prepare the data needed for rendering
        $this->prepare();

        // Call the parent render method to output the template
        return parent::render();
    }

    public function renderProfile(): string
    {
        $output = '';
    
        // Assuming $this->Profile is an array of UserModel instances
        foreach ($this->Profile as $user) { // Use $user to refer to each instance
            $output .= "<div class='bg-white shadow-md rounded-lg p-4 mb-4'>";
            $output .= "<h2 class='text-xl font-bold mb-2'>Profile Information</h2>";
            $output .= "<div class='grid grid-cols-1 md:grid-cols-2 gap-4'>"; // Responsive grid layout
    
            $output .= "<div class='p-4 border rounded-lg'><strong>User ID:</strong> " . htmlspecialchars($user->getUserID() ?? 'No User ID') . "</div>";
            $output .= "<div class='p-4 border rounded-lg'><strong>Username:</strong> " . htmlspecialchars($user->getUserName() ?? 'No Username') . "</div>";
            $output .= "<div class='p-4 border rounded-lg'><strong>Email:</strong> " . htmlspecialchars($user->getEmail() ?? 'No Email') . "</div>";
            $output .= "<div class='p-4 border rounded-lg'><strong>Role:</strong> " . htmlspecialchars($user->getRole() ?? 'No Role') . "</div>";
    
            $output .= "</div>"; // Close grid
            $output .= "<div class='flex justify-end mt-4'>";
            $output .= "<div class='flex justify-end mt-4'>";

            $output .= "</div>"; // Close flex
            $output .= "</div>"; // Close flex
            $output .= "</div>"; // Close card
        }
    
        return $output; // Return the accumulated output for rendering
    }
}