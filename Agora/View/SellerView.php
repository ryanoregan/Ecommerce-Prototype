<?php

namespace Agora\View;

class SellerView extends AbstractView
{
    private array $listings = [];
    private array $Profile = [];
    private string $location;
    private array $businesses = [];

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
    public function setBusinesses(array $businesses)
    {
        $this->businesses = $businesses;
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

        foreach ($this->Profile as $user) { // Use $user to refer to each instance
            $output .= "<div class='bg-white shadow-md rounded-lg p-4 mb-4'>";
            $output .= "<h2 class='text-xl font-bold mb-2'>Profile Information</h2>";
            $output .= "<div class='grid grid-cols-1 md:grid-cols-2 gap-4'>"; // Responsive grid layout

            $output .= "<div class='p-4 border rounded-lg'><strong>User ID:</strong> " . htmlspecialchars($user->getUserID() ?? 'No User ID') . "</div>";
            $output .= "<div class='p-4 border rounded-lg'><strong>Username:</strong> " . htmlspecialchars($user->getUserName() ?? 'No Username') . "</div>";
            $output .= "<div class='p-4 border rounded-lg'><strong>Email:</strong> " . htmlspecialchars($user->getEmail() ?? 'No Email') . "</div>";
            $output .= "<div class='p-4 border rounded-lg'><strong>Role:</strong> " . htmlspecialchars($user->getRole() ?? 'No Role') . "</div>";
            $output .= "<div class='p-4 border rounded-lg'><strong>Location:</strong> " . htmlspecialchars($this->location ?? 'No Location') . "</div>";

            $output .= "</div>"; // Close grid
            $output .= "<div class='flex justify-end mt-4'>";
            $output .= "<div class='flex justify-end mt-4'>";
            $output .= "<a href='http://localhost/MyWebsite/Assessment%203/index.php/profile?action=edit&userID=" . htmlspecialchars($user->getUserID()) . "' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg'>Edit</a>";
            $output .= "</div>"; // Close flex
            $output .= "</div>"; // Close flex
            $output .= "</div>"; // Close card
        }

        return $output; // Return the accumulated output for rendering
    }

    public function renderEditForm()
    {

        foreach ($this->Profile as $user) {
            // Start form HTML with a card-like layout
            $output = "<div class='bg-white shadow-md rounded-lg p-4 mb-4'>";
            $output .= "<h2 class='text-xl font-bold mb-2'>Edit Profile</h2>";
            $output .= "<form method='POST' action='/MyWebsite/Assessment%203/index.php/submitEdit'>";
            
            // Populate fields with current user data, each in a styled container
            $output .= "<div class='grid grid-cols-1 md:grid-cols-2 gap-4'>"; // Responsive grid layout

            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="username" class="block font-medium mb-1">Username:</label>';
            $output .= '<input type="text" name="username" value="' . htmlspecialchars($user->getUserName()) . '" required class="border rounded-lg p-2 w-full">';
            $output .= "</div>"; // Close container for username

            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="email" class="block font-medium mb-1">Email:</label>';
            $output .= '<input type="email" name="email" value="' . htmlspecialchars($user->getEmail()) . '" required class="border rounded-lg p-2 w-full">';
            $output .= "</div>"; // Close container for email

            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="password" class="block font-medium mb-1">Password:</label>';
            $output .= '<input type="password" name="password" placeholder="Enter new password (optional)" class="border rounded-lg p-2 w-full">';
            $output .= "</div>"; // Close container for password

            $output .= "<div class='p-4 border rounded-lg'>";
            $output .= '<label for="location" class="block font-medium mb-1">Location:</label>';
            $output .= '<input type="text" name="location" value="' . htmlspecialchars($this->location) . '" required class="border rounded-lg p-2 w-full">';
            $output .= "</div>"; // Close container for location

            // Hidden input to pass the user ID
            $output .= '<input type="hidden" name="userID" value="' . htmlspecialchars($user->getUserID()) . '">';

            $output .= "</div>"; // Close grid
            // Submit button
            $output .= '<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mt-4">Save Changes</button>';




            $output .= '</form>';
            $output .= '<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mt-4"onclick="window.history.back()">Cancel</button>';
            $output .= '</div>'; // Close card

            // Echo the form
            echo $output;
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
