<?php
	require "./log.php"; //on importe la fonction log qui nous permettera d'ecrire dans le fichier log chaque hash que l'on fait
	function hash(string $str){ //fonction permettant de hasher tout et n'importe quoi (surtout les nom / preons pour secret médicale) et ecrit les logs au passage
		$tab_hash =  hash_algos(); // on recupere la liste de tous les hash possible
		$algo = null; //le choix de l'algo de hash se fera ici
		$binaire = FALSE; //savoir si on veut le hash en binaire (ou hexa)
		
		foreach ($tab_hash as $key => $value) { //on cherche le sha256 (tres utilisé mais pas forcément présent partout, quoi que)
			if ($tab_hash[$key] == "sha256"){ //on cherche si une valeur de l'algo correspond
				$algo = "sha256";// dans ce cas, l'algo prend la valeur de sha256
				break;// on arrete les calculs sinon ca change
			} else {
				$algo = $tab_hash[$key]; // dans le cas ou il n'y a pas sha256 (peu probable mais on sait jamais), on prend n'importe lequel
			}
		}
		$hash = hash($algo, $str,$binaire); //on reeer un hash avec la methode choisise (sha 256 ou autre)
		$log = log("Création du hash : ".$hash.". Avec la methode : ".$algo);//on ecrit dans le fihier log le hash qui a été créé ainsi que la methode
		return $hash; //on return le hash crée
	}
?>