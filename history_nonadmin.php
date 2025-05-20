<?php
// history.php
session_start();
include 'db_connect.php';

// Fetch borrowed items
$borrowed_sql = "SELECT history_id, type, borrowed_by, date, date_return, category, item_name, quantity, sn, status, remarks, created_at, is_approved FROM history WHERE type = 'BORROW' ORDER BY created_at DESC";
$borrowed_result = $conn->query($borrowed_sql);

// Fetch reported items
$reported_sql = "SELECT history_id, type, borrowed_by, date, date_return, category, item_name, quantity, sn, status, remarks, created_at, is_approved FROM history WHERE type = 'REPORT' ORDER BY created_at DESC";
$reported_result = $conn->query($reported_sql);

// Fetch approved borrowed items
$approved_borrowed_sql = "SELECT history_id, type, borrowed_by, date, date_return, category, item_name, quantity, sn, status, remarks, created_at, is_approved FROM history WHERE type = 'BORROW' AND is_approved = 1 ORDER BY created_at DESC";
$approved_borrowed_result = $conn->query($approved_borrowed_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
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
        /* Rest of the existing styles */
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
       <?php $section_title = 'History'; include 'header_nonadmin.php'; ?>

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
        <!-- History Tables -->
        <div class="table-container">
            <!-- Borrowed Items Table -->
            <h2 class="section-title">Borrow Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Borrowed By</th>
                        <th>Date to Borrow</th>
                        <th>Date to Return</th>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Student Number</th>
                        <th>Remarks</th>
                        <th>Approval</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    if ($borrowed_result->num_rows > 0) {
                        while($row = $borrowed_result->fetch_assoc()) {
                            if ($row['is_approved']) continue; // Only show unapproved here
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['borrowed_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date_return']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sn']) . "</td>";
                            
                            echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                            echo "<td>" . ($row['is_approved'] ? "Approved" : "Pending") . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>
                                    <button class='edit-btn' onclick='openEditModal(" . $row['history_id'] . ", " . json_encode($row) . ")'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <button class='remove-btn' onclick='confirmDelete(" . $row['history_id'] . ")'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align: center;'>No borrowed items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
             <!-- Borrowed Items Table (Approved) -->
             <h2 class="section-title">Borrowed Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Borrowed By</th>
                        <th>Date to Borrow</th>
                        <th>Date to Return</th>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Student Number</th>
                        <th>Remarks</th>
                        <th>Approval</th>
                        <th>Date Created</th>
                       
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($approved_borrowed_result->num_rows > 0) {
                        while($row = $approved_borrowed_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['borrowed_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date_return']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sn']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                            echo "<td>Approved</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>";
                           
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align: center;'>No borrowed items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Reported Items Table -->
            <h2 class="section-title">Reported Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Reported By</th>
                        <th>Date Reported</th>
                        <th>Date Returned</th>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Student Number</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Approval</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($reported_result->num_rows > 0) {
                        while($row = $reported_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['borrowed_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date_return'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sn']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                            echo "<td>" . ($row['is_approved'] ? "Resolved" : "Pending") . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>
                                    <button class='edit-btn' onclick='openEditModal(" . $row['history_id'] . ", " . json_encode($row) . ")'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <button class='remove-btn' onclick='confirmDelete(" . $row['history_id'] . ")'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11' style='text-align: center;'>No reported items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>EDIT ITEM</h2>
            <form id="editForm" action="update_history.php" method="POST">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="type" id="editType">
                <div class="form-group">
                    <label for="editBorrowedBy">Borrowed/Reported by:</label>
                    <input type="text" id="editBorrowedBy" name="borrowedBy" required>
                </div>
                
                <div class="form-group">
                    <label for="editDate">Date:</label>
                    <input type="date" id="editDate" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="editCategory">Category:</label>
                    <input type="text" id="editCategory" name="category" required>
                </div>
                
                <div class="form-group">
                    <label for="editItemName">Item name:</label>
                    <input type="text" id="editItemName" name="itemName" required>
                </div>
                
                <div class="form-group">
                    <label for="editQuantity">Quantity:</label>
                    <select id="editQuantity" name="quantity" required>
                        <!-- Options will be populated via JavaScript -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="editSn">SN:</label>
                    <input type="text" id="editSn" name="sn">
                </div>
                
                <div class="form-group">
                    <label for="editStatus">Status:</label>
                    <input type="text" id="editStatus" name="status" required>
                </div>
                
                <div class="form-group">
                    <label for="editRemarks">Remarks:</label>
                    <textarea id="editRemarks" name="remarks" rows="4"></textarea>
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openEditModal(id, data) {
        document.getElementById('editId').value = id;
        document.getElementById('editType').value = data.type;
        document.getElementById('editBorrowedBy').value = data.borrowed_by;
        document.getElementById('editDate').value = data.date;
        document.getElementById('editCategory').value = data.category;
        document.getElementById('editItemName').value = data.item_name;
        
        // Fetch quantities from database based on category and item name
        fetchQuantities(data.category, data.item_name, data.quantity);
        
        document.getElementById('editSn').value = data.sn;
        document.getElementById('editStatus').value = data.status;
        document.getElementById('editRemarks').value = data.remarks;
        
        document.getElementById('editModal').style.display = 'flex';
    }
    
    function fetchQuantities(category, itemName, currentQuantity) {
        // Clear the dropdown first
        const quantityDropdown = document.getElementById('editQuantity');
        quantityDropdown.innerHTML = '';
        
        // Add a loading option
        const loadingOption = document.createElement('option');
        loadingOption.text = 'Loading...';
        quantityDropdown.add(loadingOption);
        
        // Fetch quantities via AJAX
        fetch(`get_item_quantity.php?category=${encodeURIComponent(category)}&item_name=${encodeURIComponent(itemName)}`)
            .then(response => response.json())
            .then(data => {
                // Clear the dropdown
                quantityDropdown.innerHTML = '';
                
                if (data.error) {
                    // If there's an error, add a default option with the current quantity
                    const option = document.createElement('option');
                    option.value = currentQuantity;
                    option.text = currentQuantity;
                    quantityDropdown.add(option);
                } else {
                    // Add options for each quantity
                    data.quantities.forEach(qty => {
                        const option = document.createElement('option');
                        option.value = qty;
                        option.text = qty;
                        
                        // Select the current quantity by default
                        if (qty == currentQuantity) {
                            option.selected = true;
                        }
                        
                        quantityDropdown.add(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching quantities:', error);
                
                // On error, add a default option with the current quantity
                quantityDropdown.innerHTML = '';
                const option = document.createElement('option');
                option.value = currentQuantity;
                option.text = currentQuantity;
                quantityDropdown.add(option);
            });
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            window.location.href = 'delete_history.php?id=' + id;
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        var modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
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
