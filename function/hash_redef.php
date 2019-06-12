<?php
	include_once "./function/log_redef.php"; //on importe la fonction log qui nous permettera d'ecrire dans le fichier log chaque hash que l'on fait
	function hash_redef(string $str){ //fonction permettant de hasher tout et n'importe quoi (surtout les nom / preons pour secret médicale) et ecrit les logs au passage
		$tab_hash =  hash_algos(); // on recupere la liste de tous les hash possible
		$algo = null; //le choix de l'algo de hash se fera ici
		$binaire = FALSE; //savoir si on veut le hash en binaire (ou hexa)
		
		foreach ($tab_hash as $key => $value) { //on cherche le sha256 (tres utilisé mais pas forcément présent partout, quoi que)
			if ($value == "sha256"){ //on cherche si une valeur de l'algo correspond
				$algo = "sha256";// dans ce cas, l'algo prend la valeur de sha256
				break;// on arrete les calculs sinon ca change
			} else {
				$algo = $tab_hash[$key]; // dans le cas ou il n'y a pas sha256 (peu probable mais on sait jamais), on prend n'importe lequel
			}
		}
		$hash = hash($algo, $str,$binaire); //on creeer un hash avec la methode choisise (sha 256 ou autre)
		if ($algo =! "sha256") log_redef("Sha256 non trouvé sur ce pc","./log/data.log");//si sha256 pas trouvé, on ecrit dans les logs, ca peut toujour servir; 
		log_redef("Création du hash : ".$hash.". Avec la methode : ".$algo , "./log/hash.log");//on ecrit dans le fihier log le hash qui a été créé ainsi que la methode
		return $hash; //on return le hash crée
	}
?>
