<?php
    session_start();
    if(isset($_SESSION['username'])){
        header("Location: welcome.php");
    }
?>

<?php
    include("connection.php");
    include('navbar.php');
    $imagePath = '';
    $gridImages = [];

    if(isset($_POST['submit'])){
        $username = mysqli_real_escape_string($conn, $_POST['user']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['pass']);
        $cpassword = mysqli_real_escape_string($conn, $_POST['cpass']);
        
        // Handle image upload
        $image = $_FILES['pic']['name'];  // Get the uploaded image name
        $tempname = $_FILES['pic']['tmp_name'];  // Temporary file name

        // Define the folder to store the uploaded image
        $folder = "images/".$image;

        $sql = "SELECT * FROM userss WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        $count_user = mysqli_num_rows($result);

        $sql = "SELECT * FROM userss WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        $count_email = mysqli_num_rows($result);

        if($count_user == 0 && $count_email == 0){
            if($password == $cpassword){
                // Hash the password
                $hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert the user along with the image filename
                $sql = "INSERT INTO userss (username, email, password, file) VALUES ('$username', '$email', '$hash', '$image')";
                $result = mysqli_query($conn, $sql);

                if($result){
                    // Move the uploaded image to the images folder
                    if(move_uploaded_file($tempname, $folder)){
                        $imagePath = $folder;  // Store the image path to display later
                        $gridImages = createImageGrid($imagePath, $username, $conn);  // Create and store the grid images
                        echo '<script>alert("Signup successful! Image uploaded and segmented successfully.");</script>';
                    } else {
                        echo '<script>alert("Image upload failed.");</script>';
                    }
                } else {
                    echo '<script>alert("Signup failed.");</script>';
                }
            } else {
                echo '<script>
                    alert("Passwords do not match.");
                    window.location.href = "signup.php";
                </script>';
            }
        } else {
            if($count_user > 0){
                echo '<script>
                    alert("Username already exists.");
                    window.location.href = "signup.php";
                </script>';
            }
            if($count_email > 0){
                echo '<script>
                    alert("Email already exists.");
                    window.location.href = "signup.php";
                </script>';
            }
        }
    }

    // Function to create a 3x3 grid from the uploaded image and save them in the database
    function createImageGrid($imagePath, $username, $conn) {
        list($width, $height, $type) = getimagesize($imagePath);
        $newWidth = $width / 3;
        $newHeight = $height / 3;
        $gridImages = [];

        // Create an image resource based on the image type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($imagePath);
                break;
            default:
                return []; // Return empty array if the image type is unsupported
        }

        for ($y = 0; $y < 3; $y++) {
            for ($x = 0; $x < 3; $x++) {
                $gridImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($gridImage, $source, 0, 0, $x * $newWidth, $y * $newHeight, $newWidth, $newHeight, $newWidth, $newHeight);
                
                // Generate unique filename for the grid image
                $gridFileName = "images/grid_{$x}_{$y}_" . uniqid() . ".jpg";
                imagejpeg($gridImage, $gridFileName);
                $gridImages[] = $gridFileName;  // Add to the list of grid images
                
                // Destroy the grid image resource to free memory
                imagedestroy($gridImage);

                // Save the grid image filename in the appropriate column in the database
                $column = "order" . ($y * 3 + $x);  // Determine the column name (order0 to order8)
                $sql = "UPDATE userss SET $column='$gridFileName' WHERE username='$username'";
                mysqli_query($conn, $sql);
            }
        }

        imagedestroy($source);
        return $gridImages;
    }

    // Function to retrieve grid images from the database for display
    function fetchGridImages($username, $conn) {
        $sql = "SELECT order0, order1, order2, order3, order4, order5, order6, order7, order8 FROM userss WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    // Fetch grid images for the current user
    if (isset($username)) {
        $gridImages = fetchGridImages($username, $conn);
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup with Image Upload and Grid Segmentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="form" class="container">
        <h1 id="heading" class="text-center">Signup Form</h1><br>
        <form name="form" action="signup.php" method="POST" enctype="multipart/form-data" class="mb-5">
            <div class="mb-3">
                <label>Enter Username: </label>
                <input type="text" id="user" name="user" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Enter Email: </label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Create Password: </label>
                <input type="password" id="pass" name="pass" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password: </label>
                <input type="password" id="cpass" name="cpass" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Upload Image: </label>
                <input type="file" id="pic" name="pic" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>

    <!-- Display uploaded and segmented grid images if available -->
    <?php if(!empty($gridImages)): ?>
        <div class="d-flex justify-content-center mt-5">
            <div class="text-center">
                <h3>Segmented Images:</h3>
                <div class="row">
                    <?php foreach($gridImages as $key => $gridImage): ?>
                        <div class="col-4">
                            <img src="<?php echo $gridImage; ?>" alt="Segmented Image <?php echo $key; ?>" style="width: 100px; height: 100px;">
                            <p><?php echo basename($gridImage); ?></p> <!-- Display the filename -->
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

