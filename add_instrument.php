<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $instrumentName = $_POST['instrumentName'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];
    
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO instruments (instrument_name, `condition`, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $instrumentName, $condition, $quantity);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Instrument added successfully!');
                window.location.href = 'instruments.php';
              </script>";
    } else {
        echo "<script>
                alert('Error adding instrument: " . $stmt->error . "');
                window.location.href = 'instruments.php';
              </script>";
    }
    
    $stmt->close();
}

$conn->close();
?> 