<?php
session_start();
require 'db_connection.php'; // Include the database connection
require 'functions.php'; // Include the logAuditTrail function

// Assuming you store the user ID in the session
$user_id = $_SESSION['user_id'] ?? 0; // Use 0 as a default if user_id is not set

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_order'])) {
        $order_id = $_POST['order_id'] ?? 0; // Use 0 as a default if order_id is not set
        $status = $_POST['status'] ?? ''; // Use an empty string as a default if status is not set

        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);

        // Log the action
        logAuditTrail($user_id, 'update_order', $order_id, "Status changed to $status");
    } elseif (isset($_POST['delete_order'])) {
        $order_id = $_POST['order_id'] ?? 0; // Use 0 as a default if order_id is not set

        // Delete order
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);

        // Log the action
        logAuditTrail($user_id, 'delete_order', $order_id);
    }
}

// Fetch cart details
$cart_qry = $conn->query("SELECT c.*, b.title, b.price FROM cart c 
                          INNER JOIN books b ON b.id = c.book_id 
                          WHERE c.customer_id = {$_SESSION['login_id']}");
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="col-lg-12">
        <p><b>Note:</b> This transaction accepts only cash on delivery. Please wait for a verification email or call from the management after checkout.</p>
        
        <h4 class="mt-3">User Details</h4>
        <form id="manage-order">
            <div class="card p-3">
                <div class="form-group">
                    <label for="name" class="control-label">Full Name:</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email" class="control-label">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="contact" class="control-label">Contact Number:</label>
                    <input type="text" name="contact" id="contact" class="form-control" required>
                </div>
            </div>

            <h4 class="mt-3">Delivery Address</h4>
            <div class="form-group">
                <label for="address" class="control-label">Enter Delivery Address:</label>
                <textarea name="address" id="address" cols="30" rows="4" class="form-control" required></textarea>
            </div>

            <h4 class="mt-3">Order Summary</h4>
            <ul class="list-group mb-3">
                <?php while($row = $cart_qry->fetch(PDO::FETCH_ASSOC)): 
                    $subtotal = $row['qty'] * $row['price'];
                    $total += $subtotal;
                ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <b><?php echo htmlspecialchars($row['title']); ?></b> <br>
                        Quantity: <?php echo (int)$row['qty']; ?> <br>
                        Price: <?php echo number_format($row['price'], 2); ?>
                    </span>
                    <span><b><?php echo number_format($subtotal, 2); ?></b></span>
                </li>
                <?php endwhile; ?>
            </ul>
            
            <h4 class="text-right">Total Amount: <b>$<?php echo number_format($total, 2); ?></b></h4>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Place Order</button>
        </form>
    </div>
</div>

<script>
    $('#manage-order').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'admin/ajax.php?action=save_order',
            method: 'POST',
            data: $(this).serialize(),
            error: function(err) {
                console.log(err);
                alert('An error occurred while submitting the order. Please try again.');
                end_load();
            },
            success: function(resp){
                if(resp == 1){
                    alert_toast('Order successfully submitted.', "success");
                    setTimeout(function(){
                        location.href = 'index.php?page=home';
                    }, 1000);
                    // Log the checkout action
                    $.ajax({
                        url: 'admin/ajax.php?action=log_action',
                        method: 'POST',
                        data: {
                            user_id: <?php echo $_SESSION['login_id']; ?>,
                            action: 'checkout',
                            details: 'Order total: $<?php echo number_format($total, 2); ?>'
                        }
                    });
                } else {
                    alert('Failed to submit the order. Please try again.');
                    end_load();
                }
            }
        });
    });


    function alert_toast(message, type) {
        // Display a toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} fixed-top`;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
</body>
</html>