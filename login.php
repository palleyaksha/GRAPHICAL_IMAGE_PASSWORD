<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: welcome.php");
    exit;
}

$login = false;
include('connection.php');

if (isset($_POST['submit'])) {
    $username = $_POST['user'];
    $password = $_POST['pass'];
    $sql = "SELECT * FROM userss WHERE username = '$username' OR email = '$username'";  
    $result = mysqli_query($conn, $sql);  
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
    $count = mysqli_num_rows($result);  

    if ($row) {  
        if (password_verify($password, $row["password"])) {
            $login = true;
            session_start();

            // Store username and image path in the session
            $_SESSION['username'] = $row['username'];
            $_SESSION['image'] = $row['file'];  // Assuming the image filename is stored in the 'file' column
            $_SESSION['loggedin'] = true;

            // Redirect to the image-based authentication page
            header("Location: auth_image.php");
            exit();
        } else {
            // Invalid password
            echo '<script>
                    alert("Login failed. Invalid password!");
                    window.location.href = "login.php";
                  </script>';
        }
    } else {  
        // Invalid username
        echo '<script>
                alert("Login failed. Invalid username or email!");
                window.location.href = "login.php";
              </script>';
    }     
}
?>

<?php 
include("connection.php");
include("navbar.php");
?>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <br><br>
    <div id="form">
        <h1 id="heading">Login Form</h1>
        <form name="form" action="login.php" method="POST" required>
            <label>Enter Username/Email: </label>
            <input type="text" id="user" name="user" required></br></br>
            <label>Password: </label>
            <input type="password" id="pass" name="pass" required></br></br>
            <input type="submit" id="btn" value="Login" name="submit"/>
        </form>
    </div>
</body>
</html>
