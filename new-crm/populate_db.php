<?php
// populate_db.php (Regenerated and Updated to use PDO)

// Enable full error reporting to see any PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php'; // Include database connection and table creation functions

echo "Starting populate_db.php script...<br>";

// Connect to the database using PDO (from db_connect.php)
$conn = connect_db();

// Create tables if they do not exist (using PDO from db_connect.php)
echo "Calling create_tables function...<br>";
create_tables($conn);
echo "Finished create_tables function.<br>";

// --- BEGIN JSON DATA PROCESSING AND INSERTION (using PDO) ---
// Path to your JSON file containing company data
$json_file = 'autotask_company_data.json';

echo "Checking for JSON file: '{$json_file}'...<br>";
if (!file_exists($json_file)) {
    die("Error: JSON file '{$json_file}' not found. Make sure it's in the same directory as populate_db.php.<br>");
}
echo "JSON file found. Reading contents...<br>";

// Read the JSON file content
$json_content = file_get_contents($json_file);
if ($json_content === false) {
    die("Error: Could not read content from '{$json_file}'.<br>");
}

// Decode JSON data
$data = json_decode($json_content, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON from '{$json_file}': " . json_last_error_msg() . "<br>");
}

echo "JSON data decoded successfully. Processing companies...<br>";

$companies_inserted = 0;
// Removed $contacts_inserted as mock contacts are no longer generated

// Prepare SQL statements for inserting/updating companies
// Using INSERT ... ON DUPLICATE KEY UPDATE for companies to handle existing entries
$sql_insert_company = "
    INSERT INTO companies (
        id, companyName, phone, webAddress, additionalAddressInformation, address1, address2,
        city, state, postalCode, countryID, lastActivityDate, lastTrackedModifiedDateTime,
        invoiceNonContractItemsToParentCompany, isActive, isClientPortalActive,
        isEnabledForComanaged, isTaskFireActive, isTaxExempt, sharepointSite
    ) VALUES (
        :id, :companyName, :phone, :webAddress, :additionalAddressInformation, :address1, :address2,
        :city, :state, :postalCode, :countryID, :lastActivityDate, :lastTrackedModifiedDateTime,
        :invoiceNonContractItemsToParentCompany, :isActive, :isClientPortalActive,
        :isEnabledForComanaged, :isTaskFireActive, :isTaxExempt, :sharepointSite
    )
    ON DUPLICATE KEY UPDATE
        companyName = VALUES(companyName),
        phone = VALUES(phone),
        webAddress = VALUES(webAddress),
        additionalAddressInformation = VALUES(additionalAddressInformation),
        address1 = VALUES(address1),
        address2 = VALUES(address2),
        city = VALUES(city),
        state = VALUES(state),
        postalCode = VALUES(postalCode),
        countryID = VALUES(countryID),
        lastActivityDate = VALUES(lastActivityDate),
        lastTrackedModifiedDateTime = VALUES(lastTrackedModifiedDateTime),
        invoiceNonContractItemsToParentCompany = VALUES(invoiceNonContractItemsToParentCompany),
        isActive = VALUES(isActive),
        isClientPortalActive = VALUES(isClientPortalActive),
        isEnabledForComanaged = VALUES(isEnabledForComanaged),
        isTaskFireActive = VALUES(isTaskFireActive),
        isTaxExempt = VALUES(isTaxExempt),
        sharepointSite = VALUES(sharepointSite);
";
$stmt_company = $conn->prepare($sql_insert_company);

// Removed SQL statement for inserting contacts as mock contacts are no longer generated
// $sql_insert_contact = "
//     INSERT INTO contacts (company_id, first_name, last_name, email, phone, title)
//     VALUES (:company_id, :first_name, :last_name, :email, :phone, :title);
// ";
// $stmt_contact = $conn->prepare($sql_insert_contact);


// Start a transaction for bulk insertion performance
$conn->beginTransaction();

foreach ($data as $company_item) {
    $company_data = $company_item['item']; // Access the 'item' object

    // Extract company data
    $id = $company_data['id'];
    $company_name = $company_data['companyName'];

    echo "Processing company ID: {$id}, Name: '{$company_name}'...<br>";

    // Extract Sharepoint Site from userDefinedFields
    $sharepointSite = null;
    if (isset($company_data['userDefinedFields']) && is_array($company_data['userDefinedFields'])) {
        foreach ($company_data['userDefinedFields'] as $field) {
            if (isset($field['name']) && $field['name'] === 'Sharepoint Site' && isset($field['value'])) {
                $sharepointSite = $field['value'];
                break;
            }
        }
    }

    // Bind parameters for company insertion
    $stmt_company->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt_company->bindValue(':companyName', $company_name, PDO::PARAM_STR);
    $stmt_company->bindValue(':phone', $company_data['phone'] ?? null, PDO::PARAM_STR);
    $stmt_company->bindValue(':webAddress', $company_data['webAddress'] ?? null, PDO::PARAM_STR);
    $stmt_company->bindValue(':additionalAddressInformation', $company_data['additionalAddressInformation'] ?? null, PDO::PARAM_STR);
    $stmt_company->bindValue(':address1', $company_data['address1'] ?? null, PDO::PARAM_STR);
    $stmt_company->bindValue(':address2', $company_data['address2'] ?? null, PDO::PARAM_STR);
    $stmt_company->bindValue(':city', $company_data['city'] ?? null, PDO::PARAM_STR);
    $stmt_company->bindValue(':state', $company_data['state'] ?? null, PDO::PARAM_STR);
    $stmt_company->bindValue(':postalCode', $company_data['postalCode'] ?? null, PDO::PARAM_STR);
    $stmt_company->bindValue(':countryID', $company_data['countryID'] ?? null, PDO::PARAM_INT);
    // Convert dates to YYYY-MM-DD HH:MM:SS format or NULL
    $stmt_company->bindValue(':lastActivityDate', (isset($company_data['lastActivityDate']) && $company_data['lastActivityDate']) ? date('Y-m-d H:i:s', strtotime($company_data['lastActivityDate'])) : null, PDO::PARAM_STR);
    $stmt_company->bindValue(':lastTrackedModifiedDateTime', (isset($company_data['lastTrackedModifiedDateTime']) && $company_data['lastTrackedModifiedDateTime']) ? date('Y-m-d H:i:s', strtotime($company_data['lastTrackedModifiedDateTime'])) : null, PDO::PARAM_STR);
    // Handle boolean fields from JSON (which might be true/false or 1/0)
    $stmt_company->bindValue(':invoiceNonContractItemsToParentCompany', (int)($company_data['invoiceNonContractItemsToParentCompany'] ?? false), PDO::PARAM_INT);
    $stmt_company->bindValue(':isActive', (int)($company_data['isActive'] ?? false), PDO::PARAM_INT);
    $stmt_company->bindValue(':isClientPortalActive', (int)($company_data['isClientPortalActive'] ?? false), PDO::PARAM_INT);
    $stmt_company->bindValue(':isEnabledForComanaged', (int)($company_data['isEnabledForComanaged'] ?? false), PDO::PARAM_INT);
    $stmt_company->bindValue(':isTaskFireActive', (int)($company_data['isTaskFireActive'] ?? false), PDO::PARAM_INT);
    $stmt_company->bindValue(':isTaxExempt', (int)($company_data['isTaxExempt'] ?? false), PDO::PARAM_INT);
    $stmt_company->bindValue(':sharepointSite', $sharepointSite, PDO::PARAM_STR); // Use the extracted $sharepointSite

    if ($stmt_company->execute()) {
        $companies_inserted++;
        // Removed mock contact generation
    } else {
        $errorInfo = $stmt_company->errorInfo();
        echo "ERROR: Error executing company insert for company ID {$id} ('{$company_name}'): (" . $errorInfo[1] . ") " . $errorInfo[2] . "<br>";
    }
    echo "<br>"; // Add a line break for readability between companies
}

// Commit the transaction
$conn->commit();
echo "Transaction committed for bulk JSON data insertion.<br>";

echo "Data population complete.<br>";
echo "Inserted/Updated {$companies_inserted} companies.<br>";
echo "No mock contacts were inserted in this run.<br>"; // Updated message

$stmt_company->closeCursor(); // For PDO, analogous to close() for mysqli
// Removed $stmt_contact->closeCursor();

// Close the database connection (PDO connection is automatically closed when script ends or $conn goes out of scope)
$conn = null; // Explicitly close the connection

?>