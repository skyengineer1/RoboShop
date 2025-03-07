<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$name = "";
$type = "";
$price = "";
$image_path = "";
$error = "";
$image_error = "";

$connection = new mysqli("localhost", "root", "Gogliko123$", "roboshop");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $type = trim($_POST["type"]);
    $price = trim($_POST["price"]);

    // Handle image upload
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // Directory to save uploaded images
        $image_path = $upload_dir . basename($_FILES["image"]["name"]);

        // Check file type (only allow images)
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);
        $file_size = $_FILES['image']['size'];
        $max_size = 10 * 1024 * 1024; // 10MB

        // If the file is not an image or exceeds 10MB, set an error
        if (!in_array($file_type, $allowed_image_types)) {
            $image_error = "Only image files (JPG, JPEG, PNG, WEBP) are allowed.";
        } elseif ($file_size > $max_size) {
            $image_error = "File size must not exceed 10MB.";
        } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            $image_error = "Sorry, there was an error uploading your file.";
        }
    }

    // Validate other fields
    if (empty($name)) {
        $error = "Name is required";
    } elseif (empty($type)) {
        $error = "Type is required";
    } elseif (empty($price)) {
        $error = "Price is required";
    } elseif (!is_numeric($price)) {
        $error = "Price must be a number";
    } elseif (empty($image_error)) {
        $sql = "INSERT INTO robots (name, type, price, image_path, createdAt, updatedAt) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssds", $name, $type, $price, $image_path); // 'ssds' means: string, string, double, string

        if ($stmt->execute()) {
            $stmt->close();
            $connection->close();
            echo "<script>alert('Robot added successfully!'); window.location.href='index.php';</script>";
            exit();
        } else {
            $error = "Error: " . $stmt->error;
            $stmt->close();
        }

        $connection->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoboShop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Add Robot</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <input type="text" class="form-control" id="type" name="type" value="<?php echo htmlspecialchars($type); ?>">
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Image</label>
                <input type="file" class="form-control" id="image" name="image">
                <?php if (!empty($image_error)): ?>
                    <div class="alert alert-danger mt-2"><?php echo $image_error; ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
</body>
</html>
