<?php
require_once 'config.php';
require_once 'Database.php';

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

// Function to get referer
function getReferer() {
    return $_SERVER['HTTP_REFERER'] ?? 'Direct';
}

// Get short code from URL parameter
$shortCode = $_GET['c'] ?? '';

if (empty($shortCode)) {
    header('Location: index.php?error=' . urlencode(ERROR_URL_NOT_FOUND));
    exit;
}

try {
    $db = new Database();
    
    // Get original URL
    $originalUrl = $db->getOriginalUrl($shortCode);
    
    if (!$originalUrl) {
        header('Location: index.php?error=' . urlencode(ERROR_URL_NOT_FOUND));
        exit;
    }
    
    // Get client information for tracking
    $ipAddress = getClientIp();
    $userAgent = getUserAgent();
    $referer = getReferer();
    
    // Log the click
    $db->logClick($shortCode, $ipAddress, $userAgent, $referer);
    
    // Increment click counter
    $db->incrementClick($shortCode);
    
    // Redirect to original URL
    header('Location: ' . $originalUrl, true, 301);
    exit;
    
} catch (Exception $e) {
    header('Location: index.php?error=' . urlencode(ERROR_DATABASE));
    exit;
}
?> 