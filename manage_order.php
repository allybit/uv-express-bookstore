<?php 
session_start(); 
include 'admin/db_connect.php'; 

// Fetch cart details
$cart_qry = $conn->query("SELECT c.*, b.title, b.price FROM cart c 
                          INNER JOIN books b ON b.id = c.book_id 
                          WHERE c.customer_id = {$_SESSION['login_id']}");
$total = 0;
?>

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
                <?php while($row = $cart_qry->fetch_array()): 
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
            error: err => {
                console.log(err);
            },
            success: function(resp){
                if(resp == 1){
                    alert_toast('Order successfully submitted.', "success");
                    setTimeout(function(){
                        location.href = 'index.php?page=home';
                    }, 1000);
                }
            }
        });
    });
</script>
