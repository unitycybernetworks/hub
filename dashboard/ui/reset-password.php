<?php
// reset-password.php - Allows user to set a new password after a reset request

// Start the session
session_start();

// Check if the user is already logged in, if so redirect to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

require_once "../db.php";

$token = $_GET['token'] ?? '';
$username = $_GET['username'] ?? '';
$password = $confirm_password = "";
$password_err = $confirm_password_err = "";
$message = "";

// In a real application, you would validate the token against a database record
// and check its expiry. For this simulation, we'll just check if it's present.
if (empty($token) || empty($username)) {
    $message = "<div class='message error'>Invalid reset link or missing information.</div>";
} else {
    // Process form submission for new password
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate new password
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a new password.";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }

        // Validate confirm password
        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm the new password.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Password did not match.";
            }
        }

        // Update password if no errors
        if (empty($password_err) && empty($confirm_password_err)) {
            $sql = "UPDATE users SET password = :password WHERE username = :username";

            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $param_username = $username; // Use the username from the URL parameter

                if ($stmt->execute()) {
                    $message = "<div class='message success'>Your password has been reset successfully. You can now <a href='../login.php' class='link'>login</a>.</div>";
                    // In a real app, you would also delete the reset token from the database here
                } else {
                    $message = "<div class='message error'>Oops! Something went wrong. Please try again later.</div>";
                }
                unset($stmt);
            }
        }
    }
}
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Skye Cloud Hub</title>
    <!-- Link to login.css for consistent styling -->
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
                <h2>Reset Password</h2>
                <?php echo $message; ?>
                <?php if (empty($message) || strpos($message, 'success') === false): // Only show form if no success message or invalid link ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?token=' . urlencode($token) . '&username=' . urlencode($username); ?>" method="post">
                        <div class="form-row">
                            <label for="password">New Password:</label>
                            <input type="password" name="password" id="password" required>
                        </div>
                        <?php if (!empty($password_err)): ?><div class="message error"><?php echo $password_err; ?></div><?php endif; ?>

                        <div class="form-row">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" name="confirm_password" id="confirm_password" required>
                        </div>
                        <?php if (!empty($confirm_password_err)): ?><div class="message error"><?php echo $confirm_password_err; ?></div><?php endif; ?>

                        <!-- Security warning section, copied from login.php -->
                        <div class="security-warning">
                            <h4>Security</h4>
                            <p class="warning-text">Warning: By logging in to this web page, you confirm that this computer complies with your organization's security policy.</p>
                        </div>

                        <div class="form-group-button">
                            <input type="submit" class="btn-signin" value="Reset Password">
                        </div>
                    </form>
                <?php endif; ?>
                <div class="form-group-links">
                    <p>Back to <a href="../login.php">Login</a></p>
                </div>
            </div>

            <!-- Timeout message section, copied from login.php -->
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
