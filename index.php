<?php 
require_once 'config.php';
include 'includes/header-home.php'; 
?>

<div class="search-container">
    <form action="search" method="GET">
        <div class="home-search">
            <div class="search-box">
                <input type="text" name="q" placeholder="Search..." required>
                <button type="submit">
                    <i class="ri-search-line"></i>
                </button>
            </div>
        </div>
    </form>
</div>


<?php include 'includes/footer.php'; ?>