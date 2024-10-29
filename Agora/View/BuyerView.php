<?php

namespace Agora\View;

class BuyerView extends AbstractView
{
    // Optional: You can define properties specific to the BuyerView if needed
    private $Profile;
    private array $items = [];


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

    public function setItems(array $items): void
    {
        $this->items = $items; // Store the items in the property
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

    public function renderAllItems(): string
    {
        $output = '';
    
        // Assuming $this->items is an array of ItemModel instances
        foreach ($this->items as $item) {
            $output .= "<div class='bg-white shadow-md rounded-lg p-4 mb-4'>";
            $output .= "<div class='grid grid-cols-1 md:grid-cols-2 gap-4'>"; // Responsive grid layout
    
            // Define the base URL for your uploads directory
            $baseURL = '/MyWebsite/Assessment%203/'; // Adjust this according to your actual directory structure
    
            // Display image if available
            if ($item->getImagePath()) {
                // Construct the full image URL
                $fullImagePath = $baseURL . htmlspecialchars($item->getImagePath());
    
                $output .= "<div class='p-4 border rounded-lg'><img src='" . $fullImagePath . "' alt='Item Image' class='w-full h-auto rounded-lg mb-2'></div>";
            } else {
                $output .= "<div class='p-4 border rounded-lg'><strong>No Image Available</strong></div>";
            }
    
            // Display item name and price with flexbox layout
            $output .= "<div class='flex justify-between items-center'>";
            $output .= "<div class='p-4 border rounded-lg'><span class='text-lg font-semibold'>" . htmlspecialchars($item->getItemName() ?? 'No Item Name') . "</span></div>";
            $output .= "<div class='p-4 border rounded-lg'><span class='text-xl font-bold'>$" . htmlspecialchars($item->getPrice() ?? 'No Price') . "</span></div>";
            $output .= "</div>"; // Close flex container
    
            // Display description in smaller font
            $output .= "<div class='p-4 border rounded-lg text-sm'>" . htmlspecialchars($item->getDescription() ?? 'No Description') . "</div>";
    
            $output .= "</div>"; // Close grid
            $output .= "</div>"; // Close card
        }
    
        return $output; // Return the accumulated output for rendering
    }
}