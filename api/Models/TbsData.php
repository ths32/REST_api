<?php

namespace Api\Models;
use \PDO;

class TbsData
{
	private static function getDBConn()
    {
        try {
            return new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        } catch (\Exception $e) {
            return null;
        }
    }

    // TEST DONE
	public static function getByImei($imei)
    {
		$pdo = self::getDBConn();
        if (!$pdo) return ['error' => 'Failed to connect to the database.'];
        try {
            $query = "SELECT * FROM tbs_data WHERE imei = :imei ORDER BY rdate DESC LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':imei', $imei);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
   
    // sensorDetail
    // TEST DONE
	public static function getByColImeiDate($col, $imei, $s_date, $e_date)
	{
		$pdo = self::getDBConn();
        if (!$pdo) return ['error' => 'Failed to connect to the database.'];
        try {
            $s_date = $s_date . ' 00:00:00';
            $e_date = $e_date . ' 23:59:59';
            $query = "SELECT ROUND(AVG($col), 1) AS v, LEFT(rdate, 10) AS date 
				FROM tbs_data WHERE imei = :imei 
					AND rdate BETWEEN :start_date AND :end_date  
                    GROUP BY LEFT(rdate, 10) 
						ORDER BY rdate ASC";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':imei', $imei);
			//$stmt->bindParam(':start_date', $s_date . ' 00:00:00');
            //$stmt->bindParam(':end_date', $e_date . ' 23:59:59');
			$stmt->bindParam(':start_date', $s_date);
            $stmt->bindParam(':end_date', $e_date);
            
            // check values of this :col when this method is combined with other methods.
            //$stmt->bindParam(':col', $col);
            //$stmt->bindValue(':col', $col);
            $stmt->execute();
            //$stmt->execute([':imei' => $imei, ':start_date' => $s_date . ' 00:00:00', ':end_date' => $e_date . ' 23:59:59']);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
	}


     // ----------------------------------------------------------------------------------------------------

    // TEST DONE
    // sensorData // sensorDataExcel
    public static function getDataListByImeiDate2($org, $area, $device, $s_date, $e_date)
	{
    try {
        $pdo = self::getDBConn();
        if (!$pdo) return ['error' => 'Failed to connect to the database.'];
        $s_date = $s_date . ' 00:00:00';
        $e_date = $e_date . ' 23:59:59';
        $query = "SELECT id, device, bat, temp, humi, ph, conduc, nitro, phos, pota, weather_tmp, weather_pcp, device_time, rdate 
                    FROM tbs_data 
					WHERE device IN (" . implode(",", array_fill(0, count($device), "?")) . ")
						AND device_time != '' AND device != ''
						AND rdate BETWEEN ? AND ?
                        AND org = ? and area = ?
						ORDER BY device_time DESC, device ASC";
        //echo "org : ".$org;
        //echo "device : ".$device;
        //echo "device : " . implode(", ", $device) . "<br>";
        //echo "area : ".$area;
        //echo "s_date : ".$s_date;
        //echo "e_date : ".$e_date;
        //echo $query;
        $stmt = $pdo->prepare($query);

        foreach ($device as $index => $value) {
            $stmt->bindParam(($index + 1), $device[$index]);
        }
        // Bind other parameters
        $startIndex = count($device) + 1;
        $stmt->bindParam($startIndex++, $s_date);
        $stmt->bindParam($startIndex++, $e_date);
        $stmt->bindParam($startIndex++, $org);
        $stmt->bindParam($startIndex, $area);
        //$stmt->execute(array_merge($device, [$org, $area, $s_date, $e_date]));
        //echo "Query with bound parameters: " . $stmt->queryString . "<br>";

        $stmt->execute();


        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    	} catch (\Exception $e) {
        	return ['error' => $e->getMessage()];
    	}
	}



}