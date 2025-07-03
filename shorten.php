<?php
require_once 'config.php';
require_once 'Database.php';

// Function to validate URL
function validateUrl($url) {
    // Check if URL is valid
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // Parse URL to check protocol and domain
    $parsed = parse_url($url);
    
    // Check if protocol is allowed
    if (!in_array($parsed['scheme'], ALLOWED_PROTOCOLS)) {
        return false;
    }
    
    // Check if domain is blocked
    if (in_array($parsed['host'], BLOCKED_DOMAINS)) {
        return false;
    }
    
    return true;
}

// Function to get client IP
function getClientIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Function to get user agent
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalUrl = trim($_POST['url'] ?? '');
    
    if (empty($originalUrl)) {
        header('Location: index.php?error=' . urlencode(ERROR_INVALID_URL));
        exit;
    }
    
    // Validate URL
    if (!validateUrl($originalUrl)) {
        header('Location: index.php?error=' . urlencode(ERROR_INVALID_URL));
        exit;
    }
    
    // Get client information
    $ipAddress = getClientIp();
    $userAgent = getUserAgent();
    
    try {
        $db = new Database();
        
        // Check rate limit
        if (!$db->checkRateLimit($ipAddress)) {
            header('Location: index.php?error=' . urlencode(ERROR_RATE_LIMIT));
            exit;
        }
        
        // Check if URL already exists
        $existingCode = $db->urlExists($originalUrl);
        if ($existingCode) {
            $shortUrl = BASE_URL . '/r.php?c=' . $existingCode;
            header('Location: index.php?success=1&short_url=' . urlencode($shortUrl) . '&original_url=' . urlencode($originalUrl));
            exit;
        }
        
        // Generate short code
        $shortCode = $db->generateShortCode();
        
        // Insert URL into database
        if ($db->insertUrl($originalUrl, $shortCode, $ipAddress, $userAgent)) {
            $shortUrl = BASE_URL . '/r.php?c=' . $shortCode;
            header('Location: index.php?success=1&short_url=' . urlencode($shortUrl) . '&original_url=' . urlencode($originalUrl));
            exit;
        } else {
            header('Location: index.php?error=' . urlencode(ERROR_DATABASE));
            exit;
        }
        
    } catch (Exception $e) {
        header('Location: index.php?error=' . urlencode(ERROR_DATABASE));
        exit;
    }
} else {
    // If not POST request, redirect to homepage
    header('Location: index.php');
    exit;
}
?> 