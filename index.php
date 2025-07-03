<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shortie - URL Shortener</title>
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
        <h1>Shortie</h1>
        <p class="subtitle">Open Source URL Shortener!</p>
        
        <form class="url-form" method="post" action="shorten.php">
            <div class="input-group">
                <input type="url" name="url" placeholder="Enter your long URL" required>
                <button type="submit">Shorten</button>
            </div>
        </form>

        <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <p><?php echo htmlspecialchars($_GET['error']); ?></p>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['short']) && isset($_GET['original'])): ?>
        <div class="result-container">
            <h3>Your shortened URL is ready!</h3>
            <div class="short-url">
                <input type="text" value="<?php echo htmlspecialchars($_GET['short']); ?>" readonly>
                <button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
            </div>
            <p class="original-url">Original URL: <?php echo htmlspecialchars($_GET['original']); ?></p>
        </div>
        <?php endif; ?>

        <div class="navigation">
            <a href="stats.php" class="nav-btn">View Statistics →</a>
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