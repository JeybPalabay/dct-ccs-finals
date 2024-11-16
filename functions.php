<?php    
session_start();

function connectDatabase() {
    $host = "localhost"; // Database server (usually localhost for local dev)
    $username = "root";  // Your MySQL username
    $password = "";      // Your MySQL password (often empty for localhost)
    $database = "dct-ccs-finals";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function loginUser($email, $password) {
    $conn = connectDatabase();

    $hashed_password = md5($password); // MD5 hashing
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        return true;
    } else {
        return false;
    }
}

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

?>