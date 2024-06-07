<?php
// Set the default time zone to Eastern Time (USA)
date_default_timezone_set('America/New_York');
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
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $age = isset($_POST['age']) ? trim($_POST['age']) : '';
    $ytUsername = isset($_POST['ytUsername']) ? trim($_POST['ytUsername']) : '';
    $state = isset($_POST['state']) ? trim($_POST['state']) : '';
    $promoSource = isset($_POST['promoSource']) ? (trim($_POST['promoSource']) === 'Other' ? trim($_POST['other']) : trim($_POST['promoSource'])) : ''; // Add this line
    $createdTime = date('Y-m-d H:i:s');

    if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($phone) && !empty($age) && !empty($ytUsername) && !empty($state) && !empty($promoSource)) {
        $emailQuery = "SELECT email FROM users WHERE email = ?";
        $emailStmt = $conn->prepare($emailQuery);
        $emailStmt->bind_param("s", $email);
        $emailStmt->execute();
        $emailResult = $emailStmt->get_result();

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
            $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, phone, age, ytUsername, state, promoSource, created_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"); // Update this line
            $stmt->bind_param("sssssssss", $firstName, $lastName, $email, $phone, $age, $ytUsername, $state, $promoSource, $createdTime); // Update this line

            if ($stmt->execute()) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $emailStmt->close();
        $ytUsernameStmt->close();
    } else {
        echo "Please fill in all the fields.";
    }
}

$conn->close();
?>
