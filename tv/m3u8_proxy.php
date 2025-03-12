<?php
if (!isset($_GET['url'])) {
    header("HTTP/1.1 400 Bad Request");
    exit("No URL provided!");
}

$m3u8_url = str_replace(" ", "%20", $_GET['url']);
$cache_dir = __DIR__ . '/cache/';
if (!is_dir($cache_dir)) mkdir($cache_dir, 0755, true);

$cache_file = $cache_dir . md5($m3u8_url) . '.m3u8';
$cache_time = 300; // 5 minutes caching

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time = 300) {
    $m3u8_content = file_get_contents($cache_file);
} else {
    $ch = curl_init($m3u8_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $m3u8_content = curl_exec($ch);
    curl_close($ch);

    if (!$m3u8_content) {
        header("HTTP/1.1 404 Not Found");
        exit("M3U8 file not found!");
    }

    // Rewrite segments URLs to ts_proxy.php
    $m3u8_content = preg_replace_callback('/^(?!#)(.+\.ts.*)$/m', function($matches) use ($m3u8_url) {
        $segment_url = strpos($matches[1], 'http') === 0 ? $matches[1] : dirname($m3u8_url).'/'.$matches[1];
        return 'ts_proxy.php?segment=' . urlencode(trim($segment_url));
    }, $m3u8_content);

    file_put_contents($cache_file, $m3u8_content);
}

header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
echo file_get_contents($cache_file);
