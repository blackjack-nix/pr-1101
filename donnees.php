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

	$prevention = "<br>Nous vous consillons cependant de consulter un médecin qui pourra effectuer un vrai diagnostic avec l'aide d'instruments de mesures plus précis<br>";

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
	$tab_mesure_SPO2 = $retour["indicateurs_mesures"];
	$tab_mesure = $retour["mesures"];

	$age = $tab_donnee_utilisateur["age"];

	echo "<h1>Dossier médicale du patient n°".$tab_donnee_utilisateur["no_utilisateur"].", ".strtoupper($tab_donnee_utilisateur["nom"])." ".ucwords(strtolower($tab_donnee_utilisateur["prenom"])).". </h1>";

	$IMC = round($tab_donnee_utilisateur["poids"]/(($tab_donnee_utilisateur["taille"]/100)*($tab_donnee_utilisateur["taille"]/100)));
	foreach($tab_donnee_utilisateur as $key => $value){
		if (($key == "abrege_pratique_sportive" && $value == "HAUT_NIVEAU") || ($key == "abrege_pratique_sportive" && $value == "REGULIERE") || ($key=="fumeur" && $value==1) || ($key == "abrege_contexte_mesures_cliniques" && $value != "REPOS")) {
			echo "<p style=\"color:#FF0000\";>";
		}
		echo "<b>".ucfirst(str_replace('_',' ',$key))." : </b>".ucfirst(str_replace('_', ' ',$value)) . "</p>";
	}
	if ($IMC > 30) {
		echo "<p style=\"color:#FF0000\";>";
	}
	echo "<b>IMC : </b>".$IMC."</p>";



	$tab_mesure_bpm = array();
	foreach ($tab_mesure as $key => $value) {
		if ($value["valeur_BPM"] != 0) {
			$tab_mesure_bpm[]=$value["valeur_BPM"];
		}
	}

	$Moyenne_BPM = (array_sum($tab_mesure_bpm)/sizeof($tab_mesure_bpm));
	$Ecart_type_BPM = stats_standard_deviation($tab_mesure_bpm);
  $Min_BPM = min($tab_mesure_bpm);
	$Max_BPM = max($tab_mesure_bpm);

	if ($Moyenne_BPM <= 52) {
		//5eme percentil
		echo "<p style=\"color:#FF0000\";><b>Positionnement :</b> 5ème percentile</p>";

	}
	elseif ($Moyenne_BPM <= 56) {
		//10eme percentil
		echo "<p style=\"color:#FF8C00\";><b>Positionnement :</b> 10ème percentile</p>";

	}
	elseif ($Moyenne_BPM <= 61) {
		//25eme percentil
		echo "<p><b>Positionnement :</b>15ème percentile</p>";

	}
	elseif ($Moyenne_BPM <= 68) {
		//50eme percentil
		echo "<p><b>Positionnement :</b>50ème percentile</p>";

	}
	elseif ($Moyenne_BPM <= 75) {
		// 75eme perfentil
		echo "<p><b>Positionnement :</b>75ème percentile</p>";

	}
	elseif ($Moyenne_BPM <= 83) {
		// 90eme percentil
		echo "<p style=\"color:#FF8C00\";><b>Positionnement :</b> 90ème percentile</p>";

	}
	elseif ($Moyenne_BPM <= 88) {
		//95eme percentil
		echo "<p style=\"color:#FF0000\";><b>Positionnement :</b> 95ème percentile</p>";

	}




	$tab_mesure_SPO2 = array();
	foreach ($tab_mesure as $key => $value) {
		if ($value["valeur_SPO2"] != 0) {
			$tab_mesure_SPO2[]=$value["valeur_SPO2"];
		}
	}
	$Moyenne_SPO2 = (array_sum($tab_mesure_SPO2) / sizeof($tab_mesure_SPO2));
	$Ecart_type_SPO2 = stats_standard_deviation($tab_mesure_SPO2);
	$Min_SPO2 = min($tab_mesure_SPO2);
	$Max_SPO2 = max($tab_mesure_SPO2);

	$tab_mesure_temperature = array();
	foreach ($tab_mesure as $key => $value) {
		if ($value["valeur_temperature"] != 0) {
			$tab_mesure_temperature[]=$value["valeur_temperature"];
		}
	}
	$Moyenne_temperature = (array_sum($tab_mesure_temperature)/sizeof($tab_mesure_temperature));
	$Ecart_type_temperature =stats_standard_deviation( $tab_mesure_temperature);
	$Min_temperature = min($tab_mesure_temperature);
	$Max_temperature = max($tab_mesure_temperature);

	if ($Moyenne_temperature > 38) {
		echo "<p style=\"color:#FF0000\";>";
	}
	echo "<b>Température : </b>".round($Moyenne_temperature);


	echo "<br><br>";
	echo "<table border=\"2\"><thead><tr><th></th><th>Moyenne</th><th>Ecart Type</th><th>Minimum</th><th>Maximum</th></tr></thead><tbody>";

	echo "<tr>
		<td>BPM</td>
		<td>".round($Moyenne_BPM)."</td>
		<td>".round($Ecart_type_BPM)."</td>
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

					// ## arbre de décision ##
 					if ($Moyenne_BPM > 88)
					{
							if ($IMC > 30) {
								echo "Vous avez un IMC supperieur à la moyenne. Cela signifie que vous êtes en surpoids. Il est fort probable que votre haut rythme cardiaque soit du à cette excet de masse.";
							}
							if ($Moyenne_temperature < 38)
							{ //Pas fièvre
									if ($Moyenne_SPO2)
									{//o2 haut


										echo "Vous êtes potentiellement atteint de tachycardie.";
									}else
									{//o2 bas
										if ($age > 60)
										{
											echo "Il est probable que vous soyez atteint d'athérosclérose. L'athérosclérose, ou artériosclérose, est une maladie touchant les artères de gros et moyen calibre et caractérisée par l'apparition de plaques d'athérome, fréquente chez les personnes agées de plus de 60 ans.";
										}
										else
										{
											//maladie du coeur / maladie des poumons
											echo "Vous êtes probablement atteint d'une maladie du coeur ou des poumons";
										}
									}

							} else
							{
								echo "Vous avez de la fièvre. Il est fort probable que votre haut rythme cardiaque soit du à cette fièvre. Il faudra reprendre des mesures une fois la fièvre tombée";
							}

					}




		 echo $prevention;
	echo "</div>";
	var_dump($retour);
}
?>

</body>
</html>
