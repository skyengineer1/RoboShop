<?php
$id = "";
$name = "";
$type = "";
$price = "";
$error = "";

// Fetch data if the 'id' parameter is provided
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Create connection
    $connection = new mysqli("localhost", "root", "Gogliko123$", "roboshop");

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Fetch data for the robot with the given id
    $sql = "SELECT * FROM robots WHERE id=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id); // 'i' for integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $type = $row['type'];
        $price = $row['price'];
    } else {
        $error = "Robot not found!";
    }

    $stmt->close();
    $connection->close();
}

// If form is submitted, update the robot data
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $type = $_POST["type"];
    $price = $_POST["price"];

    // Validate input fields
    if (empty($name) || empty($type) || empty($price)) {
        $error = "All fields are required";
    } else {
        // Update the connection with your password (replace 'your_password' with the actual password)
        $connection = new mysqli("localhost", "root", "Gogliko123$", "roboshop");

        // Check connection
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Use prepared statement to prevent SQL injection
        // The SQL query is updated to UPDATE instead of INSERT
        $sql = "UPDATE robots SET name=?, type=?, price=? WHERE id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssdi", $name, $type, $price, $id); // 'ssdi' means: string, string, double, integer

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
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
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
    <button type="submit" class="btn btn-primary">Update</button>
</form>
    </div>
</body>
</html>