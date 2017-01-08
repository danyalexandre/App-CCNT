<?php

	require_once("classes/Sanitizer.php");
	require_once("classes/UserAuthentication.php");
	require_once("classes/EtatInitial.php");

	$authData = Sanitizer::getSanitizedJSInput(); // Récupère les données aseptisée

	$authentified = UserAuthentication::checkAuthentication($authData['user_id'], $authData['user_token']);

	if ($authentified == false) {
		echo("Vous n'avez pas le droit d'appeler cette requete ou requete invalide");
	} else {
		require_once("classes/EtatInitial.php");
		$res = EtatInitial::insertPersonInEstablishment($authData);
		echo($res);
	}
	
?>