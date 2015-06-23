<?PHP


/**
 * Gribouillis
 * @package Gribouillis
 * @author Olivier ROUET
 * @version 1.0.0
 */


/**
 * Préparation d'un diagramme
 *
 * @param array $tableau
 * @return array
 */
function diagramme_preparer($tableau)
{

	// initialisation des variables
	$sortie = false;
	$axe_x = [];
	$axe_y = [];
	$series = [];
	$couleurs = [
		'#50B432',
		'#ED561B',
		'#058DC7',
		'#002B36',
		'#D33682',
		'#6C71C4',
		'#B58900',
		'#CB4B16',
		'#859900',
		'#AAAAAA',
	];
	$c = 0;
	
	// traitement
	foreach ($tableau as $ligne) {
	
		if (isset($ligne['S'])) {
		
			$s = $ligne['S'];
			$i = $s;
			$x = false;
			$y = false;
			
			if (isset($ligne['S2'])) {
			
				$i = $ligne['S2'];
			
			}
			
			if (isset($ligne['X'])) {
			
				$x = $ligne['X'];
			
			}
			
			if (isset($ligne['Y'])) {
			
				$y = $ligne['Y'];
			
			}
			
			// initialisation des valeurs possibles sur l'axe X
			if (!isset($axe_x[$x])) {
			
				$axe_x[$x] = ['code' => $x, 'libelle' => $x];
			
			}
			
			// initialisation des valeurs possibles sur l'axe Y
			if (!isset($axe_y[$y])) {
			
				$axe_y[$y] = ['code' => $y, 'libelle' => $y];
			
			}
			
			// initialisation des séries
			if (!isset($series[$s])) {
			
				$series[$s] = [
					'libelle' => $i,
					'couleur' => $couleurs[$c],
					'donnees' => []
				];
				
				$c ++;
			
			}
			
			// ajout des donnees à la série
			$series[$s]['donnees'][$x] = $y;
		
		}
	
	}
	
	// préparation de la sortie
	$sortie = [
		'axe_x' => $axe_x,
		'axe_y' => $axe_y,
		'series' => $series
	];
	
	// sortie
	return $sortie;

}



/**
 * Analyse glogale et renvoie des statistiques
 *
 * @param array $parametres
 * @return array
 */
function chart_analyser($parametres)
{

	// initialisation des variables
	$sortie = false;
	
	// traitement
	$sortie = $parametres;
	
	// sortie
	return $sortie;

}



/**
 * Analyse des séries et renvoie des statistiques
 *
 * @param array $series
 * @return array
 */
function chart_series_analyser($series)
{

	// initialisation des variables
	$sortie = false;
	$max = [
		'somme' => 0,
		'valeur' => 0
	];
	$mesures = [];
	
	// on parcourt les series
	foreach ($series['donnees'] as $serie_id => $serie) {
	
		$donnees = $serie['donnees'];
		
		// on parcourt les mesures de la série
		foreach($donnees as $x => $valeur) {
		
			// on cherche la valeur maximale toutes séries confondues
			if ($valeur > $max['valeur']) {
			
				$max['valeur'] = $valeur;
			
			}
			
			// on calcul la somme
			if (!isset($mesures[$x]['somme'])) {
			
				$mesures[$x]['somme'] = 0;
			
			}
			
			$mesures[$x]['somme'] += $valeur;
			
			// on cherche la somme maximale toutes séries confondues
			if ($mesures[$x]['somme'] > $max['somme']) {
			
				$max['somme'] = $mesures[$x]['somme'];
			
			}
		
		}
		
		// maj de la serie
		// $serie['calculs']['max'] = $mesure_valeur_max;
		// $serie['calculs']['somme'] = $mesure_somme;
		
		// maj des series
		// $series['donnees'][$serie_id] = $serie;
		
		//
		// if ($mesure_valeur_max > $mesures_valeur_max) {
		
			// $mesures_valeur_max = $mesure_valeur_max;
		
		// }
		
		//
		// if ($mesure_somme > $mesures_somme_max) {
		
			// $mesures_somme_max = $mesure_somme;
		
		// }
	
	}
	
	// maj des series
	$series['calculs']['valeur_max'] = $max['valeur'];
	$series['calculs']['somme_max'] = $max['somme'];
	
	// traitement
	$sortie = $series;
	
	// var_dump($sortie);
	
	// sortie
	return $sortie;

}



/**
 * Analyse une série et renvoie des statistiques
 *
 * @param array $serie
 * @return array
 */
function chart_serie_analyser($serie)
{

	// initialisation des variables
	$sortie = false;
	
	// traitement
	$sortie = $serie;
	
	// sortie
	return $sortie;

}



/**
 * Génère l'axe des abscisses
 *
 * @param array $parametres
 * @return string
 */
function chart_abscisse($parametres)
{

	// initialisation des variables
	$sortie = '';
	
	// lecture des paramètres
	$origine_x = $parametres['x'];
	$origine_y = $parametres['y'];
	$largeur = $parametres['largeur'];
	$hauteur = $parametres['hauteur'];
	$max = $parametres['max'];
	
	// bornes des abscisses et des ordonnées maximum
	$xmin = $origine_x;
	$xmax = $origine_x + $largeur;
	$ymin = $origine_y;
	$ymax = $origine_y + $hauteur;
	
	// dimensions utiles
	$largeur_utile = $xmax - $xmin;
	$hauteur_utile = $ymax - $ymin;
	
	// nombre de séries
	$series_nombre = count($series['donnees']);
	
	// calcul de l'unité
	$unite = $largeur_utile / $max;
	
	// décalages (espacements)
	$tranche = $max / 5;
	
	$decalage_x = $tranche * $unite;
	
	
	$x = $xmin;
	$valeur = 0;
	$couleur = '#555555';
	$font_size = '10';
	
	$compteur = 0;
	
	// die($series_nombre);
	
	while ($compteur <= $series_nombre) {
	
		$sortie .= '<line x1="' . $x .'" y1="' . $ymin . '" x2="' . $x .'" y2="' . $ymax . '" fill="' . $couleur . '" stroke="' . $couleur . '" stroke-width="0.5" fill-opacity="1.0" />' . "\n";
		
		$valeur_largeur = strlen($valeur);
		$valeur_hauteur = $font_size;
		
		$sortie .= '<text x="' . ($x - 5) . '" y="' . $ymax . '" style="text-anchor:end; fill:' . $couleur . '; font-size:' . $font_size . 'px; font-weight:normal; font-family:Tahoma, Verdana; font-style:normal">';
		$sortie .= $valeur;
		$sortie .= '</text>' . "\n";
		
		$sortie .= '<text x="' . ($x - 5) . '" y="' . ($ymin + $valeur_hauteur) . '" style="text-anchor:end; fill:' . $couleur . '; font-size:' . $font_size . 'px; font-weight:normal; font-family:Tahoma, Verdana; font-style:normal">';
		$sortie .= $valeur;
		$sortie .= '</text>' . "\n";
		
		$valeur = $valeur + $tranche;
		
		$x = $x + $decalage_x;
		
		$compteur ++;
	
	}
	
	return $sortie;

}



/**
 * Génère la légende
 *
 * @param array $parametres
 * @return string
 */
function diagrammes_legendes_generer($parametres)
{

	// initialisation des variables
	$sortie = '';
	
	// lectures des paramètres
	$d_id = $parametres['d_id'];
	$origine_x = $parametres['x'];
	$origine_y = $parametres['y'];
	$largeur = $parametres['largeur'];
	$hauteur = $parametres['hauteur'];
	$legende = $parametres['legende'];
	$axes_x = $parametres['axes_x'];
	$axes_y = $parametres['axes_y'];
	$series = $parametres['series'];
	
	// contour
	// $sortie .= '<rect x="' . $origine_x .'" y="' . $origine_y . '" width="' . $largeur . '" height="' . $hauteur . '" fill="white" stroke="black" stroke-width="0.5" fill-opacity="1.0" />' . "\n";
	
	$axe_x = current($axes_x);
	$listes = $axe_x['donnees'];
	
	// Lecture des informations sur le titre
	if (isset($legende['titre']['libelle'])) {
	
		$titre_libelle = $legende['titre']['libelle'];
	
	} else {
	
		$titre_libelle = "Légende";
	
	}
	
	$titre_couleur = '#000000';
	$titre_taille = 14;
	
	// marges
	$padding_top = $titre_taille + 30;
	$padding_right = 20;
	$padding_bottom = 10;
	$padding_left = 20;
	
	// bornes des abscisses et des ordonnées maximum
	$xmin = $origine_x + $padding_left;
	$xmax = $origine_x + $largeur - $padding_right;
	$ymin = $origine_y + $padding_top;
	$ymax = $origine_y + $hauteur - $padding_bottom;
	
	// dimensions utiles
	$largeur_utile = $xmax - $xmin;
	$hauteur_utile = $ymax - $ymin;
	
	// dimensions des pavés de couleur
	$pave_largeur = 20;
	$pave_hauteur = 10;
	
	// Calculs sur les titres et sous-titres
	$titre_x = $xmin + round($largeur_utile / 2);
	$titre_y = $origine_y + $titre_taille + 10;
	
	// écriture du titre
	$sortie .= '<text x="' . $xmin . '" y="' . $titre_y . '" style="text-align:left;fill:' . $titre_couleur . ';font-size:' . $titre_taille . 'px;font-weight:bold;font-family:Tahoma, Verdana;font-style:normal;">';
	$sortie .= $titre_libelle;
	$sortie .= '</text>' . "\n";
	
	// espacements
	$espace = 10;
	$interligne = 20;
	
	$x = $xmin;
	$y = $ymin;
	
	foreach ($series['donnees'] as $liste) {
	
		$libelle = $liste['libelle'];
		$couleur = $liste['couleur'];
		
		$sortie .= '<rect x="' . $x .'" y="' . $y . '" width="' . $pave_largeur . '" height="' . $pave_hauteur . '" fill="' . $couleur . '" stroke="black" stroke-width="0.5" fill-opacity="1.0" />' . "\n";
		
		$sortie .= '<text x="' . ($x + $pave_largeur + $espace) . '" y="' . ($y + $pave_hauteur) . '" style="text-align:left; fill:#000000; font-size:10px; font-weight:normal; font-family:Tahoma, Verdana; font-style:normal">';
		$sortie .= $libelle;
		$sortie .= '</text>' . "\n";
		
		$y = $y + $interligne;
	
	}
	
	
	return $sortie;

}



/**
 * Génère l'axe des ordonnées
 *
 * @param array $parametres
 * @return string
 */
function chart_ordonnees($parametres)
{

	// initialisation des variables
	$sortie = '';
	
	// lecture des paramètres
	$origine_x = $parametres['x'];
	$origine_y = $parametres['y'];
	$largeur = $parametres['largeur'];
	$hauteur = $parametres['hauteur'];
	$max = $parametres['max'];
	
	// bornes des abscisses et des ordonnées maximum
	$xmin = $origine_x;
	$xmax = $origine_x + $largeur;
	$ymin = $origine_y;
	$ymax = $origine_y + $hauteur;
	
	// dimensions utiles
	$largeur_utile = $xmax - $xmin;
	$hauteur_utile = $ymax - $ymin;
	
	// calcul de l'unité
	$unite = $hauteur_utile / $max;
	
	// décalages (espacements)
	$tranche = $max / 5;
	$decalage_y = $tranche * $unite;
	
	//
	$y = $ymax;
	$valeur = 0;
	$couleur = '#555555';
	$font_size = '10';
	
	$compteur = 0;
	
	while ($compteur < 6) {
	
		$valeur_largeur = strlen($valeur);
		$valeur_hauteur = $font_size;
		
		$sortie .= '<line x1="' . $xmin .'" y1="' . $y . '" x2="' . $xmax .'" y2="' . $y . '" fill="' . $couleur . '" stroke="' . $couleur . '" stroke-width="0.5" fill-opacity="1.0" />' . "\n";
		
		$sortie .= '<text x="' . $xmin . '" y="' . ($y - 2) . '" style="fill:' . $couleur . '; font-size:' . $font_size . 'px; font-weight:normal; font-family:Tahoma, Verdana; font-style:normal">';
		$sortie .= $valeur;
		$sortie .= '</text>' . "\n";
		
		$sortie .= '<text x="' . ($xmax - ($valeur_largeur * $font_size * .5))  . '" y="' . ($y - 2) . '" style="fill:' . $couleur . '; font-size:' . $font_size . 'px; font-weight:normal; font-family:Tahoma, Verdana; font-style:normal">';
		$sortie .= $valeur;
		$sortie .= '</text>' . "\n";
		
		$valeur = $valeur + $tranche;
		
		$y = $y - $decalage_y;
		
		$compteur++;
	
	}
	
	return $sortie;

}



/**
 * Trouve un plafond
 *
 * @param float $valeur
 * @return float
 */
function plafond_trouver($valeur)
{

	// initialisation des variables
	$sortie = 0;
	
	// traitement
	$base = ceil($valeur);
	
	$largeur = strlen($base);
	
	$coef = pow(10, ($largeur - 1));
	
	$decimales = $base / $coef;
	
	$dessous = floor($decimales);
	$dessus = ceil($decimales);
	
	if ($decimales < ($dessous + 0.5)) {
	
		$arrondi = $dessous + 0.5;
	
	} else {
	
		$arrondi = $dessus;
	
	}
	
	$sortie = $arrondi * $coef;
	
	// var_dump($valeur . '=> ' . $sortie);
	
	// sortie
	return $sortie;

}


?>