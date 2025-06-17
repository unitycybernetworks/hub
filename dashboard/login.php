<?php
// login.php - Handles user login and session creation

// Start the session
session_start();

// Check if the user is already logged in, if so redirect to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: https://skyecloudhub.corp.skye-cloud.com/hub/dashboard/ui/welcome.php");
    exit;
}

// Include database connection file
require_once "db.php";

$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password, group_id FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Check if username exists, if so then verify password
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        $group_id = $row["group_id"];

                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_regenerate_id(); // Regenerate session ID for security
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["group_id"] = $group_id; // Store group_id in session

                            // Redirect user to welcome page
                            header("location: https://skyecloudhub.corp.skye-cloud.com/hub/dashboard/ui/welcome.php");
                            exit;
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "<div class='message error'>Oops! Something went wrong. Please try again later.</div>";
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
    <title>Login | Skye Cloud Hub</title>
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
                    <div class="header-text">Skye Cloud Login</div>
                    <div class="subheader-text">Internal Applications</div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="orange-line"></div>

            <div class="login-form-container">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-row">
                        <label for="username">User name:</label>
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
                        <p class="warning-text">Warning: By logging in to this web page, you confirm that this computer complies with your organization's security policy.</p>
                    </div>

                    <div class="form-group-button">
                        <input type="submit" class="btn-signin" value="Sign in">
                    </div>

                    <div class="form-group-links">
                        <p>Don't have an account yet ? <a href="https://skyecloudhub.corp.skye-cloud.com/hub/dashboard/ui/register.php">Register</a></p>
                        <p>Forgotten Password? <a href="https://skyecloudhub.corp.skye-cloud.com/hub/dashboard/ui/forgotten-password.php">Forgot Password</a></p>
                    </div>
                </form>
            </div>

            <div class="timeout-message">
                <p>To protect against unauthorized access, your SC Hub Access session will automatically time out after a period of inactivity. If your session ends, refresh your browser and sign in again.</p>
            </div>
        </div>

        <div class="footer">
            <img src="skyecloudlogo.jpg" alt="Skye Cloud Ltd" class="footer-logo left">
            <img src="skyecloudlogo.jpg" alt="Skye Cloud Ltd" class="footer-logo right">
        </div>
    </div>
</body>
</html>