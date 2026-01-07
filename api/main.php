<?php
require_once '../includes/db.php';

$pdo = getDB();

// 1. Get Site Status
$stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'site_status' LIMIT 1");
$stmt->execute();
$status = $stmt->fetchColumn() ?: 'online';

// 2. Fetch Tools
$stmt = $pdo->prepare("SELECT * FROM tools WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
$stmt->execute();
$tools = $stmt->fetchAll();

// Map DB structure to JS-friendly structure
$formattedTools = array_map(function($t) {
    return [
        'id' => $t['slug'],
        'name' => ['en' => $t['name_en'], 'zh' => $t['name_zh']],
        'description' => ['en' => $t['desc_en'], 'zh' => $t['desc_zh']],
        'icon' => $t['icon'],
        'category' => $t['category'],
        'subCategory' => $t['sub_category'],
        'size' => $t['size'],
        'url' => $t['url'],
        'component' => $t['component']
    ];
}, $tools);

// 3. Fetch Changelogs
$stmt = $pdo->prepare("SELECT * FROM changelogs ORDER BY date DESC, version DESC");
$stmt->execute();
$logs = $stmt->fetchAll();

$formattedLogs = array_map(function($l) {
    return [
        'version' => $l['version'],
        'date' => $l['date'],
        'title' => ['en' => $l['title_en'], 'zh' => $l['title_zh']],
        'type' => $l['type'],
        'items' => json_decode($l['items_json'])
    ];
}, $logs);

// 4. Fetch Messages (Last 50)
$stmt = $pdo->prepare("SELECT username, content, created_at, is_admin FROM messages ORDER BY id DESC LIMIT 50");
$stmt->execute();
$messages = $stmt->fetchAll();

// 5. Fetch Settings
$stmt = $pdo->prepare("SELECT `key`, value FROM settings");
$stmt->execute();
$settingsRaw = $stmt->fetchAll();
$settings = [];
foreach ($settingsRaw as $s) {
    $settings[$s['key']] = $s['value'];
}

header('Content-Type: application/json');
echo json_encode([
    'status' => $status,
    'tools' => $formattedTools,
    'changelogs' => $formattedLogs,
    'messages' => $messages,
    'settings' => $settings
]);
?>
