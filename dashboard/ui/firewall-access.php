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
        // NOTE: The table name in your provided file is 'firewall' (singular).
        // Ensure your database table is indeed named 'firewall' and not 'firewalls' (plural).
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
// NOTE: The table name in your provided file is 'firewall' (singular).
$sql = "SELECT id, customer_name, firewall_name, ip_address, location, model FROM firewall ORDER BY customer_name, firewall_name";
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
        max-width: 900px;
        overflow-x: auto; /* For responsive tables */
    }

    h1, h2 { /* Added h2 for form section title */
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    hr { /* Separator style */
        border: 0;
        border-top: 1px solid #eee;
        margin: 30px 0;
    }

    /* Form Styles */
    .form-section {
        margin-bottom: 30px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        background-color: #fcfcfc;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }

    .form-group input[type="text"] {
        width: calc(100% - 22px); /* Account for padding and border */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box; /* Include padding and border in the element's total width and height */
    }

    .form-group input[type="text"]:focus {
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
    }

    button[type="submit"]:hover {
        background-color: #218838;
    }

    /* Table Styles (existing) */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
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

    /* Alert Messages (new) */
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
   </style>
</head>
<body>
    <div class="container">
        <h1>Customer Firewalls</h1>

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

                    console.log('Attempting to connect to IP:', ipAddress);
                    alert('Simulating connection to: ' + ipAddress + '\n(In a real scenario, this would open a management interface or initiate an SSH session)');
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
        });
    </script>
</body>
</html>