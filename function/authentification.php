<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<?php
	function authentification(string $user_saisis,string $mdp_saisis){

	$fichier_user = fopen("./.user", "r"); //on ouvre le fichier contenant un hash de l'utilisateur en lécture seul (plus sécurisé)
	$fichier_mdp = fopen("./.mdp", "r"); //on ouvre le fichier contenant un hash du mot de passe en lécture seul (plus sécurisé)
	


	if (is_null($fichier_user) || is_null($fichier_mdp)){ // on regarde si le pointeur renvoyé par fopen est null, ce qui signifie qu'il n'a pas réussi a ouvrir le fichier 
		echo "Authentification impossible"; // on affiche un message d'erreur
		log_redef("Impossible d'ouvrir le fichier du hash","./log/hash.log");//on écrit dans les logs l'erreur qui peut toujours servir
		exit(); // on sort du programme 
	}

	$user = (string)fgets($fichier_user); //on stoque dans une variable la ligne correspondant au hash de l'utilisateur
	$mdp = (string)fgets($fichier_mdp);//on stoque dans une variable la ligne correspondant au hash du mot de passe


	//a faire uniquement la premiere fois 
	$parametres = array('salt' => "Ceciestunsaltfortdeplusde17caractere", );
	$mdp_hash = password_hash("congrat_10", PASSWORD_DEFAULT,$parametres);
	$user_hash = password_hash("jekill", PASSWORD_DEFAULT,$parametres);
	echo "mdp :".$mdp_hash.'<br>';
	echo "user :".$user_hash.'<br>';

	fclose($fichier_user); // on ferme le fichier, plus sur et evite les fuites mémoires
	fclose($fichier_mdp); // on ferme le fichier, plus sur et evite les fuites mémoires

	$mdp_ok = password_verify($mdp_saisis, $mdp);
	$user_ok = password_verify($user_saisis, $user);

	return ($mdp_ok && $user_ok);

	}


?>

</body>
</html>