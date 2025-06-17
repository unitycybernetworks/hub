<?php

// welcome.php - Displays content based on user's group

// Start the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: welcome.php");
    exit;
}
// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}

// Database connection details
$servername = "localhost"; // Or your database host
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "firewall_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ''; // For success/error messages
$firewall_ip_to_open = ''; // Variable to store IP for opening
$firewall_port_to_open = ''; // Variable to store Port for opening

// Handle form submission for adding new firewall
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'] ?? '';
    $firewall_name = $_POST['firewall_name'] ?? '';
    $ip_address = $_POST['ip_address'] ?? '';
    $port = $_POST['port'] ?? ''; // Get the port
    $location = $_POST['location'] ?? '';
    $model = $_POST['model'] ?? '';

    // Basic validation
    if (empty($customer_name) || empty($firewall_name) || empty($ip_address)) {
        $message = '<div class="alert error">Customer Name, Firewall Name, and IP Address are required.</div>';
    } else {
        // Ensure port is valid if provided (e.g., numeric, within range 1-65535)
        // This is basic server-side validation. More robust validation may be needed.
        if (!empty($port) && (!is_numeric($port) || $port < 1 || $port > 65535)) {
            $message = '<div class="alert error">Invalid port number. Please enter a number between 1 and 65535.</div>';
            // Set port to empty if invalid to prevent saving bad data, or handle as you prefer
            $port = '';
        }

        // Prepare and bind
        // Added 'port' column to the INSERT query
        $stmt = $conn->prepare("INSERT INTO firewall (customer_name, firewall_name, ip_address, port, location, model) VALUES (?, ?, ?, ?, ?, ?)");
        // Added 's' for the new port parameter
        $stmt->bind_param("ssssss", $customer_name, $firewall_name, $ip_address, $port, $location, $model);

        if ($stmt->execute()) {
            $message = '<div class="alert success">New firewall added successfully!</div>';
            // Store the IP and Port if insertion was successful
            $firewall_ip_to_open = htmlspecialchars($ip_address);
            $firewall_port_to_open = htmlspecialchars($port); // Store the port
        } else {
            // Check for duplicate IP address error (Error code 1062 is for duplicate entry for unique key)
            if ($conn->errno == 1062) {
                $message = '<div class="alert error">Error: IP Address "' . htmlspecialchars($ip_address) . '" already exists.</div>';
            } else {
                $message = '<div class="alert error">Error adding firewall: ' . $stmt->error . '</div>';
            }
        }
        $stmt->close();
    }
}

// Fetch firewall data (after potential insertion, so the new data appears)
// Added 'port' to the SELECT query
$sql = "SELECT id, customer_name, firewall_name, ip_address, port, location, model FROM firewall ORDER BY customer_name, firewall_name";
$result = $conn->query($sql);

$firewalls = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $firewalls[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firewall Hub | Skye Cloud Hub</title>
      <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
    }

    .container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 1200px; /* Increased max-width to accommodate side-by-side layout */
        overflow-x: auto; /* For responsive tables */
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    /* Flex container for the form and table */
    .content-wrapper {
        display: flex;
        flex-wrap: wrap; /* Allows items to wrap to next line on smaller screens */
        gap: 20px; /* Space between the form and the table */
    }

    .form-section {
        flex: 0 0 300px; /* Don't grow, don't shrink, base width 300px */
        /* Make it flexible on smaller screens */
        max-width: 100%; /* Ensure it doesn't overflow */
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        background-color: #fcfcfc;
        box-sizing: border-box; /* Include padding and border in the width */
    }

    .table-section {
        flex: 1; /* Allows the table section to grow and take remaining space */
        min-width: 450px; /* Minimum width for the table before wrapping */
        overflow-x: auto; /* Ensure horizontal scroll for tables on small screens */
    }

    h2 { /* Added h2 for form section title */
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    hr { /* Separator style (no longer needed between form/table, but kept for general use if any) */
        border: 0;
        border-top: 1px solid #eee;
        margin: 30px 0;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }

    .form-group input[type="text"],
    .form-group input[type="number"] { /* Added number type for port */
        width: calc(100% - 22px); /* Account for padding and border */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box; /* Include padding and border in the element's total width and height */
    }

    .form-group input[type="text"]:focus,
    .form-group input[type="number"]:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
    }

    button[type="submit"] {
        background-color: #28a745; /* Green for add */
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
        width: 100%; /* Make button full width of its container */
    }

    button[type="submit"]:hover {
        background-color: #218838;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0; /* Removed margin-top as it's now part of flex gap */
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
        color: #333;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .ip-link {
        color: #007bff;
        text-decoration: none;
        cursor: pointer;
    }

    .ip-link:hover {
        text-decoration: underline;
    }

    /* Alert Messages */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-weight: bold;
        text-align: center;
        opacity: 1; /* Start visible */
        transition: opacity 0.5s ease-out; /* For fading out */
    }

    .alert.success {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .alert.error {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    /* Media Queries for Responsiveness */
    @media (max-width: 768px) {
        .content-wrapper {
            flex-direction: column; /* Stack items vertically on smaller screens */
            gap: 30px; /* More space when stacked */
        }

        .form-section, .table-section {
            flex: 1 1 100%; /* Take full width when stacked */
            max-width: 100%;
        }
    }
   </style>
</head>
<body>
    <div class="container">
        <h1>Customer Firewalls</h1>

        <?php echo $message; // Display messages here ?>

        <div class="content-wrapper">
            <div class="form-section">
                <h2>Add New Firewall</h2>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="customer_name">Customer Name:</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="firewall_name">Firewall Name:</label>
                        <input type="text" id="firewall_name" name="firewall_name" required>
                    </div>
                    <div class="form-group">
                        <label for="ip_address">IP Address:</label>
                        <input type="text" id="ip_address" name="ip_address" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" title="Enter a valid IP address (e.g., 192.168.1.1)" required>
                    </div>
                    <div class="form-group">
                        <label for="port">Port (optional):</label>
                        <input type="number" id="port" name="port" min="1" max="65535" placeholder="e.g., 8443">
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location">
                    </div>
                    <div class="form-group">
                        <label for="model">Model:</label>
                        <input type="text" id="model" name="model">
                    </div>
                    <button type="submit">Add Firewall</button>
                </form>
            </div>

            <div class="table-section">
                 <?php if (!empty($firewalls)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Firewall Name</th>
                                <th>IP Address:Port</th> <th>Location</th>
                                <th>Model</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($firewalls as $firewall): ?>
                                <?php
                                    $display_ip = htmlspecialchars($firewall['ip_address']);
                                    $link_ip_port = htmlspecialchars($firewall['ip_address']);
                                    if (!empty($firewall['port'])) {
                                        $display_ip .= ':' . htmlspecialchars($firewall['port']);
                                        $link_ip_port .= ':' . htmlspecialchars($firewall['port']);
                                    }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($firewall['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($firewall['firewall_name']); ?></td>
                                    <td><a href="#" class="ip-link" data-ip-with-port="<?php echo $link_ip_port; ?>"><?php echo $display_ip; ?></a></td>
                                    <td><?php echo htmlspecialchars($firewall['location']); ?></td>
                                    <td><?php echo htmlspecialchars($firewall['model']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No firewall data found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Updated selector to use data-ip-with-port
            const ipLinks = document.querySelectorAll('.ip-link');

            ipLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default link behavior
                    // Get the IP and port combined from the data attribute
                    const ipAddressWithPort = this.dataset.ipWithPort;

                    console.log('Attempting to connect to IP:', ipAddressWithPort);
                    // Open the HTTPS link. Note: For default HTTPS port (443), you don't need to specify :443.
                    // If a port is provided, it will be used.
                    window.open(`https://${ipAddressWithPort}`, '_blank');
                });
            });

            // Optional: Hide alerts after a few seconds
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 0.5s ease-out';
                        setTimeout(() => alert.remove(), 500); // Remove after transition
                    });
                }, 5000); // Hide after 5 seconds
            }

            // New logic to open firewall after successful addition
            // Pass both IP and Port from PHP to JavaScript
            const firewallIpToOpen = "<?php echo $firewall_ip_to_open; ?>";
            const firewallPortToOpen = "<?php echo $firewall_port_to_open; ?>";
            let fullTargetUrl = '';

            if (firewallIpToOpen) {
                fullTargetUrl = `https://${firewallIpToOpen}`;
                if (firewallPortToOpen) {
                    fullTargetUrl += `:${firewallPortToOpen}`;
                }

                // Delay slightly to allow the alert message to show
                setTimeout(() => {
                    window.open(fullTargetUrl, '_blank');
                }, 1000); // 1 second delay
            }
        });
    </script>
</body>
</html>