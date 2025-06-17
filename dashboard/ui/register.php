<?php
// register.php - Handles user registration with enhanced validation

// Start the session
session_start();

// Check if the user is already logged in, if so redirect to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

// Include database connection file
require_once "../db.php";

$username = $password = "";
$username_err = $password_err = "";
$registration_success = false;

// Define a list of forbidden passwords (case-insensitive check)
$forbidden_passwords = [
    "password", "123456", "qwerty", "admin", "12345678", "123456789",
    "jesus", "iloveyou", "secret", "master", "dragon", "welcome",
    "football", "myself", "computer", "access", "qazwsx", "asdfgh",
    "zxcvbn", "test", "user", "root", "guest", "changeme", "password123", 
    "123456", "password", "123456789", "12345678", "12345", "1234567", "qwerty", "abc123", "111111", 
       "123123", "password1", "admin", "letmein", "welcome", "football", "monkey", "iloveyou", "dragon", "sunshine", "basketball", 
       "Welcome2025", "Welcome2024", "London2025", "London2024", "Summer2025", "Summer2024", 
];

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $input_username = trim($_POST["username"]);

        // Validate username format: Allows only alphabetic characters and single spaces between words.
        // E.g., "john doe", "j smith", "mary", "smith" are valid.
        // "john123", "john-doe", "john  doe", " john doe", "john doe " are invalid.
        if (!preg_match('/^[a-zA-Z]+(?: [a-zA-Z]+)*$/', $input_username)) {
            $username_err = "Username can only contain letters and single spaces between words (e.g., 'John Doe', 'J Smith').";
        } else {
            // Prepare a select statement to check if username already exists
            $sql = "SELECT id FROM users WHERE username = :username";

            if ($stmt = $pdo->prepare($sql)) {
                // Bind parameters
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

                // Set parameters
                $param_username = $input_username; // Use the cleaned input_username

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    if ($stmt->rowCount() == 1) {
                        $username_err = "This username is already taken.";
                    } else {
                        $username = $input_username; // Valid username
                    }
                } else {
                    echo "<div class='message error'>Oops! Something went wrong checking username. Please try again later.</div>";
                }

                // Close statement
                unset($stmt);
            }
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } elseif (in_array(strtolower(trim($_POST["password"])), $forbidden_passwords)) {
        $password_err = "This password is too common and forbidden. Please choose a stronger one.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before inserting into database
    if (empty($username_err) && empty($password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, group_id) VALUES (:username, :password, :group_id)";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":group_id", $param_group_id, PDO::PARAM_INT);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_group_id = 1; // Default to 'Standard User' group

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $registration_success = true;
            } else {
                echo "<div class='message error'>Something went wrong during registration. Please try again later.</div>";
            }

            // Close statement
            unset($stmt);
        }
    }

    // Close connection
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account | Skye Cloud Hub</title>
    <link rel="stylesheet" href="login.css"> 
    <link rel="icon" type="image/jpg" href="skyecloudlogo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="logo-area">
                <img src="skyecloudlogo.jpg" alt="Skye Cloud Logo" class="header-logo">
                <div class="header-titles">
                    <div class="header-text">Skye Cloud Registration</div>
                    <div class="subheader-text">Internal Applications</div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="orange-line"></div>

            <div class="login-form-container">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-row">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    <?php if (!empty($username_err)): ?><div class="message error"><?php echo $username_err; ?></div><?php endif; ?>

                    <div class="form-row">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <?php if (!empty($password_err)): ?><div class="message error"><?php echo $password_err; ?></div><?php endif; ?>

                    <div class="security-warning">
                        <h4>Security</h4>
                        <p class="warning-text">Warning: By registering an account, you confirm that this computer complies with your organization's security policy.</p>
                    </div>

                    <div class="form-group-button">
                        <input type="submit" class="btn-signin" value="Register">
                    </div>
                </form>

                <?php if ($registration_success): ?>
                    <div class="message success">Account created successfully! You can now <a href="../login.php">login</a>.</div>
                <?php endif; ?>
                <p class="redirect-link">Already have an account? <a href="../login.php" class="link">Login here</a>.</p>
            </div>

            <div class="timeout-message">
                <p>Ensure to use a strong and unique password. Refer to your organization's security guidelines for password best practices.</p>
            </div>
        </div>

        <div class="footer">
            <img src="skyecloudlogo.jpg" alt="Skye Cloud Ltd" class="footer-logo left">
            <img src="skyecloudlogo.jpg" alt="Skye Cloud Ltd" class="footer-logo right">
        </div>
    </div>
</body>
</html>