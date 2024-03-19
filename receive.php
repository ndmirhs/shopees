<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Receive Page</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
.container {
    max-width: 1000PX;
    max-height: 1500PX;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1), 0 0 20px orangered;
    text-align: center;
}


    .logo {
        display: block;
        margin: 0 auto 20px;
    }
.toolbar {
	 background-color: #ff7b54;
    text-align: center;
    margin-top: 20px;
}

.logoutButton {
	float: right; 
	font-size: 16px; 
	padding: 10px 10px;
	background-color: #ff7b54;
	
}

.toolbar button {
    background-color: #ff7b54;
    color: black;
    padding: 20px 20px;
    border: none;
    cursor: pointer;
    margin: 5px;
    border-radius: 5px;
	font-size: 20px;
    transition: background-color 0.3s;
}

.toolbar button:hover {
    background-color: whitesmoke;
}

    h2 {
        text-align: center;
        color: #333;
    }

    .item-box {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 40px;
        display: flex;
        justify-content:  space-between; /* Align item details and button to the sides */
        align-items: center;
    }

    .item-details {
		padding: 10px;
		
        text-align: center; /* Align text to the left */
    }

    .item-details p {
        margin: 5px 0;
    }

    .item-image {
        margin-right: 5px; /* Add margin to separate image from item details */
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    button {
        background-color: #ff7b54;
        color: black;
        border: none;
        padding: 10px 10px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #ff7b54;
    }
</style>
</head>
<body>
<div class="container">
  <img src="image/logoshp.png" alt="Logo" width="216" height="71" class="logo">
  <h2> My Purchases </h2> 
   <div class="toolbar">
	 <button onclick="window.location.href = 'main.html'" id="shipButton">Home</button>
    <button onclick="window.location.href = 'ship.php'" id="shipButton">To Ship</button>
    <button onclick="window.location.href = 'receive.php'" id="receiveButton">To Receive</button>
    <button onclick="window.location.href = 'complete.php'" id="completeButton">Complete</button>
    <button onclick="window.location.href = 'cancell.php'" id="cancelButton">Cancel</button>
    <button onclick="window.location.href = 'return_refund.php'" id="refundButton">Refund/Receive</button>
</div>
	<br>
    <?php
    include('dbconnect.php');
    $orderId = $_COOKIE['PHPSESSID'];
    $sql = "SELECT * FROM customer_receive_summary";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($rows = $result->fetch_assoc()) {
    ?>
            <div class="item-box">
              <div class="item-details">
                    <p><strong>Item Name:</strong> <?php echo $rows['ITEM_NAME']; ?></p>
                    <p><strong>Shop:</strong> <?php echo $rows['SHOP']; ?></p>
                    <p><strong>Price:</strong> <?php echo $rows['ITEM_PRICE']; ?></p>
                    <p><strong>Item Total:</strong> <?php echo $rows['ITEM_TOTAL']; ?></p>
                    <p><strong>Total:</strong> <?php echo $rows['ORDER_AMOUNT']; ?></p>
                    <p><strong>Status:</strong> <?php echo $rows['ORDER_STATUS']; ?></p>
                </div>
                <img src="image/<?php echo $rows["ITEM_IMAGE"];?>" width="100" height="100" class="item-image">
                <button onclick="approveOrder()">ORDER RECIVE</button> <button onclick="Rate()">RATE</button> 
            </div>
    <?php
        }
    } else {
        echo "<p>No refunds found.</p>";
    }
    $stmt->close();
    $conn->close();
    ?>
</div>

<script>
    function Rate() {
        alert('You already rate the product (★★★★★).');
        window.location.href = 'receive.php';
    }
	function approveOrder() {
        alert('Thank You.');
        window.location.href = 'receive.php';
    }
</script>
</body>
</html>
