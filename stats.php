<?php
require_once 'config.php';
require_once 'Database.php';

$shortCode = $_GET['c'] ?? '';
$stats = null;
$error = null;

if (!empty($shortCode)) {
    try {
        $db = new Database();
        $stats = $db->getUrlStats($shortCode);
        
        if (!$stats) {
            $error = ERROR_URL_NOT_FOUND;
        }
    } catch (Exception $e) {
        $error = ERROR_DATABASE;
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
        <h1>📊 URL Statistics</h1>
        <p class="subtitle">View analytics for your shortened URLs</p>
        
        <form method="GET" class="url-form">
            <div class="input-group">
                <input type="text" name="c" placeholder="Enter short code (e.g., abc123)" value="<?php echo htmlspecialchars($shortCode); ?>" required>
                <button type="submit">View Stats</button>
            </div>
        </form>
        
        <?php if ($error): ?>
            <div class="error">
                <p>❌ Error: <?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($stats): ?>
            <div class="result-container">
                <h3>📈 URL Statistics</h3>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stats['clicks']); ?></div>
                        <div class="stat-label">Total Clicks</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo date('M d, Y', strtotime($stats['created_at'])); ?></div>
                        <div class="stat-label">Created On</div>
                    </div>
                </div>
                
                <div class="url-info">
                    <h4>Original URL:</h4>
                    <p class="original-url"><?php echo htmlspecialchars($stats['original_url']); ?></p>
                    
                    <h4>Short URL:</h4>
                    <div class="short-url">
                        <input type="text" value="<?php echo BASE_URL . '/r.php?c=' . htmlspecialchars($shortCode); ?>" readonly id="shortUrl">
                        <button onclick="copyToClipboard()" class="copy-btn">Copy</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        // Show recent URLs if no specific code requested
        if (empty($shortCode) && !$error) {
            try {
                $db = new Database();
                $recentUrls = $db->getRecentUrls(5);
                
                if ($recentUrls): ?>
                    <div class="recent-urls">
                        <h3>🕒 Recent URLs</h3>
                        <div class="url-list">
                            <?php foreach ($recentUrls as $url): ?>
                                <div class="url-item">
                                    <div class="url-details">
                                        <strong><?php echo htmlspecialchars($url['short_code']); ?></strong>
                                        <span class="url-original"><?php echo htmlspecialchars(substr($url['original_url'], 0, 60)) . (strlen($url['original_url']) > 60 ? '...' : ''); ?></span>
                                    </div>
                                    <div class="url-stats">
                                        <span class="clicks"><?php echo $url['clicks']; ?> clicks</span>
                                        <span class="date"><?php echo date('M d', strtotime($url['created_at'])); ?></span>
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
            <a href="index.php" class="nav-btn">← Back to Home</a>
        </div>
    </div>
    
    <script>
        function copyToClipboard() {
            const shortUrl = document.getElementById('shortUrl');
            if (shortUrl) {
                shortUrl.select();
                document.execCommand('copy');
                
                const copyBtn = document.querySelector('.copy-btn');
                copyBtn.textContent = 'Copied!';
                setTimeout(() => {
                    copyBtn.textContent = 'Copy';
                }, 2000);
            }
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
    
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .url-info {
            text-align: left;
            margin-top: 20px;
        }
        
        .url-info h4 {
            color: #333;
            margin: 15px 0 5px 0;
        }
        
        .recent-urls {
            margin-top: 40px;
            text-align: left;
        }
        
        .url-list {
            background: rgba(0, 0, 0, 0.02);
            border-radius: 10px;
            padding: 15px;
        }
        
        .url-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .url-item:last-child {
            border-bottom: none;
        }
        
        .url-details {
            flex: 1;
        }
        
        .url-details strong {
            color: #667eea;
            font-family: monospace;
        }
        
        .url-original {
            display: block;
            font-size: 0.9em;
            color: #666;
            margin-top: 2px;
        }
        
        .url-stats {
            display: flex;
            flex-direction: column;
            text-align: right;
            font-size: 0.8em;
            color: #999;
        }
        
        .clicks {
            font-weight: bold;
            color: #4caf50;
        }
        
        .navigation {
            margin-top: 30px;
        }
        
        .nav-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        
        .nav-btn:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .url-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .url-stats {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</body>
</html> 