<?php

namespace App;

class Connection {

	public static function getDb() {
		try {

			$conn = new \PDO(
				"mysql:host=*******;dbname=*****;charset=utf8",
				"******",
				"*******" 
			);

			return $conn;

		} catch (\PDOException $e) {
			//.. tratar de alguma forma ..//
		}
	}
}

?>




























