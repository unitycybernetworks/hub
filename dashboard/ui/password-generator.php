<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
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

// ... rest of your secure page content
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Password Generator | Skye Cloud Hub</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
	<link rel="stylesheet" href="main.css" />
	<link rel="icon" type="image/jpg" href="skyecloudlogo.jpg">
    <style>
 body {
     margin: 0; /* Remove default body margin */
 }
 .header {
   background-color: darkblue;
   padding: 10px 20px;
   display: flex;
   justify-content: space-between;
   align-items: center;
   color: white;
 }

 .header-title {
   color: white;
   font-size: 24px;
   flex-grow: 1; /* Allows the title to take up available space */
   text-align: center; /* Center the text */
 }

 #logoutButton, #refreshButton {
   padding: 8px 15px;
   background-color: blue;
   color: white;
   border: none;
   cursor: pointer;
   border-radius: 5px;
   display:block; /* Ensure buttons are always visible */
 }

 .footer {
   background-color: darkblue;
   padding: 10px 20px;
   text-align: center;
   position: fixed;
   bottom: 0;
   width: 100%;
   box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
 }
    </style>
</head>
<body>
    <div class="header">
        <button id="refreshButton">REFRESH</button>
        <span class="header-title">Password Generator</span>
        <button id="logoutButton">LOGOUT</button>
</div>
	<main>
		<div class="generator">
			<center><h1>Skye Cloud Password Generator</h1></center> <br>
			<label class="password-wrap">
				<input
					type="text"
					id="password"
					readonly
					placeholder="Generate a password"
				/>
				<button class="material-icons" id="copy">content_copy</button>
			</label>

			<label>
				<span>Password Length:</span>
				<input type="number" id="length" value="10" />
			</label>

			<label>
				<span>Include Uppercase Letters:</span>
				<input type="checkbox" id="uppercase" checked />
			</label>

			<label>
				<span>Include Lowercase Letters:</span>
				<input type="checkbox" id="lowercase" checked />
			</label>

			<label>
				<span>Include Numbers:</span>
				<input type="checkbox" id="numbers" checked />
			</label>

			<label>
				<span>Include Symbols:</span>
				<input type="checkbox" id="symbols" checked />
			</label>

			<button type="submit" id="generate">Generate Password</button>

		</div>

	</main>
    <footer class="footer">
        <p>Powered by Skye Cloud's Infrastructure Team</p>
    </footer>
<script>
    // Event listener for LOGOUT button
 document.getElementById("logoutButton").addEventListener("click", function() {
   // Clear the history state
   window.history.pushState(null, "", "logged-out");
   window.location.replace('logout.php'); // Use replace to prevent back navigation
 });

 // Event listener for REFRESH button
 document.getElementById("refreshButton").addEventListener("click", function() {
   location.reload(); // Reload the current page
 });
</script>
	<script src="main.js"></script>
</body>
</html>