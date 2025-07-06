<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $sql = "SELECT * FROM userdetails WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user'] = $user;
            $_SESSION['is_admin'] = $user['is_admin'] == 1;

            // Redirect based on role
            header("Location: " . ($_SESSION['is_admin'] ? : "home.php"));
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "User does not exist. Please register first.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
        <style>
   body, html {
    height: 50%;
    margin: 0;
    font-family: Modern No. 20;
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: url('uploads/lap.jpg'); /* Add the path to your image here */
    background-size: 110% 200%; /* Ensures the image covers the entire background */
    


    .container {
        width: 400px;
        padding: 10px;
        border-radius: 8px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    h1 {
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
        <h1>Login Page</h1>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register Here</a></p>
        
        <?php
        // Display error message if it exists
        if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        }
        ?>
    </div>
</body>
</html>
