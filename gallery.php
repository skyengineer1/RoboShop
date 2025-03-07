<?php
$connection = new mysqli("localhost", "root", "Gogliko123$", "roboshop");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["robot_image"])) {
    $uploadDir = "uploads/";
    $allowedTypes = ["image/png", "image/jpeg", "image/webp"];
    $maxFileSize = 10 * 1024 * 1024; // 10MB
    $fileType = $_FILES["robot_image"]["type"];
    $fileSize = $_FILES["robot_image"]["size"];
    $fileName = basename($_FILES["robot_image"]["name"]);
    $uploadFile = $uploadDir . $fileName;

    if (!in_array($fileType, $allowedTypes)) {
        echo "<script>alert('Only PNG, JPG, and WEBP files are allowed.');</script>";
    } elseif ($fileSize > $maxFileSize) {
        echo "<script>alert('File size must not exceed 10MB.');</script>";
    } else {
        if (move_uploaded_file($_FILES["robot_image"]["tmp_name"], $uploadFile)) {
            $robotName = $_POST["robot_name"];
            $sql = "INSERT INTO robots (name, image_path) VALUES ('$robotName', '$uploadFile')";
            if ($connection->query($sql) === TRUE) {
                echo "<script>alert('Robot added successfully!');</script>";
            } else {
                echo "Error: " . $connection->error;
            }
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robot Gallery</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .gallery-item {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            width: 200px;
        }
        .gallery-item img {
            max-width: 100%;
            height: auto;
        }
        .btn-success {
            margin-top: 20px;
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <a href="index.php" class="btn btn-success">Back to Menu</a>
    <div class="container my-5">
        <h2>Robot Gallery</h2>
        <div class="gallery">
            <?php
            $sql = "SELECT name, image_path FROM robots";
            $result = $connection->query($sql);
            if ($result->num_rows > 0) {
                while ($robot = $result->fetch_assoc()) {
                    echo '<div class="gallery-item">';
                    echo '<img src="' . htmlspecialchars($robot['image_path']) . '" alt="' . htmlspecialchars($robot['name']) . '">';
                    echo '<h5>' . htmlspecialchars($robot['name']) . '</h5>';
                    echo '</div>';
                }
            } else {
                echo "<p>No robots found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>