<?php

require_once("MySQLManager.php");
require_once("ContratDAO.php");
require_once("PossedeDAO.php");
/**
* Class php qui va gérer toutes les interactions avec une personne qu'elle soit employe ou employeur. 
* Tout le CRUD sera géré ici.	
*/
class EmployeeDAO {
	
	/* Récupère tous les employés (Attention seulement les employés per_admin = 0) */
	public static function getEmployees ($eta_id) {
		$db = MySQLManager::get();
		$query = "SELECT per_id, per_nom, per_prenom, per_adresse, per_dateNaissance, per_InfoSuppAdresse, per_telFixe, per_telMobile, per_codePostal, per_ville, per_mail, per_genre, dep_id, dep_nom, con_dateIn, con_dateOut, con_particularite, hor_id, hor_nom, typ_id, typ_nom FROM ccn_personne JOIN ccn_possede ON pos_per_id = per_id JOIN ccn_departement ON pos_dep_id = dep_id JOIN ccn_contrat ON con_per_id = per_id JOIN ccn_horairecontrat ON con_hor_id = hor_id JOIN ccn_typecontrat ON con_typ_id = typ_id WHERE per_admin = 0 AND per_inactif = 0 AND dep_eta_id = ?";
		if ($stmt = $db->prepare($query)) {
			$stmt->bind_param('i', $eta_id);
			/* Exécution de la requête */
		    $stmt->execute();
		    /* Lecture des variables résultantes */
		    $stmt->bind_result($per_id, $per_nom, $per_prenom, $per_adresse, $per_dateNaissance, $per_InfoSuppAdresse, $per_telFixe, $per_telMobile, $per_codePostal, $per_ville, $per_mail, $per_genre, $dep_id, $dep_nom, $con_dateIn, $con_dateOut, $con_particularite, $hor_id, $hor_nom, $typ_id, $typ_nom);
		    /* Récupération des valeurs */
		    $array = array();
		    $person = [];
		    while($stmt->fetch()) {
		    	/* Stocker le département */
		    	$dep = [];
		    	$dep['id'] = $dep_id;
		    	$dep['nom'] = $dep_nom;
				
		    	/* Stocker le type de contrat */
		    	$typ_contrat = [];
		    	$typ_contrat['id'] = $typ_id;
		    	$typ_contrat['nom'] = $typ_nom;

		    	/* Stocker le type horaire du contrat */
		    	$hor_contrat = [];
		    	$hor_contrat['id'] = $hor_id;
		    	$hor_contrat['nom'] = $hor_nom; // Problème avec les accents

		    	/* Stocker les infos du contrat */
		    	$contrat = [];
		    	$contrat['dateIn'] = $con_dateIn;
		    	$contrat['dateOut'] = $con_dateOut;
		    	$contrat['particularite'] = $con_particularite;
		    	$contrat['horaire'] = $hor_contrat;
		    	$contrat['type'] = $typ_contrat;

		    	/* Stocker la personne avec toutes ses infos */
		    	$person['id'] = $per_id;
		        $person['nom'] = $per_nom;
		        $person['prenom'] = $per_prenom;
		        $person['adresse'] = $per_adresse;
		        $person['codePostal'] = $per_codePostal;
		        $person['ville'] = $per_ville;
		        $person['mail'] = $per_mail;
		        $person['genre'] = $per_genre;
		        $person['dateNaissance'] = $per_dateNaissance;
		        $person['adresseSup'] = $per_InfoSuppAdresse;
		        $person['telFixe'] = $per_telFixe;
		        $person['telMobile'] = $per_telMobile;
		        $person['dep'] = $dep;
		        $person['contrat'] = $contrat;

		        $array[] = $person; // L'ajouter au tableau d'objet
		    }
		  	$stmt->close();
		  	MySQLManager::close();
	  		return $array;
		}
		MySQLManager::close();
		return null;
	} // setPersonne

	/*
		Permet de modififier certaines données d'une personne :
			Nom 	Prenom  	Mail
			DateNaissance	Adresse
			InfoSuppAdresse 	CodePostal
			Ville	TelephoneFixe
			TelephoneMobile	Genre
		Contrainte : Il faut impérativement le per_id de la personne !
	*/
	public static function updateEmployee ($data) {
		$db = MySQLManager::get();
		$query = "UPDATE ccn_personne SET per_nom = ?, per_prenom = ?, per_mail = ?, per_dateNaissance = ?, per_adresse = ?, per_infoSuppAdresse = ?, per_codePostal = ?, per_ville = ?, per_telFixe = ?, per_telMobile = ?, per_genre = ? WHERE per_id = ?";
		if ($stmt = $db->prepare($query)) {
			$stmt->bind_param('ssssssissssi', $data['nom'], $data['prenom'], $data['mail'], $data['dateNaissance'], $data['adresse'], $data['adresseSup'], $data['code'], $data['localite'], $data['telFixe'], $data['telMobile'], $data['genre'], $data['id']);
		  	$stmt->execute();
		  	$stmt->close();
		  	MySQLManager::close();
		  	if (PossedeDAO::updatePossede($data)) { // Appelle la méthode static de PossedeDAO qui modifie le département assigné à la personne
				return ContratDAO::updateContratEmp($data); // Appelle la méthode static de ContratDAO qui permet de modifier son contrat
		  	} else {
		  		return false;
		  	}
		}
		MySQLManager::close();
		return false;
	}

	/*
		Permet de mettre un employé en état inactif on ne pourra pas l'utiliser dans l'application
		En paramètre: l'id de l'utilisateur qui devra être mis en inactif
	*/	
	public static function supEmployee($user_id) {
		$db = MySQLManager::get();
		$query = "UPDATE ccn_personne SET per_inactif = 1 WHERE per_id = ?";
		if ($stmt = $db->prepare($query)) {
			$stmt->bind_param('i', $user_id);
		  	$stmt->execute();
	  		MySQLManager::close();
			return true;
		}
		MySQLManager::close();
		return false;
	}

	/*
		Permet d'ajouter les données d'une personne dans la table ccn_personne
		En paramètre: un tableau de data[] contenant :
			per_nom, per_prenom, per_mail, per_mdp, per_token, per_dateNaissance, 
			per_adresse, per_infoSuppAdresse, per_codePostal, per_ville, per_admin, 
			per_telFixe, per_telMobile, per_dep_id, per_genre

		Contrainte BDD : l'attribut per_dep_id doit être > 0 et doit exister dans la bdd
	*/	
	public static function insertEmployee ($data) {
		$db = MySQLManager::get();
		/* Insertion dans la table ccn_personne */
		$query = "INSERT INTO ccn_personne (per_nom, per_prenom, per_mail, per_mdp, per_token, per_dateNaissance, per_adresse, per_infoSuppAdresse, per_codePostal, per_ville, per_admin, per_telFixe, per_telMobile, per_genre) VALUES (?, ?, ?, NULL, NULL, ?, ?, ?, ?, ?, 0, ?, ?, ?)";
		if ($stmt = $db->prepare($query)) {
			$stmt->bind_param('ssssssissss', $data['nom'], $data['prenom'], $data['mail'], $data['dateNaissance'], $data['adresse'], $data['adresseSup'], $data['code'], $data['localite'], $data['telFixe'], $data['telMobile'], $data['genre']);
		  	$stmt->execute();
		  	$per_id = $stmt->insert_id;
		  	$data['id'] = $per_id;
		  	$stmt->close();
		  	MySQLManager::close();
		  	if (PossedeDAO::insertPossede($data)) { // Appelle la méthode static de PossedeDAO qui insére le département à une personne
		  		return ContratDAO::insertContrat($data); // Appelle la méthode static de ContratDAO qui permet d'insérer un contrat à une personne
		  	}
		  	return -1;
		}
		MySQLManager::close();
		return -1;
	} // insertPersonne
}

?>