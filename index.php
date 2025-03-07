<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Allowed file types and max size (5MB)
        $allowed_types = ["image/png", "image/jpeg", "image/webp"];
        $max_size = 5 * 1024 * 1024; // 5MB

        // File details
        $file_name = basename($_FILES["image"]["name"]);
        $file_size = $_FILES["image"]["size"];
        $file_tmp = $_FILES["image"]["tmp_name"];
        $file_type = mime_content_type($file_tmp);

        // Check file type
        if (!in_array($file_type, $allowed_types)) {
            echo "Error: Only PNG, JPG, and WEBP files are allowed.";
            exit;
        }

        // Check file size
        if ($file_size > $max_size) {
            echo "Error: File size must not exceed 5MB.";
            exit;
        }

        // Create uploads directory if it doesn't exist
        $upload_directory = "uploads/";
        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }

        // Save image in uploads folder
        $file_path = $upload_directory . $file_name;
        if (move_uploaded_file($file_tmp, $file_path)) {
            echo "Success: Image uploaded successfully!";
        } else {
            echo "Error: Failed to upload the image.";
        }
    } else {
        echo "Error: No file uploaded or an error occurred.";
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
    <script>
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById("robotTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 0; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName("td");
                if (td.length > 0) {
                    let match = false;
                    for (let j = 0; j < td.length; j++) {
                        const cell = td[j];
                        if (cell && cell.textContent.toLowerCase().indexOf(filter) > -1) {
                            match = true;
                        }
                    }
                    tr[i].style.display = match ? "" : "none";
                }
            }
        }
    </script>
</head>
<body>
    <div class="container my-5">
        <a class="btn btn-success" href="/roboshop/register.php" role="button">Register</a>
        <div class="d-flex justify-content-end">
            <?php if (isset($_SESSION["user_id"])): ?>
                <p>Welcome, <?php echo $_SESSION["username"]; ?>!</p>
                <a class="btn btn-danger" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-primary" href="login.php">Login</a>
                <a class="btn btn-success" href="register.php">Register</a>
            <?php endif; ?>
        </div>

        <h2>List of Robots</h2>
        <a class="btn btn-primary" href="/roboshop/create.php" role="button">Add Robot</a>
        <br><br>

        <input type="text" id="searchInput" onkeyup="searchTable()" class="form-control" placeholder="Search for robots...">
        <br>

        <table class="table" id="robotTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $connection = new mysqli("localhost", "root", "Gogliko123$", "roboshop");

                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                $sql = "SELECT * FROM robots";
                $result = $connection->query($sql);

                if (!$result) {
                    die("Query failed: " . $connection->error);
                }

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['price'] . "</td>";
                    echo "<td><img src='" . $row['image_path'] . "' width='50'></td>";
                    echo "<td>" . $row['createdAt'] . "</td>";
                    echo "<td>" . $row['updatedAt'] . "</td>";
                    echo "<td>";
                    echo "<a class='btn btn-primary btn-sm' href='/roboshop/edit.php?id=" . $row['id'] . "'>Edit</a> ";
                    echo "<a class='btn btn-danger btn-sm' href='/roboshop/delete.php?id=" . $row['id'] . "'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <footer class="text-center py-4">
        <a href="gallery.php" class="btn btn-success">View Robot Gallery</a>
    </footer>
</body>
</html>