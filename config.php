<?php
// Application Configuration
define('APP_NAME', 'Shortie');
define('APP_VERSION', '1.0.0');

// Database Configuration
define('DB_PATH', 'shortie.db');

// URL Configuration
define('BASE_URL', 'http://localhost:8000');
define('SHORT_URL_LENGTH', 6);

// Security Configuration
define('ALLOWED_PROTOCOLS', ['http', 'https', 'ftp']);
define('BLOCKED_DOMAINS', [
    'localhost',
    '127.0.0.1',
    '0.0.0.0'
]);

// Rate Limiting
define('MAX_URLS_PER_IP_PER_HOUR', 20);

// Error Messages
define('ERROR_INVALID_URL', 'Please enter a valid URL');
define('ERROR_BLOCKED_DOMAIN', 'This domain is not allowed');
define('ERROR_RATE_LIMIT', 'Rate limit exceeded. Please try again later');
define('ERROR_DATABASE', 'Database error occurred');
define('ERROR_URL_NOT_FOUND', 'Short URL not found');

// Success Messages
define('SUCCESS_URL_CREATED', 'Short URL created successfully');
?> 