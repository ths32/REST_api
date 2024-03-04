<?php
namespace Api\Models;
use \PDO;

class Tbw
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
    public static function getCurrentWeather($org)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
            $query = "SELECT * FROM tbw WHERE org = :org 
                        AND base_date = DATE_FORMAT(NOW(), '%Y%m%d') 
                        AND base_time = LPAD(CONCAT(HOUR(NOW()), '00'), 4, '0') LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':org', $org);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // TEST DONE
    public static function getCurrentWeather_test($org)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
            $query = "SELECT * FROM tbw WHERE org = :org 
                        AND base_date = DATE_FORMAT('2023-11-01 14:40:32.000', '%Y%m%d') 
                        AND base_time = LPAD(CONCAT(HOUR('2023-11-01 14:40:32.000'), '00'), 4, '0') LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':org', $org);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public static function sumPcpByOrgAndDate($org)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
            $query = "SELECT SUM(pcp) AS total_pcp FROM tbw WHERE org = :org AND base_date = DATE_FORMAT(NOW(), '%Y%m%d')";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':org', $org);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total_pcp'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function sumPcpByOrgAndDate_test($org)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
            $query = "SELECT SUM(pcp) AS total_pcp FROM tbw WHERE org = :org AND base_date = DATE_FORMAT('2023-09-04 14:40:32.000', '%Y%m%d')";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':org', $org);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total_pcp'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

}


