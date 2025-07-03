<?php
require_once 'config.php';
require_once 'Database.php';
$db = new Database();

// Get the short code from URL if provided
$shortCode = $_GET['c'] ?? '';
$error = '';
$stats = null;

// If a short code is provided, get its stats
if (!empty($shortCode)) {
    $stats = $db->getUrlStats($shortCode);
    if (!$stats) {
        $error = 'No URL found with that code.';
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Statistics - Shortie</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="theme-toggle">
        <label class="theme-switch">
            <input type="checkbox" id="theme-toggle">
            <span class="slider"></span>
        </label>
    </div>

    <div class="container">
        <h1>Statistics</h1>
        <p class="subtitle">Track your URL performance</p>

        <form method="GET" class="url-form">
            <div class="input-group">
                <input type="text" name="c" placeholder="Enter short code (e.g., abc123)" value="<?php echo htmlspecialchars($shortCode); ?>" required>
                <button type="submit">View Stats</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="error">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($stats): ?>
            <div class="result-container">
                <h3>URL Statistics</h3>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stats['clicks']); ?></div>
                        <div class="stat-label">Total Clicks</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo date('M j, Y', strtotime($stats['created_at'])); ?></div>
                        <div class="stat-label">Created On</div>
                    </div>
                </div>

                <div class="url-info">
                    <h4>Short URL:</h4>
                    <div class="short-url">
                        <input type="text" value="<?php echo BASE_URL . '/r.php?c=' . htmlspecialchars($shortCode); ?>" readonly>
                        <button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
                    </div>

                    <h4>Original URL:</h4>
                    <p class="original-url"><?php echo htmlspecialchars($stats['original_url']); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php
        // Show recent URLs if no specific code requested
        if (empty($shortCode) && !$error) {
            try {
                $recentUrls = $db->getRecentUrls(5);
                if ($recentUrls): ?>
                    <div class="recent-urls">
                        <h3>Recent URLs</h3>
                        <div class="url-list">
                            <?php foreach ($recentUrls as $url): ?>
                            <div class="url-item">
                                <div class="url-details">
                                    <a href="?c=<?php echo urlencode($url['short_code']); ?>" class="short-code">
                                        <?php echo htmlspecialchars($url['short_code']); ?>
                                    </a>
                                    <div class="short-url">
                                        <input type="text" value="<?php echo BASE_URL . '/r.php?c=' . htmlspecialchars($url['short_code']); ?>" readonly>
                                        <button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
                                    </div>
                                    <span class="url-original"><?php echo htmlspecialchars($url['original_url']); ?></span>
                                </div>
                                <div class="url-stats">
                                    <span class="clicks"><?php echo $url['clicks']; ?> clicks</span>
                                    <span class="date"><?php echo date('M j, Y', strtotime($url['created_at'])); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif;
            } catch (Exception $e) {
                // Silently fail for recent URLs
            }
        }
        ?>

        <div class="navigation">
            <a href="index.php" class="nav-btn">← Back to Shortener</a>
        </div>
    </div>

    <script>
    function copyToClipboard(button) {
        const input = button.parentElement.querySelector('input');
        input.select();
        document.execCommand('copy');
        button.textContent = 'Copied!';
        setTimeout(() => {
            button.textContent = 'Copy';
        }, 2000);
    }

    // Theme toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;
    
    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    themeToggle.checked = savedTheme === 'dark';

    themeToggle.addEventListener('change', function() {
        const theme = this.checked ? 'dark' : 'light';
        html.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    });
    </script>
</body>
</html> 