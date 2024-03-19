<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["full_name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    // Update these values with your actual database credentials
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname= "shoopeerefund";

    // Create a new mysqli connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if username already exists
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<p>Username already exists. Please choose a different one.</p>";
        exit; // Stop further execution
    }

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $sql = "INSERT INTO users (full_name, username, password, email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullname, $username, $hashed_password, $email);

    if ($stmt->execute()) {
        echo "<p>Registration successful. You can now <a href='index.html'>login</a>.</p>";
    } else {
        echo "<p>Error during registration. Please try again later.</p>";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>
