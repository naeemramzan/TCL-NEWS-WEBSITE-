<?php

// Start a session. This is needed for the admin panel login system later.
session_start();

// --- Database Configuration ---

// Define database credentials as constants. This is a good practice.  sahu aa eho ??? g ada password t a shi aa ada
define('DB_HOST', 'localhost'); // Your database host, usually 'localhost'
define('DB_USER', 'u929353525_tcls');      // Your database username, often 'root' for local development
define('DB_PASS', 'Naeem1892#');          // Your database password, often empty for local development
define('DB_NAME', 'u929353525_tclnews'); // The name of the database we created

// --- Create Database Connection ---

// Create a new mysqli object to connect to the database.
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if the connection was successful.
if ($conn->connect_error) {
    // If the connection fails, stop the script and display an error message.
    // In a live production environment, you would log this error instead of showing it to the user.
    die("Connection Failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 to support a wide range of characters.
$conn->set_charset("utf8mb4");


// --- Optional: A helper function for debugging ---
// This can be useful during development to quickly see the contents of a variable.
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die(); // Stop the script after printing
}


// (Your existing database connection code should be at the top)
// ...

// --- VISITOR TRACKING LOGIC ---

function track_visitor($conn) {
    // Get the visitor's IP address
    // The 'HTTP_X_FORWARDED_FOR' header is checked for users behind a proxy.
    $visitor_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

    // Check if this IP address already exists in the table
    $stmt = $conn->prepare("SELECT id FROM visitors WHERE ip_address = ?");
    $stmt->bind_param("s", $visitor_ip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the visitor exists, just update their last_visit timestamp.
        // The timestamp updates automatically due to the table schema, but we run an UPDATE query to be explicit.
        $update_stmt = $conn->prepare("UPDATE visitors SET last_visit = NOW() WHERE ip_address = ?");
        $update_stmt->bind_param("s", $visitor_ip);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // If it's a new visitor, insert their IP address.
        $insert_stmt = $conn->prepare("INSERT INTO visitors (ip_address) VALUES (?)");
        $insert_stmt->bind_param("s", $visitor_ip);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $stmt->close();
}

// Call the function to track the visitor on every page load
// The $conn variable must be available from your config file
if (isset($conn)) {
    track_visitor($conn);
}

?>