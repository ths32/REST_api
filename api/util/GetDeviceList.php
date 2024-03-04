<?php

namespace Api\Util;
use Api\Models\Snsr;

chdir('../');
$path = getcwd();
require_once($path . '/config/config.php');
require_once($path.'/Models/Snsr.php');
try {
    $userOrg = (isset($_SESSION['user']) && $_SESSION['user']) ? $_SESSION['user']['org'] : 0;
    $deviceList = Snsr::getDevice($userOrg);
	if (empty($deviceList)) {
        throw new \Exception('No device data found.');
    }
    header('Content-Type: application/json');
    echo json_encode(['data' => $deviceList], JSON_UNESCAPED_UNICODE);
} catch (\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}