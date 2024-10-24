<?php

namespace Agora\View;

class SellerView extends AbstractView
{
    // Optional: You can define properties specific to the SellerView if needed
    private array $listings = [];
    private array $Profile = [];
    private string $location;

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

    public function setListings(array $listings)
    {
        $this->listings = $listings;
    }


    public function setProfile(array $Profile)
    {
        $this->Profile = $Profile['user'];
        $this->location = $Profile['location']; 
    }

    public function setLocation(string $location)
    {
        $this->location = $location;
    }

    public function renderListings(): string
    {
        $output = '';
        foreach ($this->listings as $listing) {
            // Get the image path and prepend '../' to it
            $imagePath = '../' . htmlspecialchars($listing->getImagePath() ?? 'default-image.jpg');
    
            // Log image path for debugging
            error_log("Image path: " . $imagePath);
    
            $output .= "<tr class='border-b'>";
            $output .= "<td class='px-4 py-2'><img src='" . $imagePath . "' alt='Item Image' class='table-image rounded-md'></td>";
            $output .= "<td class='px-4 py-2'>" . htmlspecialchars($listing->getItemName() ?? 'No Name') . "</td>";
            $output .= "<td class='px-4 py-2'>" . htmlspecialchars($listing->getDescription() ?? 'No Description') . "</td>";
            $output .= "<td class='px-4 py-2'>$" . htmlspecialchars(number_format($listing->getPrice() ?? 0, 2)) . "</td>";
            $output .= "<td class='px-4 py-2'>";
            $output .= "<button class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg'>Edit</button>";
            $output .= "<button class='bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg ml-2'>Delete</button>";
            $output .= "</td></tr>";
        }
        return $output; // Return the accumulated output for rendering
    }


    public function renderProfile(): string
    {
        $output = '';
    
        // Assuming $this->Profile is an array of UserModel instances
        foreach ($this->Profile as $user) { // Use $user to refer to each instance
            $output .= "<tr class='border-b'>";
            $output .= "<td class='px-4 py-2'>" . htmlspecialchars($user->getUserID() ?? 'No User ID') . "</td>";
            $output .= "<td class='px-4 py-2'>" . htmlspecialchars($user->getUserName() ?? 'No Username') . "</td>";
            $output .= "<td class='px-4 py-2'>" . htmlspecialchars($user->getEmail() ?? 'No Email') . "</td>";
            $output .= "<td class='px-4 py-2'>" . htmlspecialchars($user->getRole() ?? 'No Role') . "</td>";
            $output .= "<td class='px-4 py-2'>" . htmlspecialchars($this->location ?? 'No Location') . "</td>";
            $output .= "<td class='px-4 py-2'>";
            $output .= "<button class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg'>Edit</button>";
            $output .= "<button class='bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg ml-2'>Delete</button>";
            $output .= "</td></tr>";
        }
    
        return $output; // Return the accumulated output for rendering
    }
    
    
}
