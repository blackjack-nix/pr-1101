<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Liste de données</title>
</head>
<body>

	<?php
	require "./function/http.php";

	$tableau = $_GET;
	$url = "http://147.215.191.33/pr_1101/ws_rasp.php";
	$methode = "GET";
	if ($tableau["user"]=="jekill" & $tableau["mdp"]=="congrat_10"){
		$retour_json = lancerRequeteHTTP($url, $methode, $tableau);
		$retour = json_decode($retour_json,TRUE);
		$type_retour = gettype($retour);
		var_dump($retour);

		/*echo "<form action=\"fichier a faire nous meme\" methode=".$methode." name=\"Session\">";
		echo "Session : <select name=\"session\">";
		foreach ($retour as $key => $value) {
			echo "<option value=".$value.">".$value."</option>";*/
		//}
	} else {
		echo '<PRE/>';
		echo "L'authentification a echouée";
	}
	
?>

</body>
</html>

