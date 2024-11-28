<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include('connection.php');
include('navbar.php');
// Fetch the correct order of the puzzle pieces from the database
$username = $_SESSION['username'];
$sql = "SELECT order0, order1, order2, order3, order4, order5, order6, order7, order8 FROM userss WHERE username='$username'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Original correct order from signup
$correctOrder = [
    $row['order0'],
    $row['order1'],
    $row['order2'],
    $row['order3'],
    $row['order4'],
    $row['order5'],
    $row['order6'],
    $row['order7'],
    $row['order8']
];

// Check if the rearranged order is submitted
if (isset($_POST['rearrangedOrder'])) {
    $userOrder = explode(',', $_POST['rearrangedOrder']); // Array of image paths

    // Store the user-selected order in the database (columns sorder0 to sorder8)
    for ($i = 0; $i < count($userOrder); $i++) {
        $sorderColumn = "sorder" . $i;
        $sorderValue = mysqli_real_escape_string($conn, $userOrder[$i]); // Sanitize input
        $sql = "UPDATE userss SET $sorderColumn='$sorderValue' WHERE username='$username'";
        mysqli_query($conn, $sql);
    }

    // Fetch the user-selected order (now saved in the database)
    $sql = "SELECT sorder0, sorder1, sorder2, sorder3, sorder4, sorder5, sorder6, sorder7, sorder8 FROM userss WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $userRow = mysqli_fetch_assoc($result);

    // Compare each order with its corresponding sorder
    $isAuthenticated = true;
    for ($i = 0; $i < 9; $i++) {
        if ($userRow["sorder$i"] !== $correctOrder[$i]) {
            $isAuthenticated = false;
            break;
        }
    }

    // Redirect or alert based on authentication check
    if ($isAuthenticated) {
        header("Location: welcome.php");
        exit;
    } else {
        echo '<script>
                alert("Wrong authentication! Please try again.");
                window.location.href = "auth_image.php";
              </script>';
    }
} else {
    echo '<script>
            alert("No order submitted! Please complete the puzzle.");
            window.location.href = "auth_image.php";
          </script>';
}
?>
