<!DOCTYPE html>
<html>
<head>
	<title>Résultat des données</title>
	<meta charset="utf-8"/>
	<link rel="stylesheet" type="text/css" href="./style/style_donnee.css"/>
</head>
<body>
	<div id="fond">
		<img src="https://ak8.picdn.net/shutterstock/videos/12587588/thumb/1.jpg" alt="fond">
	</div>
<?php

	include_once "./function/http.php"; //on importe la fonction http qui permet de faire une requete
	include_once "./function/log_redef.php"; //on importe la foction log qui permet d'ecrire dans un fichier texte des infos utiles au debug
	include_once "./function/stats_standard_deviation.php";//on importe la fonction qui nous permet de recalculer les ecarts types
	include_once "./function/graphique_BPM.php";//on importe la librairie qui permet d creer un graph avec les points


	$prevention = "<br>Nous vous consillons cependant de consulter un médecin qui pourra effectuer un vrai diagnostic avec l'aide d'instruments de mesures plus précis<br>";//message de prévention

	// ## Partie préparation de la requete ##
	$tableau = $_GET; //on creer les variables qui servent a stocker les parametres de l'envoie de la requete
	$url = "http://147.215.191.35/EDP/ws_rasp.php"; //url du serveur
	$methode = "GET"; //methode de resquete

	// ## Partie Traitement du résultat ##
	$retour_json = lancerRequeteHTTP($url, $methode, $tableau);//on lance la requete http avec les bons parametres
	$retour = json_decode($retour_json,TRUE); //on décode le retour de la fonction
	$type_retour = gettype($retour);// on prend le type de retour pour savoir si ca s'est bien passé

	if ($type_retour != "array")
	{// si le type de retour est une string, cest qu'il ya eus une erreur
		echo $retour; //on affiche donc cette erreur
		exit();
	}
	echo "<PRE>";

// ## Traitement des données recues par le serveur
	$tab_donnee_utilisateur = $retour["donnees_utilisateur"];//on recupere le premier tableau correspodnant aux donées du patient
	$tab_mesure_SPO2 = $retour["indicateurs_mesures"];// ainsi que celui des indications de mesures
	$tab_mesure = $retour["mesures"];// et celui des mesures brutes

	$age = $tab_donnee_utilisateur["age"];//on récupere la valleur de l'age
	$IMC = round($tab_donnee_utilisateur["poids"]/(($tab_donnee_utilisateur["taille"]/100)*($tab_donnee_utilisateur["taille"]/100)));//calcul de l'imc


	echo "<h1>Dossier médicale du patient n°".$tab_donnee_utilisateur["no_utilisateur"].", ".strtoupper($tab_donnee_utilisateur["nom"])." ".ucwords(strtolower($tab_donnee_utilisateur["prenom"])).":</h1>";//permet de metre en forme le titre de la page

	// Partie affichage des données fournies par le serveur
echo "<div id=\gauche\">";
	echo "<div id=\"donnee_serveur\">";//div qui permet la mise en forme css
		echo "<fieldset>";//permet de mettre un cadre
			echo "<legend><b><i>Données du patient</i></b></legend>";//legende du titre
				foreach($tab_donnee_utilisateur as $key => $value){
					if (($key == "abrege_pratique_sportive" && $value == "HAUT_NIVEAU") || ($key == "abrege_pratique_sportive" && $value == "REGULIERE") || ($key=="fumeur" && $value==1) || ($key == "abrege_contexte_mesures_cliniques" && $value != "REPOS")) {
						echo "<p id=\"rouge\">";// si répond a un critère, on met une flag pour le css
					}
					echo "<b>".ucfirst(str_replace('_',' ',$key))." : </b>".ucfirst(str_replace('_', ' ',$value)) . "</p>";//on affiche la clé et la valeur, apres avoir remplacé les '_' par ' ', puis on pet en majuscule la premiere lettre
				}
		echo "</fieldset>";//on ferme le cadre
	echo "</div>";//on ferme la div

	// ## traitement des tableaux ##
	//traitement du tableau des bpm && traitement tableau des dates

	$tab_mesure_bpm = array();//tableau qui contient les valeurs des pbm
	$tab_mesure_date = array();//tableau qui contient chaque date

	foreach ($tab_mesure as $key => $value) {
		if ($value["valeur_BPM"] != 0) {//si la mesure de bpm n'est pas nulle,
			$tab_mesure_bpm[]=$value["valeur_BPM"];//on met la mesure de pbm dans le tab de pbm
			$tab_mesure_date[]=$value["date_mesure"];// et la mesure de date dans le tab de date
		}else {
			unset($key);// sinon, on supprime la ligne
		}
	}



	//traitement du tableau des spo2
	$tab_mesure_SPO2 = array();//tabeau qui contient les valeur de chaque spo2
	//on fait le meme traitement que précedement
	foreach ($tab_mesure as $key => $value) {
		if ($value["valeur_SPO2"] != 0) {
			$tab_mesure_SPO2[]=$value["valeur_SPO2"];
		}
		else
		{
			unset($key);
		}
	}

	//traitement du tableau des temperatures
	$tab_mesure_temperature = array();
	foreach ($tab_mesure as $key => $value) {
		if ($value["valeur_temperature"] != 0)
		{
			$tab_mesure_temperature[]=$value["valeur_temperature"];
		}
		else
		{
			unset($key);
		}
	}



	// ## recalcul des données après traitement ##

	$Moyenne_BPM = (array_sum($tab_mesure_bpm)/sizeof($tab_mesure_bpm));//on calcul la moyenne en faisant la somme du tableau et en divisant par la taille
	$Ecart_type_BPM = stats_standard_deviation($tab_mesure_bpm);// on recalcul l'ecart type
  $Min_BPM = min($tab_mesure_bpm); //on récupere le min
	$Max_BPM = max($tab_mesure_bpm); //et le max

	$Moyenne_SPO2 = (array_sum($tab_mesure_SPO2) / sizeof($tab_mesure_SPO2));
	$Ecart_type_SPO2 = stats_standard_deviation($tab_mesure_SPO2);
	$Min_SPO2 = min($tab_mesure_SPO2);
	$Max_SPO2 = max($tab_mesure_SPO2);

	$Moyenne_temperature = (array_sum($tab_mesure_temperature)/sizeof($tab_mesure_temperature));
	$Ecart_type_temperature =stats_standard_deviation( $tab_mesure_temperature);
	$Min_temperature = min($tab_mesure_temperature);
	$Max_temperature = max($tab_mesure_temperature);


	echo "<br>";
	echo "<div id=\"donnee_calcul\">";//on fait une div pour les données calculées
		echo "<fieldset>";//on construit le cadre pour cette div
			echo "<legend><b><i>Calculs de données</i></b></legend>";//on fait une legende en guise de titre

				if ($Moyenne_temperature > 38)
				{
					echo "<p id=\"rouge\">";
				}
				echo "<b>Température : </b>".round($Moyenne_temperature,2)."<br>";

				//on cherche dans quelle tranche se situe le patient
				if ($Moyenne_BPM <= 52)
				{	//5eme percentil
					echo "<p id=\"rouge\"><b>Positionnement :</b> 5ème percentile</p>";
				}
				elseif ($Moyenne_BPM <= 56)
				{	//10eme percentil
					echo "<p id=\"orange\"><b>Positionnement :</b> 10ème percentile</p>";
				}
				elseif ($Moyenne_BPM <= 61)
				{	//25eme percentil
					echo "<p><b>Positionnement :</b>15ème percentile</p>";
				}
				elseif ($Moyenne_BPM <= 68)
				{	//50eme percentil
					echo "<p><b>Positionnement :</b>50ème percentile</p>";
				}
				elseif ($Moyenne_BPM <= 75)
				{	// 75eme perfentil
					echo "<p><b>Positionnement :</b>75ème percentile</p>";
				}
				elseif ($Moyenne_BPM <= 83)
				{	// 90eme percentil
					echo "<p id=\"orange\"><b>Positionnement :</b> 90ème percentile</p>";
				}
				elseif ($Moyenne_BPM <= 88)
				{	//95eme percentil
					echo "<p id=\"rouge\"><b>Positionnement :</b> 95ème percentile</p>";
				}

				//on regarde si l'imc est supperieur a 30 (obésité)
				if ($IMC > 30)
				{
					echo "<p id=\"rouge\">";// si c'est le cas, on met un flag pour le css
				}
				echo "<b>IMC : </b>".$IMC."</p>";//on l'affiche

		echo "</fieldset>";//on ferme le cadre
	echo "</div>";//on ferme la div des données calculées


	echo "<br><div id=\"tab\">";
	echo "<br>";
	echo "<table border=\"2\"><thead><tr><th></th><th>Moyenne</th><th>Ecart Type</th><th>Minimum</th><th>Maximum</th></tr></thead><tbody>";

	echo "<tr>
		<td>BPM</td>
		<td>".round($Moyenne_BPM,2)."</td>
		<td>".round($Ecart_type_BPM,2)."</td>
		<td>".round($Min_BPM,2)."</td>
		<td>".round($Max_BPM,2)."</td>
	      </tr>
	      <tr>
		<td>SPO2</td>
		<td>".round($Moyenne_SPO2,2)."</td>
		<td>".round($Ecart_type_SPO2,2)."</td>
		<td>".round($Min_SPO2,2)."</td>
		<td>".round($Max_SPO2,2)."</td>
	      </tr>
	      <tr>
		<td>Temperature</td>
		<td>".round($Moyenne_temperature,2)."</td>
		<td>".round($Ecart_type_temperature,2)."</td>
		<td>".round($Min_temperature,2)."</td>
		<td>".round($Max_temperature,2)."</td>
	      </tr></tbody></table>";

	echo "</div>";//div du tableau
echo "</div>";// div gauche

echo "<div id=\"droite\">";
	//Affichage du graphique_BPM

	traceGrapheBPM($donnees_utilisateur, $tab_mesure_bpm, $tab_mesure_date);
	echo "<img src='./tmp/grapheBPM.png'>";
echo "</div>";//div gauche

	echo "<br><br><div id=resultat>";
	echo "<fieldset><legend><b><i>Résultat de l'analyse</i></b></legend>";

	// ## arbre de décision ##
	if ($Moyenne_BPM >= 88)
	{
		if ($IMC > 30)
		{	//obésité
			echo "<p id=\"orange\">L’indice de masse corporelle (IMC) permet d’évaluer rapidement votre corpulence simplement avec le poids et la taille, quel que soit le sexe. L’indice de masse corporelle (IMC) est le seul indice validé par l’Organisation mondiale de la santé pour évaluer la corpulence d’un individu et donc les éventuels risques pour la santé. L’IMC permet de déterminer si l’on est situation de maigreur, de surpoids ou d’obésité par exemple.Vous avez un IMC supperieur à 30. Cela signifie que vous êtes en <b>surpoids</b>. Il est fort probable que votre haut rythme cardiaque soit du à cette excès de masse.</p>";
		}
		if ($Moyenne_temperature < 38)
		{ //Pas fièvre
			if ($Moyenne_SPO2 >= 95)
			{	//o2 normal
				echo "<p id=\"orange\">Vous êtes potentiellement atteint de <b>tachycardie</b>. <br>Lorsqu’au repos le coeur bat trop rapidement (+ de 100 puls/min) en comparaison un coeur normale est entre 52-88 puls/min, on considère qu'une personne est en tachycardie. Elle peut provoquer des vertiges, des étourdissements ou des palpitations voir une perte de connaissance. Cependant elle peut n’entrainer aucun signes sur certaines personnes.</p>";
			}
			else
			{	//o2 bas
				if ($age > 60)
				{	//maladie du coeur et des vaisseaux
					echo "<p id=\"orange\">Il est probable que vous soyez atteint d'<b>athérosclérose</b>. L'athérosclérose, ou artériosclérose, est une maladie touchant les artères de gros et moyen calibre et caractérisée par l'apparition de plaques d'athérome, fréquente chez les personnes agées de plus de 60 ans.</p>";
				}
				else
				{	//maladie du coeur / maladie des poumons
					echo "<p id=\"red\">Votre saturation en oxygène est beaucoup trop basse. Vous souffrez donc d'<b>hypoxémie</b>. Il est probable que vous contractiez une <b>insuffisance coronarienne</b> conduisant à un défaut d'oxygénation du tissu cardiaque, ou une <b>broncho-pneumopathies</b>. Ce sont des maladies récurentes chez les personnes jeunes et soufrant d'hypoxémie.</p>";
				}
			}
		}
		else
		{	//fievre elevé qui augmente le bpm
			echo "<p id=\"orange\">Vous avez de la fièvre. Il est fort probable que votre haut rythme cardiaque soit du à cette fièvre. Il faudra reprendre des mesures une fois la fièvre tombée.</p>";
		}
	}


	//## arbre de décision bradycardie ##
	elseif ($Moyenne_BPM <= 52)
	{
		if ($tab_donnee_utilisateur["abrege_pratique_sportive"]=="HAUT_NIVEAU")
		{
		echo "<p id=\"vert\">Etant un sportif de haut niveau, il n'y a pas d'inquiétude à avoir vis à vis de votre rythme cadiaque relativement bas par rapport à la moyenne.</p>";
		
		} else 
		{

		echo "<p id=\"orange\">Votre rythme cariaque est anormalement bas. Il est probable que vous souffriez de <b>bradychardie</b>. La bradychardie correspond à un ralentissement du rythme cardiaque à moins de 52 puls/min. Généralement elle se développe sur une courte durée, si elle est redondante il faut aller consulter un médecin</p>";
		}
	}

	// ## arbre pour un individu normal ##
	elseif ($Moyenne_BPM > 52 && $Moyenne_BPM < 88)
	{
		echo "<p id=\"vert\">Le poul du patient est dans la normale.</p>";
	}


	echo $prevention;
	echo "</fieldset>";
	echo "</div>";

?>
</body>
</html>
