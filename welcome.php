<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("Location: login.php");
    exit;
}

include('connection.php');
include "navbar.php";

$username = $_SESSION['username'];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['documents'])) {
    foreach ($_FILES['documents']['name'] as $key => $filename) {
        $tmp_name = $_FILES['documents']['tmp_name'][$key];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        $file_path = 'uploads/' . uniqid() . '.' . $file_ext;

        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($tmp_name, $file_path)) {
            // Insert each file into the database
            $sql = "INSERT INTO user_documents (username, document_path) VALUES ('$username', '$file_path')";
            mysqli_query($conn, $sql);
        }
    }
}

// Handle file deletion
if (isset($_POST['delete'])) {
    $doc_path = $_POST['document_path'];
    
    // Check if the file exists before trying to delete it
    if (file_exists($doc_path)) {
        $sql = "DELETE FROM user_documents WHERE document_path = '$doc_path' AND username = '$username'";
        if (mysqli_query($conn, $sql)) {
            if (unlink($doc_path)) {
                // Successfully deleted the file from the server
                echo "<div class='alert alert-success'>File deleted successfully.</div>";
            } else {
                // File could not be deleted
                echo "<div class='alert alert-danger'>Error deleting file: " . $doc_path . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error deleting from database: " . mysqli_error($conn) . "</div>";
        }
    } else {
        // File does not exist
        echo "<div class='alert alert-warning'>File does not exist: " . $doc_path . "</div>";
    }
}

// Fetch user's uploaded documents from the database
$sql = "SELECT document_path FROM user_documents WHERE username = '$username'";
$result = mysqli_query($conn, $sql);
$documents = [];
while ($row = mysqli_fetch_assoc($result)) {
    $documents[] = $row['document_path'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .delete-button {
            margin-left: 10px; /* Space between file name and delete button */
            background-color: #dc3545; /* Bootstrap danger color */
            color: white; /* White text color */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            padding: 5px 10px; /* Padding for better appearance */
        }
        
        .delete-button:hover {
            background-color: #c82333; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

        <!-- Document Upload Form -->
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="documents" class="form-label">Upload Documents</label>
                <input type="file" class="form-control" id="documents" name="documents[]" multiple required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <!-- Display Uploaded Documents -->
        <h3 class="mt-5">Your Uploaded Documents:</h3>
        <?php if (!empty($documents)): ?>
            <ul>
                <?php foreach ($documents as $doc): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($doc); ?>" target="_blank"><?php echo htmlspecialchars(basename($doc)); ?></a>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="document_path" value="<?php echo htmlspecialchars($doc); ?>">
                            <button type="submit" name="delete" class="delete-button">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No documents uploaded yet.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
