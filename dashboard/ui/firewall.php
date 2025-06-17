<?php
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

// Handle form submission for adding new firewall
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'] ?? '';
    $firewall_name = $_POST['firewall_name'] ?? '';
    $ip_address = $_POST['ip_address'] ?? '';
    $location = $_POST['location'] ?? '';
    $model = $_POST['model'] ?? '';

    // Basic validation
    if (empty($customer_name) || empty($firewall_name) || empty($ip_address)) {
        $message = '<div class="alert error">Customer Name, Firewall Name, and IP Address are required.</div>';
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO firewall (customer_name, firewall_name, ip_address, location, model) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $customer_name, $firewall_name, $ip_address, $location, $model);

        if ($stmt->execute()) {
            $message = '<div class="alert success">New firewall added successfully!</div>';
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
$sql = "SELECT id, customer_name, firewall_name, ip_address, location, model FROM firewalls ORDER BY customer_name, firewall_name";
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
    <title>Firewall Customer Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Firewall Customer Data</h1>

        <?php echo $message; // Display messages here ?>

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

        <hr> <?php if (!empty($firewalls)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Firewall Name</th>
                        <th>IP Address</th>
                        <th>Location</th>
                        <th>Model</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($firewalls as $firewall): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($firewall['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($firewall['firewall_name']); ?></td>
                            <td><a href="#" class="ip-link" data-ip="<?php echo htmlspecialchars($firewall['ip_address']); ?>"><?php echo htmlspecialchars($firewall['ip_address']); ?></a></td>
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

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const ipLinks = document.querySelectorAll('.ip-link');

    ipLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            const ipAddress = this.dataset.ip;

            // This is the crucial part: How do you want to "connect"?

            // Option 1: Open in a new tab (e.g., for web-based management interfaces)
            // window.open(`http://${ipAddress}`, '_blank');
            // window.open(`https://${ipAddress}`, '_blank'); // Prefer HTTPS if available

            // Option 2: Attempt to open an SSH client (requires client-side configuration/protocol handler)
            // This is more complex and depends on the user's browser and OS.
            // ssh://user@ip_address or similar.
            // window.open(`ssh://${ipAddress}`, '_blank');

            // Option 3: Use an internal tool/API (if you have one)
            // You might send the IP address to a backend script that then
            // initiates a connection or provides more options.
            // Example using Fetch API to send IP to a PHP script (connect.php)
            // fetch('connect.php', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //     },
            //     body: JSON.stringify({ ip: ipAddress }),
            // })
            // .then(response => response.json())
            // .then(data => {
            //     console.log('Connect response:', data);
            //     alert('Connection initiated for ' + ipAddress + ': ' + data.message);
            // })
            // .catch((error) => {
            //     console.error('Error connecting:', error);
            //     alert('Could not initiate connection to ' + ipAddress);
            // });

            // For demonstration, let's just log and show an alert:
            console.log('Attempting to connect to IP:', ipAddress);
            alert('Simulating connection to: ' + ipAddress + '\n(In a real scenario, this would open a management interface or initiate an SSH session)');

            // You would replace the alert/console.log with your actual connection logic.
        });
    });
});
    </script>
</body>
</html>