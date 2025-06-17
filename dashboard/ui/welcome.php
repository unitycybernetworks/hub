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

// --- PHP for Clocks, Calendar, and Today's Events (from notes.php logic) ---
// Set default timezone if not already set (important for date functions)
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Europe/London');
}

// Get current year, month, day for calendar/events
$currentYear = date('Y'); // e.g., 2025
$currentMonthName = date('F'); // e.g., June
$currentDayOfWeek = date('l'); // e.g., Wednesday
$currentDayOfMonth = date('jS'); // e.g., 11th

// Function to get time for different timezones
function getTimeForTimezone($timezoneIdentifier) {
    try {
        $dateTime = new DateTime('now', new DateTimeZone($timezoneIdentifier));
        return $dateTime->format('H:i:s'); // Changed to include seconds
    } catch (Exception $e) {
        return "N/A"; // Handle invalid timezone
    }
}

$ukTime = getTimeForTimezone('Europe/London');
$germanyTime = getTimeForTimezone('Europe/Berlin');
$usEstTime = getTimeForTimezone('America/New_York');
$canadaEstTime = getTimeForTimezone('America/Toronto');
$indiaTime = getTimeForTimezone('Asia/Kolkata');
$lagosTime = getTimeForTimezone('Africa/Lagos');


// Updated News items with links
$newsItems = [
    ["BBC News", "https://www.bbc.co.uk/news"],
    ["The Hacker News", "https://thehackernews.com/"]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Skye Cloud Hub</title>
    <link rel="stylesheet" href="welcome.css">
    <link rel="icon" type="image/jpg" href="skyecloudlogo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="logo-area">
                <img src="skyecloudlogo.jpg" alt="Skye Cloud Logo" class="header-logo">
                <div class="header-text">
                    <div>Skye Cloud Hub</div>
                    <div class="subheader-text">Internal Applications</div>
                </div>
            </div>
            <div class="user-info">
                <span class="welcome-message"><?php echo $greeting . ", " . htmlspecialchars($username) . ""; ?></span><br><br><br>
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="logout.php" class="sign-out-link">Sign Out</a>
            </div>
        </div>
        <div id="orange-line"></div>
        <div class="folder-path" id="currentFolderPath">Skye Cloud Internal Applications | Parent folder: /</div>

        <div class="content-wrapper">
            <div class="left-sidebar">
                <div class="sidebar-section">
                    <h3 onclick="toggleSidebarSection(this)"><b>Clocks</b> <i class="fas fa-angle-down toggle-icon"></i></h3>
                    <div class="sidebar-content">
                        <div class="clock-item">UK: <strong id="ukTime"><?php echo $ukTime; ?></strong></div> <br>
                        <div class="clock-item">Germany: <strong id="germanyTime"><?php echo $germanyTime; ?></strong></div> <br>
                        <div class="clock-item">US: <strong id="usEstTime"><?php echo $usEstTime; ?></strong></div><br>
                        <div class="clock-item">Canada: <strong id="canadaEstTime"><?php echo $canadaEstTime; ?></strong></div><br>
                        <div class="clock-item">India: <strong id="indiaTime"><?php echo $indiaTime; ?></strong></div><br>
                        <div class="clock-item">Lagos: <strong id="lagosTime"><?php echo $lagosTime; ?></strong></div><br>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3 onclick="toggleSidebarSection(this)"> <b>Calendar</b> <i class="fas fa-angle-down toggle-icon"></i></h3>
                    <div class="sidebar-content">
                        <div class="calendar-info">Today is: <strong><?php echo $currentDayOfWeek; ?></strong></div> <br>
                        <div class="calendar-info">Date: <strong><?php echo $currentDayOfMonth; ?> <?php echo $currentMonthName; ?>, <?php echo $currentYear; ?></strong></div>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3 onclick="toggleSidebarSection(this)"> <b>News</b> <i class="fas fa-angle-down toggle-icon"></i></h3>
                    <div class="sidebar-content">
                        <?php if (!empty($newsItems)): ?>
                            <?php foreach ($newsItems as $news): ?>
                                <div class="news-item">                                    <a href="<?php echo htmlspecialchars($news[1]); ?>" target="_blank">
                                        <?php echo htmlspecialchars($news[0]); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3 onclick="toggleSidebarSection(this)"><b>Quick Actions</b> <i class="fas fa-angle-down toggle-icon"></i></h3>
                    <div class="sidebar-content">
                        <a href="http://app.brighthr.com/absence?source=dashboard" target="_blank" class="action-button">Request Time Off</a>
                        <a href="http://app.brighthr.com/e-learning/view/assigned-to-me" target="_blank" class="action-button">E-learning</a>
                    </div>
                </div>
            </div>

            <div class="content-area welcome-content">
                <div class="application-grid" id="mainApplicationGrid">
                    <?php if ($group_id == 1): // Standard User Group ?>
                        <a href="skye-cloud-KB.php" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Knowledge Base Icon" class="app-icon">
                            <span class="app-name">Skye Cloud Knowledge Base</span>
                        </a>
                        <a href="password-generator.php" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Password Generator</span>
                        </a>
                        <a href="https://skyecloudhub.corp.skye-cloud.com/hub/crm/login/login.php" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">CRM</span>
                        </a>
                        <a href="https://account.arrowsphere.com/login" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Arrowsphere</span>
                        </a>
                        <a href="https://portal.exclaimer.com/sign-in/" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Exclaimer</span>
                        </a>
                        <a href="https://keepersecurity.eu/vault/#" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Keeper Security</span>
                        </a>
                        <button class="folder-button" onclick="showResourcesFolder()">
                            <i class="fas fa-folder app-icon" style="color: white;"></i> <span class="app-name">Resources</span>
                        </button>

                    <?php elseif ($group_id == 2): // Senior Engineering Group ?>
                        <a href="skye-cloud-KB.php" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Knowledge Base Icon" class="app-icon">
                            <span class="app-name">SC KB</span>
                        </a>
                        <a href="password-generator.php" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Password Generator</span>
                        </a>
                         <a href="https://skyecloudhub.corp.skye-cloud.com/hub/crm/login/login.php" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">CRM</span>
                        </a>
                        <a href="https://fgt-maid.corp.skye-cloud.com:444/ng" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Firewall Icon" class="app-icon">
                            <span class="app-name">DC FW</span>
                        </a>
                        <a href="firewall-hub.php" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Firewall Hub Icon" class="app-icon">
                            <span class="app-name">FW Hub</span>
                        </a>
                        <a href="https://account.arrowsphere.com/login" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Arrowsphere</span>
                        </a>
                        <a href="https://portal.exclaimer.com/sign-in/" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Exclaimer</span>
                        </a>
                        <a href="https://keepersecurity.eu/vault/#" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Keeper Security</span>
                        </a>
                        <button class="folder-button" onclick="showResourcesFolder()">
                            <i class="fas fa-folder app-icon" style="color: white;"></i>
                            <span class="app-name">Resources</span>
                        </button>

                    <?php elseif ($group_id == 4): // Cyber Security ?>
                        <a href="skye-cloud-KB.php" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Knowledge Base Icon" class="app-icon">
                            <span class="app-name">Skye Cloud Knowledge Base</span>
                        </a>
                        <a href="password-generator.php" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Password Generator</span>
                        </a>
                         <a href="https://skyecloudhub.corp.skye-cloud.com/hub/crm/login/login.php" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">CRM</span>
                        </a>
                        <a href="https://eu02.protect.eset.com/era/webconsole/#id=CLIENTS" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="ESET Portal Icon" class="app-icon">
                            <span class="app-name">ESET Portal</span>
                        </a>
                        <a href="https://account.arrowsphere.com/login" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Arrowsphere</span>
                        </a>
                        <a href="https://portal.exclaimer.com/sign-in/" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Exclaimer</span>
                        </a>
                        <a href="https://keepersecurity.eu/vault/#" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Keeper Security</span>
                        </a>
                        <button class="folder-button" onclick="showResourcesFolder()">
                            <i class="fas fa-folder app-icon" style="color: white;"></i>
                            <span class="app-name">Resources</span>
                        </button>

                    <?php elseif ($group_id == 3): // Team Leader Group ?>
                        <a href="skye-cloud-KB.php"  target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Knowledge Base Icon" class="app-icon">
                            <span class="app-name">Skye Cloud Knowledge Base</span>
                        </a>
                        <a href="password-generator.php" target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Password Generator</span>
                        </a>
                         <a href="https://skyecloudhub.corp.skye-cloud.com/hub/crm/login/login.php" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">CRM</span>
                        </a>
                        <a href="#"  target="_blank" class="app-item">
                            <img src="skyecloudlogo.jpg" alt="Daily Reports Icon" class="app-icon">
                            <span class="app-name">Daily Reports</span>
                        </a>
                        <a href="https://account.arrowsphere.com/login" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Arrowsphere</span>
                        </a>
                        <a href="https://portal.exclaimer.com/sign-in/" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Exclaimer</span>
                        </a>
                        <a href="https://keepersecurity.eu/vault/#" target="_blank" class="app-item">
                             <img src="skyecloudlogo.jpg" alt="Password Generator Icon" class="app-icon">
                            <span class="app-name">Keeper Security</span>
                        </a>
                        <button class="folder-button" onclick="showResourcesFolder()">
                            <i class="fas fa-folder app-icon" style="color: white;"></i>
                            <span class="app-name">Resources</span>
                        </button>

                    <?php else: // Fallback for any other group_id ?>
                        <p class="text-gray-600">Your group access is not defined for this page.</p>
                    <?php endif; ?>
                </div>

                <div id="resources-page" class="folder-page">
                    <h2>Resources</h2>
                    <a href="#" class="app-item" onclick="showPage('statuses-page', 'Statuses'); return false;">
                        <i class="fas fa-signal app-icon"></i>
                        <span class="app-name">Statuses</span>
                    </a>
                    <a href="#" class="app-item" onclick="showPage('admin-support-page', 'Admin Support'); return false;">
                        <i class="fas fa-headset app-icon"></i>
                        <span class="app-name">Admin Support</span>
                    </a>
                    <a href="#" class="app-item" onclick="showPage('autotask-page', 'Autotask'); return false;">
                        <i class="fas fa-ticket-alt app-icon"></i>
                        <span class="app-name">Autotask</span>
                    </a>
                    <a href="#" class="app-item" onclick="showPage('general-page', 'General'); return false;">
                        <i class="fas fa-cogs app-icon"></i>
                        <span class="app-name">General</span>
                    </a>
                    <div class="back-button-container">
                        <button class="back-button" onclick="showMainApplications()">
                            <i class="fas fa-arrow-left"></i> Back to Main Applications
                        </button>
                    </div>
                </div>

                <div id="statuses-page" class="folder-page">
                    <h2>Statuses</h2>
                    <a href="https://status.skye-cloud.com" target="_blank" class="app-item">
                        <i class="fas fa-cloud app-icon"></i>
                        <span class="app-name">Skye Cloud Status</span>
                    </a>
                    <a href="https://azure.status.microsoft/" target="_blank" class="app-item">
                        <i class="fas fa-microsoft app-icon"></i>
                        <span class="app-name">Microsoft Azure Status</span>
                    </a>
                    <a href="https://status.office.com/" target="_blank" class="app-item">
                        <i class="fas fa-envelope app-icon"></i>
                        <span class="app-name">Microsoft 365 Status</span>
                    </a>
                    <div class="back-button-container">
                        <button class="back-button" onclick="showResourcesFolder()">
                            <i class="fas fa-arrow-left"></i> Back to Resources
                        </button>
                    </div>
                </div>

                <div id="admin-support-page" class="folder-page">
                    <h2>Admin Support</h2>
                    <a href="https://admin.microsoft.com/" target="_blank" class="app-item">
                        <i class="fas fa-user-cog app-icon"></i>
                        <span class="app-name">Microsoft 365 Admin</span>
                    </a>
                    <a href="https://portal.azure.com/" target="_blank" class="app-item">
                        <i class="fas fa-cloud app-icon"></i>
                        <span class="app-name">Azure Portal</span>
                    </a>
                    <a href="https://admin-66f9cd30.duosecurity.com/login?next=%2F" target="_blank" class="app-item">
                        <i class="fas fa-shield-alt app-icon"></i>
                        <span class="app-name">Duo Admin</span>
                    </a>
                    <a href="https://client.securemail.management/" target="_blank" class="app-item">
                        <i class="fas fa-envelope-open-text app-icon"></i>
                        <span class="app-name">Mail Assure</span>
                    </a>
                    <div class="back-button-container">
                        <button class="back-button" onclick="showResourcesFolder()">
                            <i class="fas fa-arrow-left"></i> Back to Resources
                        </button>
                    </div>
                </div>

                <div id="autotask-page" class="folder-page">
                    <h2>Autotask</h2>
                    <a href="https://ww4.autotask.net/Mvc/Framework/Authentication.mvc/Authenticate?ReturnUrl=%2fMvc%2fFramework%2fNavigation.mvc%2fLanding" target="_blank" class="app-item">
                        <i class="fas fa-laptop-code app-icon"></i>
                        <span class="app-name">Autotask Login</span>
                    </a>
                    <a href="https://ww4.autotask.net/AutotaskOnyx/LandingPage?view=primary-standard-ticket-widget-drilldown&view-data=eyJ1cmwiOiJodHRwczovL3d3NC5hdXRvdHRhc2submV0LzMvMjcvc2VydmljZWRlc2svVGlja2V0R3JpZldpZGdldERyaWxsb3dkb3duP0NvbnRlbnRJZD03MjIxJkNvbnRlbnRUeXBlSWQ9MiZIYXNQcmltYXJ5R3JvdXBCeVZhbHVlPWZhbHNlJkhhc0Vjb25kYXJ5R3JvdXBBZVZhbHVlPWZhbHNlJmlzUG9wLW91dD1mYWxzZQ==" target="_blank" class="app-item">
                        <i class="fas fa-reply-all app-icon"></i>
                        <span class="app-name">Autotask Custom Responses</span>
                    </a>
                    <div class="back-button-container">
                        <button class="back-button" onclick="showResourcesFolder()">
                            <i class="fas fa-arrow-left"></i> Back to Resources
                        </button>
                    </div>
                </div>

                <div id="general-page" class="folder-page">
                    <h2>General Applications</h2>
                    <a href="https://www.google.com" target="_blank" class="app-item">
                        <i class="fab fa-google app-icon"></i>
                        <span class="app-name">Google</span>
                    </a>
                    <a href="https://gemini.google.com/" target="_blank" class="app-item">
                        <i class="fas fa-gem app-icon"></i> <span class="app-name">Gemini</span>
                    </a>
                    <a href="https://chat.openai.com/" target="_blank" class="app-item">
                        <i class="fas fa-comments app-icon"></i> <span class="app-name">ChatGPT</span>
                    </a>
                    <a href="https://copilot.microsoft.com/" target="_blank" class="app-item">
                        <i class="fab fa-microsoft app-icon"></i> <span class="app-name">CoPilot</span>
                    </a>
                    <div class="back-button-container">
                        <button class="back-button" onclick="showResourcesFolder()">
                            <i class="fas fa-arrow-left"></i> Back to Resources
                        </button>
                    </div>
                </div>

            </div>
        </div>
        <div class="footer">
        </div>
    </div>

    <script>
        // Get references to the main grid and folder pages
        const mainGrid = document.getElementById('mainApplicationGrid');
        const resourcesPage = document.getElementById('resources-page');
        const statusesPage = document.getElementById('statuses-page');
        const adminSupportPage = document.getElementById('admin-support-page');
        const autotaskPage = document.getElementById('autotask-page');
        const generalPage = document.getElementById('general-page'); // New: Reference to general page
        const currentFolderPath = document.getElementById('currentFolderPath');

        // Function to hide all main content areas
        function hideAllContent() {
            mainGrid.style.display = 'none';
            resourcesPage.style.display = 'none';
            statusesPage.style.display = 'none';
            adminSupportPage.style.display = 'none';
            autotaskPage.style.display = 'none';
            generalPage.style.display = 'none'; // New: Hide general page
        }

        // Function to show a specific folder page and update the path
        function showPage(pageId, subFolderName) {
            hideAllContent();
            // document.getElementById(pageId).style.display = 'flex'; // Changed to 'grid' in CSS
            document.getElementById(pageId).style.display = 'grid'; // Ensure it's 'grid' for new layout
            currentFolderPath.innerText = `Skye Cloud Internal Applications | Parent folder: / Resources / ${subFolderName}`;
            resetIdleTimer(); // Reset timer on page navigation
        }

        // Function to show the Resources folder and update the path
        function showResourcesFolder() {
            hideAllContent();
            // resourcesPage.style.display = 'flex'; // Changed to 'grid' in CSS
            resourcesPage.style.display = 'grid'; // Ensure it's 'grid' for new layout
            currentFolderPath.innerText = 'Skye Cloud Internal Applications | Parent folder: / Resources';
            resetIdleTimer(); // Reset timer on page navigation
        }

        // Function to show the main applications grid and update the path
        function showMainApplications() {
            hideAllContent();
            mainGrid.style.display = 'grid'; // Main grid should be 'grid'
            currentFolderPath.innerText = 'Skye Cloud Internal Applications | Parent folder: /';
            resetIdleTimer(); // Reset timer on page navigation
        }

        // --- Sidebar Toggle Logic ---
        function toggleSidebarSection(headerElement) {
            const content = headerElement.nextElementSibling; // Get the next sibling (the sidebar-content div)
            const toggleIcon = headerElement.querySelector('.toggle-icon');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                headerElement.classList.remove('collapsed');
            } else {
                content.classList.add('hidden');
                headerElement.classList.add('collapsed');
            }
            resetIdleTimer(); // Reset timer on user interaction
        }

        // --- Idle Timer Logic ---
        let idleTime; // Variable to hold the timeout ID
        const timeoutInMilliseconds = 5 * 60 * 1000; // 5 minutes in milliseconds

        /**
         * Function to log out the user after idle timeout.
         */
        function logoutUser() {
            window.location.href = 'logout.php'; // Redirect to logout page
        }

        /**
         * Function to reset the idle timer.
         * Clears any existing timer and sets a new one.
         */
        function resetIdleTimer() {
            clearTimeout(idleTime); // Clear the existing timer
            idleTime = setTimeout(logoutUser, timeoutInMilliseconds); // Set a new timer
            console.log("Idle timer reset."); // For debugging
        }

        // Function to update clocks
        function updateClocks() {
            const timezones = {
                'ukTime': 'Europe/London',
                'germanyTime': 'Europe/Berlin',
                'usEstTime': 'America/New_York',
                'canadaEstTime': 'America/Toronto',
                'indiaTime': 'Asia/Kolkata',
                'lagosTime': 'Africa/Lagos'
            };

            for (const id in timezones) {
                const now = new Date();
                const options = {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: timezones[id]
                };
                const formattedTime = new Intl.DateTimeFormat('en-GB', options).format(now);
                document.getElementById(id).textContent = formattedTime;
            }
        }

        // Initial state: ensure main grid is visible on load
        document.addEventListener('DOMContentLoaded', () => {
            showMainApplications();
            resetIdleTimer(); // Start the idle timer when the page loads

            // Initialize all sidebar sections to be collapsed by default
            document.querySelectorAll('.sidebar-section h3').forEach(header => {
                const content = header.nextElementSibling;
                content.classList.add('hidden');
                header.classList.add('collapsed');
            });

            // Initial call to display clocks immediately
            updateClocks();
            // Update clocks every second
            setInterval(updateClocks, 1000);
        });

        // Event listeners to reset the timer on user activity
        document.addEventListener('mousemove', resetIdleTimer);
        document.addEventListener('keydown', resetIdleTimer);
        document.addEventListener('click', resetIdleTimer);
        document.addEventListener('scroll', resetIdleTimer); // Also reset on scroll
    </script>
</body>
</html>