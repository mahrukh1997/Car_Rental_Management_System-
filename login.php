<?php
session_start();
$servername = 'localhost'; // Database host
$dbname = 'carrental'; // Database name
$username= 'root'; // Database username
$password = ''; // Database password

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['loginUsername'];
    $password = $_POST['loginPassword'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $username;
            header("Location: index.html"); // Redirect to main page
            exit();
        } else {
            $loginError = "Invalid username or password.";
        }
    } else {
        $loginError = "Invalid username or password.";
    }
    $stmt->close();
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['registerUsername'];
    $email = $_POST['registerEmail'];
    $phone = $_POST['registerPhone'];
    $password = password_hash($_POST['registerPassword'], PASSWORD_DEFAULT); // Hash the password

    $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $phone, $password);
    
    if ($stmt->execute()) {
        $registrationSuccess = "Thanks for registering, $username! Redirecting to main page...";
        header("Location: index.html"); // Redirect to main page
        exit();
    } else {
        $registrationError = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Car Rental Login</title>
    <link rel="stylesheet" href="logo.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  
    <div class="overlay">
        <div class="form-box">
            <div class="tabs">
                <span id="loginTab" class="active" onclick="switchTab('login')">Login</span>
                <span id="registerTab" onclick="switchTab('register')">Register</span>
            </div>

            <!-- Login Form -->
            <form id="loginForm" class="form" method="POST">
                <input type="text" name="loginUsername" placeholder="Username" required />
                <input type="password" name="loginPassword" placeholder="Password" required />
                <button type="submit" name="login">Login</button>
                <?php if (isset($loginError)) echo "<p>$loginError</p>"; ?>
            </form>

            <!-- Register Form -->
            <form id="registerForm" class="form hidden" method="POST">
                <input type="text" name="registerUsername" placeholder="Username" required />
                <input type="email" name="registerEmail" placeholder="Email" required />
                <input type="tel" name="registerPhone" placeholder="Phone Number" required />
                <input type="password" name="registerPassword" placeholder="Password" required />
                <button type="submit" name="register">Register</button>
                <?php if (isset($registrationError)) echo "<p>$registrationError</p>"; ?>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                loginTab.classList.remove('active');
                registerTab.classList.add('active');
            }
        }
    </script>

</body>
</html>
