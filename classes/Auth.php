<?php
require_once 'config/config.php';

class Auth {
    
    // Generate JWT token
    public static function generateToken($user_data) {
        $header = json_encode(['typ' => 'JWT', 'alg' => JWT_ALGORITHM]);
        $payload = json_encode([
            'user_id' => $user_data['id'],
            'email' => $user_data['email'],
            'user_type' => $user_data['user_type'],
            'exp' => time() + JWT_EXPIRATION
        ]);

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, JWT_SECRET, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    // Verify JWT token
    public static function verifyToken($token) {
        if (!$token) {
            return false;
        }

        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            return false;
        }

        $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[0]));
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
        $signatureProvided = $tokenParts[2];

        // Verify signature
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, JWT_SECRET, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        if (!hash_equals($base64Signature, $signatureProvided)) {
            return false;
        }

        $payloadData = json_decode($payload, true);

        // Check expiration
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false;
        }

        return $payloadData;
    }

    // Get token from header
    public static function getTokenFromHeader() {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    // Require authentication
    public static function requireAuth() {
        $token = self::getTokenFromHeader();
        $payload = self::verifyToken($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        return $payload;
    }

    // Require tutor role
    public static function requireTutor() {
        $payload = self::requireAuth();
        
        if ($payload['user_type'] !== 'tutor') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Tutor access required']);
            exit();
        }

        return $payload;
    }
}
?>
