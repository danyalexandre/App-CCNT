(function(){
'use strict';
/**
 * Déclaration de l'application ctrlCCNT
 */
var ctrlCCNT = angular.module('ctrlCCNT', ['ngRoute','ngMaterial', 'ui-notification']);
/**
 * Configuration du module principal : ctrlCCNT
 */
ctrlCCNT.config(['$routeProvider',
    function($routeProvider) { 
        // Système de routage
        $routeProvider
        .when('/home', {
            templateUrl: 'app/components/home/homeView.html',
        })
        .when('/connexion', {
            templateUrl: 'app/components/connexion/connexionView.html',
            controller: 'connexionController'
        })
        .otherwise({
            redirectTo: '/connexion'
        });
    }
]);

ctrlCCNT.config(function ($httpProvider) {
    $httpProvider.interceptors.push(function ($location) {
        return {
            'responseError': function (rejection) {
                if (rejection.status === 401) {
                    $location.url('/connexion?returnUrl=' + $location.path());
                }
            }
        };
    });
});

})();