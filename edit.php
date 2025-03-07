<?php
$id = "";
$name = "";
$type = "";
$price = "";
$image_path = "";
$error = "";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $connection = new mysqli("localhost", "root", "Gogliko123$", "roboshop");

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "SELECT * FROM robots WHERE id=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $type = $row['type'];
        $price = $row['price'];
        $image_path = $row['image_path'];
    } else {
        $error = "Robot not found!";
    }

    $stmt->close();
    $connection->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $type = $_POST["type"];
    $price = $_POST["price"];
    $image_path = $_POST["current_image"]; // Keep the existing image if no new file is uploaded

    // Handle image upload
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $image_path = $upload_dir . basename($_FILES["image"]["name"]);
        $image_file_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
        if (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error = "Only image files (JPG, JPEG, PNG, GIF) are allowed.";
        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }

    if (empty($name) || empty($type) || empty($price)) {
        $error = "All fields are required";
    } else {
        $connection = new mysqli("localhost", "root", "Gogliko123$", "roboshop");

        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        $sql = "UPDATE robots SET name=?, type=?, price=?, image_path=? WHERE id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssdsi", $name, $type, $price, $image_path, $id);

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
    <title>Edit Robot</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Edit Robot</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($image_path); ?>">
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
                <?php if (!empty($image_path)): ?>
                    <img src="<?php echo $image_path; ?>" alt="Robot Image" width="100">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</body>
</html>