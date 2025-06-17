<?php
// config.php
// This file contains all configuration settings for the CRM platform.

// MySQL Database Credentials
// IMPORTANT: Replace with your actual database credentials.
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // e.g., 'root'
define('DB_PASS', ''); // e.g., '' for no password
define('DB_NAME', 'autotask_db'); // Name of the database you are using - ENSURE THIS IS 'autotask_db'

// Autotask API Credentials (for logging purposes, not directly used in this version for data fetching as per instruction to use JSON file)
// Note: These credentials are provided for logging purposes as per your request.
// If you were to fetch data directly from Autotask REST APIs, you would use these
// for Basic Authentication. For this implementation, we are using the provided
// autotask_company_data.json file for initial data population.
define('AUTOTASK_TRACKING_IDENTIFIER', 'DPREOA6A2MTZWBRIQGK2VXJ74OB');
define('AUTOTASK_USER', 'dpobhrp2r2pd46c@SKYE-CLOUD.COM');
define('AUTOTASK_SECRET', 'G~o2qE8@K#w19eJ$p*0W*3Dsx');

?>