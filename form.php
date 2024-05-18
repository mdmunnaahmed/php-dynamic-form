<?php
// Set the default time zone to Eastern Time (USA)
date_default_timezone_set('America/New_York');
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if keys exist in $_POST array
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $ytUsername = isset($_POST['ytUsername']) ? trim($_POST['ytUsername']) : '';
    $createdTime = date('Y-m-d H:i:s'); // Current timestamp

    // Server-side validation
    if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($phone) && !empty($ytUsername)) {
        // Check if email already exists
        $emailQuery = "SELECT email FROM users WHERE email = ?";
        $emailStmt = $conn->prepare($emailQuery);
        $emailStmt->bind_param("s", $email);
        $emailStmt->execute();
        $emailResult = $emailStmt->get_result();

        // Check if YouTube username already exists
        $ytUsernameQuery = "SELECT ytUsername FROM users WHERE ytUsername = ?";
        $ytUsernameStmt = $conn->prepare($ytUsernameQuery);
        $ytUsernameStmt->bind_param("s", $ytUsername);
        $ytUsernameStmt->execute();
        $ytUsernameResult = $ytUsernameStmt->get_result();

        if ($emailResult->num_rows > 0) {
            echo "Email already exists";
        } elseif ($ytUsernameResult->num_rows > 0) {
            echo "YouTube username already exists";
        } else {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, phone, ytUsername, created_time) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $firstName, $lastName, $email, $phone, $ytUsername, $createdTime);

            // Execute the statement
            if ($stmt->execute()) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }

        // Close the result sets
        $emailStmt->close();
        $ytUsernameStmt->close();
    } else {
        echo "Please fill in all the fields.";
    }
}

// Close the connection
$conn->close();
?>
