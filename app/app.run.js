/**
* Module qui permet de gérer le fonctionnement de l'application
* En effet, la fonction $on du $rootScope va permettre de vérifier si l'utilisateur est connecté
* dès que l'application change de route il va appeler cette méthode
* 
**/
var ctrlCCNT = angular.module('ctrlCCNT'); // Importe les dépendances du parent ctrlCCNT

/**
* Fonction Run 
* Ecoute de tous les changements qui se font dans l'application
* 
**/
ctrlCCNT.run(function($rootScope, $location, AuthenticationService, SessionService, $http, NotifService, $mdDialog){
	/* Ici nous mettrons toutes les routes que l'utilisateur pourra accéder sans qu'il soit connecté */
	var routeSansLogin = ['/connexion'];

	/* Ici nous mettrons toutes les routes que l'utilisateur pourra accéder en devant être connecté */
	var routeAvecLogin = ['/home', '/config-init', '/construction'];

	/* Fonction déclenché quand un changement de route se fait dans le run de l'application */
	$rootScope.$on('$routeChangeStart', function(event, next, current) {
		if (SessionService.get('user_token') == null) { // Si le service ne trouve aucune donnée pour le token
			$location.path('/connexion'); // Redirection sur connexion
		} else {
			/* Stocke les données nécessaires : 'token' et 'id' */
			var data = {'id' : SessionService.get('user_id'), 'token' : SessionService.get('user_token')};
			/* Envoi la promesse vers l'API afin de recevoir la réponse si l'utilisateur est authentifié ou pas */
			var $promise = $http.post("assets/php/checkAuthentication.php", data);
			$promise.then(function (message) {
				/* Si l'utilisateur essaye d'accéder sur une route qui n'est pas besoin de */
				if (routeAvecLogin.indexOf($location.path()) != -1 && !message.data) {
					$location.path('/connexion');
				};

				if (routeSansLogin.indexOf($location.path()) != -1 && message.data) {
					$location.path('/home');
					NotifService.infoCon($rootScope.user.nom);
				}

				if (current != null && current.originalPath == routeAvecLogin[1] && next.originalPath != current.originalPath) {

				}
			});
		}
	});
});