<?php
// deleted_items_nonadmin.php
session_start();
include 'db_connect.php';

// Fetch deleted items
$sql = "SELECT id, item_id, item_name, item_type, quantity, condition_status, image_path, deleted_at, deleted_by, reason, details FROM deleted_items ORDER BY deleted_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives</title>
    <link rel="stylesheet" href="instruments_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .table-container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: white;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #444;
        }
        th {
            background-color: rgba(44, 36, 116, 0.9);
            color: white;
            font-weight: bold;
        }
        td {
            background-color: rgba(5, 5, 5, 0.7);
        }
        tr:hover td {
            background-color: rgba(44, 36, 116, 0.7);
        }
        .section-title {
            color: white;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
            font-size: 24px;
        }
        .item-image {
            max-width: 80px;
            max-height: 80px;
            border-radius: 5px;
        }
        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .type-instrument {
            background-color: #4CAF50;
            color: white;
        }
        .type-accessory {
            background-color: #2196F3;
            color: white;
        }
        .type-clothing {
            background-color: #9C27B0;
            color: white;
        }
        .type-member {
            background-color: #FF9800;
            color: white;
        }
          /* Sidebar styles */
          .sidebar {
         width: 80px;
         background-color: rgba(44, 36, 116, 0.9); /* Semi-transparent blue background */
        height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 30px;
        position: fixed;
        left: 0;
        transition: width 0.3s ease;
        border-right: 4px solid #ffcc00; /* Yellow line at the right edge */
        }
        
        .sidebar:hover {
            width: 200px;
        }
        .icon-btn {
            color: white;
            text-decoration: none;
            padding: 15px;
            width: 100%;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
        }
        .icon-btn i {
            font-size: 24px;
            margin-right: 15px;
            min-width: 24px;
            transition: opacity 0.3s ease;
        }
        .icon-btn span {
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
            position: absolute;
            left: 60px;
        }
        .sidebar:hover .icon-btn span {
            opacity: 1;
        }
        .sidebar:hover .icon-btn i {
            opacity: 0;
        }
        .icon-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
            width: 170px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="instruments_nonadmin.php" class="icon-btn">
            <i class="fas fa-guitar"></i>
            <span>Instruments</span>
        </a>
        <a href="accessory_nonadmin.php" class="icon-btn">
            <i class="fas fa-gem"></i>
            <span>Accessories</span>
        </a>
        <a href="clothing_nonadmin.php" class="icon-btn">
            <i class="fas fa-tshirt"></i>
            <span>Clothing</span>
        </a>
        <a href="members_nonadmin.php" class="icon-btn">
            <i class="fas fa-user"></i>
            <span>Members</span>
        </a>
        <a href="report_nonadmin.php" class="icon-btn">
            <i class="fas fa-file-alt"></i>
            <span>Report</span>
        </a>
        <a href="history_nonadmin.php" class="icon-btn">
            <i class="fas fa-clock"></i>
            <span>History</span>
        </a>
        <a href="deleted_items_nonadmin.php" class="icon-btn">
            <i class="fas fa-trash-alt"></i>
            <span>Archives</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php $section_title = 'Archives'; include 'header_nonadmin.php'; ?>

        <!-- Feedback Messages -->
        <?php
        if(isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">';
            echo $_SESSION['success_message'];
            echo '<button type="button" class="close" onclick="this.parentElement.style.display=\'none\';">&times;</button>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        
        if(isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">';
            echo $_SESSION['error_message'];
            echo '<button type="button" class="close" onclick="this.parentElement.style.display=\'none\';">&times;</button>';
            echo '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <!-- Deleted Items Table -->
        <div class="table-container">
            <h2 class="section-title">Deleted Items</h2>
            
            <?php
            // Display success message if set
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert success-alert" style="background-color: rgba(76, 175, 80, 0.8); color: white; padding: 12px; margin-bottom: 15px; border-radius: 5px; text-align: center;">';
                echo '<i class="fas fa-check-circle"></i> ' . $_SESSION['success_message'];
                echo '</div>';
                unset($_SESSION['success_message']); // Clear the message after displaying
            }
            
            // Display error message if set
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert error-alert" style="background-color: rgba(244, 67, 54, 0.8); color: white; padding: 12px; margin-bottom: 15px; border-radius: 5px; text-align: center;">';
                echo '<i class="fas fa-exclamation-triangle"></i> ' . $_SESSION['error_message'];
                echo '</div>';
                unset($_SESSION['error_message']); // Clear the message after displaying
            }
            ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Type</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                        <th>Reason for Deletion</th>
                        <th>Deleted By</th>
                        <th>Deleted At</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            
                            // Image column
                            $imagePath = !empty($row['image_path']) ? $row['image_path'] : 'picture-1.png';
                            echo "<td><img src='" . $imagePath . "' class='item-image' alt='Item image'></td>";
                            
                            // Item type with styled badge
                            echo "<td>";
                            $type = $row['item_type'];
                            echo "<span class='type-badge type-" . $type . "'>" . ucfirst($type) . "</span>";
                            echo "</td>";
                            
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['condition_status']) . "</td>";
                            
                            // Reason column
                            echo "<td>" . htmlspecialchars($row['reason'] ?? 'No reason provided') . "</td>";
                            
                            echo "<td>" . htmlspecialchars($row['deleted_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['deleted_at']) . "</td>";
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center;'>No deleted items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const header = document.querySelector('.header');
    
    sidebar.addEventListener('mouseenter', function() {
        sidebar.style.width = '200px';
       
        header.style.marginLeft = '110px'; // Push header to match sidebar expansion
    });
    
    sidebar.addEventListener('mouseleave', function() {
        sidebar.style.width = '80px';
       
        header.style.marginLeft = '-10px';
    });
});
</script>
</body>
</html>
<?php
$conn->close();
?> 