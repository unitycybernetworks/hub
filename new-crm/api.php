<?php
// api.php

// --- START DEBUGGING LINES (REMOVE IN PRODUCTION) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END DEBUGGING LINES ---

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin (for development)
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration
require_once 'config.php'; // IMPORTANT: Include the config file
$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASS;
$dbname = DB_NAME; // Use the constant from config.php

// Create database connection using PDO
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$response = ['status' => 'error', 'message' => 'Invalid action.']; // Default response

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getAllCompanies':
        try {
            $searchQuery = $_GET['search'] ?? ''; // Get the search query from the URL
            $sql = "SELECT * FROM companies";
            $params = [];

            if (!empty($searchQuery)) {
                $sql .= " WHERE companyName LIKE :searchQuery OR phone LIKE :searchQuery OR webAddress LIKE :searchQuery OR address1 LIKE :searchQuery OR city LIKE :searchQuery OR postalCode LIKE :searchQuery";
                $params[':searchQuery'] = '%' . $searchQuery . '%';
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format boolean and datetime fields for JSON output if necessary
            foreach ($companies as &$company) {
                if (isset($company['lastActivityDate'])) {
                    $company['lastActivityDate'] = $company['lastActivityDate'] ? date('c', strtotime($company['lastActivityDate'])) : null;
                }
                if (isset($company['lastTrackedModifiedDateTime'])) {
                    $company['lastTrackedModifiedDateTime'] = $company['lastTrackedModifiedDateTime'] ? date('c', strtotime($company['lastTrackedModifiedDateTime'])) : null;
                }
                // Ensure boolean fields are correctly cast for JSON output
                $company['invoiceNonContractItemsToParentCompany'] = (bool)($company['invoiceNonContractItemsToParentCompany'] ?? false);
                $company['isActive'] = (bool)($company['isActive'] ?? false);
                $company['isClientPortalActive'] = (bool)($company['isClientPortalActive'] ?? false);
                $company['isEnabledForComanaged'] = (bool)($company['isEnabledForComanaged'] ?? false);
                $company['isTaskFireActive'] = (bool)($company['isTaskFireActive'] ?? false);
                $company['isTaxExempt'] = (bool)($company['isTaxExempt'] ?? false);
            }

            $response = ["status" => "success", "data" => $companies];
        } catch(PDOException $e) {
            $response = ["status" => "error", "message" => "Error fetching companies: " . $e->getMessage()];
        }
        break;

    case 'getCompanyById':
        $id = (int) ($_GET['id'] ?? 0); // Explicitly cast to int
        try {
            $stmt = $conn->prepare("SELECT * FROM companies WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $company = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($company) {
                // Format boolean and datetime fields for JSON output
                if (isset($company['lastActivityDate'])) {
                    $company['lastActivityDate'] = $company['lastActivityDate'] ? date('c', strtotime($company['lastActivityDate'])) : null;
                }
                if (isset($company['lastTrackedModifiedDateTime'])) {
                    $company['lastTrackedModifiedDateTime'] = $company['lastTrackedModifiedDateTime'] ? date('c', strtotime($company['lastTrackedModifiedDateTime'])) : null;
                }
                // Ensure boolean fields are correctly cast for JSON output
                $company['invoiceNonContractItemsToParentCompany'] = (bool)($company['invoiceNonContractItemsToParentCompany'] ?? false);
                $company['isActive'] = (bool)($company['isActive'] ?? false);
                $company['isClientPortalActive'] = (bool)($company['isClientPortalActive'] ?? false);
                $company['isEnabledForComanaged'] = (bool)($company['isEnabledForComanaged'] ?? false);
                $company['isTaskFireActive'] = (bool)($company['isTaskFireActive'] ?? false);
                $company['isTaxExempt'] = (bool)($company['isTaxExempt'] ?? false);

                $response = ["status" => "success", "data" => $company];
            } else {
                $response = ["status" => "error", "message" => "Company not found."];
            }
        } catch(PDOException $e) {
            $response = ["status" => "error", "message" => "Error fetching company: " . $e->getMessage()];
        }
        break;

    case 'getUsersByCompanyId':
        $companyId_str = $_GET['companyId'] ?? '';
        error_log("Raw companyId from GET: " . $companyId_str);

        // --- UPDATED VALIDATION LOGIC FOR companyId ---
        // Validate if it's a valid integer string, explicitly allowing "0"
        // Check if it's numeric and if converting to int and back to string matches the original,
        // which helps filter out floats (e.g., "1.5") or non-numeric strings (e.g., "abc")
        if (!is_numeric($companyId_str) || (string)(int)$companyId_str !== $companyId_str) {
            $response = ['status' => 'error', 'message' => 'Invalid company ID.'];
            error_log("Validation failed for company ID: " . $companyId_str);
            break;
        }
        // --- END UPDATED VALIDATION LOGIC ---

        // Now that it's validated, cast to int for database query
        $companyId = (int) $companyId_str;
        error_log("Casted companyId (after validation): " . $companyId);

        try {
            // Fetch contacts (users) associated with the given company_id
            $stmt = $conn->prepare("SELECT id, first_name, last_name, email, phone, title FROM contacts WHERE company_id = :companyId");
            $stmt->bindParam(':companyId', $companyId, PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Fetched " . count($users) . " users for company ID: " . $companyId);

            $response = ['status' => 'success', 'data' => $users];

        } catch(PDOException $e) {
            $response = ['status' => 'error', 'message' => 'Error fetching users: ' . $e->getMessage()];
            error_log("Database error fetching users for company ID {$companyId}: " . $e->getMessage());
        }
        break;

    default:
        // Default 'Invalid action' response is already set
        break;
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>