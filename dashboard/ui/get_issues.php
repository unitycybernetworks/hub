<?php
// get_issues.php
// This script reads and outputs the content of issues.txt

// Define the path to the issues file
$file_path = 'issues.txt';

// Check if the file exists
if (file_exists($file_path)) {
    // If the file exists, read its content and output it
    echo file_get_contents($file_path);
} else {
    // If the file does not exist, output a default message
    echo "No ongoing issues reported."; 
}
?>
