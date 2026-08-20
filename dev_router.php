<?php
// Dev-only router for `php -S 127.0.0.1:8081 -t /home/leafy/projects/pondokhuda`.
// Serves ONLY /api/* endpoints (prod URL shape). Everything else -> 404,
// so web/.env, sql/, config/ etc. stay unreachable.

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($uri, '/api/') !== 0) {
    http_response_code(404);
    echo json_encode(array('error' => 'not_found'));
    return true;
}

$rel = substr($uri, 5); // strip '/api/'
if (strpos($rel, '.') === 0 || strpos($rel, '/.') !== false) {
    http_response_code(404);
    echo json_encode(array('error' => 'not_found'));
    return true;
}

$file = __DIR__ . '/api/' . $rel;
if (!is_file($file)) {
    http_response_code(404);
    echo json_encode(array('error' => 'not_found'));
    return true;
}

return false; // let the built-in server serve it