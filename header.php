<?php
if (!isset($section_title)) $section_title = '';
?>
<div class="header">
    <img src="picture-1.png" alt="Logo" class="header-logo">
    <div class="section-indicator"><?php echo htmlspecialchars($section_title); ?></div>
    <h2>CDM Chorale Inventory System</h2>
    <div style="display: flex; align-items: center; gap: 10px;">
        <a href="my_profile.php" class="logout profile-link">
            <i class="fas fa-user-circle"></i> My Profile
        </a>
        <a href="logout.php" class="logout">Log Out</a>
    </div>
</div>
<style>
.header .logout {
    background-color: #000066 !important;
    color: white !important;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    min-width: 90px;
    text-align: center;
    margin-right: 8px;
    padding: 8px 15px;
    display: inline-block;
    font-weight: normal !important;
    box-shadow: none !important;
}
.header .logout:hover {
    background-color: #000044 !important;
}
.header .profile-link {
    background-color: #ffcc00 !important;
    color: #000066 !important;
}
.header .profile-link:hover {
    background-color: #e6b800 !important;
}
</style> 