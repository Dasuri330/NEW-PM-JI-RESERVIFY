<?php
    // Update this connection code with your actual credentials
    $con = mysqli_connect("localhost", "root", "", "test_site"); // Example with default XAMPP credentials

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_POST['submit']) && isset($_FILES['image'])) {
        $file_name = $_FILES['image']['name'];
        $tempname = $_FILES['image']['tmp_name'];
        $folder = 'Images/' . $file_name;

        // Ensure the file is uploaded before attempting to insert it into the database
        if (move_uploaded_file($tempname, $folder)) {
            // Insert the file name into the database
            $query = mysqli_query($con, "INSERT INTO images (file) VALUES ('$file_name')");

            if ($query) {
                // Display success message using JavaScript alert
                echo "<script>alert('File uploaded successfully');</script>";
            } else {
                echo "<h2>Database error: Could not save file</h2>";
            }
        } else {
            echo "<h2>File not uploaded</h2>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="testing_reservation.css">
</head>
<body>

    <div class="container">
        <h2>Upload Image</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" name="image" />
            </div>
            <div class="form-group">
                <button type="submit" name="submit">Submit</button>
            </div>
        </form>

        <div class="image-gallery">
            <?php
                $res = mysqli_query($con, "SELECT * FROM images");
                while ($row = mysqli_fetch_assoc($res)) {
            ?>
            <img src="Images/<?php echo $row['file']; ?>" alt="Uploaded Image" />
            <?php } ?>
        </div>
    </div>

</body>
</html>
