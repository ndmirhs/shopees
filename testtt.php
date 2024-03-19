<?php
include('dbconnect.php');

// Check if the form is submitted
if(isset($_POST['Btnsubmit'])) {
    // Retrieve form data
    $refundReason = $conn->real_escape_string($_POST['REFUND_REASON']);
    $refundSolution = $conn->real_escape_string($_POST['REFUND_SOLUTION']);
    $refundDescription = $conn->real_escape_string($_POST['REFUND_DESCRIPTION']);
    $orderId = isset($_POST['ORDER_ID']) ? $_POST['ORDER_ID'] : die("Order ID is missing.");
    $refundStatus = "WAITING"; // Set the refund status to WAITING

    // Handle file upload
    $targetDir = "image/";
    $targetFile = $targetDir . basename($_FILES["REFUND_EVIDENCE"]["name"]);
    if (move_uploaded_file($_FILES["REFUND_EVIDENCE"]["tmp_name"], $targetFile)) {
        echo "The file ". htmlspecialchars(basename($_FILES["REFUND_EVIDENCE"]["name"])). " has been uploaded.";

        // Insert data into the refund table using prepared statements
        $insert_query = $conn->prepare("INSERT INTO refund (ORDER_ID, REFUND_REASON, REFUND_SOLUTION, REFUND_DESCRIPTION, REFUND_EVIDENCE, REFUND_STATUS) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_query->bind_param("ssssss", $orderId, $refundReason, $refundSolution, $refundDescription, $targetFile, $refundStatus);

        if ($insert_query->execute()) {
            echo "New record created successfully";
            echo "Order ID: $orderId"; // Display the order ID
            // Redirect to return_refund.php
            header("Location: return_refund.php");
            exit; // Ensure script stops here to prevent further execution
        } else {
            echo "Error: " . $insert_query->error;
        }

        $insert_query->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Return/Refund</title>
    <link rel="stylesheet" href="return.css">
</head>
<body>

<div class="container">
    <form action="testtt.php" method="post" enctype="multipart/form-data" onsubmit="return redirectToMyPurchases()">
        <header class="header">
            <h2>Request Return/Refund</h2>
        </header>
        <div class="product-info">
            <?php
            include('dbconnect.php');

            $orderId = isset($_GET['orderId']) ? $_GET['orderId'] : null;

            if($orderId){
                $target = "image/";

                $item_sql = "SELECT * FROM customer_order_summary WHERE ORDER_ID = $orderId"; 
                $item_result = $conn->query($item_sql);

                if ($item_result->num_rows > 0) {
                    while ($row = $item_result->fetch_assoc()) {
                        $productImage = $row['ITEM_IMAGE'];
                        $productName = $row['ITEM_NAME'];
                        $productPrice = $row['ITEM_PRICE'];

                        // Check if the image file exists
                        $image_path = $target . $productImage;
                        if (file_exists($image_path)) {
                            // Display the image
                            echo '<img src="' . $image_path . '" alt="' . $productName . '">';
                        } else {
                            // Image file does not exist, display a placeholder or handle the error
                            echo 'Image not found';
                        }

                        echo '<div>';
                        echo '<p> Product Name:' . $productName . '</p>';
                        echo '<p> Price: RM' . $productPrice . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo "No results found";
                }
            } else {
                echo "No order ID provided.";
            }
            ?>
        </div>
        <div class="form-group">
            <label for="reason">Reason for Return</label>
            <select id="reason" name="REFUND_REASON" required onchange="showSolution()">
                <option value="select">-Please select-</option>
                <option value="missing-product">Missing quantities/accessories</option>
                <option value="wrong-item">Received wrong item</option>
                <option value="damage-item">Damaged item</option>
                <option value="faulty-product">Faulty product</option>
                <option value="expired-product">Expired product(s)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="solution">Solution</label>
            <select id="solution" name="REFUND_SOLUTION" required onchange="showSolution()">
                <option value="select">-Please select-</option>
                <option value="refund">Refund</option>
                <option value="return_refund">Return and Refund</option>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description (Optional)</label>
            <textarea id="description" name="REFUND_DESCRIPTION" rows="4" cols="50"></textarea>
        </div>
        <div class="form-group">
            <label for="evidence">Upload Photo Evidence</label>
            <input type="file" id="evidence" name="REFUND_EVIDENCE">
        </div>
        <div class="form-group">
            <br><label for="email">Email</label>
            <?php
            if($orderId){
                $customer_sql = "SELECT * FROM customer_order_summary WHERE ORDER_ID = $orderId";
                $customer_result = $conn->query($customer_sql);
                if ($customer_result->num_rows > 0) {
                    while ($row = $customer_result->fetch_assoc()) {
                        $userEmail = $row['CUST_EMAIL']; // Assuming this is the column name for email in the customer table
                        echo '<input type="email" id="email" name="CUST_EMAIL" value="' . $userEmail . '" required>';
                    }
                }
            }
            ?>
        </div>
        <input type="hidden" name="ORDER_ID" value="<?php echo $orderId; ?>">
        <div class="form-group">
            <input type="submit" id="Btnsubmit" name="Btnsubmit" value="Submit">
        </div>
    </form>
</div>
	
<script>
    function redirectToMyPurchases() {
        // Redirect to "return_refund.php" after form submission
        return true; // Allows default form submission
    }
</script>

</body>
</html>
