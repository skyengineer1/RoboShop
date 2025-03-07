<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "Gogliko123$";
    $database = "roboshop";

    // Create connection
    $connection = new mysqli($servername, $username, $password, $database);

    $sql = "DELETE FROM robots WHERE id=$id";
    $connection->query($sql);
}

header("Location: /roboshop/index.php");
exit();
?>