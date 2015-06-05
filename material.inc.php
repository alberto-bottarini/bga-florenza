<?php

$this->locationCards = array(

	"PICCONIERE" => array(
		"title" => clienttranslate("Picconiere"),
		"titleTr" => self::_('Picconiere'),
		"description" => clienttranslate('Get 1 marble')
	),
	"BOSCAIUOLO" => array(
		"title" => clienttranslate("Boscaiuolo"),
		"titleTr" => self::_('Boscaiuolo'),
		"description" => clienttranslate('Get 1 wood')
	),
	"MONTEPIETA" => array(
		"title" => clienttranslate("Monte di pietà"),
		"titleTr" => self::_('Monte di pietà'),
		"description" => clienttranslate('Get 1 gold')
	),
	"FABBRO" => array(
		"title" => clienttranslate("Fabbro"),
		"titleTr" => self::_('Fabbro'),
		"description" => clienttranslate('Get 1 metal')
	),
	"LANAIUOLO" => array(
		"title" => clienttranslate("Lanaiuolo"),
		"titleTr" => self::_('Lanaiuolo'),
		"description" => clienttranslate('Get 1 fabric')
	),
	"ERBOLAIO" => array(
		"title" => clienttranslate("Erbolaio"),
		"titleTr" => self::_('Erbolaio'),
		"description" => clienttranslate('Get 1 spice')
	),
	"BARATTO" => array(
		"title" => clienttranslate("Baratto"),
		"titleTr" => self::_('Baratto'),
		"description" => clienttranslate('Exchange 1 resource for 1 different resource')
	),
	"SCARPELLINO" => array(
		"title" => clienttranslate("Scarpellino"),
		"titleTr" => self::_('Scarpellino'),
		"description" => clienttranslate('Sell 1 marble for 200 money')
	),
	"LEGNAIUOLO" => array(
		"title" => clienttranslate("Legnaiuolo"),
		"titleTr" => self::_('Legnaiuolo'),
		"description" => clienttranslate('Sell 1 wood for 200 money')
	),
	"ORAFO" => array(
		"title" => clienttranslate("Orafo"),
		"titleTr" => self::_('Orafo'),
		"description" => clienttranslate('Sell 1 gold for 200 money')
	),
	"OTTONAIO" => array(
		"title" => clienttranslate("Ottonaio"),
		"titleTr" => self::_('Ottonaio'),
		"description" => clienttranslate('Sell 1 metal for 200 money')
	),
	"TINTORE" => array(
		"title" => clienttranslate("Tintore"),
		"titleTr" => self::_('Tintore'),
		"description" => clienttranslate('Sell 1 fabric for 200 money')
	),
	"SPEZIALE" => array(
		"title" => clienttranslate("Speziale"),
		"titleTr" => self::_('Speziale'),
		"description" => clienttranslate('Sell 1 spice for 200 money')
	),
	"CAPITANO" => array(
		"title" => clienttranslate("Capitano del popolo"),
		"titleTr" => self::_('Capitano del popolo'),
		"description" => clienttranslate('Be the first player in the next round')
	)
);
		
$this->florenzaCards = array(
	
	"CASA" => array(
		"title" => clienttranslate("Casa"),
		"titleTr" => self::_('Casa'),
		"description" => clienttranslate('You can play an extra turn (the 7th) and draw an extra card'),
		"class" => "ABITAZIONE",
		"card_value" => "CASA",
		"cost" => array(
			"wood" => 2
		),
		"cardsDrawn" => 1
	),
	
	"CASAMENTO" => array(
		"title" => clienttranslate("Casamento"),
		"titleTr" => self::_('Casamento'),
		"description" => clienttranslate('You can play an extra turn (the 6th), draw an extra card and get an income of 50 money'),
		"class" => "ABITAZIONE",
		"cost" => array(
			"wood" => 2,
			"marble" => 1
		),
		"income" => array(
			"money" => 50
		),
		"cardsDrawn" => 1
	),
	
	"PALAGIO" => array(
		"title" => clienttranslate("Palagio"),
		"titleTr" => self::_('Palagio'),
		"description" => clienttranslate('You can play an extra turn (the 5th), draw an extra card and get an income of 100 money'),
		"class" => "ABITAZIONE",
		"cost" => array(
			"wood" => 1,
			"marble" => 2,
			"fabric" => 1
		),
		"income" => array(
			"money" => 100
		),
		"cardsDrawn" => 1
	),
	
	"ABSIDE" => array(
		"title" => clienttranslate("Abside"),
		"titleTr" => self::_('Abside'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"marble" => 1,
			"spice" => 1,
			"money" => 50
		),
		"scorePoint" => 4,
		"artists" => array("painter")
	),
	
	"CAMPANILE" => array(
		"title" => clienttranslate("Campanile"),
		"titleTr" => self::_('Campanile'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"marble" => 1,
			"wood" => 1,
			"metal" => 1,
			"money" => 100
		),
		"scorePoint" => 5,
		"artists" => array("architect")
	),
	
	"CAPPELLA" => array(
		"title" => clienttranslate("Cappella"),
		"titleTr" => self::_('Cappella'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"marble" => 2,
			"gold" => 1,
			"spice" => 1,
			"money" => 50
		),
		"scorePoint" => 5,
		"artists" => array("sculptor", "painter")
	),
	
	"CROCIFISSO" => array(
		"title" => clienttranslate("Crocifisso"),
		"titleTr" => self::_('Crocifisso'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"wood" => 1,
			"gold" => 1,
			"metal" => 1
		),
		"scorePoint" => 3,
		"artists" => array("sculptor")
	),
	
	"CUPOLA" => array(
		"title" => clienttranslate("Cupola"),
		"titleTr" => self::_('Cupola'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"wood" => 2,
			"marble" => 2,
			"money" => 50
		),
		"scorePoint" => 5,
		"artists" => array("architect", "painter")
	),		
	
	"FACCIATACHIESA" => array(
		"title" => clienttranslate("Facciata"),
		"titleTr" => self::_('Facciata'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"marble" => 3,
			"money" => 100
		),
		"scorePoint" => 5,
		"artists" => array("architect", "sculptor")
	),
	
	"NAVATA" => array(
		"title" => clienttranslate("Navata"),
		"titleTr" => self::_('Navata'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"gold" => 1,
			"spice" => 1,
			"fabric" => 1,						
			"money" => 50
		),
		"scorePoint" => 4,
		"artists" => array("architect", "painter")
	),
	
	"PULPITO" => array(
		"title" => clienttranslate("Pulpito"),
		"titleTr" => self::_('Pulpito'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"marble" => 1,
			"wood" => 1
		),
		"scorePoint" => 2,
		"artists" => array("sculptor")
	),	
	
	"SAGRATO" => array(
		"title" => clienttranslate("Sagrato"),
		"titleTr" => self::_('Sagrato'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"marble" => 1,
			"money" => 100
		),
		"scorePoint" => 3,
		"artists" => array("architect")
	),	
	
	"SAGRESTIA" => array(
		"title" => clienttranslate("Sagrestia"),
		"titleTr" => self::_('Sagrestia'),
		"class" => "CHIESARIONE",
		"cost" => array(
			"wood" => 1,
			"fabric" => 1,
			"money" => 100
		),
		"scorePoint" => 4,
		"artists" => array("architect", "sculptor", "painter")
	),		
	
	"PALADALTARE" => array(
		"title" => clienttranslate("Pala d'altare"),
		"titleTr" => self::_("Pala d'altare"),
		"class" => "CHIESARIONE",
		"cost" => array(
			"wood" => 1,
			"gold" => 1,
			"spice" => 1
		),
		"scorePoint" => 3,
		"artists" => array("painter")
	),
	
	"TRITTICO" => array(
		"title" => clienttranslate("Trittico"),
		"titleTr" => self::_("Trittico"),
		"class" => "CHIESARIONE",
		"cost" => array(
			"metal" => 1,
			"gold" => 1,
			"spice" => 1,
			"fabric" => 1
		),
		"scorePoint" => 4,
		"artists" => array("painter", "sculptor")
	),
	
	"LASTRICATO" => array(
		"title" => clienttranslate("Lastricato"),
		"titleTr" => self::_("Lastricato"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"marble" => 1,
			"money" => 50
		),
		"scorePoint" => 2
	),
	
	"MONUMENTOEQUESTRE" => array(
		"title" => clienttranslate("Monumento equestre"),
		"titleTr" => self::_("Monumento equestre"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"gold" => 1,
			"metal" => 2,
			"money" => 50
		),
		"scorePoint" => 4
	),
	
	"POZZO" => array(
		"title" => clienttranslate("Pozzo"),
		"titleTr" => self::_("Pozzo"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"marble" => 1,
			"metal" => 1,
			"money" => 50
		),
		"scorePoint" => 3
	),
	
	"MERCATO" => array(
		"title" => clienttranslate("Mercato"),
		"titleTr" => self::_("Mercato"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"wood" => 1,
			"gold" => 1,
			"spice" => 1,
			"fabric" => 1,
			"money" => 50
		),
		"scorePoint" => 5
	),
	
	"CONVENTO" => array(
		"title" => clienttranslate("Convento"),
		"titleTr" => self::_("Convento"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"wood" => 2,
			"marble" => 1,
			"fabric" => 1,
			"money" => 50
		),
		"scorePoint" => 5
	),
	
	"SACELLO" => array(
		"title" => clienttranslate("Sacello"),
		"titleTr" => self::_("Sacello"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"wood" => 1,
			"gold" => 1,
			"fabric" => 1
		),
		"scorePoint" => 3
	),
	
	"BIBLIOTECA" => array(
		"title" => clienttranslate("Biblioteca"),
		"titleTr" => self::_("Biblioteca"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"wood" => 2,
			"money" => 150
		),
		"scorePoint" => 5
	),
	
	"EDICOLA" => array(
		"title" => clienttranslate("Edicola"),
		"titleTr" => self::_("Edicola"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"marble" => 1,
			"spice" => 1,
			"money" => 100
		),
		"scorePoint" => 4
	),
	
	"LOGGIA" => array(
		"title" => clienttranslate("Loggia"),
		"titleTr" => self::_("Loggia"),
		"class" => "EDIFICIORIONE",
		"cost" => array(
			"wood" => 1,
			"marble" => 1,
			"metal" => 1,
			"money" => 50
		),
		"scorePoint" => 4
	),
	
	"AFFRESCO" => array(
		"title" => clienttranslate("Affresco"),
		"titleTr" => self::_("Affresco"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"spice" => 2,
			"money" => 50
		),
		"scorePoint" => 3,
		"artists" => array("painter")
	),
	
	"GIARDINO" => array(
		"title" => clienttranslate("Giardino"),
		"titleTr" => self::_("Giardino"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"marble" => 1,
			"gold" => 1,
			"money" => 50
		),
		"scorePoint" => 3,
		"artists" => array("architect", "sculptor")
	),
	
	"SALA" => array(
		"title" => clienttranslate("Sala"),
		"titleTr" => self::_("Sala"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"wood" => 1,
			"spice" => 1,
			"fabric" => 1,
			"money" => 100
		),
		"scorePoint" => 5,
		"artists" => array("architect", "painter")
	),
	
	"INGRESSO" => array(
		"title" => clienttranslate("Ingresso"),
		"titleTr" => self::_("Ingresso"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"wood" => 1,
			"marble" => 1,
			"metal" => 1,
			"money" => 50
		),
		"scorePoint" => 4,
		"artists" => array("architect", "sculptor")
	),
	
	"STATUADIBRONZO" => array(
		"title" => clienttranslate("Statua di bronzo"),
		"titleTr" => self::_("Statua di bronzo"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"metal" => 2,
			"money" => 100
		),
		"scorePoint" => 4,
		"artists" => array("sculptor")
	),
	
	"FACCIATA" => array(
		"title" => clienttranslate("Facciata"),
		"titleTr" => self::_("Facciata"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"marble" => 2,
			"money" => 100
		),
		"scorePoint" => 4,
		"artists" => array("architect", "sculptor")
	),
	
	"CAMINO" => array(
		"title" => clienttranslate("Camino"),
		"titleTr" => self::_("Camino"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"marble" => 1,
			"gold" => 1,
			"spice" => 1
		),
		"scorePoint" => 3,
		"artists" => array("architect", "sculptor", "painter")
	),
	
	"GRANDEQUADRO" => array(
		"title" => clienttranslate("Grande quadro"),
		"titleTr" => self::_("Grande quadro"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"spice" => 1,
			"fabric" => 2,
			"money" => 100
		),
		"scorePoint" => 5,
		"artists" => array("painter")
	),
	
	"RITRATTO" => array(
		"title" => clienttranslate("Ritratto"),
		"titleTr" => self::_("Ritratto"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"spice" => 1,
			"fabric" => 1
		),
		"scorePoint" => 2,
		"artists" => array("painter")
	),
	
	"CAMERANUZIALE" => array(
		"title" => clienttranslate("Camera nuziale"),
		"titleTr" => self::_("Camera nuziale"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"wood" => 1,
			"fabric" => 2
		),
		"scorePoint" => 3,
		"artists" => array("painter")
	),
	
	"FONTANA" => array(
		"title" => clienttranslate("Fontana"),
		"titleTr" => self::_("Fontana"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"marble" => 1,
			"metal" => 1,
			"money" => 50
		),
		"scorePoint" => 3,
		"artists" => array("architect", "sculptor")
	),
	
	"SCALONE" => array(
		"title" => clienttranslate("Scalone"),
		"titleTr" => self::_("Scalone"),
		"class" => "PALAZZOFAMIGLIA",
		"cost" => array(
			"marble" => 1,
			"gold" => 1,
			"money" => 50
		),
		"scorePoint" => 3,
		"artists" => array("architect", "sculptor")
	),
	
	"GIORDANOBRUNO" => array(
		"title" => clienttranslate("Giordano Bruno"),
		"titleTr" => self::_("Giordano Bruno"),
		"description" => clienttranslate('You can play an extra turn (the 8th)'),
		"class" => "PREDICATORE",
		"cost" => array(
			"money" => 100
		)
	),
	
	"SANFRANCESCODASSISI" => array(
		"title" => clienttranslate("San Francesco d'Assisi"),
		"titleTr" => self::_("San Francesco d'Assisi"),
		"description" => clienttranslate('You can play an extra turn (the 8th)'),
		"class" => "PREDICATORE",
		"cost" => array(
			"money" => 100
		)
	),
	
	"TOMMASODAQUINO" => array(
		"title" => clienttranslate("Tommaso d'Aquino"),
		"titleTr" => self::_("Tommaso d'Aquino"),
		"description" => clienttranslate('You can play an extra turn (the 8th)'),
		"class" => "PREDICATORE",
		"cost" => array(
			"money" => 100
		)
	),
	
	"GIROLAMOSAVONAROLA" => array(
		"title" => clienttranslate("Girolamo Savonarola"),
		"titleTr" => self::_("Girolamo Savonarola"),
		"description" => clienttranslate('You can play an extra turn (the 8th)'),
		"class" => "PREDICATORE",
		"cost" => array(
			"money" => 100
		)
	),
	
	"ANTONIODAPADOVA" => array(
		"title" => clienttranslate("Antonio da Padova"),
		"titleTr" => self::_("Antonio da Padova"),
		"description" => clienttranslate('You can play an extra turn (the 8th)'),
		"class" => "PREDICATORE",
		"cost" => array(
			"money" => 100
		)
	),
	
	"BERNARDODICHIARAVALLE" => array(
		"title" => clienttranslate("Bernardo di Chiaravalle"),
		"titleTr" => self::_("Bernardo di Chiaravalle"),
		"description" => clienttranslate('You can play an extra turn (the 8th)'),
		"class" => "PREDICATORE",
		"cost" => array(
			"money" => 100
		)
	),
	
	"LANAIUOLO" => array(
		"title" => clienttranslate("Lanaiuolo"),
		"titleTr" => self::_("Lanaiuolo"),
		"description" => clienttranslate('You get an income of a fabric'),
		"class" => "BOTTEGA",
		"cost" => array(
			"wood" => 1,
			"spice" => 1
		), 
		"income" => array(
			"fabric" => 1
		)
	),
	
	"ERBOLAIO" => array(
		"title" => clienttranslate("Erbolaio"),
		"titleTr" => self::_("Erbolaio"),
		"description" => clienttranslate('You get an income of a spice'),
		"class" => "BOTTEGA",
		"cost" => array(
			"wood" => 1,
			"fabric" => 1
		), 
		"income" => array(
			"spice" => 1
		)
	),	
	
	"CAMBIAVALUTE" => array(
		"title" => clienttranslate("Cambiavalute"),
		"titleTr" => self::_("Cambiavalute"),
		"description" => clienttranslate('You get an income of 100 money'),
		"class" => "BOTTEGA",
		"cost" => array(
			"wood" => 1,
			"gold" => 1
		), 
		"income" => array(
			"money" => 100
		)
	),	
	
	"MONTEDIPIETA" => array(
		"title" => clienttranslate("Monte di Pietà"),
		"titleTr" => self::_("Monte di Pietà"),
		"description" => clienttranslate('You get an income of a gold'),
		"class" => "BOTTEGA",
		"cost" => array(
			"metal" => 1,
			"marble" => 1
		), 
		"income" => array(
			"gold" => 1
		)
	),	
	
	"FABBRO" => array(
		"title" => clienttranslate("Fabbro"),
		"titleTr" => self::_("Fabbro"),
		"description" => clienttranslate('You get an income of a metal'),
		"class" => "BOTTEGA",
		"cost" => array(
			"wood" => 1,
			"marble" => 1
		), 
		"income" => array(
			"metal" => 1
		)
	),	
	
	"PICCONIERE" => array(
		"title" => clienttranslate("Picconiere"),
		"titleTr" => self::_("Picconiere"),
		"description" => clienttranslate('You get an income of a marble'),
		"class" => "BOTTEGA",
		"cost" => array(
			"metal" => 1,
			"gold" => 1
		), 
		"income" => array(
			"marble" => 1
		)
	),	
	
	"BOSCAIUOLO" => array(
		"title" => clienttranslate("Boscaiuolo"),
		"titleTr" => self::_("Boscaiuolo"),
		"description" => clienttranslate('You get an income of a wood'),
		"class" => "BOTTEGA",
		"cost" => array(
			"metal" => 1,
			"fabric" => 1
		), 
		"income" => array(
			"wood" => 1
		)
	),	
	
	"MINIERA" => array(
		"title" => clienttranslate("Miniera"),
		"titleTr" => self::_("Miniera"),
		"description" => clienttranslate('You get an income of a gold and 50 money'),
		"class" => "BOTTEGA",
		"cost" => array(
			"wood" => 1,
			"metal" => 1,
			"money" => 50
		), 
		"income" => array(
			"gold" => 1,
			"money" => 50
		)
	),	
	
	"FONDERIA" => array(
		"title" => clienttranslate("Fonderia"),
		"titleTr" => self::_("Fonderia"),
		"description" => clienttranslate('You get an income of a metal and 50 money'),
		"class" => "BOTTEGA",
		"cost" => array(
			"wood" => 1,
			"marble" => 1,
			"money" => 50
		), 
		"income" => array(
			"metal" => 1,
			"money" => 50
		)
	),	
	
	"SEGHERIA" => array(
		"title" => clienttranslate("Segheria"),
		"titleTr" => self::_("Segheria"),
		"class" => "BOTTEGA",
		"description" => clienttranslate('You get an income of a wood and 50 money'),
		"cost" => array(
			"metal" => 1,
			"marble" => 1,
			"money" => 50
		), 
		"income" => array(
			"wood" => 1,
			"money" => 50
		)
	),	
	
	"CAVA" => array(
		"title" => clienttranslate("Cava"),
		"titleTr" => self::_("Cava"),
		"description" => clienttranslate('You get an income of a marble and 50 money'),
		"class" => "BOTTEGA",
		"cost" => array(
			"metal" => 1,
			"wood" => 1,
			"money" => 50
		), 
		"income" => array(
			"marble" => 1,
			"money" => 50
		)
	),	
	
	"SETAIUOLO" => array(
		"title" => clienttranslate("Setaiuolo"),
		"titleTr" => self::_("Setaiuolo"),
		"description" => clienttranslate('You get an income of a fabric and 50 money'),
		"class" => "BOTTEGA",
		"cost" => array(
			"spice" => 1,
			"wood" => 1,
			"money" => 50
		), 
		"income" => array(
			"fabric" => 1,
			"money" => 50
		)
	),	
	
	"MERCATANTE" => array(
		"title" => clienttranslate("Mercatante"),
		"titleTr" => self::_("Mercatante"),
		"description" => clienttranslate('You get an income of a spice and 50 money'),
		"class" => "BOTTEGA",
		"cost" => array(
			"gold" => 1,
			"fabric" => 1,
			"money" => 50
		), 
		"income" => array(
			"spice" => 1,
			"money" => 50
		)
	),
	
	"ARTEMAGGIORE" => array(
		"title" => clienttranslate("Arte Maggiore"),
		"titleTr" => self::_("Arte Maggiore"),
		"description" => clienttranslate('At the end of the game, you receive 3 presige points for each Workshop (yellow) you have built'),
		"class" => "ARTE",
		"cost" => array(
			"fabric" => 1,
			"wood" => 1,
			"money" => 50
		)
	),
	
	"SIGNOREDELORIONE" => array(
		"title" => clienttranslate("Signore de lo Rione"),
		"titleTr" => self::_("Signore de lo Rione"),
		"description" => clienttranslate('At the end of the game, you receive 2 extra prestige points (7 points instead of 5) for each set of Family Palace (green), District Church (blue) and District Building (grey) you have built'),
		"class" => "ARTE",
		"cost" => array(
			"gold" => 1,
			"spice" => 1,
			"money" => 50
		)
	),	
	
	"PROTETTORE" => array(
		"title" => clienttranslate("Protettore"),
		"titleTr" => self::_("Protettore"),
		"description" => clienttranslate('At the end of the game, you receive 3 prestige points for each Monuments you have built'),
		"class" => "ARTE",
		"cost" => array(
			"gold" => 2,
			"money" => 50
		)
	),
	
	"MASTROARTISTA" => array(
		"title" => clienttranslate("Mastro Artista"),
		"titleTr" => self::_("Mastro Artista"),
		"description" => clienttranslate('At the end of the game, you receive 5 prestige points for each set of Architect, Painter and Sculptur you have hired'),
		"class" => "ARTE",
		"cost" => array(
			"wood" => 1,
			"fabric" => 1,
			"money" => 50
		)
	),	
	
	"BOTTEGADARTE" => array(
		"title" => clienttranslate("Bottega d'Arte"),
		"titleTr" => self::_("Bottega d'Arte"),
		"description" => clienttranslate('You receive a discount of 50 fiorini when you hire any Artist'),
		"class" => "ARTE",
		"cost" => array(
			"wood" => 1,
			"spice" => 1,
			"money" => 50
		)
	),		
	
	"EDIFICATORE" => array(
		"title" => clienttranslate("Edificatore"),
		"titleTr" => self::_("Edificatore"),
		"description" => clienttranslate('You receive a discount of 50 fiorini when you complete a Monument'),
		"class" => "ARTE",
		"cost" => array(
			"marble" => 1,
			"fabric" => 1
		)
	),		
	
	"ARTEDEIMERCATANTI" => array(
		"title" => clienttranslate("Arte dei Mercatanti"),
		"titleTr" => self::_("Arte dei Mercatanti"),
		"description" => clienttranslate('During collect income phase, you receive an extra resource of your choice'),
		"class" => "ARTE",
		"cost" => array(
			"fabric" => 1,
			"spice" => 1,
			"money" => 50
		)
	),		
	
	"PADRONE" => array(
		"title" => clienttranslate("Padrone"),
		"titleTr" => self::_("Padrone"),
		"description" => clienttranslate('At the end of the game, you receive 3 prestige points for each Residence (purple) you have built'),
		"class" => "ARTE",
		"cost" => array(
			"marble" => 1,
			"gold" => 1,
			"money" => 50
		)
	)
	
);

$this->artistCards = array(

	"ALBERTI-ARCHITECT" => array(
		"title" => "Leon Battista Alberti",
		"class" => "ARCHITECT",
		"cost" => 50,
		"scorePoints" => array(1)
	),

	"ANGELICO-PAINTER" => array(
		"title" => "Beato Angelico",
		"class" => "PAINTER",
		"cost" => 100,
		"scorePoints" => array(2)
	),

	"BERNINI-ARCHITECT" => array(
		"title" => "Gian Lorenzo Bernini",
		"class" => "ARCHITECT",
		"cost" => 250,
		"scorePoints" => array(4,4,5,6)
	),

	"BERNINI-SCULPTOR" => array(
		"title" => "Gian Lorenzo Bernini",
		"class" => "SCULPTOR",
		"cost" => 200,
		"scorePoints" => array(3,4,4,5)
	),

	"BORROMINI-ARCHITECT" => array(
		"title" => "Francesco Borromini",
		"class" => "ARCHITECT",
		"cost" => 150,
		"scorePoints" => array(2,2,3,4)
	),

	"BOTTICELLI-PAINTER" => array(
		"title" => "Sandro Botticelli",
		"class" => "PAINTER",
		"cost" => 200,
		"scorePoints" => array(3,4,5)
	),	

	"BRAMANTE-ARCHITECT" => array(
		"title" => "Donato Bramante",
		"class" => "ARCHITECT",
		"cost" => 100,
		"scorePoints" => array(2,2)
	),	

	"BRUNELLESCHI-ARCHITECT" => array(
		"title" => "Filippo Brunelleschi",
		"class" => "ARCHITECT",
		"cost" => 200,
		"scorePoints" => array(2,4,6)
	),	

	"BUONARROTI-ARCHITECT" => array(
		"title" => "Michelangelo Buonarroti",
		"class" => "ARCHITECT",
		"cost" => 150,
		"scorePoints" => array(3)
	),

	"BUONARROTI-PAINTER" => array(
		"title" => "Michelangelo Buonarroti",
		"class" => "PAINTER",
		"cost" => 250,
		"scorePoints" => array(4,5,6,7)
	),

	"BUONARROTI-SCULPTOR" => array(
		"title" => "Michelangelo Buonarroti",
		"class" => "SCULPTOR",
		"cost" => 300,
		"scorePoints" => array(5,5,6,7)
	),	

	"CARAVAGGIO-PAINTER" => array(
		"title" => "Caravaggio",
		"class" => "PAINTER",
		"cost" => 250,
		"scorePoints" => array(4,5,6)
	),

	"CELLINI-SCULPTOR" => array(
		"title" => "Benvenuto Cellini",
		"class" => "SCULPTOR",
		"cost" => 150,
		"scorePoints" => array(2,3,4)
	),	

	"DASANGALLO-ARCHITECT" => array(
		"title" => "Antonio Da Sangallo",
		"class" => "ARCHITECT",
		"cost" => 100,
		"scorePoints" => array(1,2,3)
	),

	"DAVINCI-PAINTER" => array(
		"title" => "Leonardo Da Vinci",
		"class" => "PAINTER",
		"cost" => 200,
		"scorePoints" => array(3,7)
	),

	"DAVINCI-SCULPTOR" => array(
		"title" => "Leonardo Da Vinci",
		"class" => "SCULPTOR",
		"cost" => 150,
		"scorePoints" => array(3)
	),	

	"DELLAFRANCESCA-PAINTER" => array(
		"title" => "Piero Della Francesca",
		"class" => "PAINTER",
		"cost" => 200,
		"scorePoints" => array(3,4)
	),

	"DELVERROCCHIO-SCULPTOR" => array(
		"title" => "Andrea Del Verrocchio",
		"class" => "SCULPTOR",
		"cost" => 100,
		"scorePoints" => array(1,2,2,3)
	),				

	"DONATELLO-SCULPTOR" => array(
		"title" => "Donatello",
		"class" => "SCULPTOR",
		"cost" => 200,
		"scorePoints" => array(3,4,4,5)
	),

	"GHIBERTI-SCULPTOR" => array(
		"title" => "Lorenzo Ghiberti",
		"class" => "SCULPTOR",
		"cost" => 150,
		"scorePoints" => array(2,3)
	),

	"GHIRLANDAIO-PAINTER" => array(
		"title" => "Domenico Ghirlandaio",
		"class" => "PAINTER",
		"cost" => 150,
		"scorePoints" => array(2,3)
	),

	"GIAMBOLOGNA-SCULPTOR" => array(
		"title" => "Giambologna",
		"class" => "SCULPTOR",
		"cost" => 100,
		"scorePoints" => array(2,2,2,3)
	),		

	"GIOTTO-ARCHITECT" => array(
		"title" => "Giotto",
		"class" => "ARCHITECT",
		"cost" => 150,
		"scorePoints" => array(3)
	),

	"GIOTTO-PAINTER" => array(
		"title" => "Giotto",
		"class" => "PAINTER",
		"cost" => 200,
		"scorePoints" => array(3,4)
	),	

	"LIPPI-PAINTER" => array(
		"title" => "Filippo Lippi",
		"class" => "PAINTER",
		"cost" => 150,
		"scorePoints" => array(2,3)
	),	

	"MOCHI-SCULPTOR" => array(
		"title" => "Francesco Mochi",
		"class" => "SCULPTOR",
		"cost" => 100,
		"scorePoints" => array(1,2)
	),	

	"PALLADIO-ARCHITECT" => array(
		"title" => "Andrea Palladio",
		"class" => "ARCHITECT",
		"cost" => 150,
		"scorePoints" => array(2,3)
	),

	"PERUGINO-PAINTER" => array(
		"title" => "Pietro Perugino",
		"class" => "PAINTER",
		"cost" => 100,
		"scorePoints" => array(2,2)
	),	

	"ROMANO-ARCHITECT" => array(
		"title" => "Giulio Romano",
		"class" => "ARCHITECT",
		"cost" => 100,
		"scorePoints" => array(2,2)
	),	

	"SANZIO-PAINTER" => array(
		"title" => "Raffaello Sanzio",
		"class" => "PAINTER",
		"cost" => 250,
		"scorePoints" => array(4,5,5,6)
	),	

	"VASARI-ARCHITECT" => array(
		"title" => "Giorgio Vasari",
		"class" => "ARCHITECT",
		"cost" => 100,
		"scorePoints" => array(2)
	),	

	"VECELLIO-PAINTER" => array(
		"title" => "Tiziano Vecellio",
		"class" => "PAINTER",
		"cost" => 200,
		"scorePoints" => array(4,5)
	),			

	"VIGNOLA-ARCHITECT" => array(
		"title" => "Vignola",
		"class" => "ARCHITECT",
		"cost" => 150,
		"scorePoints" => array(2,3,3,4)
	),

	"ANONYMOUS-ARCHITECT" => array(
		"title" => _("Anonymous Architect"),
		"class" => "ARCHITECT",
		"cost" => 50,
		"scorePoints" => array(0),
		"anonymous" => true
	),

	"ANONYMOUS-PAINTER" => array(
		"title" => _("Anonymous Painter"),
		"class" => "PAINTER",
		"cost" => 50,
		"scorePoints" => array(0),
		"anonymous" => true
	),

	"ANONYMOUS-SCULPTOR" => array(
		"title" => _("Anonymous Sculptur"),
		"class" => "SCULPTOR",
		"cost" => 50,
		"scorePoints" => array(0),
		"anonymous" => true
	)

);

$this->monumentsCards = array(

	"BANCO-ATRIO" => array(
		"title" => "Atrio",
		"location" => "Banco",
		"scorePoint" => 9,
		"cost" => array(
			"wood" => 1,
			"fabric" => 1,
			"gold" => 1,
			"metal" => 1,
			"money" => 200
		),
		"artists" => array("sculptor", "painter", "architect")
 	),

	"BATTISTERO-CUPOLA" => array(
		"title" => "Cupola",
		"location" => "Battistero",
		"scorePoint" => 7,
		"cost" => array(
			"marble" => 3,
			"gold" => 1,
			"money" => 100
		),
		"artists" => array("painter", "architect")
 	), 	

	"BATTISTERO-PORTALI" => array(
		"title" => "Portali",
		"location" => "Battistero",
		"scorePoint" => 8,
		"cost" => array(
			"wood" => 2,
			"metal" => 2,
			"money" => 150
		),
		"artists" => array("sculptor", "architect")
 	), 	

	"DUOMO-ABSIDE" => array(
		"title" => "Abside",
		"location" => "Duomo",
		"scorePoint" => 14,
		"cost" => array(
			"wood" => 2,
			"marble" => 3,
			"gold" => 1,
			"spice" => 1,
			"money" => 200
		),
		"artists" => array("painter")
 	),

	"DUOMO-ALTARE" => array(
		"title" => "Altare",
		"location" => "Duomo",
		"scorePoint" => 12,
		"cost" => array(
			"marble" => 2,
			"gold" => 2,
			"metal" => 1,
			"fabric" => 1,
			"money" => 150
		),
		"artists" => array("painter", "architect", "sculptor")
 	), 	

	"DUOMO-CAMPANILE" => array(
		"title" => "Campanile",
		"location" => "Duomo",
		"scorePoint" => 15,
		"cost" => array(
			"marble" => 4,
			"wood" => 2,
			"metal" => 2,
			"money" => 200
		),
		"artists" => array("architect")
 	), 

	"DUOMO-CAPPELLA" => array(
		"title" => "Cappella",
		"location" => "Duomo",
		"scorePoint" => 14,
		"cost" => array(
			"wood" => 1,
			"marble" => 2,
			"metal" => 2,
			"spice" => 2,
			"money" => 200
		),
		"artists" => array("sculptor", "painter")
 	),

	"DUOMO-CUPOLA" => array(
		"title" => "Cupola",
		"location" => "Duomo",
		"scorePoint" => 19,
		"cost" => array(
			"marble" => 4,
			"wood" => 2,
			"gold" => 2,
			"spice" => 2,
			"money" => 300
		),
		"artists" => array("painter", "architect")
 	), 	

	"DUOMO-FACCIATA" => array(
		"title" => "Facciata",
		"location" => "Duomo",
		"scorePoint" => 18,
		"cost" => array(
			"marble" => 5,
			"wood" => 1,
			"gold" => 1,
			"metal" => 1,
			"spice" => 1,
			"money" => 300
		),
		"artists" => array("sculptor", "architect")
 	), 

	"DUOMO-NAVATA" => array(
		"title" => "Navata",
		"location" => "Duomo",
		"scorePoint" => 14,
		"cost" => array(
			"wood" => 2,
			"marble" => 2,
			"fabric" => 2,
			"money" => 250
		),
		"artists" => array("painter", "architect")
 	),

	"DUOMO-PULPITO" => array(
		"title" => "Pulpito",
		"location" => "Duomo",
		"scorePoint" => 9,
		"cost" => array(
			"marble" => 2,
			"fabric" => 1,
			"money" => 200
		),
		"artists" => array("sculptor")
 	), 	

	"LASTRICATO-FONTANA" => array(
		"title" => "Fontana",
		"location" => "Lastricato",
		"scorePoint" => 8,
		"cost" => array(
			"marble" => 2,
			"metal" => 1,
			"money" => 200
		),
		"artists" => array("architect", "sculptor")
 	), 

	"OSPEDALE-CAPPELLA" => array(
		"title" => "Cappella",
		"location" => "Ospedale",
		"scorePoint" => 7,
		"cost" => array(
			"wood" => 1,
			"marble" => 2,
			"gold" => 1,
			"money" => 100
		),
		"artists" => array("sculptor", "painter")
 	),

	"OSPEDALE-CHIOSTRO" => array(
		"title" => "Chiostro",
		"location" => "Ospedale",
		"scorePoint" => 7,
		"cost" => array(
			"marble" => 4,
			"money" => 100
		),
		"artists" => array("sculptor", "architect")
 	), 	

	"PALAZZODELLASIGNORIA-FACCIATA" => array(
		"title" => "Facciata",
		"location" => "Palazzo della Signoria",
		"scorePoint" => 7,
		"cost" => array(
			"marble" => 3,
			"metal" => 1,
			"money" => 100
		),
		"artists" => array("sculptor", "architect")
 	), 	

	"PALAZZODELLASIGNORIA-SALONE" => array(
		"title" => "Salone",
		"location" => "Palazzo della Signoria",
		"scorePoint" => 9,
		"cost" => array(
			"wood" => 2,
			"metal" => 1,
			"spice" => 1,
			"fabric" => 1,
			"money" => 100
		),
		"artists" => array("painter")
 	), 	

	"PALAZZOVESCOVILE-FACCIATA" => array(
		"title" => "Facciata",
		"location" => "Palazzo Vescovile",
		"scorePoint" => 8,
		"cost" => array(
			"marble" => 1,
			"gold" => 2,
			"metal" => 1,
			"spice" => 1,
			"money" => 100
		),
		"artists" => array("architect", "sculptor")
 	), 	

	"PALAZZOVESCOVILE-SALONE" => array(
		"title" => "Salone",
		"location" => "Palazzo Vescovile",
		"scorePoint" => 7,
		"cost" => array(
			"wood" => 1,
			"gold" => 1,
			"metal" => 1,
			"fabric" => 1,
			"money" => 100
		),
		"artists" => array("painter")
 	), 	

	"PONTEVECCHIO-STRUTTURA" => array(
		"title" => "Struttura",
		"location" => "Ponte Vecchio",
		"scorePoint" => 8,
		"cost" => array(
			"wood" => 1,
			"marble" => 1,
			"money" => 250
		),
		"artists" => array("architect")
 	), 	

	"SANLORENZO-NAVATA" => array(
		"title" => "Navata",
		"location" => "San Lorenzo",
		"scorePoint" => 6,
		"cost" => array(
			"wood" => 1,
			"spice" => 1,
			"fabric" => 1,
			"money" => 100
		),
		"artists" => array("painter")
 	), 	

	"SANLORENZO-SAGRESTIA" => array(
		"title" => "Sagrestia",
		"location" => "San Lorenzo",
		"scorePoint" => 7,
		"cost" => array(
			"wood" => 1,
			"marble" => 1,
			"spice" => 1,
			"money" => 150
		),
		"artists" => array("sculptor", "painter")
 	), 	

	"SANTACROCE-CAPPELLA" => array(
		"title" => "Cappella",
		"location" => "Santa Croce",
		"scorePoint" => 8,
		"cost" => array(
			"wood" => 1,
			"metal" => 1,
			"fabric" => 1,
			"money" => 200
		),
		"artists" => array("architect", "painter")
 	), 	

	"SANTACROCE-MONUMENTOFUNEBRE" => array(
		"title" => "Monumento Funebre",
		"location" => "Santa Croce",
		"scorePoint" => 7,
		"cost" => array(
			"marble" => 2,
			"gold" => 1,
			"spice" => 1,
			"money" => 100
		),
		"artists" => array("sculptor", "painter")
 	), 	

	"SANTAMARIANOVELLA-CAPPELLA" => array(
		"title" => "Cappella",
		"location" => "Santa Maria Novella",
		"scorePoint" => 6,
		"cost" => array(
			"gold" => 1,
			"spice" => 1,
			"fabric" => 1,
			"money" => 100
		),
		"artists" => array("sculptor", "painter")
 	), 	

	"SANTAMARIANOVELLA-CROCIFISSO" => array(
		"title" => "Crocifisso",
		"location" => "Santa Maria Novella",
		"scorePoint" => 6,
		"cost" => array(
			"wood" => 1,
			"spice" => 1,
			"money" => 150
		),
		"artists" => array("sculptor")
 	), 	

	"SANTAMARIANOVELLA-FACCIATA" => array(
		"title" => "Facciata",
		"location" => "Santa Maria Novella",
		"scorePoint" => 8,
		"cost" => array(
			"marble" => 2,
			"wood" => 1,
			"metal" => 1,
			"money" => 150
		),
		"artists" => array("architect", "sculptor")
 	), 	

	"UFFIZZI-INTERNI" => array(
		"title" => "Interni",
		"location" => "Uffizi",
		"scorePoint" => 6,
		"cost" => array(
			"metal" => 1,
			"spice" => 1,
			"fabric" => 1,
			"money" => 100
		),
		"artists" => array("architect", "painter")
 	)		 	


);
