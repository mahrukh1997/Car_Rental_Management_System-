<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "carrental";

// Connect to database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$thankYouMessage = '';
$showThankYou = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $fathername = $_POST['fathername'];
    $cnic = $_POST['cnic'];
    $address = $_POST['address'];
    $payment = $_POST['payment'];

    $stmt = $conn->prepare("INSERT INTO rentpage (fullname, fathername, cnic, address, payment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullname, $fathername, $cnic, $address, $payment);

    if ($stmt->execute()) {
        $thankYouMessage = "$fullname, your information has been submitted successfully!";
        $showThankYou = true;
    } else {
        $thankYouMessage = "Error: " . $stmt->error;
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
  <title>RENT NOW</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Poppins', sans-serif;
      background: url('https://cdn.dribbble.com/userupload/13612606/file/original-615bc082fce4dd6b3e0e8aacaf4affa1.jpg?resize=2048x1229&vertical=center') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .overlay {
      position: absolute;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
    }

    .panel {
      position: relative;
      width: 400px;
      padding: 40px 30px;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
      color: #fff;
      z-index: 10;
    }

    .panel h1, .panel h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 1.8rem;
      color: #fff;
    }

    label {
      display: block;
      margin-bottom: 0.4rem;
      font-size: 0.95rem;
      font-weight: 500;
    }

    input, textarea, select {
      width: 100%;
      padding: 12px;
      margin-bottom: 1rem;
      border: none;
      border-radius: 10px;
      font-size: 14px;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      outline: none;
    }

    input::placeholder, textarea::placeholder {
      color: #eee;
    }

    textarea { resize: vertical; min-height: 60px; }

    select option { color: #000; }

    button {
      width: 100%;
      padding: 14px;
      background-color: #b07a57;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover { background-color: #9e6645; }

    .hidden { display: none; }

    #thankYouPanel p { color: #fff; text-align: center; font-weight: bold; }
  </style>
</head>
<body>

<div class="overlay"></div>

<?php if ($showThankYou): ?>
  <div id="thankYouPanel" class="panel">
    <h2>Thank You!</h2>
    <p><?php echo $thankYouMessage; ?></p>
  </div>
<?php else: ?>
  <div id="formPanel" class="panel">
    <h1>User Information</h1>
    <form method="POST" novalidate>
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required />

      <label for="fathername">Father's Name</label>
      <input type="text" id="fathername" name="fathername" placeholder="Enter your father's name" required />

      <label for="cnic">CNIC</label>
      <input type="text" id="cnic" name="cnic" placeholder="12345-1234567-1" required />

      <label for="address">Address</label>
      <textarea id="address" name="address" placeholder="123 Main St, City, Country" required></textarea>

      <label for="payment">Payment Method</label>
      <select id="payment" name="payment" required>
        <option value="" disabled selected>Select payment method</option>
        <option value="credit_card">Credit Card</option>
        <option value="debit_card">Debit Card</option>
        <option value="paypal">Paypal</option>
        <option value="cash">Cash</option>
      </select>

      <button type="submit">Submit</button>
    </form>
  </div>
<?php endif; ?>

</body>
</html>
