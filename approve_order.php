<?php
include('dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['orderId']) && isset($_POST['item_id'])) {
    $orderId = $_POST['orderId'];
    $itemId = $_POST['item_id'];

    // Debugging: Check values of orderId and itemId
    echo "orderId: $orderId, itemId: $itemId <br>";

    // Attempt to insert into customer_refund_summary base table directly
    $insertSql = "INSERT INTO retun (ORDER_ID, ITEM_ID) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ii", $orderId, $itemId);
    
    if ($insertStmt->execute()) {
        // If insertion was successful, delete from admin_refund_summary
        $deleteSql = "DELETE FROM admin_refund_summary WHERE ORDER_ID = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $orderId);
        $deleteStmt->execute();
        $deleteStmt->close();
    } else {
        // If insertion failed
        if ($conn->errno == 1062) { // Error number for duplicate entry
            echo "Error: Duplicate entry for ORDER_ID $orderId.";
        } else {
            echo "Error: Failed to insert data into customer_refund_summary base table. SQL Error: " . $conn->error;
        }
    }

    $insertStmt->close();
    $conn->close();

    // Redirect back to the admin page
    header("Location: admin_page.php");
    exit();
}
?>
