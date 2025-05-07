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
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <img src="picture-1.png" alt="Logo" class="header-logo">
            <h2>CDM Chorale Inventory System</h2>
            <a href="index.php" class="logout">Log Out</a>
        </div>

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

        <!-- Card Section -->
        <div class="card-container">
            <?php
            include 'db_connect.php';
            // Fetch members from database
            $sql = "SELECT * FROM members";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Determine the image to display
                    $imagePath = !empty($row["image_path"]) ? $row["image_path"] : 'default image.jpg';
                    
                    echo "<div class='card'>";
                    echo "<img src='" . $imagePath . "' alt='Member'>";
                    echo "<h3>" . $row["members_name"] . "</h3>";
                
                    echo "<p>Program: " . $row["program"] . "</p>";
                    echo "<p>Position: " . $row["position"] . "</p>";
                    echo "<button class='borrow-btn' data-id='" . $row["member_id"] . "' 
                          data-name='" . $row["members_name"] . "' 
                          data-program='" . $row["program"] . "' 
                          data-position='" . $row["position"] . "' 
                          data-birthdate='" . (isset($row["birthdate"]) ? $row["birthdate"] : "") . "' 
                          data-address='" . (isset($row["address"]) ? $row["address"] : "") . "'>View Profile</button>";
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

    <script>
    // Get the modal
    var modal = document.getElementById("profileModal");

    // Get all buttons that should open the modal
    var btns = document.getElementsByClassName("borrow-btn");

    // When the user clicks on a button, open the modal with member data
    for (var i = 0; i < btns.length; i++) {
        btns[i].onclick = function() {
            var memberId = this.getAttribute("data-id");
            var memberName = this.getAttribute("data-name");
            var memberProgram = this.getAttribute("data-program");
            var memberPosition = this.getAttribute("data-position");
            var memberBirthdate = this.getAttribute("data-birthdate");
            var memberAddress = this.getAttribute("data-address");
            
            // Set the values in the modal
            document.getElementById("memberName").textContent = memberName;
            document.getElementById("memberProgram").textContent = memberProgram;
            document.getElementById("memberPosition").textContent = memberPosition;
            document.getElementById("memberBirthdate").textContent = memberBirthdate || "Not available";
            document.getElementById("memberAddress").textContent = memberAddress || "Not available";
            
            modal.style.display = "flex"; // Use flex to center the modal
        }
    }

    // Close button functionality
    document.getElementById("closeProfileBtn").onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal content, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>
</html>