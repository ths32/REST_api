<?php

namespace Api\Util;
use Api\Models\Tbi;

chdir('../');
$path = getcwd();
require_once($path.'/Models/Irrigation.php');
require_once($path . '/config/config.php');
require_once($path.'/Models/Snsr.php');
try {
    $irrigationList = Tbi::getIrrigation();
	if (empty($irrigationList)) {
        throw new \Exception('No irrigation data found.');
    }
    header('Content-Type: application/json');
    echo json_encode(['data' => $irrigationList], JSON_UNESCAPED_UNICODE);
} catch (\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
