<?PHP


//
function chart_circulaires($parametres)
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
	
	// marges
	$padding_top = 10;
	$padding_right = 10;
	$padding_bottom = 30;
	$padding_left = 10;
	
	// bornes des abscisses et des ordonnées maximum
	$xmin = $origine_x + $padding_left;
	$xmax = $origine_x + $largeur - $padding_right;
	$ymin = $origine_y + $padding_top;
	$ymax = $origine_y + $hauteur - $padding_bottom;
	
	// dimensions utiles
	$largeur_utile = $xmax - $xmin;
	$hauteur_utile = $ymax - $ymin;
	
	// encombrements minimum
	$angle_mini = 10;
	$rayon_mini = 50;
	$x_padding_mini = 20;
	$y_padding_mini = 30;
	$largeur_mini = ($rayon_mini * 2) + $x_padding_mini;
	$hauteur_mini = ($rayon_mini * 2) + $y_padding_mini;
	
	// calculs
	$colonnes_max = floor($largeur_utile / $largeur_mini);
	$lignes_max = floor($hauteur_utile / $hauteur_mini);
	$cercles_max = $colonnes_max * $lignes_max;

	//
	$series_nombre = count($series['donnees']);
	
	// centre camembert
	$cercle_x = floor($largeur_utile / 2) + $xmin;
	$cercle_y = floor($hauteur_utile / 2) + $ymin;
	
	// rayon du camembert
	$rayon = floor(min($largeur_utile, $hauteur_utile) /2);
	
	// on regarde si le rayon assez grand
	if ($rayon > 10) {
	
		$compteur = 0;
		
		// Hack temporaire
		$hack = current($listes);
		$listes = [$hack];
		
		// Calculs
		$somme = 0;
		
		foreach ($listes as $liste) {
		
			$code = $liste['code'];
			
			foreach ($series['donnees'] as $serie) {
			
				$valeur = $serie['donnees'][$code];
				$somme += $valeur;
			
			}
		
		}
		
		$deg = $somme / 360;
		$moitie = $somme / 2;
		
		// Tracé
		foreach($listes as $liste) {
	
			$compteur2 = 0;
			
			$code = $liste['code'];
			
			// Starting point:
			$dx = $rayon;
			$dy = 0;
			$angle_total = 0;
			
			// Tracé
			foreach ($series['donnees'] as $serie) {
			
				$valeur = 0;
				
				if (isset($serie['donnees'][$code])) {
				
					$valeur = $serie['donnees'][$code];
				
				}
				
				$couleur = $serie['couleur'];
				
				//
				$font_size = 10;
				$font_color = '#ffffff';
				
				// Coordonnées absolues du début de l'arc de cercle
				$arc_x1 = $cercle_x + $dx;
				$arc_y1 = $cercle_y + $dy;
			
				// Calcul des angles
				$angle = $valeur / $deg;
				$angle_demi = $angle / 2;
				$angle_legende = $angle_total + $angle_demi;
				$angle_legende_rad = deg2rad($angle_legende);
				$angle_total = $angle_total + $angle;
				$angle_total_rad = deg2rad($angle_total);
				
				// Coordonnées ralatives de la légende
				$texte_xr = cos($angle_legende_rad) * $rayon * 0.80;
				$texte_yr = sin($angle_legende_rad) * $rayon * 0.80;
				
				// Coordonnées absolues de la fin de l'arc de cercle
				$texte_x = $cercle_x + $texte_xr - 2;
				$texte_y = $cercle_y + $texte_yr + 4;
				
				// Coordonnées relatives de la fin de l'arc de cercle
				$arc_x2r = cos($angle_total_rad) * $rayon;
				$arc_y2r = sin($angle_total_rad) * $rayon;
				
				// Coordonnées absolues de la fin de l'arc de cercle
				$arc_x2 = $cercle_x + $arc_x2r;
				$arc_y2 = $cercle_y + $arc_y2r;

				// Détermination de la courbure de l'arc
				if ($valeur > $moitie) {
				
					$laf = 1;
				
				} else {
				
					$laf = 0;
				
				}
				
				//
				$g_id = $d_id . '1S' . $compteur . 'G' . $compteur2;
				$tooltip = $valeur;
				$onmouseout = "HideTooltip(evt);Assombrir('" . $g_id . "');";
				$onmousemove = "ShowTooltip(evt, '" . $tooltip . "');";
				$onmouseover = "Eclaircir('" . $g_id . "');";
				
				$sortie .= '<g id="' . $g_id . '"' . "\n";
				$sortie .= ' onmouseout="' . $onmouseout . '" onmousemove="' . $onmousemove . '" onmouseover="' . $onmouseover . '"';
				$sortie .= '>' . "\n";
				
				$sortie .= '<path';
				
				$sortie .= ' ' . 'd="';
				
				// move cursor to center
				$sortie .= 'M' . $cercle_x . ',' . $cercle_y;
				
				$sortie .= ' ';
				
				// draw line away away from cursor
				$sortie .= 'L' . $arc_x1 . ',' . $arc_y1;
				
				$sortie .= ' ';
				
				// draw arc
				$sortie .= 'A' . $rayon . ',' . $rayon . ' 0 ' . $laf . ',1 ' . $arc_x2 . ',' . $arc_y2;
				
				$sortie .= ' ';
				
				// z = close path
				$sortie .= 'Z';
				
				$sortie .= '"';
				
				$sortie .= ' fill="' . $couleur . '" stroke="white" stroke-width="1" fill-opacity="1.0" stroke-linejoin="round"';
				
				$sortie .= ' />' . "\n";
				
				if ($angle > $angle_mini) {
				
					$sortie .= '<text x="' . $texte_x . '" y="' . $texte_y . '" style="text-anchor:middle; fill:' . $font_color . '; font-size:11px; font-weight:normal; font-family:Tahoma, Verdana; font-style:normal">';
					$sortie .= $valeur;
					$sortie .= '</text>' . "\n";
				
				}
				
				$sortie .= '</g>' . "\n";
				
				// old end points become new starting point
				$dx = $arc_x2r;
				$dy = $arc_y2r;
				
				$compteur2++;
			
			}
			
			// Lecture des informations sur le titre
			$titre_libelle = $liste['libelle'];
			$titre_couleur = '#000000';
			$titre_taille = 12;
			
			// Calculs sur les titres et sous-titres
			$titre_x = $xmin + round($largeur_utile / 2);
			$titre_y = $ymax + $titre_taille + 10;
			
			// écriture du titre
			$sortie .= '<text x="' . $titre_x . '" y="' . $titre_y . '" style="text-anchor:middle;fill:' . $titre_couleur . ';font-size:' . $titre_taille . 'px;font-weight:normal;font-family:Tahoma, Verdana;font-style:normal;">';
			$sortie .= $titre_libelle;
			$sortie .= '</text>' . "\n";
			
			$compteur++;
		
		}
	
	}
	
	return $sortie;

}


?>