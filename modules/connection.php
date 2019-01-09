<?php

/******************************
*  Connexion principale du site
******************************/

class Connect {
    
	/*******************************
    * Connexion à la base de données
    *******************************/
	public static function getDB() {
		try {
			$host = ""; // Hôte de la base de données
			$dbname = ""; // Nom de la base de données
			$user = ""; // Nom d'utilisateur de la base de données
			$pass = ""; // Mot de passe de la base de données
            
			$db = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $user, $pass);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->exec("SET CHARACTER SET utf8");
			return $db;
		}

		catch(PDOException $e) {
			die('La base de données est déconnecté');
		}
	}
}
