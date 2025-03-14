
<?php
session_start();
require 'db_connection.php'; // Include the database connection

try {
    $result = $conn->query("SELECT * FROM audit_trail ORDER BY created_at DESC");

    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>User ID</th><th>Action</th><th>Order ID</th><th>Details</th><th>Timestamp</th></tr>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . ($row['id'] ?? 'N/A') . "</td>"; // Use 'N/A' if key is missing
        echo "<td>" . ($row['user_id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['action'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['order_id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['details'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['created_at'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    die("Error fetching audit trail: " . $e->getMessage());
}
?>