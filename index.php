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
    <a class="btn btn-success" href="/roboshop/register.php" role="button">Register</a>
    <?php
session_start();
?>

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
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
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

                // read all row frmo database table
                $sql = "SELECT * FROM robots";
                $result = $connection->query($sql);

                if (!$result) {
                    die("Query failed: " . $connection->error);
                }

                // read data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['price'] . "</td>";
                    echo "<td>";
                    echo "<a class='btn btn-primary btn-sm' href='/roboshop/edit.php?id=" . $row['id'] . "'>Edit</a>";
                    echo "<a class='btn btn-danger btn-sm' href='/roboshop/delete.php?id=" . $row['id'] . "'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>