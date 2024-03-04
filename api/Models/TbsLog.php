<?php

namespace Api\Models;
$path = getcwd();
require_once($path.'/util/util.php');


class TbsLog
{
	private static function getDBConn()
    {
        try {
            return new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        } catch (\Exception $e) {
            return null;
        }
    }

    private static function dbConnErr() {
        return ['error' => 'Failed to connect to the database.'];
    }

    // '계측기 로그' 게시판
	// func SensorLog
    public static function sensorLog($input_type,$input)
	{
		$pdo = self::getDBConn();
        if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT id, imei, device, message from tbs_log WHERE 1=1";
            if ($input_type == 'device'){
                $query .= " AND device = :input";
            }
            if ($input_type == 'imei'){
                $query .= " AND imei = :input";
            }
            $query .= " ORDER BY id desc";
            $stmt = $pdo->prepare($query);
			if ($input !== null) $stmt->bindParam(':input', $input);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($result as &$row) {
                $row['formatted_message'] = msgHandler($row['message']);
            }
            return $result;
		} catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
	}



}


