<?php
session_start();
require 'config.php';

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // Get the username
    $password = $_POST['password']; // Get the password

    // Prepare and execute SQL query to check if admin exists
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Direct password comparison (not recommended for production)
        if ($password == $admin['password']) {
            // Password matches, set session variables
            $_SESSION['admin_user'] = $username;
            $_SESSION['is_admin'] = true;

            // Log to verify the session variables are set correctly
            error_log("Admin logged in: " . $username);

            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit(); // Ensure no further code is executed after redirection
        } else {
            $error = "Invalid password."; // Password mismatch
            error_log("Invalid password for user: " . $username); // Debugging: log invalid password
        }
    } else {
        $error = "Invalid credentials or you are not an authorized admin."; // User not found
        error_log("Invalid credentials for username: " . $username); // Debugging: log invalid username
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* Styling for the login page */
        body, html {
            height: 100%;
            margin: 0;
            background-image:url(bg.jpg);
            font-family: Modern No. 20;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f5;
        }
        .container {
            width: 300px;
            padding: 20px;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            font-weight: bold;
            color: #555;
            margin-bottom: 8px;
            text-align: left;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0b1957;
        }
        p {
            margin-top: 15px;
            font-size: 14px;
        }
        p a {
            color: #4CAF50;
            text-decoration: none;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Login</h2>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
</div>
</body>
</html>
