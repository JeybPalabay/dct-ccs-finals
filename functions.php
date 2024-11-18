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

function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

function displayErrors(array $errors): string {
    if (!$errors) {
        return '';
    }

    $html = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    $html .= '<strong>System Errors</strong><ul class="mb-0">';

    foreach ($errors as $error) {
        $html .= sprintf('<li>%s</li>', htmlspecialchars($error));
    }

    $html .= '</ul>';
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    $html .= '</div>';

    return $html;
}

function renderErrorsToView(string $error): string {
    if (!$error) {
        return '';
    }

    return sprintf(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">%s<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>',
        htmlspecialchars($error)
    );
}


function guard() {
    if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        header("Location: index.php");
        exit;
    }
}

function validateSubjectData($subject_data) {
    $errors = [];
    
    if (empty($subject_data['subject_code'])) {
        $errors[] = "Subject Code is required";
    }
    
    if (empty($subject_data['subject_name'])) {
        $errors[] = "Subject Name is required";
    }
    
    return $errors;
}
?>