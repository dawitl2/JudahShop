<?php
$servername = 'localhost';
$username = 'root';
$password = 'password';
$database = 'test_shop';

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        $sql = "SELECT user_id FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user, $pass);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            $_SESSION['user_id'] = $user_id;
            header("Location: home.php?user_id=$user_id");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }

    if (isset($_POST['signup'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $user = $_POST['username'];
        $pass = $_POST['password'];

        $sql = "INSERT INTO users (name, email, username, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $user, $pass);

        if ($stmt->execute()) {
            $success = "Account created successfully. Please log in.";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #ffffff;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2), 0 0 30px rgba(108, 99, 255, 0.5);
            width: 300px;
            text-align: start;
        }
        .container h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: #8591E6;
        }
        .container h2 span {
            color: #757575;
        }
        .container form {
            display: flex;
            flex-direction: column;
        }
        .container form input {
            margin-bottom: 12px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }
        .container form input:focus {
            outline: none;
            border-color: #8591E6;
            box-shadow: 0 0 5px rgba(108, 99, 255, 0.5);
        }
        .container form a {
            text-decoration: none;
            font-size: 0.9rem;
            color: #8591E6;
            margin-bottom: 15px;
        }
        .container form a:hover {
            text-decoration: underline;
        }
        .container form button {
            background: #8591E6;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
        }
        .container form button:hover {
            background: #6c63ff;
        }
        .toggle-link {
            font-size: 0.9rem;
            margin-top: 15px;
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: center;
        }
        .toggle-link a {
            text-decoration: none;
            color: #8591E6;
        }
        .toggle-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 411px) {
    body {
        background-color: red;
    }
    .container {
        width: 60%;
        padding: 30px;
        transform: scale(0.9);
    }
    .container h2 {
        font-size: 1.5rem;
    }
    .container form input {
        font-size: 0.9rem;
        padding: 8px;
    }
    .container form button {
        font-size: 0.9rem;
        padding: 10px;
    }
    .toggle-link {
        font-size: 0.8rem;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <p class="error" style="color:red; text-align:center;"> <?= $error ?> </p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success" style="color:green; text-align:center;"> <?= $success ?> </p>
        <?php endif; ?>
        <?php if (!isset($_GET['action']) || $_GET['action'] == 'login'): ?>
            <h2>Welcome <span>Back!</span></h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <a href="#">Forgot password?</a>
                <button type="submit" name="login">Log In</button>
            </form>
            <div class="toggle-link">
                Don't have an account? <a href="?action=signup">Sign up</a>
            </div>
        <?php else: ?>
            <h2>Welcome!</h2>
            <form method="POST">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="signup">Sign Up</button>
            </form>
            <div class="toggle-link">
                Already have an account? <a href="?action=login">Log in</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>