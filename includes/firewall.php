<?php
class SecurityFirewall {
    // Firewall configuration
    private $config = [
        'blocked_ips' => [],
        'blocked_user_agents' => [],
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'max_upload_size' => 5 * 1024 * 1024, // 5MB
        'log_file' => 'firewall_log.txt'
    ];

    // Initialize firewall protections
    public function __construct() {
        // Add common default blocked IPs and patterns
        $this->config['blocked_ips'] = [
            //'127.0.0.1', // localhost (for demonstration)
            // Add known malicious IPs
        ];

        $this->config['blocked_user_agents'] = [
            'sqlmap', 'nikto', 'wget', 'curl', 'python-requests'
        ];
    }

    // Main firewall check method
    public function protect() {
        $this->checkIPAddress();
        $this->checkUserAgent();
        $this->sanitizeInputs();
        $this->fileUploadProtection();
        $this->preventSQLInjection();
        $this->preventXSSAttacks();
    }

    // Block malicious IP addresses
    private function checkIPAddress() {
        $client_ip = $this->getCurrentIP();
        
        // Check against blocked IPs
        if (in_array($client_ip, $this->config['blocked_ips'])) {
            $this->blockAccess("Blocked IP: $client_ip");
        }

        // Additional IP reputation checks could be added here
    }

    // Check and filter user agents
    private function checkUserAgent() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        foreach ($this->config['blocked_user_agents'] as $blocked_agent) {
            if (stripos($user_agent, $blocked_agent) !== false) {
                $this->blockAccess("Suspicious User Agent: $user_agent");
            }
        }
    }

    // Sanitize all input data
    private function sanitizeInputs() {
        // Sanitize GET, POST, and COOKIE data
        $_GET = $this->sanitizeArray($_GET);
        $_POST = $this->sanitizeArray($_POST);
        $_COOKIE = $this->sanitizeArray($_COOKIE);
    }

    // Sanitize array of inputs
    private function sanitizeArray($input) {
        $clean = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $clean[$key] = $this->sanitizeArray($value);
            } else {
                $clean[$key] = $this->sanitizeString($value);
            }
        }
        return $clean;
    }

    // Comprehensive string sanitization
    private function sanitizeString($string) {
        // Remove control characters
        $string = preg_replace('/[\x00-\x1F\x7F]/', '', $string);
        
        // Encode special characters
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        
        // Remove potential script tags
        $string = strip_tags($string);
        
        return trim($string);
    }

    // Prevent SQL Injection
    private function preventSQLInjection() {
        $sql_dangerous_patterns = [
            '/(\b(SELECT|UNION|INSERT|UPDATE|DELETE|DROP|ALTER)\b)/i',
            '/\/\*.*?\*\//s',
            '/--\s.*$/m'
        ];

        foreach ($_GET as $key => $value) {
            foreach ($sql_dangerous_patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $this->blockAccess("Potential SQL Injection detected in GET parameter: $key");
                }
            }
        }

        foreach ($_POST as $key => $value) {
            foreach ($sql_dangerous_patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $this->blockAccess("Potential SQL Injection detected in POST parameter: $key");
                }
            }
        }
    }

    // Prevent Cross-Site Scripting (XSS)
    private function preventXSSAttacks() {
        $xss_patterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/onerror\s*=/i',
            '/javascript:/i',
            '/vbscript:/i'
        ];

        foreach ($_GET as $key => $value) {
            foreach ($xss_patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $this->blockAccess("Potential XSS attack detected in GET parameter: $key");
                }
            }
        }

        foreach ($_POST as $key => $value) {
            foreach ($xss_patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $this->blockAccess("Potential XSS attack detected in POST parameter: $key");
                }
            }
        }
    }

    private function fileUploadProtection() {
        if (!empty($_FILES)) {
            foreach ($_FILES as $file) {
                // Check file size
                if ($file['size'] > $this->config['max_upload_size']) {
                    $this->blockAccess("File too large: " . $file['name']);
                }
    
                // Check file extension
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                // Enhanced file type validation
                $allowed_extensions = array_merge(
                    $this->config['allowed_file_types'], 
                    ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'pl', 'cgi', 'jsp']
                );
    
                // Strict file extension check
                if (!in_array($file_ext, $this->config['allowed_file_types'])) {
                    $this->blockAccess("Unauthorized file type: " . $file['name']);
                }
    
                // Additional file type verification
                $file_info = finfo_open(FILEINFO_MIME_TYPE);
                $detected_type = finfo_file($file_info, $file['tmp_name']);
                finfo_close($file_info);
    
                // Enhanced MIME type blocking
                $blocked_mime_types = [
                    'application/x-executable',
                    'application/x-ms-dos-executable',
                    'application/x-shellscript',
                    'application/x-php',
                    'text/x-php',
                    'application/x-perl',
                    'application/x-python',
                    'application/x-ruby'
                ];
    
                // Check for potential file type spoofing
                $image_mime_types = [
                    'image/jpeg', 
                    'image/png', 
                    'image/gif', 
                    'image/webp'
                ];
    
                // If file claims to be an image, do additional checks
                if (in_array($detected_type, $image_mime_types)) {
                    // Additional image validation
                    $image_check = @getimagesize($file['tmp_name']);
                    if ($image_check === false) {
                        $this->blockAccess("Invalid image file: " . $file['name']);
                    }
    
                    // Check for embedded PHP or script content in image
                    $image_content = file_get_contents($file['tmp_name']);
                    $dangerous_patterns = [
                        '/<\?php/i',
                        '/eval\(/i',
                        '/base64_decode/i',
                        '/system\(/i',
                        '/exec\(/i'
                    ];
    
                    foreach ($dangerous_patterns as $pattern) {
                        if (preg_match($pattern, $image_content)) {
                            $this->blockAccess("Potentially malicious content in image: " . $file['name']);
                        }
                    }
                }
    
                // Block executable and potentially dangerous file types
                if (in_array($detected_type, $blocked_mime_types)) {
                    $this->blockAccess("Potentially malicious file detected: " . $file['name']);
                }
    
                // Additional name sanitization
                $safe_filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', $file['name']);
                if ($safe_filename !== $file['name']) {
                    $this->blockAccess("Invalid filename characters: " . $file['name']);
                }
            }
        }
    }

    // Get current client IP
    private function getCurrentIP() {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }

    // Block access and log the incident
    private function blockAccess($reason) {
        // Log the incident
        $log_entry = date('Y-m-d H:i:s') . " | IP: " . $this->getCurrentIP() . " | Reason: $reason\n";
        file_put_contents($this->config['log_file'], $log_entry, FILE_APPEND);

        // Respond with a generic error
        header('HTTP/1.1 403 Forbidden');
        die('Access Denied');
    }

    // Method to add custom blocked IPs or patterns
    public function addBlockedIP($ip) {
        $this->config['blocked_ips'][] = $ip;
    }

    // Method to add custom blocked user agents
    public function addBlockedUserAgent($agent) {
        $this->config['blocked_user_agents'][] = $agent;
    }
}

// Usage example
try {
    $firewall = new SecurityFirewall();
    $firewall->protect();
    
    // Add custom rules if needed
    // $firewall->addBlockedIP('192.168.1.100');
    // $firewall->addBlockedUserAgent('BadBot');

} catch (Exception $e) {
    // Handle any unexpected errors
    error_log('Firewall Error: ' . $e->getMessage());
    die('System Error');
}
?>