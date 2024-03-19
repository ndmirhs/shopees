<?php
include('dbconnect.php');

// Error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $refund_id = intval($_POST['refund_id']);
    $action = $_POST['action'];

    if ($action == 'approve') {
        // Update refund status to "approve" in the database
        $sql_update_refund_status = "UPDATE refund SET REFUND_STATUS = 'APPROVE' WHERE REFUND_ID = ?";
    } elseif ($action == 'not_approve') {
        // Update refund status to "not approve" in the database
        $sql_update_refund_status = "UPDATE refund SET REFUND_STATUS = 'NOT APPROVE' WHERE REFUND_ID = ?";
    }

    $stmt_update_refund_status = $conn->prepare($sql_update_refund_status);
    $stmt_update_refund_status->bind_param("i", $refund_id);
    $stmt_update_refund_status->execute();
    $stmt_update_refund_status->close();
}

// Logout functionality
if(isset($_POST['logout'])) {
    // Destroy session and redirect to login page
    session_destroy();
    header("Location: index.html");
    exit;
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Page</title>
<link rel="stylesheet" href="admins.css">
<style>
    .logoutButton {
        float: right; 
        font-size: 16px; 
        margin:5px;
        padding: 10px 10px;
        background-color: #ff7b54;
    }
</style>
</head>

<body>
<div class="container">
    <br>
    <form method="post" action="">
        <button type="submit" name="logout" id="logoutButton" class="logoutButton">LOG OUT</button>
    </form>
    <br><br>
    <img src="image/logoshp.png" alt="Logo"  class="logo" align="center">
    <h2 > SHOPEE ADMIN</h2> 
<br>
<table width="800" border="1" align="center">
        <thead>
            <tr>
                <th width="20">REFUND ID</th>
                <th width="50">CUSTOMER NAME</th>
                <th width="20">ORDER ID</th>
                <th width="50">ITEM NAME</th>
                <th width="20">ORDER AMOUNT</th>
                <th width="50">ORDER STATUS</th>
                <th width="50">REFUND STATUS</th>
                <th width="20">ACTION</th>
            </tr>
        </thead>
     <?php
            // Retrieve data from database 
            $sql = "SELECT * FROM admin_refund_summary";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($rows = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $rows['REFUND_ID']; ?></td>
                        <td><?php echo $rows['CUSTOMER_NAME']; ?></td>
                        <td><?php echo $rows['ORDER_ID']; ?></td>
                        <td><?php echo $rows['ITEM_NAME']; ?></td>
                        <td>
                           <?php echo $rows['ORDER_AMOUNT']; ?>
                        </td>
                        <td><?php echo $rows['ORDER_STATUS']; ?></td>
                        <td><?php echo $rows['REFUND_STATUS']; ?></td>
						<td align="center" width="50">
						<form method="post" action="">
							<input type="hidden" name="refund_id" value="<?php echo $rows['REFUND_ID']; ?>">
							<button type="button" onclick="confirmApprove(<?php echo $rows['REFUND_ID']; ?>)">APPROVE</button>
							<br>
							<button type="button" onclick="confirmNotApprove(<?php echo $rows['REFUND_ID']; ?>)" style="margin-top: 5px;">NOT APPROVE</button>
						</form>
						</td>
                    </tr>
                <?php
                }
            } else {
                echo "<tr><td colspan='11'>No refunds found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
<br>
<br>
</div>

<!-- JavaScript for confirmation dialogs -->
<script>
    function confirmApprove(refundId) {
        if (confirm("Are you sure you want to approve this refund?")) {
            document.getElementById('refund_id').value = refundId;
            document.getElementById('action').value = 'approve';
            document.getElementById('approvalForm').submit();
        }
    }

    function confirmNotApprove(refundId) {
        if (confirm("Are you sure you want not to approve this refund?")) {
            document.getElementById('refund_id').value = refundId;
            document.getElementById('action').value = 'not_approve';
            document.getElementById('approvalForm').submit();
        }
    }
</script>

<!-- Hidden form for approving/not approving refunds -->
<form id="approvalForm" method="post" action="">
    <input type="hidden" name="refund_id" id="refund_id">
    <input type="hidden" name="action" id="action">
</form>
</body>
</html>
