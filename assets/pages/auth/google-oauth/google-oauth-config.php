<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


(function () {
    // FIX: add missing slash
    $envPath = __DIR__ . '../../../../../.env';
    if (!is_readable($envPath)) {
        return;
    }
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(ltrim($line), '#') === 0) continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        // Strip optional quotes (PHP 8+ str_starts_with/str_ends_with)
        if ((function_exists('str_starts_with') && function_exists('str_ends_with'))
            && ((str_starts_with($val, '"') && str_ends_with($val, '"'))
                || (str_starts_with($val, "'") && str_ends_with($val, "'")))) {
            $val = substr($val, 1, -1);
        }
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
        if (!getenv($key)) {
            putenv("$key=$val");
        }
    }
})();

class GoogleOAuth {
    private string $client_id;
    private string $client_secret;
    private string $redirect_uri;
    private string $token_url;
    private string $user_info_url;

    public function __construct() {
        $this->client_id     = getenv('GOOGLE_CLIENT_ID') ?: '';
        $this->client_secret = getenv('GOOGLE_CLIENT_SECRET') ?: '';

        // Prefer explicit REDIRECT_URI; otherwise build it from current host/path
        $envRedirect = getenv('GOOGLE_REDIRECT_URI');
        if ($envRedirect) {
            $this->redirect_uri = $envRedirect;
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $path     = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
            // FIX: build a callback path, do NOT append env here
            $this->redirect_uri = $protocol . '://' . $host . $path . '/google-oauth-callback.php';
        }

        $this->token_url     = 'https://oauth2.googleapis.com/token';
        $this->user_info_url = 'https://www.googleapis.com/oauth2/v3/userinfo';
    }

    public function getAuthUrl(string $userType = 'mama'): string {
        if (empty($_SESSION['oauth_csrf'])) {
            $_SESSION['oauth_csrf'] = bin2hex(random_bytes(32));
        }

        $statePayload = [
            'csrf'     => $_SESSION['oauth_csrf'],
            'userType' => $userType,
            'ts'       => time(),
        ];
        $state = rtrim(strtr(base64_encode(json_encode($statePayload)), '+/', '-_'), '=');

        $params = [
            'client_id'     => $this->client_id,
            'redirect_uri'  => $this->redirect_uri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
            'state'         => $state,
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    public function getAccessToken(string $code): array|false {
        $params = [
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri'  => $this->redirect_uri,
            'grant_type'    => 'authorization_code',
            'code'          => $code,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 20,
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            error_log("Google OAuth token cURL error: " . $err);
            return false;
        }
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        error_log("Google OAuth token error ($httpCode): " . $response);
        return false;
    }

    public function getUserInfo(string $access_token): array|false {
        $url = $this->user_info_url . '?access_token=' . urlencode($access_token);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $access_token],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 20,
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            error_log("Google user info cURL error: " . $err);
            return false;
        }
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        error_log("Google user info error ($httpCode): " . $response);
        return false;
    }
}
