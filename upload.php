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
    <title>File Upload</title>
</head>
<body>
    <h2>Upload Image</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <label for="image">Choose an image to upload:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
