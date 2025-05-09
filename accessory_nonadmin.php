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
            <span>Deleted </span>
        </a>
    </div>

   <!-- Main Content -->
   <div class="main-content">
        <!-- Header -->
        <div class="header">
            <img src="picture-1.png" alt="Logo" class="header-logo">
            <div class="section-indicator">Accessories</div>
            <h2>CDM Chorale Inventory System</h2>
           
            <a href="index.php" class="logout">Log Out</a>
        </div>

        <!-- Card Section -->
        <div class="card-container">
            <?php
            include 'db_connect.php';
            // Fetch instruments from database
            $sql = "SELECT * FROM accessories";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Determine image to display
                    $imagePath = !empty($row["image_path"]) ? $row["image_path"] : 'picture-1.png';
                    
                    echo "<div class='card'>";
                    echo "<img src='" . $imagePath . "' alt='Accessory'>";
                    echo "<h3>" . $row["deco_name"] . "</h3>";
                    echo "<p>Condition: " . $row["condition"] . "</p>";
                    echo "<p>Quantity: " . $row["quantity"] . "</p>";
                    
                    // Check if quantity is 0, if so disable the borrow button
                    if ($row["quantity"] > 0) {
                        echo "<button class='borrow-btn' data-name='" . $row["deco_name"] . "'>Borrow</button>";
                    } else {
                        echo "<button class='borrow-btn disabled' disabled>Out of Stock</button>";
                    }
                    
                    echo "</div>";
                }
            } else {
                echo "<p>No instruments found</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>
   
    <!-- Modal -->
    <div id="accessoriesBorrowModal" class="modal">
        <div class="modal-content">
            <h2>Borrow item</h2>
            <form action="save_history.php" method="POST">
                <input type="hidden" name="type" value="BORROW">
                <div class="form-group">
                    <label for="borrowedBy">Borrowed by:</label>
                    <input type="text" id="borrowedBy" name="borrowedBy" required>
                </div>
                
                <div class="form-group">
                    <label for="date">Date to be Borrowed:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="date">Date to be Returned:</label>
                    <input type="date" id="date_return" name="date_return" required>
                </div>
                

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" class="form-select" onchange="loadItems()" required>
                        <option value="Accessories" selected>Accessories</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="itemName">Item name:</label>
                    <input type="text" id="itemName" name="itemName" readonly required>
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
                    <label for="remarks">Remarks:</label>
                    <textarea id="remarks" name="remarks" rows="4"></textarea>
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Get the modal
    var modal = document.getElementById("accessoriesBorrowModal");

    // Get all buttons that should open the modal
    var btns = document.getElementsByClassName("borrow-btn");
    
    // Student Number validation
    document.getElementById('sn').addEventListener('input', function() {
        var value = this.value;
        if (value.length > 0 && (value.length < 10 || value.length > 11)) {
            this.setCustomValidity('Student Number must be 10-11 characters long');
        } else {
            this.setCustomValidity('');
        }
    });

    // Function to load items based on selected category
    function loadItems() {
        const category = document.getElementById('category').value;
        
        // Clear quantity options
        document.getElementById('quantity').innerHTML = '<option value="">-- Select quantity --</option>';
        
        // Fetch items for the selected category
        fetch('get_items.php?category=' + category)
            .then(response => response.json())
            .then(data => {
                // Store the data for later use when selecting quantities
                window.itemsData = data;
                
                // If opened from a specific accessory card, update quantity options
                const preselectedItem = document.getElementById("itemName").value;
                if (preselectedItem) {
                    updateQuantityDropdown(preselectedItem);
                }
            })
            .catch(error => {
                console.error('Error loading items:', error);
                alert('Error loading items. Please try again.');
            });
    }
    
    // Function to update quantity dropdown based on selected item
    function updateQuantityDropdown(itemName) {
        const quantitySelect = document.getElementById('quantity');
        
        // Clear existing options
        quantitySelect.innerHTML = '<option value="">-- Select quantity --</option>';
        
        if (itemName) {
            // Find the selected item's data
            const itemData = window.itemsData ? window.itemsData.find(item => item.name === itemName) : null;
            
            if (itemData) {
                const maxQuantity = parseInt(itemData.quantity);
                // Create options from 1 to maxQuantity
                for (let i = 1; i <= maxQuantity; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    quantitySelect.appendChild(option);
                }
                
                // Select the first quantity option by default
                if (maxQuantity > 0) {
                    quantitySelect.value = "1";
                }
            }
        }
    }

    // When the user clicks on a button, open the modal
    for (var i = 0; i < btns.length; i++) {
        btns[i].onclick = function() {
            modal.style.display = "block";
            
            // Set category to Accessories by default
            document.getElementById("category").value = "Accessories";
            
            // Set the item name directly in the text field
            const itemName = this.getAttribute("data-name");
            document.getElementById("itemName").value = itemName;
            
            // Load the items and update quantity dropdown
            loadItems();
        }
    }

    // When the user clicks anywhere outside of the modal content, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    // Load items on page load for the borrow modal
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize items if the borrow modal is shown immediately
        if (modal.style.display === "block") {
            loadItems();
        }
    });
    </script>
</body>
</html>

<?php

?>