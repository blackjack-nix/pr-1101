<!DOCTYPE html>
<html>
<head>
	<title>Résultat des données</title>
</head>
<body>
<?php
	
	include_once "./function/http.php"; //on importe la fonction http qui permet de faire une requete
	include_once "./function/log_redef.php"; //on importe la foction log qui permet d'ecrire dans un fichier texte des infos utiles au debug
	include_once "./function/stats_standard_deviation.php";

	$prevention = "Nous vous consillons cependant de consulter un médecin qui pourra effectuer un vrai diagnostic avec l'aide d'instruments de mesures plus précis";

	$tableau = $_GET; //on creer les variables qui servent a stocker les parametres de l'envoie de la requete
	$url = "http://147.215.191.35/EDP/ws_rasp.php"; //url du serveur
	$methode = "GET"; //methode de resquete
	// ## Partie Traitement du résultat ##
	$retour_json = lancerRequeteHTTP($url, $methode, $tableau);//on lance la requete http avec les bons parametres
	$retour = json_decode($retour_json,TRUE); //on décode le retour de la fonction
	$type_retour = gettype($retour);// on prend le type de retour pour savoir si ca s'est bien passé
	
	if ($type_retour == "string"){// si le type de retour est une string, cest qu'il ya eus une erreur
		echo $retour; //on affiche donc cette erreur
		exit();
	} 
	if ($type_retour == "array"){
	echo "<PRE>";


	$tab_donnee_utilisateur = $retour["donnees_utilisateur"];
	$tab_indicateur_mesure = $retour["indicateurs_mesures"];
	$tab_mesure = $retour["mesures"];

	echo "<h1>Dossier médicale du patient n°".$tab_donnee_utilisateur["no_utilisateur"].", ".strtoupper($tab_donnee_utilisateur["nom"])." ".ucwords(strtolower($tab_donnee_utilisateur["prenom"])).". </h1>";

	foreach($tab_donnee_utilisateur as $key => $value){
		echo "<b>".ucfirst(str_replace('_',' ',$key))." : </b>".ucfirst(str_replace('_', ' ',$value)) . "<br>";
	}

	foreach ($tab_mesure as $key => $value) {
		if ($value["BPM"] == 0) {
			unset($key);
		}
	}

	$Moyenne_BPM = (array_sum($tab_mesure)/sizeof($tab_mesure));
	$Ecart_type_BPM = stats_standard_deviation($tab_mesure);
    $Min_BPM = min($tab_mesure);
	$Max_BPM = max($tab_mesure);

	$Moyenne_SPO2 = $tab_indicateur_mesure['Moyenne_SPO2'];
	$Ecart_type_SPO2 = $tab_indicateur_mesure['Ecart_type_SPO2'];
	$Min_SPO2 = $tab_indicateur_mesure['Min_SPO2'];
	$Max_SPO2 = $tab_indicateur_mesure['Max_SPO2'];

	$Moyenne_temperature = $tab_indicateur_mesure['Moyenne_temperature'];
	$Ecart_type_temperature = $tab_indicateur_mesure['Ecart_type_temperature'];
	$Min_temperature = $tab_indicateur_mesure['Min_temperature'];
	$Max_temperature = $tab_indicateur_mesure['Max_temperature'];


	$IMC = round($tab_donnee_utilisateur["poids"]/(($tab_donnee_utilisateur["taille"]/100)*($tab_donnee_utilisateur["taille"]/100)));
	echo "<b>IMC : </b>".$IMC;
	echo "<br><br>";
	echo "<table border=\"2\"><thead><tr><th></th><th>Moyenne</th><th>Ecart Type</th><th>Minimum</th><th>Maximum</th></tr></thead><tbody>";

	echo "<tr>
		<td>BPM</td>
		<td>".round($Moyenne_BPM)."</td>
		<td>".round($$Ecart_type_BPM)."</td>
		<td>".round($Min_BPM)."</td>
		<td>".round($Max_BPM)."</td>
	      </tr>
	      <tr>
		<td>SPO2</td>
		<td>".round($Moyenne_SPO2)."</td>
		<td>".round($Ecart_type_SPO2)."</td>
		<td>".round($Min_SPO2)."</td>
		<td>".round($Max_SPO2)."</td>
	      </tr>
	      <tr>
		<td>Temperature</td>
		<td>".round($Moyenne_temperature)."</td>
		<td>".round($Ecart_type_temperature)."</td>
		<td>".round($Min_temperature)."</td>
		<td>".round($Max_temperature)."</td>
	      </tr></tbody></table>";

	      

	     echo "<div id=resultat>
	      	<h2> Les résultats de l'analyse par notre algorithme sont les suivants : </h2>";


	      	

	    
		 echo $prevention;
	echo "</div>";
	var_dump($retour);
}
?>

</body>
</html>