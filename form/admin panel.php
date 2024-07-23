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

$uploadStatusMessage = "";
$uploadedFilePath = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    // Correctly define the directory path
    $target_dir = "C:/xampp/aryan/htdocs/mineportfolio/portfolio-aryan2/form/uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadStatusMessage .= "File is an image - " . $check["mime"] . ". ";
        $uploadOk = 1;
    } else {
        $uploadStatusMessage .= "File is not an image. ";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadStatusMessage .= "Sorry, file already exists. ";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        $uploadStatusMessage .= "Sorry, your file is too large. ";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadStatusMessage .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed. ";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $uploadStatusMessage .= "Sorry, your file was not uploaded.";
    } else {
        // Ensure the upload directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $uploadStatusMessage .= "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
            $uploadedFilePath = "uploads/" . basename($_FILES["image"]["name"]);

            // Update the database with the new file path
            $user_id = 4; // Assuming a fixed user_id for demonstration
            $sql = "UPDATE login SET profile_picture = '" . basename($_FILES["image"]["name"]) . "' WHERE id = $user_id";
            if ($conn->query($sql) === TRUE) {
                $uploadStatusMessage .= " Database updated successfully.";
            } else {
                $uploadStatusMessage .= " Error updating database: " . $conn->error;
            }
        } else {
            $uploadStatusMessage .= "Sorry, there was an error uploading your file.";
        }
    }
}

// Fetch data from the database
$sql = "SELECT * FROM login WHERE id = 20";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    ?>

    <style>
        .fetch-card {
            margin: 50px 303px 0px 195px;
            background-color: aquamarine;
            border-radius: 59px;
            height: 79%;
            width: 54%;
            box-shadow: 10px 10px 30px rgb(19 14 11);
            padding: 20px;
        }
        .user-dashboard {
            margin: 11px 24% 0px 168px;
        }
        .table-feftch {
            margin: 0px 0px 0px 72px;
        }
        .uploaded-image {
            margin-top: 20px;
            text-align: center;
        }
    </style>

    <div class="card fetch-card">
        <h1 class="user-dashboard">User Data</h1>
        <table class="table-feftch">
            <tr>
                <th>User id.</th>
                <td><?php echo $row['id']; ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?php echo $row['name']; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $row['email']; ?></td>
            </tr>
            <tr>
                <th>Picture</th>
                <td>
                    <img src="uploads/<?php echo $row['profile_picture']; ?>" alt="Profile Picture" width="100">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="file" name="image" accept=".jpg,.png,.jpeg,.gif">
                        <button type="submit">Upload</button>
                    </form>
                    <p><?php echo $uploadStatusMessage; ?></p>
                </td>
            </tr>
        </table>
        <?php if ($uploadedFilePath): ?>
        <div class="uploaded-image">
            <h2>Uploaded Image:</h2>
            <img src="<?php echo $uploadedFilePath; ?>" alt="Uploaded Image" width="150">
        </div>
        <?php endif; ?>
    </div>
    <?php
} else {
    echo "No user found";
}

$conn->close();
?>
