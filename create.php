<?php
session_start(); // Start the session

if (!isset($_SESSION["user_id"])) {
    // Redirect users who are not logged in
    header("Location: login.php");
    exit();
}
?>

<?php
$name = "";
$type = "";
$price = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $type = trim($_POST["type"]);
    $price = trim($_POST["price"]);

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

        // Create connection
        $connection = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Use prepared statement to prevent SQL injection
        $sql = "INSERT INTO robots (name, type, price) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssd", $name, $type, $price); // 'ssd' means: string, string, double

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

        <form method="post">
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
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
</body>
</html>