<?php
// Start the session
session_start();

// Include the database connection file
include 'admin/db_connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the username/email and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate inputs (basic validation)
    if (empty($username) || empty($password)) {
        die("Username/Email and password are required.");
    }

    // Prepare a SQL query to fetch the user from the database
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the username/email to the query
    $stmt->bind_param("ss", $username, $username);

    // Execute the query
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    // Get the result
    $result = $stmt->get_result();

    // Check if a user was found
    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['login_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Redirect to the home page or dashboard
            header("Location: home.php");
            exit();
        } else {
            // Invalid password
            die("Invalid password.");
        }
    } else {
        // No user found
        die("User not found.");
    }

    // Close the statement
    $stmt->close();
} else {
    // If the form is not submitted, redirect to the login page
    header("Location: login.php");
    exit();
}

// Close the database connection
$conn->close();
?>