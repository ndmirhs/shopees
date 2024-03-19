<?php
error_reporting(E_ALL);
session_start();

// Update these values with your actual database credentials
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shoopeerefund";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create a new mysqli connection
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if username and password are set in the POST data
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (empty($username) || empty($password)) {
            echo "Username or password not set.";
            exit;
        }

        // Prepare a SQL statement to retrieve the hashed password and user status for the entered username
        $stmt = $conn->prepare("SELECT password, user_status FROM users WHERE username = ?");
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }

        $stmt->bind_result($hashed_password, $user_status);
        $stmt->fetch();
        
        // Verify the entered password against the retrieved hashed password
        if ($hashed_password !== null && password_verify($password, $hashed_password)) {
            // Set the username in the session variable
            $_SESSION["username"] = $username;

            // Check if the user is an admin
            if ($user_status === 'admin') {
                // Redirect admin to admin.php
                header("Location: admin.php");
                exit;
            } else {
                // Redirect customer to main.html
                header("Location: main.html");
                exit;
            }
        } else {
            echo "<p>Invalid username or password. Please try again.</p>";
        }
    }
    // Close the database connection
    $conn->close();
}
?>

