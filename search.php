<?php
require_once 'config.php';
include 'includes/header-search.php';
include 'scraper/index.php';

$query = '';
$videoResults = [];
$imageResults = [];
$videoError = null;
$imageError = null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$limit = IMAGES_LIMIT;
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

if (isset($_GET['q'])) {
    $query = trim(htmlspecialchars($_GET['q']));

    if (strlen($query) < 3) {
        echo "<div class='error-message'>Please enter at least 3 characters for the search.</div>";
    } else {
        try {
            $videoResults = scrapeVideos($query);
        } catch (Exception $e) {
            $videoError = "Error retrieving videos.";
        }

        try {
            $imageResults = scrapeImages($query, $page, $limit);
        } catch (Exception $e) {
            $imageError = "Error retrieving images.";
        }
    }
}
?>

<div class="tabs-container">
    <div class="search-tabs">
        <a href="?q=<?php echo urlencode($query); ?>&tab=all" class="tab <?php echo $activeTab == 'all' ? 'active' : ''; ?>">
            <span>All</span>
        </a>
        <a href="?q=<?php echo urlencode($query); ?>&tab=videos" class="tab <?php echo $activeTab == 'videos' ? 'active' : ''; ?>">
            <span>Videos</span>
        </a>
        <a href="?q=<?php echo urlencode($query); ?>&tab=images" class="tab <?php echo $activeTab == 'images' ? 'active' : ''; ?>">
            <span>Images</span>
        </a>
    </div>
</div>

<div class="results-container">
    <?php if ($videoError): ?>
        <div class="error-message"> <?php echo $videoError; ?> </div>
    <?php endif; ?>

    <?php if ($imageError): ?>
        <div class="error-message"> <?php echo $imageError; ?> </div>
    <?php endif; ?>

    <?php if (!empty($videoResults) || !empty($imageResults)): ?>
        <div class="results-count">
            About <?php echo count($videoResults) + count($imageResults); ?> results
        </div>

        <?php if (!empty($videoResults) && ($activeTab == 'all' || $activeTab == 'videos')): ?>
            <h2 class="section-title">Videos</h2>
            <div class="results-list">
                <?php foreach ($videoResults as $result): ?>
                    <div class="result-wrapper">
                        <a href="<?php echo $result['url']; ?>" target="_blank" rel="noopener noreferrer">
                            <div class="result">
                                <div class="result-header">
                                    <div class="source <?php echo strtolower($result['source']); ?>"><?php echo $result['source']; ?></div>
                                </div>
                                <div class="title-container">
                                    <h2><?php echo $result['title']; ?></h2>
                                </div>
                                <div class="thumbnail-row">
                                    <div class="thumbnail-container">
                                        <img src="../proxy/?url=<?php echo urlencode($result['thumbnail']); ?>" alt="<?php echo $result['title']; ?>" class="thumbnail" loading="lazy">
                                        <div class="duration"><?php echo $result['duration']; ?></div>
                                    </div>
                                    <div class="content">
                                        <p class="description">
                                            Video <?php echo $result['duration']; ?> - This video matches your search "<?php echo $query; ?>".
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($imageResults) && ($activeTab == 'all' || $activeTab == 'images')): ?>
            <h2 class="section-title">Images</h2>
            <div class="image-grid">
                <?php foreach ($imageResults as $result): ?>
                    <div class="image-item">
                        <a href="../proxy/?url=<?php echo urlencode($result['url']); ?>" target="_blank" rel="noopener noreferrer" class="image-link">
                            <div class="image-container">
                                <img src="../proxy/?url=<?php echo urlencode($result['preview_url']); ?>" alt="<?php echo $result['title']; ?>" loading="lazy">
                            </div>
                            <div class="image-info">
                                <div class="tags"><?php echo $result['title']; ?></div>
                                <div class="dimensions"><?php echo $result['width']; ?>x<?php echo $result['height']; ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="no-results">No results found for "<?php echo $query; ?>"</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="../js/app.js"></script>
