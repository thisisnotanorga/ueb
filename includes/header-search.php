<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($_GET['query']) ? htmlspecialchars($_GET['query']) . ' - ' : '' ?><?php echo SITE_TITLE; ?></title>
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css">
    <link rel="icon" href="../images/logo.png">
</head>
<body>
    <div class="search-results-header">
        <div class="search-container">
            <form action="" method="GET">
                <div class="search-box">
                    <input type="text" name="q" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" placeholder="Search..." required>
                    <button type="submit">
                        <i class="ri-search-line"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="container main-content">