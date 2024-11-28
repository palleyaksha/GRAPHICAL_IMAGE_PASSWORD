<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include('connection.php');
include('navbar.php');

// Get the stored images for the logged-in user
$username = $_SESSION['username'];
$sql = "SELECT order0, order1, order2, order3, order4, order5, order6, order7, order8 FROM userss WHERE username='$username'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Extract the segmented images from the database
$segmentedImages = [
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

// Shuffle the images for the user to solve the puzzle
$shuffledImages = $segmentedImages;
shuffle($shuffledImages);
?>

<html>
<head>
    <title>Image Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 50px;
        }
        h1 {
            margin-bottom: 20px;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            width: 90%;
            max-width: 600px;
        }
        .grid-item {
            cursor: pointer;
            position: relative;
            border: 1px solid #ddd;
            text-align: center;
        }
        .grid-item img {
            width: 100%;
            object-fit: cover; /* Ensures the full image is displayed */
            height: auto;
            max-height: 120px; /* Adjust this value if necessary */
        }
        .grid-item.highlight {
            border: 2px solid #007bff;
        }
        .image-number {
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 18px; /* Size of the number */
            font-weight: bold; /* Make it bold */
            color: #fff; /* Color of the number */
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent background */
            border-radius: 5px; /* Rounded corners */
            padding: 2px 5px; /* Padding around the number */
            display: none; /* Initially hidden */
        }
        .btn-login {
            margin-top: 20px;
        }
        .timer {
            font-size: 24px; /* Increased font size */
            font-weight: bold; /* Bold text */
            color: #ff5733; /* Stylish color */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); /* Add shadow for depth */
            margin-bottom: 20px;
            font-family: 'Arial', sans-serif; /* Stylish font-family */
        }
    </style>
    <script>
        let selectedOrder = [];
        let clickCount = 0;
        let timeLeft = 60; // 60 seconds timer
        let timerInterval;
        let timerPaused = false; // Track if the timer is paused

        function startTimer() {
            const timerElement = document.getElementById('timer');
            timerInterval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    alert("Time's up! Submitting your selection.");
                    document.getElementById('rearrangedOrder').value = selectedOrder.join(',');
                    document.getElementById('imageForm').submit(); // Automatically submit the form
                } else {
                    timerElement.innerText = `Time remaining: ${timeLeft} seconds`;
                }
                timeLeft -= 1;
            }, 1000);
        }

        document.addEventListener("DOMContentLoaded", function() {
            const gridItems = document.querySelectorAll('.grid-item');
            
            gridItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (!this.classList.contains('highlight')) {
                        clickCount++;
                        this.setAttribute('data-order', clickCount);
                        this.classList.add('highlight');
                        
                        // Get the image's src attribute
                        let fullSrc = this.querySelector('img').src;
                        
                        // Remove the base URL and extract the relative path
                        let relativeSrc = fullSrc.replace("http://localhost/signuplogin/", "");

                        // Store only the relative image path
                        selectedOrder.push(relativeSrc);

                        // Create a number element
                        const numberElement = document.createElement('div');
                        numberElement.classList.add('image-number');
                        numberElement.textContent = clickCount;

                        // Display the number in the clicked grid item
                        this.appendChild(numberElement);
                        numberElement.style.display = 'block'; // Show the number
                    }
                });
            });

            // Start the timer
            startTimer();

            // Pause the timer when the page is hidden
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    clearInterval(timerInterval);
                    timerPaused = true; // Set the timer as paused
                } else {
                    // If the timer was paused, restart it
                    if (timerPaused) {
                        startTimer();
                        timerPaused = false; // Reset the paused state
                    }
                }
            });
        });

        function setOrder() {
            document.getElementById('rearrangedOrder').value = selectedOrder.join(',');
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Rearrange the Image to Log In:</h1>
        <div id="timer" class="timer">Time remaining: 60 seconds</div> <!-- Timer display -->
        <div class="image-grid">
            <?php
                foreach ($shuffledImages as $index => $image) {
                    echo '<div class="grid-item" data-index="'.$index.'">
                              <img src="' . $image . '" alt="Segmented Image">
                          </div>';
                }
            ?>
        </div>
        <form action="verify_image.php" method="POST" id="imageForm">
            <input type="hidden" name="rearrangedOrder" id="rearrangedOrder">
            <button type="submit" class="btn btn-primary btn-login" onclick="setOrder()">Login</button>
        </form>
    </div>
</body>
</html>
