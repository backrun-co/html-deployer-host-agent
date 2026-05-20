<?php
/**
 * HTML Deployer — Host Agent (official deployment helper)
 *
 * What this file does:
 *   - Accepts HTML from the "HTML Deployer" Chrome extension only
 *   - Saves static .html pages in this folder (no shell, no eval, no downloads)
 *
 * What this file does NOT do:
 *   - Run system commands, load remote PHP, or modify files outside this directory
 *
 * Product: https://backrun.co/html-deployer/
 * Remove this file if you do not use HTML Deployer.
 *
 * ─── If you got this file from GitHub ─────────────────────────────────────
 * 1. Upload this file to YOUR web hosting (PHP), e.g. public_html/backrun.php
 *    (GitHub only hosts the source; PHP must run on your server.)
 * 2. Replace SECRET_KEY below with your own long random string (32+ chars).
 *    Same value must be pasted in the extension → Options → Host Agent → Secret key.
 * 3. In the extension, set Agent URL to https://your-domain.com/backrun.php
 */

// =============================================================================
//  SECRET KEY — MUST be unique on your server; never use the example value
//  in production. Chrome → HTML Deployer → Extension options → Host Agent
// =============================================================================
define('SECRET_KEY', 'CHANGE_ME_USE_YOUR_OWN_RANDOM_SECRET_32_CHARS_MIN');
// =============================================================================

/** Max HTML payload size (2 MB). */
define('MAX_SIZE_BYTES', 2097152);

/**
 * Send a JSON response and stop.
 */
function html_deployer_json($payload, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload);
    exit;
}

/**
 * CORS headers for the HTML Deployer Chrome extension (cross-origin API calls).
 */
function html_deployer_send_cors_headers() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

/**
 * Compare the request key with SECRET_KEY (timing-safe).
 */
function html_deployer_is_authorized($providedKey) {
    return $providedKey !== '' && hash_equals(SECRET_KEY, $providedKey);
}

/**
 * Sanitize a page filename (alphanumeric, underscore, hyphen only).
 */
function html_deployer_safe_filename($raw) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $raw);
}

html_deployer_send_cors_headers();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    exit;
}

$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');
$providedKey = (string) ($_GET['key'] ?? $_POST['key'] ?? '');

if (!html_deployer_is_authorized($providedKey)) {
    html_deployer_json(['error' => 'Unauthorized'], 401);
}

switch ($action) {
    case 'ping':
        html_deployer_json([
            'status'  => 'ok',
            'version' => '1.0',
            'agent'   => 'html-deployer-host-agent',
        ]);
        break;

    case 'deploy':
        $filename = html_deployer_safe_filename((string) ($_POST['filename'] ?? ''));
        $html = (string) ($_POST['html'] ?? '');
        if ($filename === '' || strlen($filename) > 50) {
            html_deployer_json(['error' => 'Invalid filename']);
        }
        if (strlen($html) > MAX_SIZE_BYTES) {
            html_deployer_json(['error' => 'File too large']);
        }
        $targetPath = __DIR__ . '/' . $filename . '.html';
        if (file_put_contents($targetPath, $html) === false) {
            html_deployer_json(['error' => 'Could not write file']);
        }
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $publicUrl = $scheme . '://' . $_SERVER['HTTP_HOST']
            . rtrim(dirname($_SERVER['REQUEST_URI'] ?? ''), "/\\")
            . '/' . $filename . '.html';
        html_deployer_json(['success' => true, 'url' => $publicUrl]);
        break;

    case 'list':
        $files = glob(__DIR__ . '/*.html') ?: [];
        html_deployer_json(['files' => array_map('basename', $files)]);
        break;

    case 'delete':
        $filename = html_deployer_safe_filename((string) ($_POST['filename'] ?? ''));
        if ($filename === '') {
            html_deployer_json(['error' => 'Invalid filename']);
        }
        $path = __DIR__ . '/' . $filename . '.html';
        $realPath = realpath($path);
        $realDir = realpath(__DIR__);
        if ($realPath && $realDir && strpos($realPath, $realDir) === 0 && is_file($realPath)) {
            unlink($realPath);
            html_deployer_json(['success' => true]);
        }
        html_deployer_json(['error' => 'Not found']);
        break;

    default:
        html_deployer_json(['error' => 'Unknown action']);
}
