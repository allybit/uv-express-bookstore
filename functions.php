
<?php
function logAuditTrail($user_id, $action, $order_id = 0, $details = '') {
    global $conn;

    // Debugging: Print the action being logged
    echo "Logging action: User ID = $user_id, Action = $action, Order ID = $order_id, Details = $details<br>";

    try {
        // Prepare the SQL query
        $stmt = $conn->prepare("INSERT INTO audit_trail (user_id, action, order_id, details) VALUES (:user_id, :action, :order_id, :details)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':details', $details);
        $stmt->execute();

        // Debugging: Confirm the insertion
        echo "Audit trail logged successfully!<br>";
    } catch (PDOException $e) {
        // Debugging: Print any errors
        echo "Error logging audit trail: " . $e->getMessage() . "<br>";
    }
}
?>