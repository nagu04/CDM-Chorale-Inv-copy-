<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link rel="stylesheet" href="instruments_style.css">
    <!-- Font Awesome for icons -->
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

        <!-- Report Form -->
        <div style="display: flex; justify-content: center; align-items: start; padding-top: 20px;">
            <div class="modal-content" style="margin: 0;">
                <h2 style="text-align: center; text-transform: uppercase;">Report item</h2>
                <form action="save_history.php" method="POST">
                    <input type="hidden" name="type" value="REPORT">
                    <div class="form-group">
                        <label for="borrowedBy">Reported by:</label>
                        <input type="text" id="borrowedBy" name="borrowedBy" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" class="form-select">
                        <option value="Instruments">Instruments</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Clothing">Clothing</option>
                    </select>
                </div>
                
                    
                    <div class="form-group">
                        <label for="itemName">Item name:</label>
                        <input type="text" id="itemName" name="itemName" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sn">SN:</label>
                        <input type="text" id="sn" name="sn">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <input type="text" id="status" name="status" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="remarks">Remarks:</label>
                        <textarea id="remarks" name="remarks" rows="4"></textarea>
                    </div>
                    
                    <div class="submit-container">
                        <button type="submit" class="submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>