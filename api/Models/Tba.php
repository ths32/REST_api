<?php
namespace Api\Models;
use \PDO;

class Tba
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

	// TEST DONE
	public static function getArea($userOrg)
	{
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT * FROM tba WHERE org IS NOT NULL";
			if ($userOrg > 0) $query .= " AND org = :user_org";
			$stmt = $pdo->prepare($query);
			if ($userOrg > 0) $stmt->bindParam(':user_org', $userOrg);
			$stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			$data = [];
			foreach($result as $row){
				$data[$row['org']][] = $row;
			}
			return $data;
		} catch (\Exception $e){
			return ['error' => $e->getMessage()];
		}
	}

	// TEST DONE // 2024-02-23기준, 모든 areaId가 1개씩 밖에 ROW가 없어서 limit이 true든 false든 하나씩 뜸.
	public static function getFilePathById($areaId, $limit1 = true)
    {
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT file_path FROM tba WHERE id = :area_id";
            if ($limit1) $query .= " LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':area_id', $areaId);
            $stmt->execute();

            return $stmt->fetchColumn();
        } catch (\Exception $e) {
            return null; // or handle the error
        }
    }

	// TEST DONE
	// $columns = ['id', 'name']; // ex. $data = Tba::getAreaWithColumns($columns);
	public static function getAreaWithColumns($columns)
    {
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT " . implode(',', $columns) . " FROM tba ORDER BY created_at DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

	// TEST DONE
	// '현장 관리' 게시판 ('admin' ONLY)
	// part of the function areaList 
	// input page should be included in the func areaList after the func is fully implemented
	public static function getAreaList()
	{
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();	
		try {
            $query = "SELECT ROW_NUMBER() OVER (ORDER BY a.created_at ASC) AS num, 
						a.name as name, o.name AS org, a.created_at FROM tba a
							LEFT JOIN tbo o ON a.org = o.id
								ORDER BY a.created_at DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
	}


}