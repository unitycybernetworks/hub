<?php
// forgotten-password.php - Allows users to request a password reset

// Start the session
session_start();

// Check if the user is already logged in, if so redirect to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

require_once "../db.php";

$username = "";
$username_err = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if username exists
    if (empty($username_err)) {
        $sql = "SELECT id FROM users WHERE username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    // Username found, redirect directly to reset-password.php
                    $reset_token = "SIMULATED_TOKEN_12345"; // Placeholder token
                    header("location: reset-password.php?token=" . urlencode($reset_token) . "&username=" . urlencode($username));
                    exit(); // Important to exit after a header redirect
                } else {
                    // Username not found, give a generic message to prevent enumeration
                    $message = "<div class='message success'>If an account with that username exists, a password reset link has been 'sent'.</div>";
                }
            } else {
                $message = "<div class='message error'>Oops! Something went wrong. Please try again later.</div>";
            }
            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgotten Password | Skye Cloud Hub</title>
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
                <h2>Forgot Password</h2>
                <p>Please enter your username to reset your password.</p>
                <?php echo $message; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-row">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    <?php if (!empty($username_err)): ?><div class="message error"><?php echo $username_err; ?></div><?php endif; ?>

                    <!-- Security warning section, copied from login.php -->
                    <div class="security-warning">
                        <h4>Security</h4>
                        <p class="warning-text">Warning: By logging in to this web page, you confirm that this computer complies with your organization's security policy.</p>
                    </div>

                    <div class="form-group-button">
                        <input type="submit" class="btn-signin" value="Reset Password">
                    </div>

                    <div class="form-group-links">
                        <p>Back to <a href="../login.php">Login</a></p>
                    </div>
                </form>
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
