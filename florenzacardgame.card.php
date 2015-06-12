<?php

class FlorenzaCardHelper {

	static function createLocationDeck($playersCount, $locationCards) {
		$cards = "PICCONIERE,";
		if($playersCount >= 3) $cards.="PICCONIERE,";
		$cards.= "BOSCAIUOLO,";
		if($playersCount >= 4) $cards.="BOSCAIUOLO,";
		$cards.= "MONTEPIETA,FABBRO,LANAIUOLO,ERBOLAIO,BARATTO,";
		if($playersCount >= 3) $cards.="BARATTO,";
		$cards.= "SCARPELLINO,LEGNAIUOLO,ORAFO,OTTONAIO,TINTORE,SPEZIALE,CAPITANO";
		$cards = explode(",", $cards);
		$sql = "INSERT INTO location_card (type, card_order, tapped) VALUES ";
		$values = array();
		foreach($cards as $index => $card) $values[] = "('$card', $index, 0)";
		$sql .= implode($values, ',');
        FlorenzaCardGame::DbQuery($sql);
	}

	static function createFlorenzaDeck($playersCount, $florenzaCards) {
		
		$cards = array(
			"1" => array(
				"CASA" => $playersCount >= 3 ? ($playersCount >= 4 ? 4 : 3) : 2,
				"CASAMENTO" => 2,
				"PALADALTARE" => $playersCount >= 4 ? 1 : 0,
				"SAGRATO" => 1,
				"BIBLIOTECA" => 1,
				"CONVENTO" => $playersCount >= 4 ? 1 : 0,
				"RITRATTO" => 1,
				"FONTANA" => $playersCount >= 4 ? 1 : 0,
				"GIORDANOBRUNO" => $playersCount >= 3 ? 1 : 0,
				"GIROLAMOSAVONAROLA" => 1,
				"LANAIUOLO" => 1,
				"ERBOLAIO" => 1,
				"CAMBIAVALUTE" => 1,
				"MONTEDIPIETA" => 1,
				"FABBRO" => 1,
				"PICCONIERE" => $playersCount >= 3 ? 1 : 0,
				"BOSCAIUOLO" => $playersCount >= 3 ? 1 : 0,
				"SIGNOREDELORIONE" => $playersCount >= 3 ? 1 : 0,
				"MASTROARTISTA" => $playersCount >= 4 ? 1 : 0,
				"BOTTEGADARTE" => 1,
				"PROTETTORE" => 1,
				"ARTEMAGGIORE" => 1
			),
			"2" => array(
				"CASAMENTO" => $playersCount >= 3 ? 1 : 0,
				"PALAGIO" => $playersCount >= 4 ? 2 : 1,
				"TRITTICO" => $playersCount >= 4 ? 1 : 0,
				"ABSIDE" => 1,
				"CROCIFISSO" => 1,
				"PULPITO" => 1,
				"POZZO" => 1,
				"MERCATO" => $playersCount >= 4 ? 1 : 0,
				"MONUMENTOEQUESTRE" => 1,
				"LASTRICATO" => 1,
				"AFFRESCO" => 1,
				"GIARDINO" => 1,
				"CAMINO" => 1,
				"SCALONE" => $playersCount >= 4 ? 1 : 0,
				"SANFRANCESCODASSISI" => 1,
				"BERNARDODICHIARAVALLE" => $playersCount >= 4 ? 1 : 0,
				"MINIERA" => $playersCount >= 3 ? 1 : 0,
				"FONDERIA" => $playersCount >= 3 ? 1 : 0,
				"SEGHERIA" => 1,
				"CAVA" => 1,
				"SETAIUOLO" => $playersCount >= 3 ? 1 : 0,
				"MERCATANTE" => $playersCount >= 3 ? 1 : 0,
				"EDIFICATORE" => 2,
				"ARTEDEIMERCATANTI" => 1
			),
			"3" => array(
				"NAVATA" => 1,
				"FACCIATACHIESA" => 1,
				"CAPPELLA" => 1,
				"CUPOLA" => 1,
				"CAMPANILE" => $playersCount >= 3 ? 1 : 0,
				"SAGRESTIA" => 1,
				"CONVENTO" => 1,
				"BIBLIOTECA" => $playersCount >= 4 ? 1 : 0,
				"MERCATO" => 1,
				"SACELLO" => 1,
				"EDICOLA" => $playersCount >= 3 ? 1 : 0,
				"LOGGIA" => 1,
				"SALA" => 1,
				"INGRESSO" => 1,
				"STATUADIBRONZO" => $playersCount >= 4 ? 1 : 0,
				"FACCIATA" => 1,
				"GRANDEQUADRO" => $playersCount >= 3 ? 1 : 0,
				"CAMERANUZIALE" => 1,
				"TOMMASODAQUINO" => 1,
				"ANTONIODAPADOVA" => 1,
				"PROTETTORE" => $playersCount >= 3 ? 1 : 0,
				"ARTEMAGGIORE" => $playersCount >= 3 ? 1 : 0,
				"BOTTEGADARTE" => $playersCount >= 3 ? 1 : 0,
				"PADRONE" => 1
			)
		);
		
		$values = array();
		foreach($cards as $round => $roundCards) {
			foreach($roundCards as $type => $quantity) {
				$card = $florenzaCards[$type];
				$class = $card['class'];
				for($i = 0; $i < $quantity; $i++) {
					$values[] = "('$type', '$class', $round, 'box', 0)";
				}
			}
		}
		$sql = "INSERT INTO florenza_card (type, class, round, location, card_order) VALUES ";
		$sql .= implode( $values, ',' );
		FlorenzaCardGame::DbQuery( $sql );

	}
	
	static function createArtistDeck($artistCards) {
		$values = array();
		foreach($artistCards as $type => $artistCard) {
			foreach($artistCard['scorePoints'] as $scorePoint) {
				if(isset($artistCard['anonymous'])) {
					$values[] = "('$type', '" . $artistCard['class'] . "', $scorePoint, 'deck', 0, 1)";
				} else {
					$values[] = "('$type', '" . $artistCard['class'] . "', $scorePoint, 'deck', 0, 0)";
				}
			}
		}
		$sql = "INSERT INTO artist_card (type, class, score_point, location, card_order, anonymous) VALUES ";
		$sql .= implode( $values, ',' );
		FlorenzaCardGame::DbQuery( $sql );
		
	}

	static function createMonumentDeck($monumentCards) {
		$values = array();
		foreach($monumentCards as $type => $monumentCard) {
			$scorePoint = $monumentCard['scorePoint'];
			$values[] = "('$type', $scorePoint, 'deck', 0)";
		}
		$sql = "INSERT INTO monument_card (type, score_point, location, card_order) VALUES ";
		$sql .= implode( $values, ',' );
		FlorenzaCardGame::DbQuery( $sql );		
	}

}
