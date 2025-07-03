<?php
require_once 'config.php';

class Database {
    private $db;
    
    public function __construct() {
        $this->connect();
        $this->createTables();
    }
    
    private function connect() {
        try {
            $this->db = new PDO('sqlite:' . DB_PATH);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    private function createTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS urls (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                original_url TEXT NOT NULL,
                short_code TEXT UNIQUE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                clicks INTEGER DEFAULT 0,
                ip_address TEXT,
                user_agent TEXT
            );
            
            CREATE TABLE IF NOT EXISTS clicks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                short_code TEXT NOT NULL,
                ip_address TEXT,
                user_agent TEXT,
                referer TEXT,
                clicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (short_code) REFERENCES urls(short_code)
            );
            
            CREATE INDEX IF NOT EXISTS idx_short_code ON urls(short_code);
            CREATE INDEX IF NOT EXISTS idx_clicks_short_code ON clicks(short_code);
        ";
        
        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            die('Database table creation failed: ' . $e->getMessage());
        }
    }
    
    public function generateShortCode() {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        
        do {
            $code = '';
            for ($i = 0; $i < SHORT_URL_LENGTH; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while ($this->shortCodeExists($code));
        
        return $code;
    }
    
    public function shortCodeExists($code) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM urls WHERE short_code = ?");
        $stmt->execute([$code]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function insertUrl($originalUrl, $shortCode, $ipAddress, $userAgent) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO urls (original_url, short_code, ip_address, user_agent) 
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$originalUrl, $shortCode, $ipAddress, $userAgent]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getOriginalUrl($shortCode) {
        $stmt = $this->db->prepare("SELECT original_url FROM urls WHERE short_code = ?");
        $stmt->execute([$shortCode]);
        return $stmt->fetchColumn();
    }
    
    public function incrementClick($shortCode) {
        $stmt = $this->db->prepare("UPDATE urls SET clicks = clicks + 1 WHERE short_code = ?");
        return $stmt->execute([$shortCode]);
    }
    
    public function logClick($shortCode, $ipAddress, $userAgent, $referer) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO clicks (short_code, ip_address, user_agent, referer) 
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$shortCode, $ipAddress, $userAgent, $referer]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getUrlStats($shortCode) {
        $stmt = $this->db->prepare("
            SELECT original_url, created_at, clicks 
            FROM urls 
            WHERE short_code = ?
        ");
        $stmt->execute([$shortCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getRecentUrls($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT short_code, original_url, created_at, clicks 
            FROM urls 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function checkRateLimit($ipAddress) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM urls 
            WHERE ip_address = ? 
            AND created_at > datetime('now', '-1 hour')
        ");
        $stmt->execute([$ipAddress]);
        return $stmt->fetchColumn() < MAX_URLS_PER_IP_PER_HOUR;
    }
    
    public function urlExists($originalUrl) {
        $stmt = $this->db->prepare("SELECT short_code FROM urls WHERE original_url = ?");
        $stmt->execute([$originalUrl]);
        return $stmt->fetchColumn();
    }
}
?> 