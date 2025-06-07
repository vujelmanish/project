<?php
require 'db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $msg = "❌ All fields are required!";
    } else {
        // Check if user already exists
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $msg = "❌ Username already taken!";
        } else {
            // Hash password and insert
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed);

            if ($stmt->execute()) {
                $msg = "✅ Registration successful!";
            } else {
                $msg = "❌ Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #aaa; }
        input { padding: 10px; width: 100%; margin: 10px 0; }
        input[type="submit"] { background: #007bff; color: white; border: none; cursor: pointer; }
        .message { margin-top: 10px; color: red; }
    </style>
</head>
<body>

<form method="post" action="">
    <h2>Signup</h2>
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <input type="submit" value="Register" />
    <div class="message"><?= $msg ?></div>
</form>

</body>
</html>
