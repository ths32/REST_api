<?php

namespace Api\Models;

class Tbo
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
    // '기관 관리' 게시판 (SELECT ONLY)  ('admin' only)
    // $orderBy // ex1 : 'ORDER BY id ASC' // ex2 : 'ORDER BY created_at DESC 
    // ex3. $organizationsDefaultOrder = Organization::getOrg($userId); 
    // ex4.$organizationsCustomOrder = Organization::getOrg($userId, 'ORDER BY created_at DESC');
    // MODIFIED // usage examples : getOrg($userId,true), getOrg($userId,false), getOrg(true), getOrg()
    // part of the func 'orgList'
   
    // TEST DONE
    // '기관 관리' 게시판 (SELECT ONLY)  ('admin' only)
    // orgList
    public static function getOrg2()
    {
        $pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
        try {
            $query = "select ROW_NUMBER() OVER (ORDER BY created_at ASC) AS num, name, created_at from tbo ORDER BY created_at desc";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    // MODIFICATION NEEDED

    // orgForm
    // TEST DONE
    public static function findOrganizationById($id)
    {
        $pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT * FROM tbo WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // =============================================================================================================

    // used for AreaForm
    // TEST DONE
    public static function getIdName()
    {
        $pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT id, name FROM tbo ORDER BY created_at DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // TEST DONE
    public static function countOrganizationsByName($name)
    {
        $pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT COUNT(*) FROM tbo WHERE name = :org_name";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':org_name', $name);
            $stmt->execute();

            return $stmt->fetchColumn();
        } catch (\Exception $e) {
            return -1;
        }
    }

}