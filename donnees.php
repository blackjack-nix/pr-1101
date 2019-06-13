<!DOCTYPE html>
<html>
<head>
	<title>Résultat des données</title>
</head>
<body>
<?php
	
	include_once "./function/http.php"; //on importe la fonction http qui permet de faire une requete
	include_once "./function/log_redef.php"; //on importe la foction log qui permet d'ecrire dans un fichier texte des infos utiles au debug

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
	echo "<h1>Dossier médicale du patient n°".$tab_donnee_utilisateur["no_utilisateur"].", ".strtoupper($tab_donnee_utilisateur["nom"])." ".ucwords(strtolower($tab_donnee_utilisateur["prenom"])).". </h1>";

	foreach($tab_donnee_utilisateur as $key => $value){
		echo "<b>".ucfirst(str_replace('_',' ',$key))." : </b>".ucfirst(str_replace('_', ' ',$value)) . "<br>";
	}

	$IMC = round($tab_donnee_utilisateur["poids"]/(($tab_donnee_utilisateur["taille"]/100)*($tab_donnee_utilisateur["taille"]/100)));
	echo "<b>IMC : </b>".$IMC;
	echo "<br><br>";
	echo "<table border=\"2\"><thead><tr><th></th><th>Moyenne</th><th>Ecart Type</th><th>Minimum</th><th>Maximum</th></tr></thead><tbody>";
	$tab_indicateur_mesure = $retour["indicateurs_mesures"];
	echo "<tr>
		<td>BPM</td>
		<td>".round($tab_indicateur_mesure['Moyenne_BPM'])."</td>
		<td>".round($tab_indicateur_mesure['Ecart_type_BPM'])."</td>
		<td>".round($tab_indicateur_mesure['Min_BPM'])."</td>
		<td>".round($tab_indicateur_mesure['Max_BPM'])."</td>
	      </tr>
	      <tr>
		<td>SPO2</td>
		<td>".round($tab_indicateur_mesure['Moyenne_SPO2'])."</td>
		<td>".round($tab_indicateur_mesure['Ecart_type_SPO2'])."</td>
		<td>".round($tab_indicateur_mesure['Min_SPO2'])."</td>
		<td>".round($tab_indicateur_mesure['Max_SPO2'])."</td>
	      </tr>
	      <tr>
		<td>Temperature</td>
		<td>".round($tab_indicateur_mesure['Moyenne_temperature'])."</td>
		<td>".round($tab_indicateur_mesure['Ecart_type_temperature'])."</td>
		<td>".round($tab_indicateur_mesure['Min_temperature'])."</td>
		<td>".round($tab_indicateur_mesure['Max_temperature'])."</td>
	      </tr></tbody></table>";

	      $Moyenne_BPM = $tab_indicateur_mesure['Moyenne_BPM'];
	      $Ecart_type_BPM = $tab_indicateur_mesure['Ecart_type_BPM'];
	      $Min_BPM = $tab_indicateur_mesure['Min_BPM'];
	      $Max_BPM = $tab_indicateur_mesure['Max_BPM'];

	      $Moyenne_SPO2 = $tab_indicateur_mesure['Moyenne_SPO2'];
	      $Ecart_type_SPO2 = $tab_indicateur_mesure['Ecart_type_SPO2'];
	      $Min_SPO2 = $tab_indicateur_mesure['Min_SPO2'];
	      $Max_SPO2 = $tab_indicateur_mesure['Max_SPO2'];

	      $Moyenne_temperature = $tab_indicateur_mesure['Moyenne_temperature'];
	      $Ecart_type_temperature = $tab_indicateur_mesure['Ecart_type_temperature'];
	      $Min_temperature = $tab_indicateur_mesure['Min_temperature'];
	      $Max_temperature = $tab_indicateur_mesure['Max_temperature'];

	      echo "<div id=resultat>
	      	<h2> Les résultats de l'analyse par notre algorithme sont les suivants : </h2>";




	    if ($Moyenne_BPM > 100) 
	    { //rythme cardique superieur à 100
	      	if ($tab_donnee_utilisateur["abrege_contexte_mesures_cliniques"] == "REPOS") 
	      	{
	      		if ($Moyenne_temperature < 38) 
	      		{
	      			if ($IMC < 30) 
	      			{
	      				if ($Moyenne_SPO2 > 97) 
	      				{
	      					echo "<h4> Vous êtes atteint de tachycaride.</h4><br>";
	      					tachycardie:
	      					echo "Lorsqu’au repos le coeur bat trop rapidement (+ de 100 puls/min). En comparaison, un coeur normale est entre 60-100 puls/min. <br> Elle peut provoquer des <b>vertiges</b>, 
	      					des <b>étourdissements</b> ou des <b>palpitations</b> voir une <b>perte de connaissance</b>. Cependant elle peut n’entrainer aucun signes sur certaines personnes.";
	      					echo "Il existe cependant differents types de tachycaride. Selon vos données, vous seriez atteint de :";
	      					
	      					if ($Moyenne_BPM <= 120 && $Moyenne_BPM > 100) 
	      					{
	      						echo "<h6> tachycardie sinusale :</h6>";
	      						exit();
	      					} //

	      					if ($Moyenne_BPM > 120 && $Moyenne_BPM <= 160) 
	      					{
	      						echo "<h6> tachycardie ventriculaire :</h6>";
	      						exit();
	      					} //

	      					if ($Moyenne_BPM > 160 && $Moyenne_BPM <= 150) 
	      					{
	      						echo "<h6> tachycardie supraventriculaire :</h6>";
	      						exit();
	      					} //

	      				} //
	      				else 
	      				{
	      					if ($tab_donnee_utilisateur["age"] < 60) 
	      					{
	      						echo "maladie coeur /poumon";
	      						exit();
	      					} //
	      					else 
	      					{
	      						echo "maladie des vaisseau / coronaires";
	      						exit();
	      					}//
	      				}//
	      			} //
	      			else 
	      			{
	      				echo "Vous êtes en surpoids. Le surpoids peut entrainer une hausse du rythme cardiaque.";
	      				if ($Moyenne_BPM >= 110) 
	      				{
	      					echo "En revanche, il se peut que soyez atteint de tachycardie";
	      					goto tachycardie;
	      				} //
	      				else 
	      				{
	      					echo $prevention;
	      					exit();
	      				} //
	      			} //
	      		} //
	      		else 
	      		{// temperature pas ok
	      			echo "Vous êtes fievreux. La fièvre peut augmenter de façon consequente le rythme cardique. Attendez que votre vièvre tombe avant de reprendre des mesures";
	      			echo $prevention;
	      			exit();
	      		}//
	   		}//
		}//
	}
	echo "</div>";
	var_dump($retour);
?>

</body>
</html>