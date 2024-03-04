<?php

namespace Api\Models;
use \PDO;

class Mqtt
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
	// 3분전 ~ 현재 센서 데이터 개수 확인
	public static function getCntCurrentSensor($imei)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
            $query = "SELECT COUNT(*) AS cnt FROM tbs_data WHERE imei = :imei AND rdate BETWEEN DATE_ADD(NOW(), INTERVAL -3 MINUTE) AND NOW()";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':imei', $imei);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // TEST DONE
	// imei가 기등록 되어있는지 확인
	public static function checkImei($imei)
	{
		try {
            $pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
            $query = "SELECT * FROM tbs WHERE imei = :imei LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':imei', $imei);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
	}

}
