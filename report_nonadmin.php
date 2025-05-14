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
    <style>
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
            <span>Deleted</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
       <!-- Header -->
       <?php $section_title = 'Instruments'; include 'header.php'; ?>

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
                    <select id="category" name="category" class="form-select" onchange="loadItems()">
                        <option value="Instruments">Instruments</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Clothing">Clothing</option>
                    </select>
                </div>
                
                    
                    <div class="form-group">
                        <label for="itemName">Item name:</label>
                        <select id="itemName" name="itemName" class="form-select" required>
                            <option value="">-- Select an item --</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <select id="quantity" name="quantity" class="form-select" required>
                            <option value="">-- Select quantity --</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sn">Student Number:</label>
                        <input type="text" id="sn" name="sn" minlength="10" maxlength="11" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Item Status</label>
                        <select class="form-control" id="status" name="status" onchange="toggleCustomStatus()" required>
                            <option value="needs repair">Needs Repair</option>
                            <option value="needs replacement">Needs Replacement</option>
                            <option value="not working">Not Working</option>
                            <option value="working">Working</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <!--
                    <div class="form-group" id="customStatusGroup" style="display: none;">
                        <label for="customStatus">Specify Status:</label>
                        <input type="text" id="customStatus" name="custom_status" class="form-control" placeholder="Enter custom status...">
                       
                    </div>
    -->
                    
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
<script>
    // Function to load items based on selected category
    function loadItems() {
        const category = document.getElementById('category').value;
        const itemSelect = document.getElementById('itemName');
        
        // Clear existing options
        itemSelect.innerHTML = '<option value="">-- Select an item --</option>';
        // Clear quantity options too
        document.getElementById('quantity').innerHTML = '<option value="">-- Select quantity --</option>';
        
        // Fetch items for the selected category
        fetch('get_items.php?category=' + category)
            .then(response => response.json())
            .then(data => {
                // Store the data for later use when selecting quantities
                window.itemsData = data;
                
                // Add new options
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = item.name;
                    option.dataset.quantity = item.quantity;
                    itemSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading items:', error));
    }
    
    // Function to update quantity dropdown based on selected item
    function updateQuantityDropdown() {
        const itemSelect = document.getElementById('itemName');
        const quantitySelect = document.getElementById('quantity');
        const selectedItem = itemSelect.options[itemSelect.selectedIndex];
        
        // Clear existing options
        quantitySelect.innerHTML = '<option value="">-- Select quantity --</option>';
        
        if (selectedItem && selectedItem.value) {
            // Find the selected item's data
            const itemData = window.itemsData.find(item => item.name === selectedItem.value);
            
            if (itemData) {
                const maxQuantity = parseInt(itemData.quantity);
                // Create options from 1 to maxQuantity
                for (let i = 1; i <= maxQuantity; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    quantitySelect.appendChild(option);
                }
            }
        }
    }
    
    // Function to fill in the user's full name from profile
    function loadUserFullName() {
        fetch('get_user_profile.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('borrowedBy').value = data.full_name;
                }
            })
            .catch(error => {
                console.error('Error loading user profile:', error);
            });
    }
    
    // Add event listener for item change
    document.getElementById('itemName').addEventListener('change', updateQuantityDropdown);
    
    // Load items on page load
    loadItems();

    // Load the user's full name on page load
    loadUserFullName();
    
    // Toggle custom status field
    function toggleCustomStatus() {
        const statusSelect = document.getElementById('status');
        const customStatusGroup = document.getElementById('customStatusGroup');
        
        if (statusSelect.value === 'other') {
            customStatusGroup.style.display = 'block';
            document.getElementById('customStatus').setAttribute('required', 'required');
        } else {
            customStatusGroup.style.display = 'none';
            document.getElementById('customStatus').removeAttribute('required');
        }
    }

    // Student Number validation
    document.getElementById('sn').addEventListener('input', function() {
        var value = this.value;
        if (value.length > 0 && (value.length < 10 || value.length > 11)) {
            this.setCustomValidity('Student Number must be 10-11 characters long');
        } else {
            this.setCustomValidity('');
        }
    });

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(event) {
        const statusSelect = document.getElementById('status');
        const customStatusInput = document.getElementById('customStatus');
        
        if (statusSelect.value === 'other' && customStatusInput.value.trim() === '') {
            event.preventDefault();
            alert('Please specify a custom status');
            customStatusInput.focus();
        }
    });
</script>