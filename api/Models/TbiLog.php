<?php

namespace Api\Models;

class TbiLog
{
	private static function getDBConn()
    {
        try {
            return new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        } catch (\Exception $e) {
            return null;
        }
    }

    private static function dbConnErr() 
	{
        return ['error' => 'Failed to connect to the database.'];
    }

	// TEST DONE
	// '관수 로그' 게시판
	// func irrLog
	public static function irrLog($irr_id = null,$irr_chn = null)
	{
		$pdo = self::getDBConn();
        if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT ROW_NUMBER() OVER (ORDER BY created_at) AS irr_num, irr_nm, irr_chn, message, result, created_at FROM tbi_log where 1=1";
			$conditions = [];
			if ($irr_id !== null && is_numeric($irr_id)) $conditions[] = "irr_id = :irr_id";
			if ($irr_chn !== null && is_numeric($irr_chn)) $conditions[] = "irr_chn = :irr_chn";
			if (!empty($conditions)) $query .= " AND " . implode(" AND ", $conditions);
			$query .= " ORDER BY created_at DESC";
			$stmt = $pdo->prepare($query);
			if ($irr_id !== null) $stmt->bindParam(':irr_id', $irr_id);
			if ($irr_chn !== null) $stmt->bindParam(':irr_chn', $irr_chn);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
	}



}