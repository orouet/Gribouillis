<?PHP


//
function diagramme_generer($parametres) {

	// initialisation des variables
	$sortie = '';
	
	$defaut = [
		'id' => 'd1',
		'x' => 0,
		'y' => 0,
		'largeur' => 400,
		'hauteur' => 400,
		'type' => 'batons',
		'police' => [
			'famille' => 'Tahoma, Verdana',
			'taille' => 10,
			'epaisseur' => 'normal',
			'style' => 'normal',
			'couleur' => '#000000'
		],
		'titre' => [
			'libelle' => 'Titre',
			'police' => [
				'taille' => 10,
				'epaisseur' => 'normal',
				'couleur' => '#000000'
			]
		],
		'sous_titre' => [
			'libelle' => 'Sous-titre',
			'police' => [
				'taille' => 10,
				'epaisseur' => 'normal',
				'couleur' => '#000000'
			]
		],
		'axes_x' => [
			[
				'titre' => [
					'libelle' => 'Axe X'
				],
				'type' => 'valeurs',
				'position' => 'bas'
			]
		],
		'axes_y' => [
			[
				'titre' => [
					'libelle' => 'Axe Y'
				],
				'type' => 'valeurs',
				'position' => 'gauche'
			]
		],
		'series' => [
			'donnees' => [
				[
					'libelle' => 'A',
					'couleur' => '#50B432',
					'donnees' => ['A' => '110', 'B' => '70', 'C' => '50', 'D' => '10', 'E' => '30', 'F' => '40']
				],
				[
					'libelle' => 'B',
					'couleur' => '#ED561B',
					'donnees' => ['A' => '90', 'B' => '50', 'C' => '30', 'D' => '60', 'E' => '50', 'F' => '60']
				],
				[
					'libelle' => 'C',
					'couleur' => '#058DC7',
					'donnees' => ['A' => '138', 'B' => '100', 'C' => '70', 'D' => '30', 'E' => '70', 'F' => '80']
				],
				[
					'libelle' => 'D',
					'couleur' => '#000000',
					'donnees' => ['A' => '10', 'B' => '20', 'C' => '15', 'D' => '5', 'E' => '90', 'F' => '100']
				],
				[
					'libelle' => 'E',
					'couleur' => '#6C71C4',
					'donnees' => ['A' => '25', 'B' => '63', 'C' => '48', 'D' => '30', 'E' => '50', 'F' => '150']
				],
				[
					'libelle' => 'F',
					'couleur' => '#B58900',
					'donnees' => ['A' => '80', 'B' => '110', 'C' => '80', 'D' => '110', 'E' => '100', 'F' => '35']
				]
			]
		]
	];
	
	// analyse et correction des paramètres
	$parametres = chart_analyser($parametres);
	
	// lectures des paramètres
	if (isset($parametres['d_id'])) {
		$d_id = $parametres['d_id'];
	}
	if (isset($parametres['x'])) {
		$origine_x = $parametres['x'];
	}
	if (isset($parametres['y'])) {
		$origine_y = $parametres['y'];
	}
	if (isset($parametres['largeur'])) {
		$largeur = $parametres['largeur'];
	}
	if (isset($parametres['hauteur'])) {
		$hauteur = $parametres['hauteur'];
	}
	if (isset($parametres['type'])) {
		$type = $parametres['type'];
	}
	if (isset($parametres['police'])) {
		$police = $parametres['police'];
	}
	if (isset($parametres['titre'])) {
		$titre = $parametres['titre'];
	}
	if (isset($parametres['sous-titre'])) {
		$soustitre = $parametres['sous-titre'];
	}
	if (isset($parametres['axes_x'])) {
		$axes_x = $parametres['axes_x'];
	}
	if (isset($parametres['axes_y'])) {
		$axes_y = $parametres['axes_y'];
	}
	if (isset($parametres['series'])) {
		$series = $parametres['series'];
	}
	
	// calculs automatiques
	$series = chart_series_analyser($series);
	
	// var_dump($series);
	
	// contour
	// $sortie .= '<rect x="' . $origine_x .'" y="' . $origine_y . '" width="' . $largeur . '" height="' . $hauteur . '" fill="white" stroke="black" stroke-width="0.5" fill-opacity="1.0" />' . "\n";
	
	// Lecture des informations sur le titre
	$titre_libelle = $titre['libelle'];
	$titre_couleur = '#000000';
	$titre_taille = 14;
	
	// Lecture des informations sur le sous-titre
	$soustitre_libelle = $soustitre['libelle'];
	$soustitre_couleur = '#000000';
	$soustitre_taille = 12;
	
	// marges
	$padding_top = $titre_taille + $soustitre_taille + 10;
	$padding_right = 10;
	$padding_bottom = 10;
	$padding_left = 10;
	
	// bornes des abscisses et des ordonnées maximum
	$xmin = $origine_x + $padding_left;
	$xmax = $origine_x + $largeur - $padding_right;
	$ymin = $origine_y + $padding_top;
	$ymax = $origine_y + $hauteur - $padding_bottom;
	
	// dimensions utiles
	$largeur_utile = $xmax - $xmin;
	$hauteur_utile = $ymax - $ymin;
	
	// Calculs sur les titres et sous-titres
	$titre_x = $xmin + round($largeur_utile / 2);
	$titre_y = $origine_y + $titre_taille + 10;
	$soustitre_x = $xmin + round($largeur_utile / 2);
	$soustitre_y = $titre_y + $soustitre_taille + 5;
	
	// écriture du titre
	$sortie .= '<text x="' . $titre_x . '" y="' . $titre_y . '" style="text-anchor:middle;fill:' . $titre_couleur . ';font-size:' . $titre_taille . 'px;font-weight:bold;font-family:Tahoma, Verdana;font-style:normal;">';
	$sortie .= $titre_libelle;
	$sortie .= '</text>' . "\n";
	
	// écriture du sous-titre
	$sortie .= '<text x="' . $soustitre_x . '" y="' . $soustitre_y . '" style="text-anchor:middle;fill:' . $soustitre_couleur . ';font-size:' . $soustitre_taille . 'px;font-weight:normal;font-family:Tahoma, Verdana;font-style:normal;">';
	$sortie .= $soustitre_libelle;
	$sortie .= '</text>' . "\n";
	
	switch ($type) {
	
		case 'rectangles':
		
			$diagramme_x = $xmin;
			$diagramme_largeur = $largeur_utile;
			
			$diagramme_y = $ymin + 20;
			$diagramme_hauteur = $hauteur_utile - 20;
		
			$param1 = [
				'd_id' => $d_id . 'R',
				'x' => $diagramme_x,
				'y' => $diagramme_y,
				'largeur' => $diagramme_largeur,
				'hauteur' => $diagramme_hauteur,
				'axes_x' => $axes_x,
				'axes_y' => $axes_y,
				'series' => $series,
			];
			
			$sortie .= diagrammes_rectangles($param1);
		
		break;
		
		case 'circulaires':
		
			$diagramme_x = $xmin;
			$diagramme_largeur = $largeur_utile;
			
			$diagramme_y = $ymin + 20;
			$diagramme_hauteur = $hauteur_utile - 20;
			
			$param1 = [
				'd_id' => $d_id . 'C',
				'x' => $diagramme_x,
				'y' => $diagramme_y,
				'largeur' => $diagramme_largeur,
				'hauteur' => $diagramme_hauteur,
				'axes_x' => $axes_x,
				'axes_y' => $axes_y,
				'series' => $series,
			];
			
			$sortie .= chart_circulaires($param1);
		
		break;
		
		case 'empilements':
		
			$diagramme_x = $xmin;
			$diagramme_largeur = $largeur_utile;
			
			$diagramme_y = $ymin + 20;
			$diagramme_hauteur = $hauteur_utile - 20;
			
			$param1 = [
				'd_id' => $d_id . 'E',
				'x' => $diagramme_x,
				'y' => $diagramme_y,
				'largeur' => $diagramme_largeur,
				'hauteur' => $diagramme_hauteur,
				'axes_x' => $axes_x,
				'axes_y' => $axes_y,
				'series' => $series,
			];
			
			$sortie .= diagrammes_empilements($param1);
		
		break;
		
		case 'lignes':
		
			$diagramme_x = $xmin;
			$diagramme_largeur = $largeur_utile;
			
			$diagramme_y = $ymin + 20;
			$diagramme_hauteur = $hauteur_utile - 20;
			
			$param1 = [
				'd_id' => $d_id . 'L',
				'x' => $diagramme_x,
				'y' => $diagramme_y,
				'largeur' => $diagramme_largeur,
				'hauteur' => $diagramme_hauteur,
				'axes_x' => $axes_x,
				'axes_y' => $axes_y,
				'series' => $series,
			];
			
			$sortie .= diagrammes_lignes($param1);
		
		break;
		
	}
	
	return $sortie;

}


// $sortie .= '<text x="' . $xmilieu . '" y="' . $y . '" style="text-anchor:middle; fill:#000000; font-size:12px; font-weight:normal; font-family:Tahoma, Verdana; font-style:normal" transform="rotate(-90 ' . $xmilieu . ' ' . $y . ')">' . "\n";


?>