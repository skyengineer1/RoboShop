<?php
$connection = new mysqli("localhost", "root", "Gogliko123$", "roboshop");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$sql = "SELECT name, image_path FROM robots";
$result = $connection->query($sql);

if (!$result) {
    die("Query failed: " . $connection->error);
}

$robots = $result->fetch_all(MYSQLI_ASSOC);

$connection->close();
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
    <!-- Back to Menu Button -->
    <a href="index.php" class="btn btn-success">Back to Menu</a>
    <div class="container my-5">
        <h2>Robot Gallery</h2>
        <div class="gallery">
            <?php foreach ($robots as $robot): ?>
                <div class="gallery-item">
                    <img src="<?php echo htmlspecialchars($robot['image_path']); ?>" alt="<?php echo htmlspecialchars($robot['name']); ?>">
                    <h5><?php echo htmlspecialchars($robot['name']); ?></h5>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>