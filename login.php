<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($input_password, $row['password'])) {
            $_SESSION['username'] = $input_username;
            header("Location: admin.php");
            exit();
        } else {
            header("Location: index.php?error=1");
            exit();
        }
    } else {
        header("Location: index.php?error=1");
        exit();
    }

    $stmt->close();
} else {
    header("Location: index.php?error=1");
    exit();
}

$conn->close();
?>