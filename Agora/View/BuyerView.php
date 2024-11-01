<?php

namespace Agora\View;

class BuyerView extends AbstractView
{
    private $Profile;
    private array $items = [];
    private array $businesses = [];



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

    public function setBusinesses(array $businesses)
    {
        $this->businesses = $businesses;
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
        $output = '<div class="container mx-auto px-4 mt-8">'; // Centered container with padding
        $output .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">'; // Responsive grid
    
        foreach ($this->items as $item) {
            $output .= "<div class='bg-white shadow-lg rounded-lg overflow-hidden transition-transform transform hover:scale-105 hover:shadow-xl'>"; // Card hover effect
            $output .= "<div class='relative'>"; // Container for image
    
            // Define the base URL for your uploads directory
            $baseURL = '/MyWebsite/Assessment%203/';
    
            // Display image if available
            if ($item->getImagePath()) {
                // Construct the full image URL
                $fullImagePath = $baseURL . htmlspecialchars($item->getImagePath());
    
                $output .= "<img src='" . $fullImagePath . "' alt='Item Image' class='w-full h-48 object-cover'>"; // Image styling
            } else {
                $output .= "<div class='h-48 bg-gray-200 flex items-center justify-center'><strong>No Image Available</strong></div>"; // Placeholder for no image
            }
    
            $output .= "</div>"; // Close relative container
    
            // Display item name and price with flexbox layout
            $output .= "<div class='p-4'>";
            $output .= "<h2 class='text-lg font-semibold text-gray-800'>" . htmlspecialchars($item->getItemName() ?? 'No Item Name') . "</h2>"; // Item name
            $output .= "<p class='text-xl font-bold text-green-600'>" . htmlspecialchars('$' . $item->getPrice() ?? 'No Price') . "</p>"; // Price
            $output .= "<p class='mt-2 text-gray-600 text-sm'>" . htmlspecialchars($item->getDescription() ?? 'No Description') . "</p>"; // Description
    
            // Add a View button that links to the details page
            $itemID = htmlspecialchars($item->getItemID());
            $output .= "<div class='flex justify-end mt-4'>";
            $output .= "<a href='?action=viewItem&itemID=" . $itemID . "' class='bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-200'>View</a>";
            $output .= "</div>"; // Close flex
            $output .= "</div>"; // Close padding container
            $output .= "</div>"; // Close card
        }
    
        $output .= '</div>'; // Close the grid
        $output .= '</div>'; // Close the container
    
        return $output; // Return the accumulated output for rendering
    }

    public function renderItemDetail()
    {
        $output = "<div class='container mx-auto px-4 mt-8'>"; // Centered container with padding
        $output .= "<div class='bg-white shadow-lg rounded-lg p-6 flex flex-col items-center'>"; // Card with flexbox
    
        // Define the base URL for your uploads directory
        $baseURL = '/MyWebsite/Assessment%203/';
    
        // Fetch the first item (assuming this method is called for a single item detail)
        $item = $this->items[0];
    
        // Set fixed dimensions for the image
        $imageWidth = '300px'; // Set desired width
        $imageHeight = '300px'; // Set desired height
    
        // Display image if available
        if ($item->getImagePath()) {
            // Construct the full image URL
            $fullImagePath = $baseURL . htmlspecialchars($item->getImagePath());
            $output .= "<div class='w-full h-80 flex items-center justify-center mb-4'>"; // Container for image
            $output .= "<img src='" . $fullImagePath . "' alt='Item Image' class='w-full h-full object-cover rounded-lg shadow-md' style='max-width: {$imageWidth}; max-height: {$imageHeight};'>"; // Fixed size with cover
            $output .= "</div>"; // Close image container
        } else {
            $output .= "<div class='h-80 w-full flex items-center justify-center bg-gray-200 rounded-lg mb-4'><strong>No Image Available</strong></div>"; // Placeholder for no image
        }
    
        // Content section with full width
        $output .= "<div class='w-full text-center'>"; // Full width centered text section
        $output .= "<h2 class='text-3xl font-bold text-gray-800 mb-4'>" . htmlspecialchars($item->getItemName()) . "</h2>"; // Item Name
        $output .= "<div class='text-xl text-green-600 mb-4'>$" . htmlspecialchars($item->getPrice()) . "</div>"; // Price
    
        // Description
        $output .= "<p class='text-gray-700 mb-4'>" . nl2br(htmlspecialchars($item->getDescription())) . "</p>"; // Description with margin for spacing
    
        // "Buy Now" button
        $output .= "<button onclick='buyNow()' class='mt-6 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200'>Buy Now</button>";
    
        $output .= "</div>"; // Close content section
        $output .= "</div>"; // Close card
        $output .= "</div>"; // Close container
    
        // JavaScript for "Buy Now" button action
        $output .= "
            <script>
                function buyNow() {
                    alert('Thank you for your purchase!');
                    window.location.href = window.location.href; // Reload the current page
                }
            </script>
        ";
    
        return $output; // Return the accumulated output for rendering
    }
    public function renderConnections()
    {
        // Start the output with a container for styling
        $output = '<div class="container mx-auto px-4 mt-8">';
        $output .= '<h2 class="text-2xl font-bold mb-4">Business Connections</h2>';
        $output .= '<div class="overflow-x-auto shadow-md rounded-lg">'; // Add shadow and rounded corners
        $output .= '<table class="min-w-full bg-white">'; // Set the background color of the table
        $output .= '<thead class="bg-gray-200 text-gray-600 uppercase text-sm">'; // Header styling
        $output .= '<tr>';
        $output .= '<th class="py-3 px-4 text-left">Business Name</th>';
        $output .= '<th class="py-3 px-4 text-left">Legal Details</th>';
        $output .= '<th class="py-3 px-4 text-left">HQ Location</th>';
        $output .= '<th class="py-3 px-4 text-left">Additional Locations</th>';
        $output .= '</tr></thead>';
        $output .= '<tbody class="text-gray-700">'; // Body text color
        foreach ($this->businesses as $business) {
            $output .= '<tr class="border-b hover:bg-gray-100">'; // Row with hover effect
            $output .= '<td class="py-3 px-4">' . htmlspecialchars($business['BusinessName']) . '</td>';
            $output .= '<td class="py-3 px-4">' . htmlspecialchars($business['LegalBusinessDetails']) . '</td>';
            $output .= '<td class="py-3 px-4">' . htmlspecialchars($business['HQLocation']) . '</td>';
            $output .= '<td class="py-3 px-4">' . htmlspecialchars($business['AdditionalLocations']) . '</td>';
            $output .= '</tr>';
        }
        $output .= '</tbody>';
        $output .= '</table>';
        $output .= '</div>'; // Close table wrapper
        $output .= '</div>'; // Close container
    
        echo $output; // Output the entire string
    }
}