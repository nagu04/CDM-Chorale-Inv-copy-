<?php
// history.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="history_style.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="instruments.php" class="icon-btn">
            <i class="fas fa-guitar"></i>
        </a>
        <a href="accessory.php" class="icon-btn">
            <i class="fas fa-gem"></i>
        </a>
        <a href="clothing.php" class="icon-btn">
            <i class="fas fa-tshirt"></i>
        </a>
        <a href="members.php" class="icon-btn">
            <i class="fas fa-user"></i>
        </a>
        <a href="report.php" class="icon-btn">
            <i class="fas fa-file-alt"></i>
        </a>
        <a href="history.php" class="icon-btn">
            <i class="fas fa-clock"></i>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <img src="picture-1.png" alt="Logo" class="header-logo">
            <h2>CDM Chorale Inventory System</h2>
            
            <a href="index.php" class="logout">Log Out</a>
        </div>
<body>
    <div class="main-content">
        <h2>Borrowed Item Details</h2>
        <div class="details">
            <p><strong>Reported by:</strong> <?php echo htmlspecialchars($_POST['borrowedBy']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($_POST['date']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($_POST['category']); ?></p>
            <p><strong>Item name:</strong> <?php echo htmlspecialchars($_POST['itemName']); ?></p>
            <p><strong>Quantity:</strong> <?php echo htmlspecialchars($_POST['quantity']); ?></p>
            <p><strong>SN:</strong> <?php echo htmlspecialchars($_POST['sn']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($_POST['status']); ?></p>
            <p><strong>Remarks:</strong> <?php echo htmlspecialchars($_POST['remarks']); ?></p>
        </div>
    </div>
</body>
</html>
