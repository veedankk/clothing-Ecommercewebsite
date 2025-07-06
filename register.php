<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name=$_POST["name"];
    $username = $_POST["username"];
    $email= $_POST["email"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO userdetails (name,username,email,phone, password) VALUES (?, ?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss",$name, $username,$email,$phone, $hashed_password);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <style>
        body, html {
            height: 80%;
            margin: 0;
            font-family: Modern No. 20;
            display: flex;
            background-image: url('uploads/gg.jpg'); /* Add the path to your image here */
            background-size: 100% 130%; /* Ensures the image covers the entire background */
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
            background-color: #45a049;
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
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form id="registrationForm">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <span id="name-validation"></span>
            
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <span id="username-validation"></span>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            <span id="email-validation"></span>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>
            <span id="phone-validation"></span>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <span id="password-validation"></span>
            
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login Here</a></p>
    </div>

    <script>
        $(document).ready(function() {
            // Username validation
            $('#username').on('input', function() {
                const username = $(this).val();
                if (username.length > 2) {
                    $.post('validate.php', { username: username }, function(response) {
                        $('#username-validation').text(response);
                    });
                } else {
                    $('#username-validation').text("");
                }
            });

            // Email validation
            $('#email').on('input', function() {
                const email = $(this).val();
                const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
                if (!emailPattern.test(email)) {
                    $('#email-validation').text("Email must be a valid @gmail.com address.");
                } else {
                    $('#email-validation').text("");
                }
            });

            // Phone validation
            $('#phone').on('input', function() {
                const phone = $(this).val();
                const phonePattern = /^\d{10}$/;
                if (!phonePattern.test(phone)) {
                    $('#phone-validation').text("Phone number must be 10 digits.");
                } else {
                    $('#phone-validation').text("");
                }
            });

            // Password validation
            $('#password').on('input', function() {
                const password = $(this).val();
                const passwordPattern = /^[A-Z][A-Za-z\d]{5,}$/;
                if (!passwordPattern.test(password)) {
                    $('#password-validation').text("Password must start with a capital letter and be at least 6 characters.");
                } else {
                    $('#password-validation').text("");
                }
            });

            // Form submission
            $('#registrationForm').submit(function(e) {
                e.preventDefault();
                $.post('register.php', $(this).serialize(), function(response) {
                    if (response === "success") {
                        alert("Registration successful! Redirecting to login...");
                        window.location.href = 'login.php';
                    } else {
                        alert("Registration failed. Please try again.");
                    }
                });
            });
        });
    </script>
</body>
</html>
