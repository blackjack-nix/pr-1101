<?php
function lancerRequeteHTTP(string $url, string $methode, array $parametres)
{
	$liste_parametres = '';
	$index = 0;
	foreach ($parametres as $nom_parametre => $valeur_parametre)
	{
		if ($index == 0)
		{
			$liste_parametres = $liste_parametres.'?'.$nom_parametre.'='.$valeur_parametre;
		}
		else
		{
			$liste_parametres = $liste_parametres.'&'.$nom_parametre.'='.$valeur_parametre;			
		}
	$index++;
	}
	// URL du script serveur
	$url = $url.$liste_parametres;
	// Initialisation de la ressource CURL
	$CURL=curl_init();
		$options=array(
			CURLOPT_URL            => $url, // Url cible (l'url la page que vous voulez télécharger)
			CURLOPT_RETURNTRANSFER => true, // Retourner le contenu téléchargé dans une chaine (au lieu de l'afficher directement)
			CURLOPT_HEADER         => false, // Ne pas inclure l'entête de réponse du serveur dans la chaine retournée
			CURLOPT_CUSTOMREQUEST  => $methode,
			);

		// Création de la ressource cURL
		$CURL=curl_init();

		// Configuration des options de téléchargement
		curl_setopt_array($CURL,$options);
		// Exécution de la requête
		$json_retour_serveur = curl_exec($CURL);      // Le contenu téléchargé est enregistré dans la variable $contenu. Libre à vous de l'afficher.
		// Fermeture de la session cURL
		curl_close($CURL);
		return($json_retour_serveur) ;
}
		
?>
