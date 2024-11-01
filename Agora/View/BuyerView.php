<?php

namespace Agora\View;

class BuyerView extends AbstractView
{
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
        $output = '';

        foreach ($this->items as $item) {
            $output .= "<div class='bg-white shadow-md rounded-lg p-4 mb-4'>";
            $output .= "<div class='grid grid-cols-1 md:grid-cols-2 gap-4'>"; // Responsive grid layout

            // Define the base URL for your uploads directory
            $baseURL = '/MyWebsite/Assessment%203/';

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
            $output .= "<div class='p-4 border rounded-lg'><span class='text-xl font-bold'>" . htmlspecialchars($item->getPrice() ?? 'No Price') . "</span></div>";
            $output .= "</div>"; // Close flex container

            // Display description in smaller font
            $output .= "<div class='p-4 border rounded-lg text-sm'>" . htmlspecialchars($item->getDescription() ?? 'No Description') . "</div>";

            // Add a View button that links to the details page
            $itemID = htmlspecialchars($item->getItemID());
            $output .= "<div class='flex justify-end mt-4'>";
            $output .= "<a href='?action=viewItem&itemID=" . $itemID . "' class='bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600'>View</a>";
            $output .= "</div>"; // Close flex

            $output .= "</div>"; // Close grid
            $output .= "</div>"; // Close card
        }

        return $output; // Return the accumulated output for rendering
    }

    public function renderItemDetail()
    {
        foreach ($this->items as $item) {
            $output = "<div class='bg-white shadow-md rounded-lg p-4 mb-4'>";
            $output .= "<div class='flex flex-col items-center'>";

            // Define the base URL for your uploads directory
            $baseURL = '/MyWebsite/Assessment%203/';

            // Display image if available
            if ($item->getImagePath()) {
                // Construct the full image URL
                $fullImagePath = $baseURL . htmlspecialchars($item->getImagePath());
                $output .= "<div class='p-4 border rounded-lg'><img src='" . $fullImagePath . "' alt='Item Image' class='w-full h-auto rounded-lg mb-2'></div>";
            } else {
                $output .= "<div class='p-4 border rounded-lg'><strong>No Image Available</strong></div>";
            }

            // Item Name
            $output .= "<h2 class='text-2xl font-bold'>" . htmlspecialchars($item->getItemName()) . "</h2>";

            // Price
            $output .= "<div class='text-xl text-green-600'>$" . htmlspecialchars($item->getPrice()) . "</div>";

            // Description
            $output .= "<p class='mt-2'>" . htmlspecialchars($item->getDescription()) . "</p>";

            // "Buy Now" button
            $output .= "<button onclick='buyNow()' class='mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600'>Buy Now</button>";

            $output .= "</div>"; // Close flex container
            $output .= "</div>"; // Close card

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
    }

    public function renderConnections()
    {


        echo '<table>';
        echo '<thead><tr><th>Business Name</th><th>Legal Details</th><th>HQ Location</th><th>Additional Locations</th></tr></thead>';
        echo '<tbody>';
        foreach ($this->businesses as $business) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($business['BusinessName']) . '</td>';
            echo '<td>' . htmlspecialchars($business['LegalBusinessDetails']) . '</td>';
            echo '<td>' . htmlspecialchars($business['HQLocation']) . '</td>';
            echo '<td>' . htmlspecialchars($business['AdditionalLocations']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
}