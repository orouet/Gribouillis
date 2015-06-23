<?PHP


/**
 * Gribouillis
 * @package Gribouillis
 * @author Olivier ROUET
 * @version 1.0.0
 */


/**
 * Génère un diagramme en lignes
 *
 * @param array $parametres
 * @return string
 */
function diagrammes_lignes($parametres)
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
	$decalage_x = floor($largeur_utile / ($listes_nombre - 1));
	$decalages_x = $listes_intervalles * $decalage_x;
	
	// on lit la valeur maximale
	$valeur_max = $series['calculs']['valeur_max'];
	
	// plafond = valeur maximale jamais atteinte
	$valeur_plafond = plafond_trouver($valeur_max);
	
	// calcul de l'unité
	$unite = $hauteur_utile / $valeur_plafond;
	
	// paramétrage des points
	$point_rayon = 3;
	$point_forme = 'cercle';
	
	// affichage des axes d'ordonnées
	foreach($axes_y as $axe_y) {
	
		$param = [
			'x' => $origine_x,
			'y' => $ymin,
			'max' => $valeur_plafond,
			'largeur' => $largeur,
			'hauteur' => $hauteur_utile,
			'position' => $axe_y['position']
		];
		$sortie .= chart_ordonnees($param);
		unset($param);
	
	}
	
	// on trace les points
	$compteur = 0;
	
	foreach ($series['donnees'] as $serie) {
	
		$compteur2 = 0;
		
		$x1 = 0;
		$x2 = $xmin;
		$y1 = 0;
		$y2 = 0;
		
		$couleur = $serie['couleur'];
		
		foreach ($listes as $liste) {
		
			$code = $liste['code'];
			
			$valeur = false;
			
			if (isset($serie['donnees'][$code])) {
			
				$valeur = $serie['donnees'][$code];
			
			}
			
			$barre_largeur = 0;
			
			if ($valeur !== false) {
			
				$couleur = $serie['couleur'];
				
				$barre_hauteur = round($valeur * $unite);
				
				$y2 = ($ymax - $barre_hauteur);
				
				if ($compteur2 > 0) {
				
					$sortie .= '<line x1="' . $x1 .'" y1="' . $y1 . '" x2="' . $x2 .'" y2="' . $y2 . '" fill="' . $couleur . '" stroke="' . $couleur . '" stroke-width="1" fill-opacity="1.0" />' . "\n";
				
				}
				
				$g_id = $d_id . '1S' . $compteur . 'G' . $compteur2;
				$tooltip = $valeur;
				$onmouseout = "HideTooltip(evt);Assombrir('" . $g_id . "');";
				$onmousemove = "ShowTooltip(evt, '" . $tooltip . "');";
				$onmouseover = "Eclaircir('" . $g_id . "');";
				
				$sortie .= '<g id="' . $g_id . '"' . "\n";
				$sortie .= ' onmouseout="' . $onmouseout . '" onmousemove="' . $onmousemove . '" onmouseover="' . $onmouseover . '"';
				$sortie .= '>' . "\n";
				
				switch ($point_forme) {
				
					case 'cercle' :
					
						$sortie .= '<circle cx="' . $x2 .'" cy="' . $y2 . '" r="' . $point_rayon . '"';
						$sortie .= ' fill="' . $couleur . '" stroke="white" stroke-width="0.5" fill-opacity="1.0"';
						$sortie .= ' />' . "\n";
					
					break;
					
					
					case 'carre' :
					
						$sortie .= '<rect x="' . ($x2 - $point_rayon) .'" y="' . ($y2 - $point_rayon) . '" width="' . ($point_rayon * 2) . '" height="' . ($point_rayon * 2) . '"';
						$sortie .= ' fill="' . $couleur . '" stroke="white" stroke-width="0.5" fill-opacity="1.0"';
						$sortie .= ' />' . "\n";
					
					break;
				
				}
				
				$sortie .= '</g>' . "\n";
				
				$x1 = $x2;
				
				$y1 = $y2;
			
			}
			
			$compteur2++;
			
			if ($compteur < $series_nombre) {
			
				$x2 += $decalage_x;
			
			}
			
			
		
		}
		
		$compteur ++;
	
	}
	
	// on trace les libellés de l'axe X
	$zone_x1 = $xmin;
	$compteur = 0;
	
	foreach($listes as $liste) {
	
		// Lecture des informations sur le titre
		$titre_libelle = $liste['libelle'];
		$titre_couleur = '#000000';
		$titre_taille = 11;
		
		// Calculs sur le titre
		$titre_x = $zone_x1;
		$titre_y = $ymin + $hauteur_utile + $titre_taille + 2;
		
		// écriture du titre
		$sortie .= '<text x="' . $titre_x . '" y="' . $titre_y . '" style="text-anchor:middle;fill:' . $titre_couleur . ';font-size:' . $titre_taille . 'px;font-weight:normal;font-family:Tahoma, Verdana;font-style:normal;">';
		$sortie .= $titre_libelle;
		$sortie .= '</text>' . "\n";
		
		$zone_x1 += $decalage_x;
		
		$compteur++;
		
	}
	
	return $sortie;

}


?>