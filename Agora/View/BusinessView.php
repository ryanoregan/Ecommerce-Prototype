<?php

namespace Agora\View;

class BusinessView extends AbstractView
{
    private $businessAccounts = [];

    // Method to prepare data for rendering the business view
    public function prepare(): void
    {
        // Here you might want to set any template fields if needed
        $this->setTemplateField('errorMessage', null);
    }

    // Method to set the business accounts
    public function setBusinessAccounts(array $businessAccounts): void
    {
        $this->businessAccounts = $businessAccounts;
    }

    // Method to render the sign-up view
    public function render(): string
    {
        // Prepare the data needed for rendering
        $this->prepare();

        // Call the parent render method to output the template
        return parent::render();
    }

// Method to render the view
public function renderBusinessAccounts(): string
{
    // Prepare the data needed for rendering
    $this->prepare();

    // Start outputting the structure
    $output = "<h1 class='text-3xl font-bold mb-6'>Your Business Accounts</h1>";
    if (!empty($this->businessAccounts)) {
        $output .= "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'>"; // Grid layout

        // Define the base URL for your uploads directory
        $baseURL = '/MyWebsite/Assessment%203/'; // Adjust this according to your actual directory structure

        foreach ($this->businessAccounts as $account) {
            // Access properties using getters
            $output .= "<div class='bg-white rounded-lg shadow-md p-4'>"; // Card container

            // Logo Section
            if ($account->getImagePath()) {
                // Construct the full image URL
                $fullImagePath = $baseURL . htmlspecialchars($account->getImagePath());
                $output .= "<img src='" . $fullImagePath . "' alt='Logo for " . htmlspecialchars($account->getBusinessName()) . "' style='width: 50px; height: 50px;' class='object-cover mb-4'>"; // Logo image with explicit size
            } else {
                $output .= "<div style='width: 50px; height: 50px;' class='bg-gray-200 mb-4 flex items-center justify-center'>No Logo</div>"; // Placeholder if no logo is available
            }

            $output .= "<h2 class='text-xl font-semibold'>" . htmlspecialchars($account->getBusinessName()) . "</h2>"; // Business Name
            $output .= "<p class='text-gray-600'>Details: " . htmlspecialchars($account->getLegalBusinessDetails()) . "</p>"; // Legal Business Details
            $output .= "<p class='text-gray-600'>HQ Location: " . htmlspecialchars($account->getHQLocation()) . "</p>"; // HQ Location
            $output .= "<p class='text-gray-600'>Additional Locations: " . htmlspecialchars(implode(', ', $account->getAdditionalLocations())) . "</p>"; // Additional Locations
            
            // Action buttons container
            $output .= "<div class='mt-4 flex justify-between'>"; 
            $output .= "<a href='http://localhost/MyWebsite/Assessment%203/index.php/dashboard?action=edit&businessID=" . htmlspecialchars($account->getBusinessID()) . "'class='text-blue-500 hover:underline'>Edit</a>";
            $output .= "<a href='#' class='text-red-500 hover:underline'>Delete</a>";
            $output .= "</div>"; // End of action buttons
            
            $output .= "</div>"; // End of card container
        }

        $output .= "</div>"; // End of grid layout
    } else {
        $output .= "<p>No business accounts found.</p>";
    }

    return $output;
}

public function renderEditAccounts(int $businessID): string
{
    // Prepare the data needed for rendering
    $this->prepare();

    $output = "<h1 class='text-3xl font-bold mb-6'>Edit Business Account</h1>";

    // Define the base URL for your uploads directory
    $baseURL = '/MyWebsite/Assessment%203/';

    foreach ($this->businessAccounts as $account) {
        // Check if this account's ID matches the businessID passed for editing
        if ($account->getBusinessID() == $businessID) {
            // Start form HTML for the edit view
            $output .= "<div class='bg-white shadow-md rounded-lg p-4 mb-4'>";
            $output .= "<h2 class='text-xl font-bold mb-2'>Edit " . htmlspecialchars($account->getBusinessName()) . "</h2>";
            $output .= "<form method='POST' action='/MyWebsite/Assessment%203/index.php/submitEditAccounts' enctype='multipart/form-data'>";

            // Logo Section
            if ($account->getImagePath()) {
                $fullImagePath = $baseURL . htmlspecialchars($account->getImagePath());
                $output .= "<img src='" . $fullImagePath . "' alt='Logo for " . htmlspecialchars($account->getBusinessName()) . "' style='width: 50px; height: 50px;' class='object-cover mb-4'>";
            } else {
                $output .= "<div style='width: 50px; height: 50px;' class='bg-gray-200 mb-4 flex items-center justify-center'>No Logo</div>";
            }
            // Logo upload input
            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="logoImage" class="block font-medium mb-1">Upload New Logo:</label>';
            $output .= '<input type="file" name="logoImage" accept="image/*" class="border rounded-lg p-2 w-full">';
            $output .= "</div>";

            // Form fields for editing
            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="businessName" class="block font-medium mb-1">Business Name:</label>';
            $output .= '<input type="text" name="businessName" value="' . htmlspecialchars($account->getBusinessName()) . '" required class="border rounded-lg p-2 w-full">';
            $output .= "</div>";

            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="legalBusinessDetails" class="block font-medium mb-1">Legal Business Details:</label>';
            $output .= '<textarea name="legalBusinessDetails" required class="border rounded-lg p-2 w-full">' . htmlspecialchars($account->getLegalBusinessDetails()) . '</textarea>';
            $output .= "</div>";

            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="hqLocation" class="block font-medium mb-1">HQ Location:</label>';
            $output .= '<input type="text" name="hqLocation" value="' . htmlspecialchars($account->getHQLocation()) . '" required class="border rounded-lg p-2 w-full">';
            $output .= "</div>";

            // Additional locations
            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="additionalLocations" class="block font-medium mb-1">Additional Locations:</label>';
            $output .= '<textarea name="additionalLocations" class="border rounded-lg p-2 w-full" placeholder="Separate locations with commas">' . htmlspecialchars(implode(', ', $account->getAdditionalLocations())) . '</textarea>';
            $output .= "</div>";

            // Hidden input for the business ID
            $output .= '<input type="hidden" name="businessID" value="' . htmlspecialchars($account->getBusinessID()) . '">';

            // Submit and Cancel buttons
            $output .= '<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mt-4">Save Changes</button>';
            $output .= '<button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mt-4" onclick="window.history.back()">Cancel</button>';

            $output .= '</form>';
            $output .= '</div>'; // Close card for edit form
        }
    }

    return $output;
}
}