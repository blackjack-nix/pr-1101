<?php
function traceGrapheBPM($donnees_utilisateur, $mesures_BPM, $mesures_date)
{
	require_once ('./jpgraph-4.2.6/src/jpgraph.php');
	require_once ('./jpgraph-4.2.6/src/jpgraph_scatter.php');
	require_once ('./jpgraph-4.2.6/src/jpgraph_line.php');

	$date_min = new DateTime($mesures_date[0]);
	foreach ($mesures_date as $mesure_date)
	{
		$date = new DateTime($mesure_date);
		$diff_date = $date->diff($date_min);
		$diff_heures = $diff_date->h;
		$diff_minutes = $diff_date->i;
		$diff_secondes = $diff_date->s;
		$secondes = $diff_heures*3600 + $diff_minutes*60 + $diff_secondes;
		$secondes_mesure[] = $secondes;
	}
	$secondes_mesure_inf = min($secondes_mesure);
	$secondes_mesure_sup = max($secondes_mesure);
	$prenom = $donnees_utilisateur["prenom"] ;
	$nom = $donnees_utilisateur["nom"] ;
	$age = $donnees_utilisateur["age"] ;
	$sexe = $donnees_utilisateur["sexe"] ;
	$fumeur = $donnees_utilisateur["fumeur"] ;
	$abrege_pratique_sportive = $donnees_utilisateur["abrege_pratique_sportive"] ;
	$detail_contexte = $donnees_utilisateur["detail_contexte_mesures_cliniques"] ;
	$commentaire_session = $donnees_utilisateur["commentaire_mesures_cliniques"] ;
	if ($fumeur == 0)
	{
		$fumeur = "Non" ;
	}
	else
	{
		$fumeur = "Oui" ;
	}
	$graphic_title = $prenom." ".
					 $nom." - Age : ".
					 $age." - Sexe : ".
					 $sexe." - Fumeur : ".
					 $fumeur." - Pratique sportive : ".
					 $abrege_pratique_sportive."\nContexte : ".
					 $detail_contexte."\nDétail : ".
					 $commentaire_session;
	$data_x_1 = $secondes_mesure ;
	$data_y_1 = $mesures_BPM ;
	$intervalle_seconde_mesure = ($secondes_mesure_sup-$secondes_mesure_inf)/5 ;
	$graph_x_margin = 50 ;
	$graph_y_margin = 50 ;
	$graph_width = 800 ;
	$graph_height = 600 ;
	$graph_x_scale_min = $secondes_mesure_inf ;
	$graph_x_scale_max = $secondes_mesure_sup ;
	$graph_y_scale_min = 35 ;
	$graph_y_scale_max = 135 ;
    for ($i=0 ; $i<= 10 ; $i++)
	{
		$tics_position_x[$i] = $intervalle_seconde_mesure*$i + $secondes_mesure_inf;
		$minutes_tics_label_x[$i] = (int)$tics_position_x[$i]/60 ;
		$secondes_tics_label_x[$i] = $tics_position_x[$i] - $minutes_tics_label_x[$i]*60 ;
		$tics_label_x[$i] = $minutes_tics_label_x[$i].":".$secondes_tics_label_x[$i] ;
	}
    if (file_exists("./tmp/grapheBPM.png"))
    {
        unlink(("./tmp/grapheBPM.png")) ;
    }

	$graph = new Graph($graph_width,$graph_height);
	$graph->SetScale('intlin', $graph_y_scale_min,$graph_y_scale_max,$graph_x_scale_min,$graph_x_scale_max);
	$graph->img->SetMargin($graph_x_margin,$graph_x_margin,$graph_y_margin,$graph_y_margin);
	$graph->SetShadow();
	$graph->title->Set($graphic_title);
	//$graph->subtitle->Set($graphic_subtitle);
	$graph->legend->SetFont(FF_DV_SANSSERIF,FS_NORMAL, 10);
	$graph->title->SetFont(FF_DV_SANSSERIF,FS_NORMAL, 10);
	$graph->subtitle->SetFont(FF_DV_SANSSERIF,FS_NORMAL, 10);
	$graph->xaxis->SetTitle('Secondes');
	$graph->yaxis->SetTitle('BPM');
	$graph->xaxis->SetTitlemargin(0);
	$graph->yaxis->SetTitlemargin(0);
	$graph->xaxis->SetTitleSide(SIDE_TOP);
	$graph->yaxis->SetTitleSide(SIDE_RIGHT);
	$sp1 = new ScatterPlot($data_y_1, $data_x_1);
	$sp2 = new LinePlot($data_y_1, $data_x_1);
	$graph->Add($sp1);
	$graph->Add($sp2);
	$graph->xaxis->SetMajTickPositions($tics_position_x);
	$graph->Stroke("./tmp/grapheBPM.png");
}
?>
