<?php
header('Content-Type: application/json');

chdir('../');
$path = getcwd();

require_once($path.'/util/util.php');
require_once($path.'/config/config.php');
require_once($path.'/config/database.php');

//$result = null;

try {

    $date1 = date('Y-m-d H:i', strtotime('-9 minutes'));
    $date2 = date('Y-m-d H:i');
    $device = [];

    $db = new Database();

    $sql = "
        SELECT imei, device, device_time 
        FROM tbs 
        WHERE org IN (1,2) AND area > 0 AND deleted_at IS NULL 
        ORDER BY device ASC";
    $result = $db->query($sql);

    if ($result !== false) {
        foreach ($result as $tmp) {
            $device_time = funcDeviceTimeFormat($tmp['device_time']);

            //echo "device time : ".$device_time. "<br>";
            //echo "date1 : ".$date1."<br>";
            //echo "date2 : ".$date2."<br>";
            //echo "-----------------------------";

            if ($date1 <= $device_time && $date2 >= $device_time) {
                //
            } else {
                $device[] = $tmp['device'];
            }
    
            unset($device_time);
        }
       
        echo json_encode(['data' => $device]);
    } else {
        echo json_encode(['data' => []]);
    }
    if (is_object($result)) $result->close();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 

// finally {
//     if (isset($db)) $db->__destruct();
//     //if (isset($result)) $result->close();
// }

?>
