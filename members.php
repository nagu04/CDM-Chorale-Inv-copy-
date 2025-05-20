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
            <span>Archives</span>
        </a>
       
        <a href="manage_users.php" class="icon-btn">
            <i class="fas fa-users-cog"></i>
            <span>Manage Users</span>
        </a>
       
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php $section_title = 'Members'; include 'header.php'; ?>

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

         <!-- Action Buttons -->
         <div class="action-buttons">
            <button class="add-button" id="addButton">
                <i class="fas fa-plus"></i> Add
            </button>
        </div>

        <!-- Card Section -->
        <div class="card-container">
            <?php
            include 'db_connect.php';
            // Fetch members from database
            $sql = "SELECT * FROM members ORDER BY last_name, given_name";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Determine the image to display
                    $imagePath = !empty($row["image_path"]) ? $row["image_path"] : 'default image.jpg';
                    
                    // Replace the members_name references with the new name format
                    $display_name = $row['last_name'] . ', ' . $row['given_name'];
                    if (!empty($row['middle_initial'])) {
                        $display_name .= ' ' . $row['middle_initial'] . '.';
                    }
                    if (!empty($row['extension'])) {
                        $display_name .= ' ' . $row['extension'];
                    }
                    
                    echo "<div class='card'>";
                    echo "<img src='" . $imagePath . "' alt='Member'>"; 
                    echo "<h3>" . htmlspecialchars($display_name) . "</h3>";
                
                    echo "<p>Program: " . $row["program"] . "</p>";
                    echo "<p>Position: " . $row["position"] . "</p>";
                    echo "<button class='borrow-btn' data-id='" . $row["member_id"] . "' 
                          data-name='" . htmlspecialchars($display_name) . "' 
                          data-program='" . $row["program"] . "' 
                          data-position='" . $row["position"] . "' 
                          data-birthdate='" . (isset($row["birthdate"]) ? $row["birthdate"] : "") . "' 
                          data-address='" . (isset($row["address"]) ? $row["address"] : "") . "'>View Profile</button>";
                    echo "<div class='card-buttons'>";
                    echo "<button class='edit-btn' 
                          data-id='" . $row["member_id"] . "' 
                          data-last_name='" . htmlspecialchars($row["last_name"]) . "'
                          data-given_name='" . htmlspecialchars($row["given_name"]) . "'
                          data-middle_initial='" . htmlspecialchars($row["middle_initial"]) . "'
                          data-extension='" . htmlspecialchars($row["extension"]) . "'
                          data-program='" . $row["program"] . "' 
                          data-position='" . $row["position"] . "' 
                          data-birthdate='" . (isset($row["birthdate"]) ? $row["birthdate"] : "") . "' 
                          data-address='" . (isset($row["address"]) ? $row["address"] : "") . "'>Edit</button>";
                    echo "<button class='delete-btn' data-id='" . $row["member_id"] . "' data-name='" . htmlspecialchars($display_name) . "'>Delete</button>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No members found</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <h2>Member Profile</h2>
            <div class="profile-info">
                <label for="memberName">Name:</label>
                <p id="memberName"></p>
            </div>
            
            <div class="profile-info">
                <label for="memberProgram">Program:</label>
                <p id="memberProgram"></p>
            </div>
            
            <div class="profile-info">
                <label for="memberPosition">Position:</label>
                <p id="memberPosition"></p>
            </div>
            
            <div class="profile-info">
                <label for="memberBirthdate">Birthdate:</label>
                <p id="memberBirthdate"></p>
            </div>
            
            <div class="profile-info">
                <label for="memberAddress">Address:</label>
                <p id="memberAddress"></p>
            </div>
            
            <button class="submit-btn" id="closeProfileBtn">Close</button>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add Member</h2>
            <form action="save_member.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="addLastName">Last Name</label>
                    <input type="text" id="addLastName" name="last_name" required oninput="this.value = this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label for="addGivenName">Given Name</label>
                    <input type="text" id="addGivenName" name="given_name" required oninput="this.value = this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label for="addMiddleInitial">Middle Initial</label>
                    <input type="text" id="addMiddleInitial" name="middle_initial" maxlength="1" oninput="this.value = this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label for="addExtension">Extension (Jr., Sr., III, etc.)</label>
                    <input type="text" id="addExtension" name="extension" oninput="this.value = this.value.toUpperCase()">
                </div>
                
                <div class="form-group">
                    <label for="addProgram">Program:</label>
                    <select id="addProgram" name="program" required>
                        <option value="">Select Program</option>
                        <option value="Architecture">Architecture</option>
                        <option value="Civil Engineering">Civil Engineering</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                        <option value="Electronics Engineering">Electronics Engineering</option>
                        <option value="Environmental and Sanitary Engineering">Environmental and Sanitary Engineering</option>
                        <option value="Industrial Engineering">Industrial Engineering</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="addPosition">Position:</label>
                    <input type="text" id="addPosition" name="position" required>
                </div>
                
                <div class="form-group">
                    <label for="addBirthdate">Birthdate:</label>
                    <input type="date" id="addBirthdate" name="birthdate">
                </div>
                
                <div class="form-group">
                    <label for="addAddress">Address:</label>
                    <input type="text" id="addAddress" name="address">
                </div>
                
                <div class="form-group">
                    <label for="profileImage">Profile Image:</label>
                    <input type="file" id="profileImage" name="profile_image" accept="image/*">
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Member Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Delete Member</h2>
            <form action="delete_member.php" method="POST">
                <input type="hidden" id="deleteMemberId" name="member_id">
                <div class="form-group">
                    <label>Are you sure you want to delete:</label>
                    <p id="deleteMemberName" class="selected-item-name"></p>
                </div>
                
                <div class="form-group">
                    <label for="delete_reason">Reason for removal:</label>
                    <textarea id="delete_reason" name="delete_reason" rows="3" style="width: 100%; padding: 8px; border-radius: 4px; background: rgba(5, 5, 5, 0.7); color: white; border: 1px solid #444;" placeholder="Please provide a reason for removing this member..." required></textarea>
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn" style="background-color: #ff4444; color: white;">Delete Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Member</h2>
            <form action="update_member.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editMemberId" name="member_id">
                <div class="form-group">
                    <label for="editLastName">Last Name</label>
                    <input type="text" id="editLastName" name="last_name" required oninput="this.value = this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label for="editGivenName">Given Name</label>
                    <input type="text" id="editGivenName" name="given_name" required oninput="this.value = this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label for="editMiddleInitial">Middle Initial</label>
                    <input type="text" id="editMiddleInitial" name="middle_initial" maxlength="1" oninput="this.value = this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label for="editExtension">Extension (Jr., Sr., III, etc.)</label>
                    <input type="text" id="editExtension" name="extension" oninput="this.value = this.value.toUpperCase()">
                </div>
                
                <div class="form-group">
                    <label for="editProgram">Program:</label>
                    <select id="editProgram" name="program" required>
                        <option value="">Select Program</option>
                        <option value="Architecture">Architecture</option>
                        <option value="Civil Engineering">Civil Engineering</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                        <option value="Electronics Engineering">Electronics Engineering</option>
                        <option value="Environmental and Sanitary Engineering">Environmental and Sanitary Engineering</option>
                        <option value="Industrial Engineering">Industrial Engineering</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="editPosition">Position:</label>
                    <input type="text" id="editPosition" name="position" required>
                </div>
                
                <div class="form-group">
                    <label for="editBirthdate">Birthdate:</label>
                    <input type="date" id="editBirthdate" name="birthdate">
                </div>
                
                <div class="form-group">
                    <label for="editAddress">Address:</label>
                    <input type="text" id="editAddress" name="address">
                </div>
                
                <div class="form-group">
                    <label for="editProfileImage">Change Profile Image:</label>
                    <input type="file" id="editProfileImage" name="profile_image" accept="image/*">
                    <small>(Leave empty to keep current image)</small>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="removeImage" name="remove_image" value="5">
                        Remove current image (use default)
                    </label>
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn">Update Member</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Profile Modal
    var profileModal = document.getElementById("profileModal");
    var profileBtns = document.getElementsByClassName("borrow-btn");
    
    for (var i = 0; i < profileBtns.length; i++) {
        profileBtns[i].onclick = function() {
            var memberId = this.getAttribute("data-id");
            var memberName = this.getAttribute("data-name");
            var memberProgram = this.getAttribute("data-program");
            var memberPosition = this.getAttribute("data-position");
            var memberBirthdate = this.getAttribute("data-birthdate");
            var memberAddress = this.getAttribute("data-address");
            
            document.getElementById("memberName").textContent = memberName;
            document.getElementById("memberProgram").textContent = memberProgram;
            document.getElementById("memberPosition").textContent = memberPosition;
            document.getElementById("memberBirthdate").textContent = memberBirthdate || "Not available";
            document.getElementById("memberAddress").textContent = memberAddress || "Not available";
            
            profileModal.style.display = "flex";
        }
    }
    
    document.getElementById("closeProfileBtn").onclick = function() {
        profileModal.style.display = "none";
    }

    // Edit Modal
    var editModal = document.getElementById("editModal");
    var editBtns = document.getElementsByClassName("edit-btn");
    
    for (var i = 0; i < editBtns.length; i++) {
        editBtns[i].onclick = function() {
            var memberId = this.getAttribute("data-id");
            var lastName = this.getAttribute("data-last_name");
            var givenName = this.getAttribute("data-given_name");
            var middleInitial = this.getAttribute("data-middle_initial");
            var extension = this.getAttribute("data-extension");
            var memberProgram = this.getAttribute("data-program");
            var memberPosition = this.getAttribute("data-position");
            var memberBirthdate = this.getAttribute("data-birthdate");
            var memberAddress = this.getAttribute("data-address");
            
            // Populate the edit form with member data
            document.getElementById("editMemberId").value = memberId;
            document.getElementById("editLastName").value = lastName || "";
            document.getElementById("editGivenName").value = givenName || "";
            document.getElementById("editMiddleInitial").value = middleInitial || "";
            document.getElementById("editExtension").value = extension || "";
            document.getElementById("editProgram").value = memberProgram;
            document.getElementById("editPosition").value = memberPosition;
            document.getElementById("editBirthdate").value = memberBirthdate || "";
            document.getElementById("editAddress").value = memberAddress || "";
            
            // Reset remove image checkbox
            document.getElementById("removeImage").checked = false;
            document.getElementById("editProfileImage").disabled = false;
            
            editModal.style.display = "flex";
        }
    }
    
    // Handle checkbox for removing image
    document.getElementById('removeImage').addEventListener('change', function() {
        document.getElementById('editProfileImage').disabled = this.checked;
        
        // Clear the file input if checkbox is checked
        if (this.checked) {
            document.getElementById('editProfileImage').value = '';
        }
    });
    
    // Handle file input for uploading image
    document.getElementById('editProfileImage').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('removeImage').disabled = true;
        } else {
            document.getElementById('removeImage').disabled = false;
        }
    });
    
    // Add Modal
    var addModal = document.getElementById("addModal");
    var addButton = document.getElementById("addButton");
    
    addButton.onclick = function() {
        addModal.style.display = "flex";
    }
    
    // Delete Modal
    var deleteModal = document.getElementById("deleteModal");
    var deleteBtns = document.getElementsByClassName("delete-btn");
    
    for (var i = 0; i < deleteBtns.length; i++) {
        deleteBtns[i].onclick = function() {
            // Get the member id from data attributes
            var memberId = this.getAttribute("data-id");
            
            // Set the value in the delete confirmation form
            document.getElementById("deleteMemberId").value = memberId;
            
            // Show the delete confirmation modal
            deleteModal.style.display = "flex";
            return false; // Prevent default behavior
        }
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target == profileModal) {
            profileModal.style.display = "none";
        }
        if (event.target == addModal) {
            addModal.style.display = "none";
        }
        if (event.target == deleteModal) {
            deleteModal.style.display = "none";
        }
        if (event.target == editModal) {
            editModal.style.display = "none";
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