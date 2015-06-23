<?PHP


/**
 * Gribouillis
 * @package Gribouillis
 * @author Olivier ROUET
 * @version 1.0.0
 */


/**
 * Génère un diagramme en rectangles
 *
 * @param array $parametres
 * @return string
 */
function diagrammes_rectangles($parametres)
{

	// initialisation des variables
	$sortie = '';
	
	// lectures des paramètres
	$axes_x = $parametres['axes_x'];
	$axe_x = current($axes_x);
	
	$orientation = 'paysage';
	
	if (isset($axe_x['orientation']['mode'])) {
	
		$orientation = $axe_x['orientation']['mode'];
	
	}
	
	if ($orientation == 'portrait') {
	
		$sortie .= diagrammes_rectangles_portrait($parametres);
	
	} else {
	
		$sortie .= diagrammes_rectangles_paysage($parametres);
	
	}
	
	return $sortie;

}



/**
 * Génère un diagramme en rectangles en paysage
 *
 * @param array $parametres
 * @return string
 */
function diagrammes_rectangles_paysage($parametres)
{

	// initialisation des variables
	$sortie = '';
	
	// lectures des paramètres
	$d_id = $parametres['d_id'];
	$origine_x = $parametres['x'];
	$origine_y = $parametres['y'];
	$largeur = $parametres['largeur'];
	$hauteur = $parametres['hauteur'];
	$axes_x = $parametres['axes_x'];
	$axes_y = $parametres['axes_y'];
	$series = $parametres['series'];
	
	$axe_x = current($axes_x);
	$listes = $axe_x['donnees'];
	
	// contour
	// $sortie .= '<rect x="' . $origine_x .'" y="' . $origine_y . '" width="' . $largeur . '" height="' . $hauteur . '" fill="white" stroke="black" stroke-width="0.5" fill-opacity="1.0" />' . "\n";
	
	// Largeur de l'axe gauche
	$axes_y_largeur = 60;
	
	// marges
	$padding_top = 20;
	$padding_right = 10;
	$padding_bottom = 20;
	$padding_left = $axes_y_largeur + 10;
	
	// bornes des abscisses et des ordonnées maximum
	$xmin = $origine_x + $padding_left;
	$xmax = $origine_x + $largeur - $padding_right;
	$ymin = $origine_y + $padding_top;
	$ymax = $origine_y + $hauteur - $padding_bottom;
	
	// dimensions utiles
	$largeur_utile = $xmax - $xmin;
	$hauteur_utile = $ymax - $ymin;
	
	// décompte des éléments
	$listes_nombre = count($listes);
	$series_nombre = count($series['donnees']);
	$barres_nombre = $listes_nombre * $series_nombre;
	
	// intervalles
	$listes_intervalles = ($listes_nombre - 1);
	$series_intervalles = ($series_nombre - 1);
	
	// décalages (espacements)
	$decalage_y = floor(($largeur_utile / $barres_nombre) / 3);
	$decalages_y = $listes_intervalles * $decalage_y;
	
	// hauteurs
	$hauteur_disponible = $hauteur_utile - $decalages_y;
	$barre_hauteur = floor($hauteur_disponible / $barres_nombre);
	
	// on lit la valeur maximale
	$valeur_max = $series['calculs']['valeur_max'];
	
	// plafond = valeur maximale jamais atteinte
	$valeur_plafond = plafond_trouver($valeur_max);
	
	// calcul de l'unité
	$unite = $largeur_utile / $valeur_plafond;
	
	// axes d'abscisse
	foreach($axes_x as $axe_x) {
	
		$param = [
			'x' => $xmin,
			'y' => $origine_y,
			'max' => $valeur_plafond,
			'largeur' => $largeur_utile,
			'hauteur' => $hauteur
		];
		$sortie .= chart_abscisse($param);
		unset($param);
	
	}
	
	// on trace les barres
	$x = $xmin;
	$y = $ymin;
	$compteur = 0;
	
	foreach($listes as $liste) {
	
		// Lecture des valeurs
		$code = $liste['code'];
		
		$compteur2 = 0;
		
		foreach ($series['donnees'] as $serie) {
		
			$valeur = false;
			
			if (isset($serie['donnees'][$code])) {
			
				$valeur = $serie['donnees'][$code];
			
			}
			
			if ($valeur !== false) {
			
				$couleur = $serie['couleur'];
				
				$font_color = '#000000';
				$font_size = '10';
				
				
				// Calculs
				$caractere_largeur = ($font_size * 0.6);
				$texte_largeur = ceil(strlen($valeur) * $caractere_largeur);
				
				$barre_largeur = round($valeur * $unite);
				
				$texte_x = $x + $barre_largeur + 5;
				$texte_y = floor($y + ($barre_hauteur / 2)) + 5;
				
				
				// Affichage
				$g_id = $d_id . '1S' . $compteur . 'G' . $compteur2;
				$tooltip = $valeur;
				$onmouseout = "HideTooltip(evt);Assombrir('" . $g_id . "');";
				$onmousemove = "ShowTooltip(evt, '" . $tooltip . "');";
				$onmouseover = "Eclaircir('" . $g_id . "');";
				
				$sortie .= '<g id="' . $g_id . '"' . "\n";
				$sortie .= ' onmouseout="' . $onmouseout . '" onmousemove="' . $onmousemove . '" onmouseover="' . $onmouseover . '"';
				$sortie .= '>' . "\n";
				
				// Rectangles
				$sortie .= '<rect x="' . $x .'" y="' . $y . '" width="' . $barre_largeur . '" height="' . $barre_hauteur . '"';
				$sortie .= ' fill="' . $couleur . '" stroke="white" stroke-width="0.5" fill-opacity="1.0"';
				$sortie .= ' />' . "\n";
				
				// Valeurs
				if (($barre_hauteur > $font_size) && ($barre_largeur > $texte_largeur)) {
				
					$sortie .= '<text x="' . $texte_x . '" y="' . $texte_y . '" style="fill:' . $font_color . ';font-size:' . $font_size . 'px;font-weight:normal;font-family:Tahoma, Verdana;font-style:normal;">';
					$sortie .= $valeur;
					$sortie .= '</text>' . "\n";
				
				}
				
				$sortie .= '</g>' . "\n";
			
			}
			
			$y = $y + $barre_hauteur;
			
			$compteur2++;
		
		}
		
		$compteur ++;
		
		if ($compteur < $listes_nombre) {
		
			$y += $decalage_y;
		
		}
	
	}
	
	// on trace les libellés de l'axe X
	$zone_y1 = $ymin;
	$compteur = 0;
	
	foreach ($listes as $liste) {
	
		$zone_y2 = $zone_y1 + ($barre_hauteur * $series_nombre);
		
		// Lecture des informations sur le titre
		$titre_libelle = $liste['libelle'];
		$titre_couleur = '#000000';
		$titre_taille = 11;
		
		// Calculs sur le titre
		$titre_x = $xmin - 5;
		$titre_y = round(($zone_y1 + $zone_y2) / 2);
		
		// écriture du titre
		$sortie .= '<text x="' . $titre_x . '" y="' . $titre_y . '" style="text-anchor:end;fill:' . $titre_couleur . ';font-size:' . $titre_taille . 'px;font-weight:normal;font-family:Tahoma, Verdana;font-style:normal;">';
		$sortie .= $titre_libelle;
		$sortie .= '</text>' . "\n";
		
		$zone_y1 = $zone_y2;
		
		if ($compteur <= $series_nombre) {
		
			$zone_y1 += $decalage_y;
		
		}
		
		$compteur++;
	
	}
	
	return $sortie;

}



/**
 * Génère un diagramme en rectangles en portrait
 *
 * @param array $parametres
 * @return string
 */
function diagrammes_rectangles_portrait($parametres)
{

	// initialisation des variables
	$sortie = '';
	
	// lectures des paramètres
	$d_id = $parametres['d_id'];
	$origine_x = $parametres['x'];
	$origine_y = $parametres['y'];
	$largeur = $parametres['largeur'];
	$hauteur = $parametres['hauteur'];
	$axes_x = $parametres['axes_x'];
	$axes_y = $parametres['axes_y'];
	$series = $parametres['series'];
	
	$axe_x = current($axes_x);
	$listes = $axe_x['donnees'];
	
	// contour
	// $sortie .= '<rect x="' . $origine_x .'" y="' . $origine_y . '" width="' . $largeur . '" height="' . $hauteur . '" fill="white" stroke="black" stroke-width="0.5" fill-opacity="1.0" />' . "\n";
	
	// Calculs sur le nombre d'axes
	$axes_y_largeur = 20;
	$axes_y_nombre = count($axes_y);
	
	// marges
	$padding_top = 20;
	$padding_right = $axes_y_largeur + 10;
	$padding_bottom = 20;
	$padding_left = $axes_y_largeur + 10;
	
	// bornes des abscisses et des ordonnées maximum
	$xmin = $origine_x + $padding_left;
	$xmax = $origine_x + $largeur - $padding_right;
	$ymin = $origine_y + $padding_top;
	$ymax = $origine_y + $hauteur - $padding_bottom;
	
	// dimensions utiles
	$largeur_utile = $xmax - $xmin;
	$hauteur_utile = $ymax - $ymin;
	
	// décompte des éléments
	$listes_nombre = count($listes);
	$series_nombre = count($series['donnees']);
	$barres_nombre = $listes_nombre * $series_nombre;
	
	// intervalles
	$listes_intervalles = ($listes_nombre - 1);
	$series_intervalles = ($series_nombre - 1);
	
	// décalages (espacements)
	$decalage_x = floor(($largeur_utile / $barres_nombre) / 3);
	$decalages_x = $listes_intervalles * $decalage_x;
	
	// largeurs
	$largeur_disponible = $largeur_utile - $decalages_x;
	$barre_largeur = floor($largeur_disponible / $barres_nombre);
	
	// on lit la valeur maximale
	$valeur_max = $series['calculs']['valeur_max'];
	
	// plafond = valeur maximale jamais atteinte
	$valeur_plafond = plafond_trouver($valeur_max);
	
	// calcul de l'unité
	$unite = $hauteur_utile / $valeur_plafond;
	
	// axes d'ordonnées
	foreach($axes_y as $axe_y) {
	
		$param = [
			'x' => $origine_x,
			'y' => $ymin,
			'max' => $valeur_plafond,
			'largeur' => $largeur,
			'hauteur' => $hauteur_utile
		];
		$sortie .= chart_ordonnees($param);
		unset($param);
	
	}
	
	// on trace les barres
	$x = $xmin;
	$compteur = 0;
	
	foreach($listes as $liste) {
	
		// Lecture des valeurs
		$code = $liste['code'];
		
		$compteur2 = 0;
		
		foreach ($series['donnees'] as $serie) {
		
			$valeur = false;
			
			if (isset($serie['donnees'][$code])) {
			
				$valeur = $serie['donnees'][$code];
			
			}
			
			if ($valeur !== false) {
			
				$couleur = $serie['couleur'];
				
				$font_color = '#000000';
				$font_size = '10';
				
				
				// Calculs
				$caractere_largeur = ($font_size * 0.6);
				$texte_largeur = ceil(strlen($valeur) * $caractere_largeur);
				
				$barre_hauteur = round($valeur * $unite);
				
				$y = ($ymax - $barre_hauteur);
				
				$texte_x = round($x + ($barre_largeur / 2));
				$texte_y = $y - 3;
				
				
				// Affichage
				$g_id = $d_id . '2S' . $compteur . 'G' . $compteur2;
				$tooltip = $valeur;
				$onmouseout = "HideTooltip(evt);Assombrir('" . $g_id . "');";
				$onmousemove = "ShowTooltip(evt, '" . $tooltip . "');";
				$onmouseover = "Eclaircir('" . $g_id . "');";
				
				$sortie .= '<g id="' . $g_id . '"' . "\n";
				$sortie .= ' onmouseout="' . $onmouseout . '" onmousemove="' . $onmousemove . '" onmouseover="' . $onmouseover . '"';
				$sortie .= '>' . "\n";
				
				// Rectangles
				$sortie .= '<rect x="' . $x .'" y="' . $y . '" width="' . $barre_largeur . '" height="' . $barre_hauteur . '"';
				$sortie .= ' fill="' . $couleur . '" stroke="white" stroke-width="0.5" fill-opacity="1.0"';
				$sortie .= ' />' . "\n";
				
				// Valeurs
				// if ($barre_largeur > $texte_largeur) {
					$sortie .= '<text x="' . $texte_x . '" y="' . $texte_y . '" style="text-anchor:middle;fill:' . $font_color . ';font-size:' . $font_size . 'px;font-weight:normal;font-family:Tahoma, Verdana;font-style:normal;">';
					$sortie .= $valeur;
					$sortie .= '</text>' . "\n";
				// }
				
				$sortie .= '</g>' . "\n";
			
			}
			
			$x = $x + $barre_largeur;
			
			$compteur2++;
		
		}
		
		$compteur ++;
		
		if ($compteur <= $listes_nombre) {
		
			$x += $decalage_x;
		
		}
	
	}
	
	// on trace les libellés de l'axe X
	$zone_x1 = $xmin;
	$zone_largeur = $barre_largeur * $series_nombre;
	$compteur = 0;
	
	foreach($listes as $liste) {
	
		$zone_x2 = $zone_x1 + $zone_largeur;
		
		$police_couleur = '#000000';
		$police_taille = 11;
		$caractere_largeur = $police_taille * 0.6;
		
		// Lecture des informations sur le titre
		$titre_libelle = $liste['libelle'];
		$titre_largeur_max = $zone_largeur;
		$titre_longueur_max = floor($titre_largeur_max / $caractere_largeur);
		
		// var_dump($titre_longueur_max);
		
		$decoupe = chaine_decouper($titre_libelle, $titre_longueur_max);
		// var_dump($decoupe);
		
		$lignes = $decoupe['lignes'];
		
		if (count($lignes) <= 3 ) {
		
			$l = 1;
			
			foreach ($lignes as $ligne) {
			
				$titre_libelle = $ligne;
				
				// Calculs sur les paramètres du titre
				$titre_longueur = strlen($titre_libelle);
				$titre_largeur = $titre_longueur * $police_taille * 1;
				$titre_x = round(($zone_x1 + $zone_x2) / 2);
				$titre_y = $ymin + $hauteur_utile + ($police_taille * $l) + 2;
				
				// écriture du titre
				$sortie .= '<text x="' . $titre_x . '" y="' . $titre_y . '" style="text-anchor:middle;fill:' . $police_couleur . ';font-size:' . $police_taille . 'px;font-weight:normal;font-family:Tahoma, Verdana;font-style:normal;">';
				$sortie .= $titre_libelle;
				$sortie .= '</text>' . "\n";
				
				$l ++;
			
			}
		
		}
		
		$zone_x1 = $zone_x2;
		
		if ($compteur <= $listes_nombre) {
		
			$zone_x1 += $decalage_x;
		
		}
		
		$compteur++;
	
	}
	
	return $sortie;

}


?>