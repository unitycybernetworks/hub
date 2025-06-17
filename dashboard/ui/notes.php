<?php
// welcome.php - Displays content based on user's group

// Start the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Get user details from session
$username = $_SESSION["username"];
$group_id = $_SESSION["group_id"]; // Retrieve group_id from session


// Get the current hour in 24-hour format
$currentHour = date('H'); // 'H' returns the hour from 00 to 23

$greeting = "";
if ($currentHour >= 5 && $currentHour < 12) {
    $greeting = "Good morning";
} elseif ($currentHour >= 12 && $currentHour < 18) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Skye Cloud Hub</title>
         <link rel="icon" type="image/jpg" href="skyecloudlogo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
<style>
    /* Custom styles not covered by Tailwind, or overrides */
    body {
        font-family: 'Inter', sans-serif; /* Using Inter font */
        background-color: skyblue;
        color: #333;
        display: flex; /* Use flexbox for overall layout */
        min-height: 100vh; /* Ensure body takes full viewport height */
    }

    .main-content-area {
        flex-grow: 1; /* Allow main content to take remaining space */
        padding: 1rem;
        background-color: skyblue; /* Match body background */
        display: flex; /* Make it a flex container */
        justify-content: space-between; /* Pushes items to the ends */
        align-items: flex-start; /* Aligns items to the top */
        position: relative;
        gap: 1rem; /* Space between the two columns */
    }

    /* Removed .container styles as it's no longer used for centering */

    h2 {
        color: black;
        margin-bottom: 25px;
        font-size: 25px;
    }

    /* Styles for the left navigation menu */
    .left-nav {
        width: 250px; /* Fixed width for the left nav */
        background-color: #f3f4f6; /* Light gray background */
        padding: 1rem;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1); /* Subtle shadow */
        display: flex;
        flex-direction: column;
        gap: 10px; /* Space between menu items */
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .left-nav .btn {
        display: block; /* Make buttons block level */
        width: 100%; /* Full width within the nav */
        text-align: left; /* Align text to the left */
        background-color: darkblue; /* Green for welcome buttons */
        color: white;
        padding: 12px 15px;
        border-radius: 8px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        font-size: 1em;
    }

    .left-nav .btn:hover {
        background-color: #0056b3; /* Darker blue on hover */
    }

    /* General button styles (retained from original) */
    .btn {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 8px; /* Rounded corners */
        cursor: pointer;
        font-size: 1.1em;
        text-decoration: none;
        transition: background-color 0.3s ease;
        margin-top: 10px;
    }

    /* Styles for dropdown containers */
    .dropdown {
        position: relative;
        width: 100%;
    }

    /* Styles for dropdown buttons */
    .dropdown-btn {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        text-align: left;
        background-color: darkblue;
        color: white;
        padding: 12px 15px;
        border-radius: 8px;
        transition: background-color 0.3s ease;
        font-size: 1em;
        cursor: pointer;
        border: none; /* Remove default button border */
    }

    .dropdown-btn:hover {
        background-color: #0056b3;
    }

    /* Styles for dropdown content (hidden by default) */
    .dropdown-content {
        display: none; /* Hidden by default */
        flex-direction: column;
        background-color: #e0e7ff; /* Lighter blue for dropdown background */
        border-radius: 8px;
        margin-top: 5px;
        padding: 5px 0;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); /* Inner shadow */
    }

    .dropdown-content a {
        color: #333; /* Darker text for links */
        padding: 10px 15px;
        text-decoration: none;
        display: block;
        text-align: left;
        transition: background-color 0.2s ease;
        border-radius: 6px; /* Slightly rounded corners for links */
        margin: 0 5px; /* Spacing for links inside dropdown */
    }

    .dropdown-content a:hover {
        background-color: #c5d2ff; /* Lighter blue on hover */
    }

    /* Common styles for individual clock boxes and issues box */
    .info-box {
        background-color: #f3f4f6;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 15px; /* Space between boxes when stacked vertically */
        text-align: left;
    }

    .clock-individual-box {
        min-width: 150px; /* Adjust as needed */
        margin-right: 15px; /* Space between individual clock boxes horizontally */
        margin-bottom: 15px; /* Space below individual clock boxes when they wrap */
    }
    .clock-individual-box:last-child {
        margin-right: 0; /* No margin on the last clock box */
    }

    .issues-box {
        min-height: 100px; /* Give it some minimum height */
    }

    /* New styles for issues box content spacing */
    .issues-box p {
        margin-bottom: 5px; /* Reduced vertical spacing between issue paragraphs */
    }
    .issues-box p:last-child {
        margin-bottom: 0; /* Remove bottom margin from the last paragraph */
    }

    /* Styles for the calendar */
    .calendar-container .p-1 {
        padding: 0.25rem; /* Tailwind p-1 */
    }
    .calendar-container .bg-blue-500 {
        background-color: #3B82F6; /* Tailwind bg-blue-500 */
    }
    .calendar-container .text-white {
        color: #FFFFFF; /* Tailwind text-white */
    }
    .calendar-container .font-bold {
        font-weight: 700; /* Tailwind font-bold */
    }
    .calendar-container .hover\:bg-gray-200:hover {
        background-color: #E5E7EB; /* Tailwind hover:bg-gray-200 */
    }
    .calendar-container .border-red-500 {
        border-color: #EF4444; /* Tailwind border-red-500 */
    }

    /* Styles for the container holding clocks and issues */
    .time-and-issues-container {
        display: flex; /* Ensure it's a flex container */
        flex-direction: column; /* Arrange children (clocks and issues) vertically */
        align-items: flex-start; /* Align children to the top-left */
        width: 100%; /* Occupy full width within flex-column-left */
        margin-top: 1rem; /* Add some top margin */
        margin-left: 1rem; /* Add some left margin */
        margin-right: 1rem; /* Add some right margin */
        gap: 0; /* Remove gap between clock container and issues box when stacked vertically */
    }

    /* Styles for the container holding only clocks */
    .clocks-container {
        display: flex; /* Ensure it's a flex container */
        flex-wrap: wrap; /* Allow clocks to wrap to the next line */
        justify-content: flex-start; /* Align clocks to the left within their container */
        align-items: flex-start; /* Align clocks to the top within their container */
        /* margin-bottom is now handled by individual clock-individual-box for vertical spacing */
    }

    /* New styles for the two main columns within main-content-area */
    .flex-column-left {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        flex-grow: 1; /* Allows it to take available space */
        max-width: calc(100% - 300px - 2rem); /* Approx width of right column + gap */
    }

    .flex-column-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end; /* Align contents (user, quote, calendar) to the right */
        width: 300px; /* Fixed width for this column, or min-width */
        flex-shrink: 0; /* Prevent shrinking */
        gap: 1rem; /* Space between elements in this column */
    }

    /* User dropdown specific styling adjustments */
    .user-dropdown-wrapper {
        width: 100%; /* Ensure it takes full width within flex-column-right */
        display: flex;
        justify-content: flex-end; /* Push the dropdown button to the right */
        margin-bottom: 1rem; /* Add some space below the dropdown */
    }

    /* Override absolute positioning for the user dropdown if it was previously set */
    .main-content-area .absolute.top-4.right-4 {
        position: static; /* Override absolute positioning */
        top: auto;
        right: auto;
        flex-direction: column; /* Ensure it's column if it was flex */
        align-items: flex-end; /* Align items to end */
        /* Remove space-y-4 class, use margin-bottom instead */
        margin-top: 0;
        margin-right: 0;
        margin-bottom: 0;
    }

    .quote-box {
        background-color: #f3f4f6;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        width: 256px; /* Set width to match calendar-container (w-64 = 16rem = 256px) */
    }

    .calendar-container, .daily-events-box {
        width: 100%; /* Ensure calendar and events take full width of flex-column-right */
    }

    /* Responsive adjustments for smaller screens */
    @media (max-width: 768px) {
        body {
            flex-direction: column; /* Stack elements vertically on small screens */
        }
        .left-nav {
            width: 100%; /* Full width for nav on small screens */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Adjust shadow */
            border-radius: 0; /* Remove rounded corners */
            padding-bottom: 0; /* No extra padding at bottom */
        }
        .main-content-area {
            flex-direction: column; /* Stack columns vertically on small screens */
            align-items: center; /* Center content */
            padding-top: 0;
            gap: 1rem;
        }
        .flex-column-left, .flex-column-right {
            width: 95%; /* Adjust width for smaller screens */
            max-width: 100%; /* Remove max-width restriction */
            margin-left: 0;
            margin-right: 0;
            align-items: center; /* Center items within columns */
        }
        .time-and-issues-container {
            width: 100%; /* Full width on small screens */
            margin-left: 0;
            margin-right: 0;
            align-items: center; /* Center contents */
        }
        .clocks-container {
            flex-direction: column; /* Stack individual clocks vertically */
            width: 100%;
            margin-bottom: 15px; /* Add margin below clocks when stacked */
        }
        .clock-individual-box {
            margin-right: 0; /* Remove horizontal margin when stacked vertically */
            margin-bottom: 10px; /* Add vertical margin between stacked clocks */
        }
        .issues-box {
            width: 100%; /* Full width for issues box */
        }
        .user-dropdown-wrapper {
            justify-content: center; /* Center dropdown on small screens */
        }
        .quote-box, .calendar-container, .daily-events-box {
            width:50px; /* Full width on small screens */
            margin-top: 1rem; /* Space between content and quote */
        }
    }
</style>
</head>
<body>
    <nav class="left-nav">
        <h3 class="text-xl font-bold mb-4 text-gray-700">Navigation Menu</h3>
        <?php if ($group_id == 1): // Standard User Group ?>
            <a href="skye-cloud-KB.php" target="_blank" class="btn">Skye Cloud Knowledge Base</a>
            <a href="password-generator.php" target="_blank" class="btn">Password Generator</a>


            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('statusesDropdown')">
                    Statuses
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="statusesDropdown" class="dropdown-content">
                    <a href="https://status.skye-cloud.com" target="_blank">Skye Cloud Status</a>
                    <a href="https://azure.status.microsoft/" target="_blank">Microsoft Azure Status</a>
                    <a href="https://status.office.com/" target="_blank">Microsoft 365 Status</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('adminSupportDropdown')">
                    Admin Support
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="adminSupportDropdown" class="dropdown-content">
                    <a href="https://admin.microsoft.com/" target="_blank">Microsoft 365 Admin</a>
                    <a href="https://portal.azure.com/" target="_blank">Azure Portal</a>
                    <a href="https://admin-66f9cd30.duosecurity.com/login?next=%2F" target="_blank">Duo Admin</a>
                    <a href="https://client.securemail.management/" target="_blank">Mail Assure</a>

                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('autotaskDropdown')">
                    Autotask
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="autotaskDropdown" class="dropdown-content">
                    <a href="https://ww4.autotask.net/Mvc/Framework/Authentication.mvc/Authenticate?ReturnUrl=%2fMvc%2fFramework%2fNavigation.mvc%2fLanding" target="_blank">Autotask Login</a>
                    <a href="https://ww4.autotask.net/AutotaskOnyx/LandingPage?view=primary-standard-ticket-widget-drilldown&view-data=eyJ1cmwiOiJodHRwczovL3d3NC5hdXRvdask.net/Mvc/ServiceDesk/TicketGridWidgetDrilldown.mvc/PrimaryStandardDrilldown?ContentId=7221&ContentTypeId=2&HasPrimaryGroupByValue=false&HasSecondaryGroupByValue=false&isPopOut=false" target="_blank">Autotask Custom Responses</a>
                </div>
            </div>

        <?php elseif ($group_id == 2): // Senior Engineering Group ?>
            <a href="skye-cloud-KB.php" target="_blank" class="btn">Skye Cloud Knowledge Base</a>
            <a href="password-generator.php" target="_blank" class="btn">Password Generator</a>
            <a href="https://fgt-maid.corp.skye-cloud.com:444/ng" target="_blank" class="btn">Maidstone DC FW</a>
            <a href="firewall-access.php" target="_blank" class="btn">Firewall Hub</a>


            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('statusesDropdown')">
                    Statuses
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="statusesDropdown" class="dropdown-content">
                    <a href="https://status.skye-cloud.com" target="_blank">Skye Cloud Status</a>
                    <a href="https://azure.status.microsoft/" target="_blank">Microsoft Azure Status</a>
                    <a href="https://status.office.com/" target="_blank">Microsoft 365 Status</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('adminSupportDropdown')">
                    Admin Support
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="adminSupportDropdown" class="dropdown-content">
                    <a href="https://admin.microsoft.com/" target="_blank">Microsoft 365 Admin</a>
                    <a href="https://portal.azure.com/" target="_blank">Azure Portal</a>
                    <a href="https://admin-66f9cd30.duosecurity.com/login?next=%2F" target="_blank">Duo Admin</a>
                    <a href="https://client.securemail.management/" target="_blank">Mail Assure</a>

                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('autotaskDropdown')">
                    Autotask
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="autotaskDropdown" class="dropdown-content">
                    <a href="https://ww4.autotask.net/Mvc/Framework/Authentication.mvc/Authenticate?ReturnUrl=%2fMvc%2fFramework%2fNavigation.mvc%2fLanding" target="_blank">Autotask Login</a>
                    <a href="https://ww4.autotask.net/AutotaskOnyx/LandingPage?view=primary-standard-ticket-widget-drilldown&view-data=eyJ1cmwiOiJodHRwczovL3d3NC5hdXRvdask.net/Mvc/ServiceDesk/TicketGridWidgetDrilldown.mvc/PrimaryStandardDrilldown?ContentId=7221&ContentTypeId=2&HasPrimaryGroupByValue=false&HasSecondaryGroupByValue=false&isPopOut=false" target="_blank">Autotask Custom Responses</a>
                </div>
            </div>

        <?php elseif ($group_id == 4): // Cyber Security ?>
            <a href="skye-cloud-KB.php" target="_blank" class="btn">Skye Cloud Knowledge Base</a>
            <a href="password-generator.php" target="_blank" class="btn">Password Generator</a>
            <a href="https://eu02.protect.eset.com/era/webconsole/#id=CLIENTS" target="_blank" class="btn">ESET Portal</a>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('statusesDropdown')">
                    Statuses
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="statusesDropdown" class="dropdown-content">
                    <a href="https://status.skye-cloud.com" target="_blank">Skye Cloud Status</a>
                    <a href="https://azure.status.microsoft/" target="_blank">Microsoft Azure Status</a>
                    <a href="https://status.office.com/" target="_blank">Microsoft 365 Status</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('adminSupportDropdown')">
                    Admin Support
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="adminSupportDropdown" class="dropdown-content">
                    <a href="https://admin.microsoft.com/" target="_blank">Microsoft 365 Admin</a>
                    <a href="https://portal.azure.com/" target="_blank">Azure Portal</a>
                    <a href="https://admin-66f9cd30.duosecurity.com/login?next=%2F" target="_blank">Duo Admin</a>
                    <a href="https://client.securemail.management/" target="_blank">Mail Assure</a>

                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('autotaskDropdown')">
                    Autotask
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="autotaskDropdown" class="dropdown-content">
                    <a href="https://ww4.autotask.net/Mvc/Framework/Authentication.mvc/Authenticate?ReturnUrl=%2fMvc%2fFramework%2fNavigation.mvc%2fLanding" target="_blank">Autotask Login</a>
                    <a href="https://ww4.autotask.net/AutotaskOnyx/LandingPage?view=primary-standard-ticket-widget-drilldown&view-data=eyJ1cmwiOiJodHRwczovL3d3NC5hdXRvdask.net/Mvc/ServiceDesk/TicketGridWidgetDrilldown.mvc/PrimaryStandardDrilldown?ContentId=7221&ContentTypeId=2&HasPrimaryGroupByValue=false&HasSecondaryGroupByValue=Value=false&isPopOut=false" target="_blank">Autotask Custom Responses</a>
                </div>
            </div>

        <?php elseif ($group_id == 3): // Team Leader Group ?>
            <a href="skye-cloud-KB.php"  target="_blank" class="btn">Skye Cloud Knowledge Base</a>
            <a href="password-generator.php" target="_blank" class="btn">Password Generator</a>
            <a href="#"  target="_blank" class="btn">Daily Reports</a>


            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('statusesDropdown')">
                    Statuses
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="statusesDropdown" class="dropdown-content">
                    <a href="https://status.skye-cloud.com" target="_blank">Skye Cloud Status</a>
                    <a href="https://azure.status.microsoft/" target="_blank">Microsoft Azure Status</a>
                    <a href="https://status.office.com/" target="_blank">Microsoft 365 Status</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('adminSupportDropdown')">
                    Admin Support
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="adminSupportDropdown" class="dropdown-content">
                    <a href="https://admin.microsoft.com/" target="_blank">Microsoft 365 Admin</a>
                    <a href="https://portal.azure.com/" target="_blank">Azure Portal</a>
                    <a href="https://admin-66f9cd30.duosecurity.com/login?next=%2F" target="_blank">Duo Admin</a>
                    <a href="https://client.securemail.management/" target="_blank">Mail Assure</a>

                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('autotaskDropdown')">
                    Autotask
                    <svg class="w-4 h-4 ml-2 transform rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="autotaskDropdown" class="dropdown-content">
                    <a href="https://ww4.autotask.net/Mvc/Framework/Authentication.mvc/Authenticate?ReturnUrl=%2fMvc%2fFramework%2fNavigation.mvc%2fLanding" target="_blank">Autotask Login</a>
                    <a href="https://ww4.autotask.net/AutotaskOnyx/LandingPage?view=primary-standard-ticket-widget-drilldown&view-data=eyJ1cmwiOiJodHRwczovL3d3NC5hdXRvdask.net/Mvc/ServiceDesk/TicketGridWidgetDrilldown.mvc/PrimaryStandardDrilldown?ContentId=7221&ContentTypeId=2&HasPrimaryGroupByValue=false&HasSecondaryGroupByValue=false&isPopOut=false" target="_blank">Autotask Custom Responses</a>
                </div>
            </div>

        <?php else: // Fallback for any other group_id ?>
            <p class="text-gray-600">Your group access is not defined for this page.</p>
        <?php endif; ?>
    </nav>

    <div class="main-content-area">
        <div class="flex-column-left">
            <div class="time-and-issues-container">
                <div class="clocks-container" id="clocks-container">
                    <div class="info-box clock-individual-box">
                        <h4 class="font-semibold text-gray-700">UK</h4>
                        <div id="gmtClock" class="text-xl font-bold text-blue-800 mt-2"></div>
                    </div>
                    <div class="info-box clock-individual-box">
                        <h4 class="font-semibold text-gray-700">Germany</h4>
                        <div id="germanyClock" class="text-xl font-bold text-blue-800 mt-2"></div>
                    </div>
                    <div class="info-box clock-individual-box">
                        <h4 class="font-semibold text-gray-700">US (EST)</h4>
                        <div id="estClock" class="text-xl font-bold text-blue-800 mt-2"></div>
                    </div>
                    <div class="info-box clock-individual-box">
                        <h4 class="font-semibold text-gray-700">Canada (EST)</h4>
                        <div id="canadaEstClock" class="text-xl font-bold text-blue-800 mt-2"></div>
                    </div>
                    <div class="info-box clock-individual-box">
                        <h4 class="font-semibold text-gray-700">India</h4>
                        <div id="indiaClock" class="text-xl font-bold text-blue-800 mt-2"></div>
                    </div>
                    <div class="info-box clock-individual-box">
                        <h4 class="font-semibold text-gray-700">Lagos</h4>
                        <div id="lagosClock" class="text-xl font-bold text-blue-800 mt-2"></div>
                    </div>
                    <div class="info-box clock-individual-box">
                        <h4 class="font-semibold text-gray-700">Jamaica</h4>
                        <div id="jamaicaClock" class="text-xl font-bold text-blue-800 mt-2"></div>
                    </div>
                </div>

                <div class="issues-box info-box" id="issues-box">
                    <h4 class="font-semibold text-red-700 mb-2">Ongoing Infrastructure Issues</h4>
                    <div id="infrastructureIssues" class="text-sm text-gray-800">
                        Loading ongoing issues...
                    </div>
                </div>

                <div class="info-box" id="newsFeedBox"> <h4 class="font-semibold text-gray-700 mb-2">News Feed</h4>
                    <iframe width="1000" height="500"  src="https://rss.app/embed/v1/carousel/P9pT6f26MzmqpbNX" frameborder="0"></iframe>
                </div>
            </div>
        </div>

        <div class="flex-column-right">
            <div class="user-dropdown-wrapper">
                <div class="group relative">
                    <button id="userDropdownButton" class="flex items-center space-x-2 bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-300">
                        <span><?php echo htmlspecialchars($username); ?></span>
                        <svg class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="userDropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100 origin-top-right">
                        <a href="logout.php" id="logoutButton" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 rounded-md transition duration-200">Logout</a>
                    </div>
                </div>
            </div>

            <div class="quote-box" id="quote-box">
                <script type="text/javascript" src="https://www.brainyquote.com/link/quotebr.js"></script>
            </div>

            <div class="calendar-container bg-white p-4 rounded-lg shadow-md w-64 text-center">
                <div class="flex justify-between items-center mb-2">
                    <button id="prevMonth" class="text-gray-600 hover:text-gray-800 focus:outline-none">&lt;</button>
                    <h3 id="currentMonthYear" class="font-bold text-lg text-gray-800"></h3>
                    <button id="nextMonth" class="text-gray-600 hover:text-gray-800 focus:outline-none">&gt;</button>
                </div>
                <div class="grid grid-cols-7 gap-1 text-sm font-semibold text-gray-500">
                    <span>Sun</span>
                    <span>Mon</span>
                    <span>Tue</span>
                    <span>Wed</span>
                    <span>Thu</span>
                    <span>Fri</span>
                    <span>Sat</span>
                </div>
                <div id="calendarGrid" class="grid grid-cols-7 gap-1 mt-2 text-gray-700">
                    </div>
            </div>

            <div class="daily-events-box bg-white p-4 rounded-lg shadow-md w-64 text-left">
                <h4 class="font-semibold text-gray-700 mb-2">Today's Events</h4>
                <div id="todayEvents" class="text-sm text-gray-800">
                    No events for today.
                </div>
                <div class="mt-4 flex flex-col space-y-2">
                    <a href="https://app.brighthr.com/absence?source=dashboard" target="_blank" class="btn bg-blue-500 hover:bg-blue-600 text-white w-full py-2 rounded-md">Request Time Off</a>
                     <a href="https://app.brighthr.com/e-learning/view/assigned-to-me" target="_blank" class="btn bg-blue-500 hover:bg-blue-600 text-white w-full py-2 rounded-md">E-Learning</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Function to toggle dropdown visibility
        function toggleDropdown(id) {
            const dropdownContent = document.getElementById(id);
            const dropdownButton = dropdownContent.previousElementSibling; // Get the button associated with this dropdown
            const svgIcon = dropdownButton.querySelector('svg');

            if (dropdownContent.style.display === "flex") {
                dropdownContent.style.display = "none";
                svgIcon.classList.remove('rotate-180'); // Rotate arrow back
            } else {
                // Close all other dropdowns before opening the current one
                document.querySelectorAll('.dropdown-content').forEach(content => {
                    if (content.id !== id) {
                        content.style.display = "none";
                        content.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
                    }
                });
                dropdownContent.style.display = "flex";
                svgIcon.classList.add('rotate-180'); // Rotate arrow
            }
        }

        // Function to update multiple timezone clocks
        function updateTimezoneClocks() {
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };

            // GMT
            const nowGMT = new Date();
            document.getElementById('gmtClock').textContent = nowGMT.toLocaleTimeString('en-GB', { timeZone: 'Europe/London', ...options });

            // Germany (Central European Time - CET/CEST)
            const nowGermany = new Date();
            document.getElementById('germanyClock').textContent = nowGermany.toLocaleTimeString('en-GB', { timeZone: 'Europe/Berlin', ...options });

            // US (Eastern Standard Time - EST)
            const nowEST = new Date();
            document.getElementById('estClock').textContent = nowEST.toLocaleTimeString('en-US', { timeZone: 'America/New_York', ...options });

            // Canada (Eastern Standard Time - EST - same as US EST for practical purposes)
            const nowCanadaEST = new Date();
            document.getElementById('canadaEstClock').textContent = nowCanadaEST.toLocaleTimeString('en-US', { timeZone: 'America/Toronto', ...options });

            // India (Indian Standard Time - IST)
            const nowIndia = new Date();
            document.getElementById('indiaClock').textContent = nowIndia.toLocaleTimeString('en-GB', { timeZone: 'Asia/Kolkata', ...options });

            // Lagos (West Africa Time - WAT)
            const nowLagos = new Date();
            document.getElementById('lagosClock').textContent = nowLagos.toLocaleTimeString('en-GB', { timeZone: 'Africa/Lagos', ...options });

            // Jamaica (Eastern Standard Time - EST - No DST)
            const nowJamaica = new Date();
            document.getElementById('jamaicaClock').textContent = nowJamaica.toLocaleTimeString('en-US', { timeZone: 'America/Jamaica', ...options });
        }


        // Function to fetch and update infrastructure issues from the server
        function updateInfrastructureIssues() {
            fetch('get_issues.php') // Fetch data from the new get_issues.php endpoint
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.text();
                })
                .then(data => {
                    const issuesBox = document.getElementById('infrastructureIssues');
                    if (data.trim() !== '') {
                        // Assuming each issue is on a new line in issues.txt
                        issuesBox.innerHTML = data.split('\n')
                                                 .filter(line => line.trim() !== '') // Filter out empty lines
                                                 .map(issue => `<p>${issue}</p>`)
                                                 .join('');
                    } else {
                        issuesBox.innerHTML = 'No ongoing issues reported.';
                    }
                })
                .catch(error => {
                    console.error('Error fetching infrastructure issues:', error);
                    document.getElementById('infrastructureIssues').innerHTML = '<p class="text-red-500">Error loading issues: ' + error.message + '</p>';
                });
        }

        // Calendar and Events Logic
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        const calendarGrid = document.getElementById('calendarGrid');
        const currentMonthYearElem = document.getElementById('currentMonthYear');
        const todayEventsElem = document.getElementById('todayEvents');

        // Sample events data (in a real application, this would come from a backend)
        const events = {
            // Format: 'YYYY-MM-DD': ['Event 1', 'Event 2']
            '2025-06-25': ['Pay Day'],
            '2025-07-25': ['Pay Day'],
            '2025-08-25': ['Pay Day'],
            '2025-09-25': ['Pay Day'],
            '2025-10-25': ['Pay Day'],
            '2025-11-25': ['Pay Day'],
            '2025-12-25': ['Pay Day'],
        };

        function renderCalendar() {
            calendarGrid.innerHTML = ''; // Clear previous days

            const date = new Date(currentYear, currentMonth, 1);
            const firstDayOfMonth = date.getDay(); // 0 for Sunday, 1 for Monday, etc.
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            currentMonthYearElem.textContent = new Date(currentYear, currentMonth).toLocaleString('en-US', { month: 'long', year: 'numeric' });

            // Add blank days for the start of the month to align with weekday
            for (let i = 0; i < firstDayOfMonth; i++) {
                const blankDiv = document.createElement('div');
                calendarGrid.appendChild(blankDiv);
            }

            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayDiv = document.createElement('div');
                dayDiv.textContent = day;
                dayDiv.classList.add('p-1', 'rounded', 'flex', 'items-center', 'justify-center', 'cursor-pointer');

                const today = new Date();
                const isToday = day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear();

                if (isToday) {
                    dayDiv.classList.add('bg-blue-500', 'text-white', 'font-bold');
                } else {
                    dayDiv.classList.add('hover:bg-gray-200');
                }

                // Check for events on this day
                const eventDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                if (events[eventDate]) {
                    dayDiv.classList.add('border-b-2', 'border-red-500'); // Indicate events with a red underline
                    dayDiv.title = events[eventDate].join(', '); // Show events on hover
                }

                dayDiv.addEventListener('click', () => showDayEvents(currentYear, currentMonth, day));
                calendarGrid.appendChild(dayDiv);
            }

            // Initially show events for today when calendar is rendered/re-rendered
            showDayEvents(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
        }

        function showDayEvents(year, month, day) {
            const selectedDate = new Date(year, month, day);
            const today = new Date();

            const isSelectedToday = selectedDate.getDate() === today.getDate() &&
                                   selectedDate.getMonth() === today.getMonth() &&
                                   selectedDate.getFullYear() === today.getFullYear();

            const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayEvents = events[formattedDate];

            let eventsHtml = '';
            if (isSelectedToday) {
                todayEventsElem.previousElementSibling.textContent = "Today's Events";
            } else {
                 todayEventsElem.previousElementSibling.textContent = `Events on ${selectedDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`;
            }

            if (dayEvents && dayEvents.length > 0) {
                eventsHtml = dayEvents.map(event => `<p class="mb-1">${event}</p>`).join('');
            } else {
                eventsHtml = '<p>No events for this day.</p>';
            }
            todayEventsElem.innerHTML = eventsHtml;
        }

        // Navigation for calendar
        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });

        // Function to adjust the width of the issues box to match the clocks container
        function adjustIssuesBoxWidth() {
            const clocksContainer = document.getElementById('clocks-container');
            const issuesBox = document.getElementById('issues-box');
            const newsFeedBox = document.getElementById('newsFeedBox'); // Get the new news feed box

            if (clocksContainer && issuesBox && newsFeedBox) {
                const clocksContainerWidth = clocksContainer.offsetWidth;
                issuesBox.style.width = `${clocksContainerWidth}px`;
                newsFeedBox.style.width = `${clocksContainerWidth}px`; // Apply same width to news feed box
            }
        }

        // Function to adjust the height of the quote box
        function adjustQuoteBoxHeight() {
            const calendarContainer = document.querySelector('.calendar-container');
            const dailyEventsBox = document.querySelector('.daily-events-box');
            const quoteBox = document.getElementById('quote-box');

            if (calendarContainer && dailyEventsBox && quoteBox) {
                // Assuming a fixed gap of 1rem (16px) between elements in flex-column-right
                const calendarHeight = calendarContainer.offsetHeight;
                const dailyEventsHeight = dailyEventsBox.offsetHeight;
                const totalHeight = calendarHeight + dailyEventsHeight + gapBetween;
                quoteBox.style.height = `${totalHeight}px`;
            }
        }

        // Update clock and issues every second/minute
        setInterval(updateTimezoneClocks, 1000); // Update every second
        setInterval(updateInfrastructureIssues, 1000); // Update issues every minute (changed from 1000ms to 60000ms for efficiency)

        // Initial calls to display clock, issues, calendar, and adjust heights immediately
        updateTimezoneClocks();
        updateInfrastructureIssues();
        renderCalendar();
        adjustIssuesBoxWidth();
        adjustQuoteBoxHeight();

        // Call adjustIssuesBoxWidth and adjustQuoteBoxHeight on window load and resize to ensure correct alignment
        window.addEventListener('load', () => {
            adjustIssuesBoxWidth();
            adjustQuoteBoxHeight();
        });
        window.addEventListener('resize', () => {
            adjustIssuesBoxWidth();
            adjustQuoteBoxHeight();
        });


        // Event listener for LOGOUT button
        document.getElementById("logoutButton").addEventListener("click", function(event) {
            event.preventDefault(); // Prevent default link behavior
            // Clear the history state
            window.history.pushState(null, "", "logged-out");
            window.location.replace('logout.php'); // Use replace to prevent back navigation
        });
    </script>
</body>
</html>