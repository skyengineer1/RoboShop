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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $type = trim($_POST["type"]);
    $price = trim($_POST["price"]);

    // Handle image upload
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // Directory to save uploaded images
        $image_path = $upload_dir . basename($_FILES["image"]["name"]);

        // Check if the file is an image
        $image_file_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
        if (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error = "Only image files (JPG, JPEG, PNG, GIF) are allowed.";
        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            // File is successfully uploaded
        } else {
            $error = "Sorry, there was an error uploading your file.";
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
    } else {
        $servername = "localhost";
        $username = "root";
        $password = "Gogliko123$";
        $database = "roboshop";

        $connection = new mysqli($servername, $username, $password, $database);

        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        $sql = "INSERT INTO robots (name, type, price, image_path) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssds", $name, $type, $price, $image_path); // 'ssds' means: string, string, double, string

        if ($stmt->execute()) {
            $stmt->close();
            $connection->close();
            header("Location: /roboshop/index.php");
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
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
</body>
</html>