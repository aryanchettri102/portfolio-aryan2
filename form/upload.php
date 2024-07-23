<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "potfilio1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"]) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $file_name = $_FILES["image"]["name"];
    $file_tmp = $_FILES["image"]["tmp_name"];
    $upload_dir = "uploads/";

    // Ensure the upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $target_file = $upload_dir . basename($file_name);

    // Move the uploaded file to the upload directory
    if (move_uploaded_file($file_tmp, $target_file)) {
        // Update the database with the new file path
        $sql = "UPDATE login SET profile_picture = '$file_name' WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            echo "File uploaded and database updated successfully.";
        } else {
            echo "Error updating database: " . $conn->error;
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded or invalid request.";
}

$conn->close();
?>
