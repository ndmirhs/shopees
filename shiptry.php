<?php
include('dbconnect.php');

if(isset($_GET['orderId'])){
    $orderId = $_GET['orderId'];
    
    // Use the orderId to fetch relevant order details from the database
    // Modify your SQL queries accordingly
    // Example:
    $order_sql = "SELECT * FROM orders WHERE ORDER_ID = $orderId"; 
    $order_result = $conn->query($order_sql);
    
    // Fetch customer details using another query
    $customer_sql = "SELECT * FROM customer WHERE ORDER_ID = $orderId"; 
    $customer_result = $conn->query($customer_sql);

    // Check if customer query executed successfully
    if (!$customer_result) {
        die("Error fetching customer details: " . $conn->error);
    }

    // Fetch item details using another query
    $item_sql = "SELECT * FROM customer_ship_summary WHERE ORDER_ID = $orderId"; 
    $item_result = $conn->query($item_sql);

    // Check if item query executed successfully
    if (!$item_result) {
        die("Error fetching item details: " . $conn->error);
    }

    // Close the database connection
    $conn->close();
} else {
    die("Order ID not provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Order</title>
    <link rel="stylesheet" type="text/css" href="try.css">
</head>
<body>
<div class="container">
	<br>
        <h1>Cancel Order</h1>

        <?php
		
	    if ($item_result->num_rows > 0) {
                // Output item details
                while ($item_row = $item_result->fetch_assoc()) {
                    echo "<div class='section'>";
                    echo "<h2>Order List</h2>";
					 echo "<p>Item Name: " . $item_row["ITEM_NAME"] . "</p>";
               		 echo "<p>Item Price: " . $item_row["ITEM_PRICE"] . "</p>";
                }
			
            } else {
                echo "<p>No item details found.</p>";
            }
	
		if ($customer_result->num_rows > 0) {
                // Output customer details
                while ($customer_row = $customer_result->fetch_assoc()) {
                    
                    echo "<h2>Delivery Address</h2>";
                    echo "<p>Delivery Name: " . $customer_row["CUST_FULLNAME"] . "</p>";
                    echo "<p>Delivery Address: " . $customer_row["CUST_ADDRESS"] . "</p>";
                  
                }
            } else {
                echo "<p>No customer details found.</p>";
            }
     
        if ($order_result !== false && $order_result->num_rows > 0) {
           
            while ($row = $order_result->fetch_assoc()) {
                echo "<h2>Shipping Information</h2>";
                echo "<p>Shipping Method: " . $row["SHIPPING_OPTION"] . "</p>";
                echo "<h2>Payment Method</h2>";
                echo "<p>Payment Method: " . $row["PAYMENT"] . "</p>";
                echo "<h2>Total Amount</h2>";
                echo "<p>Total Amount: RM " . $row["ORDER_AMOUNT"] . "</p>";
                echo "<h2>Order ID</h2>";
                echo "<p>Order ID: " . $row["ORDER_ID"] . "</p>";
				echo "</div>";

            }
        } else {
            echo "No results found";
        }
        ?>

        <div class="cancel-button">
            <button onclick="cancelOrder(<?php echo $orderId; ?>)">Cancel Order</button>
        </div>
	<br>
    </div>

    <script>
        function cancelOrder(orderId) {
            // Add cancellation logic here
            if (confirm('Are you sure you want to cancel this order?')) {
                // Send AJAX request to delete order from customer_ship_summary and insert into customer_cancel_summary
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            alert('Your order has been cancelled.');
                            // You can redirect the user to another page or perform additional actions as needed
                            window.location.href = 'cancell.php';
                        } else {
                            alert('Error cancelling order.');
                        }
                    }
                };
                xhr.open('POST', 'cancell.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('orderId=' + orderId);
            }
        }
    </script>
    
</body>
</html>
