<?php
// db_connect.php (Updated to use PDO)

// Ensure config.php is included to get database credentials
require_once 'config.php';

function connect_db() {
    // Database credentials from config.php
    $servername = DB_HOST;
    $username = DB_USER;
    $password = DB_PASS;
    $dbname = DB_NAME; // Use the constant from config.php

    // Create PDO connection
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        // Set the PDO error mode to exception for better error handling
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        // Die with a clear error message if connection fails
        die("Database connection failed: " . $e->getMessage());
    }
}

function create_tables($conn) {
    echo "Attempting to create tables (if they don't exist)...<br>";

    // Companies table
    $sql_companies = "
        CREATE TABLE IF NOT EXISTS companies (
            id INT PRIMARY KEY,
            companyName VARCHAR(255) NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            webAddress VARCHAR(255) DEFAULT NULL,
            additionalAddressInformation TEXT DEFAULT NULL,
            address1 VARCHAR(255) DEFAULT NULL,
            address2 VARCHAR(255) DEFAULT NULL,
            city VARCHAR(100) DEFAULT NULL,
            state VARCHAR(100) DEFAULT NULL,
            postalCode VARCHAR(20) DEFAULT NULL,
            countryID INT DEFAULT NULL,
            createDate DATETIME DEFAULT NULL,
            createdByResourceID INT DEFAULT NULL,
            currencyID INT DEFAULT NULL,
            fax VARCHAR(50) DEFAULT NULL,
            invoiceEmailMessageID INT DEFAULT NULL,
            invoiceMethod INT DEFAULT NULL,
            invoiceTemplateID INT DEFAULT NULL,
            lastActivityDate DATETIME DEFAULT NULL,
            lastTrackedModifiedDateTime DATETIME DEFAULT NULL,
            ownerResourceID INT DEFAULT NULL,
            parentCompanyID INT DEFAULT NULL, -- This column is explicitly included
            purchaseOrderTemplateID INT DEFAULT NULL,
            quoteEmailMessageID INT DEFAULT NULL,
            quoteTemplateID INT DEFAULT NULL,
            sicCode VARCHAR(50) DEFAULT NULL,
            stockMarket VARCHAR(50) DEFAULT NULL,
            stockSymbol VARCHAR(50) DEFAULT NULL,
            surveyCompanyRating INT DEFAULT NULL,
            taxID VARCHAR(50) DEFAULT NULL,
            taxRegionID INT DEFAULT NULL,
            territoryID INT DEFAULT NULL,
            -- Boolean fields as TINYINT(1) or BOOLEAN for MySQL
            invoiceNonContractItemsToParentCompany BOOLEAN DEFAULT FALSE,
            isActive BOOLEAN DEFAULT TRUE,
            isClientPortalActive BOOLEAN DEFAULT FALSE,
            isEnabledForComanaged BOOLEAN DEFAULT FALSE,
            isTaskFireActive BOOLEAN DEFAULT FALSE,
            isTaxExempt BOOLEAN DEFAULT FALSE,
            sharepointSite VARCHAR(255) DEFAULT NULL
        );
    ";
    try {
        $conn->exec($sql_companies); // Use exec for queries that don't return a result set
        echo "Table 'companies' created successfully or already exists.<br>";
    } catch(PDOException $e) {
        echo "Error creating table 'companies': " . $e->getMessage() . "<br>";
    }

    // Contacts table
    $sql_contacts = "
        CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_id INT NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            title VARCHAR(100) DEFAULT NULL,
            FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
        );
    ";
    try {
        $conn->exec($sql_contacts);
        echo "Table 'contacts' created successfully or already exists.<br>";
    } catch(PDOException $e) {
        echo "Error creating table 'contacts': " . $e->getMessage() . "<br>";
    }
}
?>