<?php
header('Content-Type: application/json');

$path = getcwd();
require_once($path.'/config/config.php');
require_once($path.'/config/database.php');

try {

    $db = new Database();

    $sql = "SELECT imei, device, device_time FROM tbs WHERE org IN (1,2) AND area > 0 AND deleted_at IS NULL ORDER BY device ASC";
    $result = $db->query($sql);

    if ($result !== false) {
        echo json_encode(['data' => $result]);
    } else {
        echo json_encode(['data' => []]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>
