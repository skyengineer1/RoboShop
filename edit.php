<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$name = "";
$type = "";
$price = "";
$image_path = "";
$error = "";
$image_error = "";

// Fetch existing robot details
$servername = "localhost";
$username = "root";
$password = "Gogliko123$";
$database = "roboshop";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$sql = "SELECT * FROM robots WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$robot = $result->fetch_assoc();

if (!$robot) {
    die("Robot not found.");
}

$name = $robot['name'];
$type = $robot['type'];
$price = $robot['price'];
$image_path = $robot['image_path'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $robotId = $_POST["robot_id"];
    $robotName = $_POST["robot_name"];
    $uploadFile = "uploads/" . basename($_FILES["robot_image"]["name"]);

    if (move_uploaded_file($_FILES["robot_image"]["tmp_name"], $uploadFile)) {
        $sql = "UPDATE robots SET name='$robotName', image_path='$uploadFile', updatedAt=NOW() WHERE id='$robotId'";
        if ($connection->query($sql) === TRUE) {
            echo "<script>alert('Robot updated successfully!'); window.location.href='index.php';</script>";
        } else {
            echo "Error: " . $connection->error;
        }
    } else {
        echo "<script>alert('Failed to upload image.');</script>";
    }
}

$connection->close();
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
            <div class="mb-3">
                <label for="robot_id" class="form-label">Robot ID</label>
                <input type="text" class="form-control" id="robot_id" name="robot_id" value="<?php echo htmlspecialchars($id); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="robot_name" class="form-label">Name</label>
                <input type="text" class="form-control" id="robot_name" name="robot_name" value="<?php echo htmlspecialchars($name); ?>">
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
                <label for="robot_image" class="form-label">Upload Image</label>
                <input type="file" class="form-control" id="robot_image" name="robot_image">
                <?php if (!empty($image_error)): ?>
                    <div class="alert alert-danger mt-2"><?php echo $image_error; ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>
</html>