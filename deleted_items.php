<?php
// deleted_items.php
session_start();
include 'db_connect.php';

// Fetch deleted items
$sql = "SELECT id, item_id, item_name, item_type, quantity, condition_status, image_path, deleted_at, deleted_by, details FROM deleted_items ORDER BY deleted_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Items</title>
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
        <a href="instruments.php" class="icon-btn">
            <i class="fas fa-guitar"></i>
            <span>Instruments</span>
        </a>
        <a href="accessory.php" class="icon-btn">
            <i class="fas fa-gem"></i>
            <span>Accessories</span>
        </a>
        <a href="clothing.php" class="icon-btn">
            <i class="fas fa-tshirt"></i>
            <span>Clothing</span>
        </a>
        <a href="members.php" class="icon-btn">
            <i class="fas fa-user"></i>
            <span>Members</span>
        </a>
        <a href="report.php" class="icon-btn">
            <i class="fas fa-file-alt"></i>
            <span>Report</span>
        </a>
        <a href="history.php" class="icon-btn">
            <i class="fas fa-clock"></i>
            <span>History</span>
        </a>
        <a href="deleted_items.php" class="icon-btn">
            <i class="fas fa-trash-alt"></i>
            <span>Deleted</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <img src="picture-1.png" alt="Logo" class="header-logo">
            <div class="section-indicator">Deleted Items</div>
            <h2>CDM Chorale Inventory System</h2>
            <a href="index.php" class="logout">Log Out</a>
        </div>

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
                        <th>Item Name</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                        <th>Image</th>
                        <th>Deleted By</th>
                        <th>Deleted At</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td><span class='type-badge type-" . strtolower($row['item_type']) . "'>" . htmlspecialchars($row['item_type']) . "</span></td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['condition_status']) . "</td>";
                            echo "<td>";
                            if (!empty($row['image_path'])) {
                                echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='Item Image' class='item-image'>";
                            } else {
                                echo "No image";
                            }
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($row['deleted_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['deleted_at']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['details']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center;'>No deleted items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div style="text-align: right; margin-top: 20px;">
                <button class="delete-btn" onclick="confirmDeleteAll()" style="background-color: #f44336; padding: 10px 15px;">
                    <i class="fas fa-trash-alt"></i> Empty Trash
                </button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h2>Restore Item</h2>
            <p>Are you sure you want to restore this item to the inventory?</p>
            <div class="submit-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn" style="background-color: #ccc;" onclick="closeModal()">Cancel</button>
                <button type="button" class="submit-btn" id="confirmRestore">Restore</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Permanent Delete</h2>
            <p>Are you sure you want to permanently delete this item? This action cannot be undone.</p>
            <div class="submit-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn" style="background-color: #ccc;" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="submit-btn" style="background-color: #f44336;" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <!-- Delete All Confirmation Modal -->
    <div id="deleteAllModal" class="modal">
        <div class="modal-content">
            <h2>Empty Trash</h2>
            <p>Are you sure you want to permanently delete ALL items in the trash? This action cannot be undone.</p>
            <div class="submit-container" style="display: flex; justify-content: space-between;">
                <button type="button" class="submit-btn" style="background-color: #ccc;" onclick="closeDeleteAllModal()">Cancel</button>
                <button type="button" class="submit-btn" style="background-color: #f44336;" id="confirmDeleteAll">Delete All</button>
            </div>
        </div>
    </div>

    <script>
    // Get the modals
    var modal = document.getElementById("confirmModal");
    var deleteModal = document.getElementById("deleteModal");
    var deleteAllModal = document.getElementById("deleteAllModal");
    var confirmButton = document.getElementById("confirmRestore");
    var confirmDeleteButton = document.getElementById("confirmDelete");
    var confirmDeleteAllButton = document.getElementById("confirmDeleteAll");
    var itemToRestore = null;
    var itemToDelete = null;

    function restoreItem(id) {
        itemToRestore = id;
        modal.style.display = "flex";
        
        // Set up the confirmation button
        confirmButton.onclick = function() {
            window.location.href = 'restore_item.php?id=' + itemToRestore;
        }
    }

    function confirmDelete(id) {
        itemToDelete = id;
        deleteModal.style.display = "flex";
        
        // Set up the confirmation button
        confirmDeleteButton.onclick = function() {
            window.location.href = 'permanent_delete.php?id=' + itemToDelete;
        }
    }

    function closeModal() {
        modal.style.display = "none";
    }
    
    function closeDeleteModal() {
        deleteModal.style.display = "none";
    }

    function confirmDeleteAll() {
        deleteAllModal.style.display = "flex";
        
        // Set up the confirmation button
        confirmDeleteAllButton.onclick = function() {
            window.location.href = 'permanent_delete_all.php';
        }
    }

    function closeDeleteAllModal() {
        deleteAllModal.style.display = "none";
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        if (event.target == deleteModal) {
            deleteModal.style.display = "none";
        }
        if (event.target == deleteAllModal) {
            deleteAllModal.style.display = "none";
        }
    }
    </script>
</body>
</html>
<?php
$conn->close();
?> 