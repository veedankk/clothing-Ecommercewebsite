<?php
session_start();
require 'config.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php"); // Redirect to an error or login page if not admin
    exit();
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $orderId = $_POST['order_id'];
    $newPaymentMethod = $_POST['update_payment_method']; // Corrected variable name

    // Prepared statement to update payment method
    $updateQuery = "UPDATE orders SET payment_method = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);

    if ($stmt) {
        $stmt->bind_param("si", $newPaymentMethod, $orderId); // Bind correct variables
        if ($stmt->execute()) {
            echo "<script>alert('Order status updated successfully');</script>";
        } else {
            echo "<script>alert('Error updating order status');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error: unable to prepare statement');</script>";
    }
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $orderIdToDelete = $_GET['delete'];
    
    $deleteQuery = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    if ($stmt) {
        $stmt->bind_param("i", $orderIdToDelete);
        if ($stmt->execute()) {
            echo "<script>alert('Order deleted successfully'); window.location.href = 'admin_orders.php';</script>";
        } else {
            echo "<script>alert('Error deleting order');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error: unable to prepare statement');</script>";
    }
}

// Fetch orders from the database
$orders = [];
$result = mysqli_query($conn, "SELECT * FROM orders");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
} else {
    echo "Error fetching orders: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <style>
        body {
            font-family: Modern No. 20;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #0b1957;
            margin-bottom: 30px;
        }

        .buttons {
            text-align: right;
            margin-bottom: 20px;
        }

        .buttons a {
            color: #0b1957;
            font-weight: bold;
            text-decoration: none;
            padding: 8px 15px;
            background-color: #e8eaf6;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .buttons a:hover {
            background-color: #c5cae9;
        }

        .order-container {
            width: 90%;
            max-width: 500px;
            margin: 0 auto 20px;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .order-container h3 {
            margin: 0 0 10px;
            font-size: 1.2em;
            color: #333;
            font-weight: bold;
        }

        .order-container p {
            margin: 5px 0;
            font-size: 1em;
            color: #555;
        }

        .actions {
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .option-btn {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .option-btn:hover {
            background-color: #45a049;
        }

        .delete-btn {
            color: red;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #ffebee;
        }
    </style>
</head>
<body>
    <h1>Placed Orders</h1>

    <div class="buttons">
        <a href="dashboard.php">Go to Admin Dashboard</a>
    </div>

    <?php foreach ($orders as $order): ?>
        <div class="order-container">
            <h3>Order ID: <?php echo htmlspecialchars($order['id']); ?></h3>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Area:</strong> <?php echo htmlspecialchars($order['area']); ?></p>
            <p><strong>State:</strong> <?php echo htmlspecialchars($order['state']); ?></p>
            <p><strong>Zip Code:</strong> <?php echo htmlspecialchars($order['zip_code']); ?></p>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($order['country']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>Total Amount:</strong> ₹<?php echo htmlspecialchars($order['totalAmount']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>

            <div class="actions">
                <form action="admin_orders.php" method="post" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="update_payment_method" required>
                        <option value="" selected disabled><?php echo htmlspecialchars($order['payment_method']); ?></option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                    <input type="submit" value="Update" name="update_order" class="option-btn">
                </form>
                <a href="admin_orders.php?delete=<?php echo $order['id']; ?>" onclick="return confirm('Delete this order?');" class="delete-btn">Delete</a>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>
