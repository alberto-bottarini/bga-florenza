<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * FlorenzaCardGame implementation : © <Your name here> <Your email address here>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * florenzacardgame.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */

require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );

require('florenzacardgame.card.php');

define("TURN_TOTAL_NUMBER", 4);
define("ROUND_TOTAL_NUMBER", 5);

define("STARTING_DRAWN_CARD", 5);

define("MARBLE_RESOURCE_AVAILABILITY", 21);
define("WOOD_RESOURCE_AVAILABILITY", 21);
define("METAL_RESOURCE_AVAILABILITY", 11);
define("SPICE_RESOURCE_AVAILABILITY", 11);
define("FABRIC_RESOURCE_AVAILABILITY", 11);
define("GOLD_RESOURCE_AVAILABILITY", 11);
define("MONEY_RESOURCE_AVAILABILITY", 4000);

define("COMPLETED_SETS_SCORE_POINT", 5);

define("MONUMENT_CARD_PER_TURN", 5);
define("MONUMENT_CARD_KEEP_ON_BOARD", 2);

class FlorenzaCardGame extends Table {

    function FlorenzaCardGame() {
        parent::__construct();
        self::initGameStateLabels(array( 
            "current_action_number" => 10,
            "current_turn_number" => 11,
            "current_round_number" => 12,
            "captain_resource_choice" => 13,
            "mercatanti_resource_choice" => 14,
            "baratto_resource_choice" => 15,
            "trade_sell_resource_choice" => 16,
            "trade_sell2_resource_choice" => 17
        ));
    }
    
    protected function getGameName() {
        return "florenzacardgame";
    }   

    protected function setupNewGame($players, $options = array()) {  
        $sql = "DELETE FROM player WHERE 1 ";
        self::DbQuery( $sql ); 

        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $default_colors = array( "ff0000", "008000", "0000ff", "ffa500" );

        //self::initStat('player', 'monuments_number', 0);
        //self::initStat('player', 'artists_number', 0);
        //self::initStat('player', 'locations_number', 0);
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, captain) VALUES ";
        $values = array();
        $captain = 1;
        foreach( $players as $player_id => $player ) {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."', $captain)";
            $captain = 0;
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reloadPlayersBasicInfos();
        
        $playerIdList = array_keys(self::loadPlayersBasicInfos());        
        
        //Set up resources for each player
        $sql = "INSERT INTO player_resources (player_id, marble, wood, metal, fabric, gold, spice, money, captain_token) VALUES ";
        $values = array();
        foreach($playerIdList as $player_id) {
            $values[] = "('".$player_id."', 1, 1, 1, 1, 1, 1, 300, 0)";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
    
        //init db    
        FlorenzaCardHelper::createFlorenzaDeck(count($players), $this->florenzaCards);
        FlorenzaCardHelper::createLocationDeck(count($players), $this->locationCards);
        FlorenzaCardHelper::createArtistDeck($this->artistCards);
        FlorenzaCardHelper::createMonumentDeck($this->monumentsCards);
        
        //refill game deck
        $sql = "SELECT card_id AS id FROM florenza_card WHERE round = 1";
        $cards = self::getObjectListFromDB($sql);
        shuffle($cards);
        foreach($cards as $index => $card) {
            $sql = "UPDATE florenza_card SET card_order = $index, location = 'deck' WHERE card_id = " . $card['id'];
            self::DbQuery( $sql );
        }

        //setup monument deck
        $startingMonumentCard = MONUMENT_CARD_PER_TURN + MONUMENT_CARD_KEEP_ON_BOARD;
        $sql = "SELECT card_id AS id FROM monument_card";
        $cards = self::getObjectListFromDB($sql);
        shuffle($cards);
        foreach($cards as $index => $card) {
            if($index >= $startingMonumentCard) {
                $sql = "UPDATE monument_card SET card_order = $index, location = 'deck' WHERE card_id = " . $card['id'];
            } else {
                $sql = "UPDATE monument_card SET card_order = $index, location = 'board' WHERE card_id = " . $card['id'];
            }
            self::DbQuery( $sql );
        }
        
        //setup artist deck
        define("ARTIST_CARD_PER_ROUND", 2 + 2 * count($playerIdList));
        
        $sql = "SELECT card_id AS id FROM artist_card WHERE anonymous = 0";
        $cards = self::getObjectListFromDB($sql);
        shuffle($cards);
        foreach($cards as $index => $card) {
            if($index >= ARTIST_CARD_PER_ROUND) {
                $sql = "UPDATE artist_card SET card_order = $index, location = 'deck' WHERE card_id = " . $card['id'];
            } else {
                $sql = "UPDATE artist_card SET card_order = $index, location = 'board' WHERE card_id = " . $card['id'];
            }
            self::DbQuery( $sql );
        }
        self::DbQuery( "UPDATE artist_card SET location = 'board' WHERE anonymous = 1" );
        
        //give STARTING_DRAWN_CARD cards to every player
        foreach($playerIdList as $playerId) {
            $cards = self::getCollectionFromDB( "SELECT card_id AS id, location FROM florenza_card WHERE location = 'deck' ORDER BY card_order LIMIT 0, ".STARTING_DRAWN_CARD, true );
            $ids = implode(array_keys($cards), ",");
            self::DbQuery("UPDATE florenza_card SET location = 'hand', player_id = " . $playerId . " WHERE card_id IN (" . $ids . ")");
        }        
                
        self::setGameStateValue("current_action_number", 1);
        self::setGameStateValue("current_turn_number", 1);
        self::setGameStateValue("current_round_number", 1);
        self::setGameStateValue('mercatanti_resource_choice', -1);
        
        $this->activeNextPlayer();
        
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array( 'players' => array() );
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // TODO: Gather all information about current game situation (visible by player $current_player_id).
        $result['hand'] = $this->getFlorenzaCardList("WHERE player_id = $current_player_id AND location = 'hand'");
        
        
        self::debug("GET ALL DATAS => TOTALE CARTE = " . count($result['hand']));
        
        $players = $this->loadPlayersBasicInfos();
        $results['board'] = array();
        $results['score'] = array();
        $results['artist'] = array();
        $results['card_counter'] = array();
        foreach($players as $player) {
            $id = $player['player_id'];
            $result['board'][$id] = $this->getFlorenzaCardList("WHERE player_id = $id AND (location = 'board' OR location = 'pending')");
            $result['score'][$id] = $this->getPlayerScorePoint($id, false);
            $result['florenzaArtist'][$id] = $this->getArtistCardList("WHERE player_id = $id AND location = 'player' AND related_card_type = 'florenza'");
            $result['monument'][$id] = $this->getMonumentCardList("WHERE player_id = $id AND location = 'player'");
            $result['monumentArtist'][$id] = $this->getArtistCardList("WHERE player_id = $id AND location = 'player' AND related_card_type = 'monument'");
            $result['reservedArtist'][$id] = $this->getArtistCardList("WHERE player_id = $id AND location = 'reserved'");
            $result['reservedMonument'][$id] = $this->getMonumentCardList("WHERE player_id = $id AND location = 'reserved'");
            $result['card_counter'][$id] = $this->getPlayerHandCount($id);
        }
        
        $result['locationCard'] = $this->getLocationCardList();
        $result['artistCard'] = $this->getArtistCardList("WHERE location = 'board' AND anonymous = 0");
        $result['anonymousArtistCard'] = $this->getArtistCardList("WHERE anonymous = 1");
        $result['monumentCard'] = $this->getMonumentCardList("WHERE location = 'board' OR location = 'pending'");
        
        $sql = "SELECT * FROM player_resources WHERE player_id = $current_player_id";
        $result['resources'] = self::getObjectFromDB($sql);
        $result['resourcesAvailability'] = $this->getResourcesAvailability();
                
        $result['captainId'] = $this->getCurrentCaptainId();  
        
        $result['currentActionNumber'] = self::getGameStateValue("current_action_number");
        $result['currentTurnNumber'] = self::getGameStateValue("current_turn_number");
        $result['currentRoundNumber'] = self::getGameStateValue("current_round_number");
  
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        $turnNumber = self::getGameStateValue("current_turn_number"); //from 1 to 4
        $roundNumber = self::getGameStateValue("current_round_number"); //from 1 to 5
        return (($turnNumber) + ($roundNumber-1) * 4) * 5;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */
    
    function getCardPrototype() {
        return array(
            "cost" => array(
                "marble" => 0,
                "wood" => 0,
                "metal" => 0,
                "fabric" => 0,
                "gold" => 0,
                "spice" => 0,
                "money" => 0
            ),
            "description" => "",
            "cardsDrawn" => 0,
            "scorePoint" => 0,
            "income" => array(),
            "artists" => array()
        );
    }
    
    function getFlorenzaCardList($where) {
        $cards = array();
        $cardDBList = self::getObjectListFromDB("SELECT * FROM florenza_card $where");
        foreach($cardDBList as $cardDB) {
            $type = $cardDB['type'];
            $cards[] = $this->mergeFlorenzaCardWithPrototype($cardDB['card_id'], $this->florenzaCards[$type], $type);
        }
        return $cards;
    }
    
    function getFlorenzaCard($id) {
        $cardDB = self::getObjectFromDB("SELECT * FROM florenza_card WHERE card_id = $id");
        $type = $cardDB['type'];
        return $this->mergeFlorenzaCardWithPrototype($cardDB['card_id'], $this->florenzaCards[$type], $type);
    }
    
    function mergeFlorenzaCardWithPrototype($id, $card, $type) {
        $prototype = $this->getCardPrototype();
        $cost = array_replace($prototype['cost'], $card['cost']);
        return array_replace(array_replace($prototype, $card), array(
            "cost" => $cost,
            "id" => $id,
            "type" => $type
        ));
    }
    
    function getLocationCardList() {
        $cards = array();
        $cardDBList = self::getObjectListFromDB("SELECT * FROM location_card ORDER BY card_order");
        foreach($cardDBList as $cardDB) {
            $type = $cardDB['type'];
            $cards[] = array_merge($this->locationCards[$type], $cardDB);
        }
        return $cards;
    }
    
    function getLocationCard($id) {
        $cardDB = self::getObjectFromDB("SELECT * FROM location_card WHERE card_id = $id");
        $type = $cardDB['type'];
        return array_merge($this->locationCards[$type], $cardDB);
    }
    
    function hasPlayerCardInBoardByType($playerId, $cardType) {
        $row = self::getObjectFromDB("SELECT COUNT(*) AS count FROM florenza_card WHERE location = 'board' AND player_id = $playerId AND type = '$cardType'");
        return $row['count'] == 1;
    }

    function hasSomePlayerCardInBoardByType($cardType) {
        $row = self::getObjectFromDB("SELECT player_id FROM florenza_card WHERE location = 'board' AND type = '$cardType'");
        if(is_null($row)) return null;
        return $row['player_id'];
    }

    function hasPlayerCardInBoardByClass($playerId, $cardClass) {
        return $this->countPlayerCardInBoardByClass($playerId, $cardClass) > 0;
    }

    function countPlayerCardInBoardByClass($playerId, $cardClass) {
        $row = self::getObjectFromDB("SELECT COUNT(*) AS count FROM florenza_card WHERE location = 'board' AND player_id = $playerId AND class = '$cardClass'");
        return $row['count'];
    }

    function countPlayerArtistInBoardByClass($playerId, $cardClass) {
        $row = self::getObjectFromDB("SELECT COUNT(*) AS count FROM artist_card WHERE location = 'player' AND player_id = $playerId AND class = '$cardClass'");
        return $row['count'];
    }

    function countPlayerMonument($playerId) {
        $row = self::getObjectFromDB("SELECT COUNT(*) AS count FROM monument_card WHERE location = 'player' AND player_id = $playerId");
        return $row['count'];
    }
    
    function hasPlayerCardInHandById($playerId, $cardId) {
        $row = self::getObjectFromDB("SELECT COUNT(*) AS count FROM florenza_card WHERE location = 'hand' AND player_id = $playerId AND card_id = $cardId");
        return $row['count'] == 1;
    }

    function countPlayerCardInHand($playerId) {
        $row = self::getObjectFromDB("SELECT COUNT(*) AS count FROM florenza_card WHERE location = 'hand' AND player_id = $playerId");
        return $row['count'];
    }
    
    function countResource($playerId) {
        $row = self::getObjectFromDB("SELECT marble + wood + metal + fabric + gold + spice AS count FROM player_resources WHERE player_id = $playerId");
        return $row['count'];
    }    

    function countResourceCardInHand($playerId) {
        return $this->countResource($playerId);
    }
    
    function countMoney($playerId) {
        $row = self::getObjectFromDB("SELECT money AS count FROM player_resources WHERE player_id = $playerId");
        return $row['count'];
    }

    function countMoneyCardInHand($playerId) {
        $count = $this->countMoney($playerId);
        $total = 0;
        $total += floor($count/500);
        $count = $count % 500;
        $total += floor($count/100);
        $count = $count % 100;
        $total += floor($count/50);
        return $total;
    }
    
    function hasPlayerResource($playerId, $resource, $quantity) {
        $row = self::getObjectFromDB("SELECT $resource AS count FROM player_resources WHERE player_id = $playerId");
        return $row['count'] >= $quantity;
    }

    function hasPlayerAnyResource($playerId, $quantity) {
        $row = self::getObjectFromDB("SELECT * FROM player_resources WHERE player_id = $playerId");
        return ($row['marble'] + $row['wood'] + $row['metal'] + $row['fabric'] + $row['gold'] + $row['spice']) >= $quantity;
    }

    function getPlayerResourceList($playerId) {
        return self::getObjectFromDB("SELECT * FROM player_resources WHERE player_id = $playerId");    
    }
    
    function hasPlayerResourceList($playerId, $itemResources) {
        $playerResources = $this->getPlayerResourceList($playerId);
        return $itemResources['marble'] <= $playerResources['marble'] &&
            $itemResources['wood'] <= $playerResources['wood'] &&
            $itemResources['metal'] <= $playerResources['metal'] &&
            $itemResources['fabric'] <= $playerResources['fabric'] &&
            $itemResources['gold'] <= $playerResources['gold'] &&
            $itemResources['spice'] <= $playerResources['spice'] &&
            $itemResources['money'] <= $playerResources['money'];
    }
    
    function getCurrentCaptainId() {
        $row = self::getObjectFromDB("SELECT player_id AS id FROM player WHERE captain = 1");
        return $row['id'];
    }
    
    function getResourcesAvailability() {
        $sum = self::getObjectFromDB("SELECT SUM(marble) AS marble, SUM(wood) AS wood, SUM(metal) AS metal, SUM(fabric) AS fabric, SUM(spice) AS spice, SUM(gold) AS gold, SUM(money) AS money FROM player_resources");
        return array(
            "marble" => MARBLE_RESOURCE_AVAILABILITY - $sum['marble'],
            "wood" => WOOD_RESOURCE_AVAILABILITY - $sum['wood'],
            "metal" => METAL_RESOURCE_AVAILABILITY - $sum['metal'],
            "fabric" => FABRIC_RESOURCE_AVAILABILITY - $sum['fabric'],
            "spice" => SPICE_RESOURCE_AVAILABILITY - $sum['spice'],
            "gold" => GOLD_RESOURCE_AVAILABILITY - $sum['gold'],
            "money" => MONEY_RESOURCE_AVAILABILITY - $sum['money']
        );
    }
    
    function hasResourceAvailability($resource, $quantity) {
        $resourcesAvailability = $this->getResourcesAvailability();
        return $resourcesAvailability[$resource] >= $quantity;
    }

    function hasAnyResourceAvailability() {
        $resourcesAvailability = $this->getResourcesAvailability();
        return $resourcesAvailability['marble'] > 0 || $resourcesAvailability['wood'] > 0 || $resourcesAvailability['metal'] > 0 || $resourcesAvailability['fabric'] > 0
             || $resourcesAvailability['spice'] > 0  || $resourcesAvailability['gold'] > 0;
    }

   
    function getPlayerScorePoint($playerId, $endGame) {
        $florenzaCardList = $this->getFlorenzaCardList("WHERE player_id = $playerId AND location = 'hand'");
        $scorePoint = 0;
        foreach($florenzaCardList as $florenzaCard) {
            $scorePoint += $florenzaCard['scorePoint'];
        }
        return $scorePoint;
    }

    function getNaturalOrderedPlayerIdList() {
        $activePlayerId = self::getActivePlayerId();
        $afterPlayerId = self::getPlayerAfter($activePlayerId);
        $toReturn = array($activePlayerId);
        while($afterPlayerId != $activePlayerId) {
            $toReturn[] = $afterPlayerId;
            $afterPlayerId = self::getPlayerAfter($afterPlayerId);
        }
        return $toReturn;
    }

    function getMonumentPrototype() {
        return array(
            "cost" => array(
                "marble" => 0,
                "wood" => 0,
                "metal" => 0,
                "fabric" => 0,
                "gold" => 0,
                "spice" => 0,
                "money" => 0
            )
        );
    }

    function getMonumentCard($id) {
        $cardDB = self::getObjectFromDB("SELECT * FROM monument_card WHERE card_id = $id");
        $type = $cardDB['type'];
        return $this->mergeMonumentCard($id, $this->monumentsCards[$type], $type, $cardDB);
    }

    function getMonumentCardList($where) {
        $cards = array();
        $cardDBList = self::getObjectListFromDB("SELECT * FROM monument_card $where");
        foreach($cardDBList as $cardDB) {
            $type = $cardDB['type'];
            $cards[] = $this->mergeMonumentCard($cardDB['card_id'], $this->monumentsCards[$type], $type, $cardDB);
        }
        return $cards;
    }

    function mergeMonumentCard($id, $card, $type, $cardDB) {
        $prototype = $this->getMonumentPrototype();
        $cost = array_replace($prototype['cost'], $card['cost']);
        return array_replace($card, array(
            "cost" => $cost,
            "id" => $id,
            "type" => $type
        ));
    }
    
    function getArtistCard($id) {
        $cardDB = self::getObjectFromDB("SELECT * FROM artist_card WHERE card_id = $id");
        $type = $cardDB['type'];
        return $this->mergeArtistCard($id, $this->artistCards[$type], $type, $cardDB);
    }

    function getArtistCardList($where) {
        $cards = array();
        $cardDBList = self::getObjectListFromDB("SELECT * FROM artist_card $where");
        foreach($cardDBList as $cardDB) {
            $type = $cardDB['type'];
            $cards[] = $this->mergeArtistCard($cardDB['card_id'], $this->artistCards[$type], $type, $cardDB);
        }
        return $cards;
    }
    
    function mergeArtistCard($id, $card, $type, $cardDB) {
        return array_replace($card, array(
            "id" => $id,
            "type" => $type,
            "scorePoint" => $cardDB['score_point'],
            "relatedCardId" => $cardDB['related_card_id'],
            "relatedCardType" => $cardDB['related_card_type'],
            "anonymous" => $cardDB['anonymous'],
            "location" => $cardDB['location']
        ));
    }

    function areArtistsAvailable($requirements, $playerId, $cardCostMoney) {
        $playerResources = $this->getPlayerResourceList($playerId);
        $money = $playerResources['money'] - $cardCostMoney;
        if($this->hasPlayerCardInBoardByType($playerId, "BOTTEGADARTE")) {
            $money += 50; //available money
        }
        $class = array();
        foreach($requirements as $index => $requirement) {
            $class[] = "'$requirement'";
        }
        $class = implode(",", $class); //$class should be a string similar to " 'painter', 'architect' "
        $artistList = $this->getArtistCardList("WHERE (location = 'board' OR ( location = 'reserved' and player_id = $playerId )) AND class IN ($class)");
        foreach($artistList as $artist) {
            if($artist['cost'] <= $money) return true;
        }
        return false;
    }

    function getPlayerHandCount($playerId) {
        $hand = array();
        $hand['florenza'] = $this->countPlayerCardInHand($playerId);
        $hand['resource'] = $this->countResourceCardInHand($playerId);
        $hand['money'] = $this->countMoneyCardInHand($playerId);
        return $hand;
    }
    
    function resourceToInt($resource) {
        $resources = array("marble" => 0, "wood" => 1, "fabric" => 2, "gold" => 3, "spice" => 4, "metal" => 5);
        return $resources[$resource];
    }
    
    function intToResource($int) {
        $resources = array("marble", "wood", "fabric", "gold", "spice", "metal");
        return $resources[$int]; 
    }

    function costToString($cardCost) {
        $resources = array("marble", "wood", "fabric", "gold", "spice", "metal");
        $costString = "";
        foreach($resources as $resource) {
            $currentResourceCost = $cardCost[$resource];
            for($i = 0; $i < $currentResourceCost; $i++) {
                $costString .= "<span class=\"resource-small resource-small-${resource}\"></span> ";
            }
        }
        if($cardCost['money'] > 0) {
            $costString .= $cardCost['money'] . " <span class=\"resource-small resource-small-money\"></span>";
        }
        return $costString;        
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 
 
    function acPlayCard($cardId) {
        self::checkAction('playCard');
        $currentPlayerId = self::getActivePlayerId();
        if(!$this->hasPlayerCardInHandById($currentPlayerId, $cardId)) {
            throw new BgaVisibleSystemException("User $currentPlayerId are trying to play a card he does not own in his hand");
        }
        
        $card = $this->getFlorenzaCard($cardId);
        $cardCost = $card['cost'];
        $cardType = $card['type'];
        $scorePoint = $card['scorePoint'];
        
        if($this->hasPlayerCardInBoardByType($currentPlayerId, $cardType)) {
            throw new BgaUserException(self::_("You already have this kind of card in the board")); 
        }

        if($card['class'] == "PREDICATORE" && $this->hasPlayerCardInBoardByClass($currentPlayerId, "PREDICATORE")) {
            throw new BgaUserException(self::_("You already have this kind of card in the board"));
        }
                
        $hasResource = $this->hasPlayerResourceList($currentPlayerId, $cardCost);       
        if(!$hasResource) {
            throw new BgaUserException(self::_("You have not enough resources to play this card")); 
        }

        $needsArtist = false;
        if(count($card['artists']) > 0) {
            $artistAvailable = $this->areArtistsAvailable($card['artists'], $currentPlayerId, $cardCost['money']);
            if(!$artistAvailable) {
                throw new BgaUserException(self::_("There are no available artists")); 
            } else {
                $needsArtist = true;
            }
        }
        
        if($needsArtist) {

            self::DbQuery("UPDATE florenza_card SET location = 'pending' WHERE card_id = $cardId");

            self::notifyAllPlayers('cardPlayed', '', array(
                'card' => $card
            ));

            self::notifyAllPlayers('message', clienttranslate('${player_name} start building ${card_title}. He must now choose an artist!'), array(
                'card_title' => $card['titleTr'],
                'player_name' => self::getActivePlayerName()
            ));            

            $this->gamestate->nextState('actionArtist');

        } else {

            self::DbQuery("UPDATE florenza_card SET location = 'board' WHERE card_id = $cardId");
            self::DbQuery("UPDATE player_resources SET marble = marble - ".$cardCost['marble'].", wood = wood - ".$cardCost['wood'].", metal = metal - ".$cardCost['metal'].", fabric = fabric - ".$cardCost['fabric'].", gold = gold - ".$cardCost['gold'].", spice = spice - ".$cardCost['spice'].", money = money - ".$cardCost['money'] . " WHERE player_id = $currentPlayerId");
            
            self::notifyAllPlayers('cardPlayed', '', array(
                'card' => $card
            ));
                    
            self::notifyAllPlayers('resourcesPaid', '', array(
                'resources' => $cardCost,
                'card_counter' => $this->getPlayerHandCount($currentPlayerId)
            ));
            
            if($scorePoint > 0 && !$needsArtist) {
                self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $currentPlayerId");

                self::notifyAllPlayers('scorePointAcquired', '', array(
                    'score_point' => $scorePoint
                ));

                self::notifyAllPlayers('message', clienttranslate('${player_name} pays ${cost}, plays ${card_title} and gets ${score_point} score point'), array(
                    'card_title' => $card['titleTr'],
                    'player_name' => self::getActivePlayerName(),
                    'cost' => $this->costToString($cardCost),
                    'score_point' => $scorePoint
                ));
            } else {

                self::notifyAllPlayers('message', clienttranslate('${player_name} pays ${cost} and plays ${card_title}'), array(
                    'card_title' => $card['titleTr'],
                    'player_name' => self::getActivePlayerName(),
                    'cost' => $this->costToString($cardCost)                  
                )); 

                /*

                self::notifyAllPlayers('message', clienttranslate('${player_name} pays ${marble} marble, ${wood} wood, ${metal} metal, ${fabric} fabric, ${gold} gold, ${spice} spice, ${money} money and plays ${card_title}'), array(
                    'card_title' => $card['titleTr'],
                    'player_name' => self::getActivePlayerName(),
                    'marble' => $cardCost['marble'],
                    'wood' => $cardCost['wood'],
                    'metal' => $cardCost['metal'],
                    'fabric' => $cardCost['fabric'],
                    'gold' => $cardCost['gold'],
                    'spice' => $cardCost['spice'],
                    'money' => $cardCost['money']
                ));   

                */

            }
        
            $this->gamestate->nextState('actionEnd');
        }
    }

    function acPlayMonumentCard($cardId) {
        self::checkAction('playMonumentCard');
        $currentPlayerId = self::getActivePlayerId();
        $card = $this->getMonumentCard($cardId);

        $cardCost = $card['cost'];
        $scorePoint = $card['scorePoint'];

        if($this->hasPlayerCardInBoardByType($currentPlayerId, "EDIFICATORE")) {
            $cardCost['money'] = max($cardCost['money'] - 50, 0);
        }

        $hasResource = $this->hasPlayerResourceList($currentPlayerId, $cardCost);       
        if(!$hasResource) {
            throw new BgaUserException(self::_("You have not enough resources to build this monument")); 
        }

        $artistAvailable = $this->areArtistsAvailable($card['artists'], $currentPlayerId, $cardCost['money']);
        if(!$artistAvailable) {
            throw new BgaUserException(self::_("There are no available artists")); 
        }       

        self::DbQuery("UPDATE monument_card SET location = 'pending', player_id = $currentPlayerId WHERE card_id = $cardId");

        self::notifyAllPlayers('monumentPlayed', '', array(
            'card' => $card
        ));

        self::notifyAllPlayers('message', clienttranslate('${player_name} start building ${card_title}. He must now choose an artist!'), array(
            'player_name' => self::getActivePlayerName(),
            'card_title' => $card['location'] . " " . $card['title']
        ));

        self::incStat(1, 'monuments_number', $currentPlayerId);

        $this->gamestate->nextState('actionArtist');            
        
    }

    function _playArtistCardOnFlorenzaCard($artistCard, $florenzaCard) {
        $cardId = $artistCard['id'];
        $requiredArtists = $florenzaCard['artists'];
        $currentPlayerId = self::getActivePlayerId();
        if(!in_array(strtolower($artistCard['class']), $requiredArtists)) {
            throw new BgaUserException("You cannot hire this artist. He does not match your requirements");
        }

        $florenzaCardId = $florenzaCard['id'];
        $cardCost = $florenzaCard['cost'];
        $cardCost['money'] += $artistCard['cost'];
        $scorePoint = $florenzaCard['scorePoint'] + $artistCard['scorePoint'];
        if($this->hasPlayerCardInBoardByType($currentPlayerId, "BOTTEGADARTE")) {
            $cardCost['money'] = max($cardCost['money'] - 50, 0);
        }
        $hasResource = $this->hasPlayerResourceList($currentPlayerId, $cardCost);       
        if(!$hasResource) {
            throw new BgaUserException(self::_("You have not enough resources to play these cards")); 
        }

        if($artistCard['anonymous']) {
            if($artistCard['location'] == 'tapped') {
                throw new BgaUserException(self::_("You cannot hire this artist. He has already worked this round")); 
            }
            self::DbQuery("UPDATE artist_card SET location = 'tapped' WHERE card_id = $cardId");
        } else {
            self::DbQuery("UPDATE artist_card SET location = 'player', player_id = $currentPlayerId, related_card_id = $florenzaCardId, related_card_type = 'florenza' WHERE card_id = $cardId");
        }
        self::DbQuery("UPDATE florenza_card SET location = 'board' WHERE card_id = $florenzaCardId");
        self::DbQuery("UPDATE player_resources SET marble = marble - ".$cardCost['marble'].", wood = wood - ".$cardCost['wood'].", metal = metal - ".$cardCost['metal'].", fabric = fabric - ".$cardCost['fabric'].", gold = gold - ".$cardCost['gold'].", spice = spice - ".$cardCost['spice'].", money = money - ".$cardCost['money'] . " WHERE player_id = $currentPlayerId");

        return array($cardCost, $scorePoint);
    }

    function _playArtistCardOnMonumentCard($artistCard, $monumentCard) {
        $cardId = $artistCard['id'];
        $requiredArtists = $monumentCard['artists'];
        $currentPlayerId = self::getActivePlayerId();
        if(!in_array(strtolower($artistCard['class']), $requiredArtists)) {
            throw new BgaUserException("You cannot hire this artist. He does not match your requirements");
        }

        $monumentCardId = $monumentCard['id'];
        $cardCost = $monumentCard['cost'];
        $cardCost['money'] += $artistCard['cost'];

        if($this->hasPlayerCardInBoardByType($currentPlayerId, "EDIFICATORE")) {
            $cardCost['money'] = max($cardCost['money'] - 50, 0);
        }
        if($this->hasPlayerCardInBoardByType($currentPlayerId, "BOTTEGADARTE")) {
            $cardCost['money'] = max($cardCost['money'] - 50, 0);
        }

        $scorePoint = $monumentCard['scorePoint'] + $artistCard['scorePoint'];

        $hasResource = $this->hasPlayerResourceList($currentPlayerId, $cardCost);       
        if(!$hasResource) {
            throw new BgaUserException(self::_("You have not enough resources to play these cards")); 
        }

        if($artistCard['anonymous']) {
            if($artistCard['location'] == 'tapped') {
                throw new BgaUserException(self::_("You cannot hire this artist. He has already worked this round")); 
            }
            self::DbQuery("UPDATE artist_card SET location = 'tapped' WHERE card_id = $cardId");
        } else {
            self::DbQuery("UPDATE artist_card SET location = 'player', player_id = $currentPlayerId, related_card_id = $monumentCardId, related_card_type = 'monument' WHERE card_id = $cardId");
        }

        self::DbQuery("UPDATE monument_card SET location = 'player', player_id = $currentPlayerId WHERE card_id = $monumentCardId");
        self::DbQuery("UPDATE player_resources SET marble = marble - ".$cardCost['marble'].", wood = wood - ".$cardCost['wood'].", metal = metal - ".$cardCost['metal'].", fabric = fabric - ".$cardCost['fabric'].", gold = gold - ".$cardCost['gold'].", spice = spice - ".$cardCost['spice'].", money = money - ".$cardCost['money'] . " WHERE player_id = $currentPlayerId");

        return array($cardCost, $scorePoint);
    }

    function acPlayArtistCard($cardId) {
        self::checkAction('playArtistCard');
        $currentPlayerId = self::getActivePlayerId();
        $artistCard = $this->getArtistCard($cardId);
        $florenzaCardList = $this->getFlorenzaCardList("WHERE location = 'pending' and player_id = $currentPlayerId");
        if(count($florenzaCardList) != 1) {
            $monumentCardList = $this->getMonumentCardList("WHERE location = 'pending' and player_id = $currentPlayerId");
            if(count($monumentCardList) != 1) {
                throw new BgaVisibleSystemException("User $currentPlayerId are trying to play an artist card buy he does not have pending card");
            } else {
                list($cardCost, $scorePoint) = self::_playArtistCardOnMonumentCard($artistCard, $monumentCardList[0]);
            }
        } else {
            list($cardCost, $scorePoint) = self::_playArtistCardOnFlorenzaCard($artistCard, $florenzaCardList[0]);
        }

        self::notifyAllPlayers('artistCardPlayed', '', array(
            'card' => $this->getArtistCard($cardId) //ricarico i dati da db
        ));
                
        self::notifyAllPlayers('resourcesPaid', '', array(
            'resources' => $cardCost,
            'card_counter' => $this->getPlayerHandCount($currentPlayerId)
        ));
        
        if($scorePoint > 0) {
            self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $currentPlayerId");
            self::DbQuery("UPDATE player SET player_score_aux = $scorePoint WHERE player_id = $currentPlayerId AND player_score_aux < $scorePoint");

            self::notifyAllPlayers('scorePointAcquired', '', array(
                'score_point' => $scorePoint
            ));

            self::notifyAllPlayers('message', clienttranslate('${player_name} pays ${cost}, plays ${card_title} and gets ${score_point} score point'), array(
                'player_name' => self::getActivePlayerName(),
                'cost' => $this->costToString($cardCost),
                'score_point' => $scorePoint,
                'card_title' => $artistCard['title'],
            ));

        } else {
            self::notifyAllPlayers('message', clienttranslate('${player_name} pays ${cost} and plays ${card_title}'), array(
                'player_name' => self::getActivePlayerName(),
                'cost' => $this->costToString($cardCost),
                'card_title' => $artistCard['title'],
            ));
        }

        self::incStat(1, 'artists_number', $currentPlayerId);
    
        $this->gamestate->nextState('actionEnd');

    }
    
    function acPlayLocationCard($cardId) {
        self::checkAction('playCard');
        $currentPlayerId = self::getActivePlayerId();
        $card = $this->getLocationCard($cardId);
        if($card['tapped'] == 1) {
            throw new BgaUserException (self::_("Card is already used: you cannot use it")); 
        }
        
        $actionEnd = true;
        $type = $card['type'];
        
        $acquiredResourceType = explode(" ", "PICCONIERE BOSCAIUOLO FABBRO MONTEPIETA LANAIUOLO ERBOLAIO");
        $soldResourceType = explode(" ", "SCARPELLINO LEGNAIUOLO OTTONAIO ORAFO TINTORE SPEZIALE");
        $resourceList = explode(" ","marble wood metal gold fabric spice");
        
        $acquiredResourceIndex = array_search($type, $acquiredResourceType);
        $soldResourceIndex = array_search($type, $soldResourceType);
        
        if($acquiredResourceIndex !== false) {
            $resource = $resourceList[$acquiredResourceIndex];
            
            if(!$this->hasResourceAvailability($resource, 1)) {
                throw new BgaUserException(self::_("There are not resource availability to play this card"));
            }
            
            self::DbQuery("UPDATE player_resources SET $resource = $resource + 1 WHERE player_id = $currentPlayerId");
        
            self::notifyAllPlayers("actionCardPlayed", "", array(
                'card' => $card
            ));
        
            self::notifyAllPlayers("resourcesAcquired", "", array(
                'player_id' => $currentPlayerId,
                'resources' => array($resource => 1),
                'card_counter' => $this->getPlayerHandCount($currentPlayerId)
            ));

            self::notifyAllPlayers("message", clienttranslate('${player_name} plays ${card_name} and gets 1 ${resource}'), array(
                'player_name' => self::getActivePlayerName(),
                'card_name' => $card['titleTr'],
                'resource' => $resource
            ));
            
        } else if($soldResourceIndex !== false) {
            $resource = $resourceList[$soldResourceIndex];
                        
            if(!$this->hasPlayerResource($currentPlayerId, $resource, 1)) {
                throw new BgaUserException(self::_("You have not enough resources to play this card"));
            }
            
            if(!$this->hasResourceAvailability("money", 200)) {
                throw new BgaUserException(self::_("There are not money availability to play this card"));
            }
            
            self::DbQuery("UPDATE player_resources SET $resource = $resource - 1, money = money + 200 WHERE player_id = $currentPlayerId");
        
            self::notifyAllPlayers("actionCardPlayed", "", array(
                'card' => $card
            ));
        
            self::notifyAllPlayers("resourcesSwapped", "", array(
                'player_id' => self::getActivePlayerId(),
                'incomingResources' => array("money" => 200),
                'outcomingResources' => array($resource => 1),
                'card_counter' => $this->getPlayerHandCount($currentPlayerId)
            ));

            self::notifyAllPlayers("message", clienttranslate('${player_name} plays ${card_name}, sells 1 ${resource} and gets 200 money'), array(
                'player_name' => self::getActivePlayerName(),
                'card_name' => $card['titleTr'],
                'resource' => $resource
            ));
        
        } else if($type == "CAPITANO") {
            self::DbQuery("UPDATE player_resources SET captain_token = 1 WHERE player_id = $currentPlayerId");
            
            self::notifyAllPlayers("actionCardPlayed", "", array(
                'card' => $card
            ));
            
            self::notifyAllPlayers("captainTokenAcquired", "", array(
            ));

            self::notifyAllPlayers("message", clienttranslate('${player_name} plays ${card_name}: will be the first player in the next round'), array(
                'player_name' => self::getActivePlayerName(),
                'card_name' => $card['titleTr']
            ));

        } else if($type == "BARATTO") {

            if(!$this->hasPlayerAnyResource($currentPlayerId, 1)) {
                throw new BgaUserException(self::_("You have not enough resources to play this card"));
            }
        
            self::notifyAllPlayers("actionCardPlayed", "", array(
                'card' => $card
            ));

            self::notifyAllPlayers("message", clienttranslate('${player_name} plays ${card_name}'), array(
                'player_name' => self::getActivePlayerName(),
                'card_name' => $card['titleTr']
            ));
            
            $this->gamestate->nextState('actionBaratto');
            $actionEnd = false;
        }
        
        self::DbQuery("UPDATE location_card SET tapped = 1 WHERE card_id = $cardId");

        self::incStat(1, 'locations_number', $currentPlayerId);
        
        if($actionEnd) $this->gamestate->nextState('actionEnd');
    }
    
    function acConfirmBaratto($outResource) {
        self::checkAction('confirmBaratto');
        $currentPlayerId = self::getActivePlayerId();
                
        if(!$this->hasPlayerResource($currentPlayerId, $outResource, 1)) {
            throw new BgaUserException(self::_("You cannot exchange resources you do not have"));
        }

        self::setGameStateValue('baratto_resource_choice', $this->resourceToInt($outResource));
        
        $this->gamestate->nextState('actionBaratto2');
        
    }

    function acConfirmBaratto2($inResource) {
        self::checkAction('confirmBaratto2');
        $currentPlayerId = self::getActivePlayerId();

        if(!$this->hasResourceAvailability($inResource, 1)) {
            throw new BgaUserException(self::_("There are not resource availability to play this card"));
        }

        $outResource = $this->intToResource(self::getGameStateValue('baratto_resource_choice'));

        self::DbQuery("UPDATE player_resources SET $inResource = $inResource + 1, $outResource = $outResource - 1 WHERE player_id = $currentPlayerId");
        
        self::notifyAllPlayers("resourcesSwapped", "", array(
            'player_id' => self::getActivePlayerId(),
            'incomingResources' => array($inResource => 1),
            'outcomingResources' => array($outResource => 1),
            'card_counter' => $this->getPlayerHandCount($currentPlayerId)
        ));

        self::notifyAllPlayers("message", clienttranslate('${player_name} swaps 1 ${out_resource} for 1 ${in_resource}'), array(
            'player_name' => self::getActivePlayerName(),
            'in_resource' => $inResource,
            'out_resource' => $outResource,
        ));

        $this->gamestate->nextState('actionEnd');

    }
    
    function acSendWorkers() {
        self::checkAction('sendWorkers');
        
        if(!$this->hasResourceAvailability("money", 50)) {
            throw new BgaUserException(self::_("There are not resource availability to play this card"));
        }
            
        $currentPlayerId = self::getActivePlayerId();
        self::DbQuery("UPDATE player_resources SET money = money + 50 WHERE player_id = $currentPlayerId");
        
        self::notifyAllPlayers("resourcesAcquired", "", array(
            'player_id' => $currentPlayerId,
            'resources' => array("money" => 50),
            'card_counter' => $this->getPlayerHandCount($currentPlayerId)
        ));

        self::notifyAllPlayers("message", clienttranslate('${player_name} sends workers out'), array(
            'player_name' => self::getActivePlayerName()
        ));
                
        $this->gamestate->nextState('actionEnd');
    }

    function acGoToMarket() {
        self::checkAction('goToMarket');
        $currentPlayerId = self::getActivePlayerId();

        $canSell = $this->hasPlayerAnyResource($currentPlayerId, 1) && $this->hasResourceAvailability("money", 100);
        $canBuy = $this->hasPlayerResource($currentPlayerId, "money", 100) && $this->hasAnyResourceAvailability();

        if(!$canSell && !$canBuy) {
            throw new BgaUserException(self::_("You have not enough resources or there are not resource availability to play this action"));
        }

        $this->gamestate->nextState('actionMarket');
    }

    function acGoToMarketSell() {
        self::checkAction('sellResourceMarket');
        $currentPlayerId = self::getActivePlayerId();

        if(!$this->hasPlayerAnyResource($currentPlayerId, 1)) {
            throw new BgaUserException(self::_("You have not enough resources to play this action"));
        }

        if(!$this->hasResourceAvailability("money", 100)) {
            throw new BgaUserException(self::_("There are not money availability to play this action"));
        }

        $this->gamestate->nextState('actionMarketSell');
    } 

    function acGoToMarketBuy() {
        self::checkAction('buyResourceMarket');
        $currentPlayerId = self::getActivePlayerId();

        if(!$this->hasPlayerResource($currentPlayerId, "money", 100)) {
            throw new BgaUserException(self::_("You have not enough money to play this action"));
        }

        if(!$this->hasAnyResourceAvailability()) {
            throw new BgaUserException(self::_("There are not resource availability to play this action"));
        }

        $this->gamestate->nextState('actionMarketBuy');
    }  

    function acGoToMarketTrade() {
        self::checkAction('tradeResourceMarket');
        $currentPlayerId = self::getActivePlayerId();

        if(!$this->hasPlayerAnyResource($currentPlayerId, 2)) {
            throw new BgaUserException(self::_("You have not enough resources to play this action"));
        }

        if(!$this->hasAnyResourceAvailability()) {
            throw new BgaUserException(self::_("There are not resource availability to play this action"));
        }

        $this->gamestate->nextState('actionMarketTradeSell');
    }  

    function acConfirmActionMarketSell($resource) {
        self::checkAction('confirmActionMarketSell');
        $currentPlayerId = self::getActivePlayerId();
                        
        if(!$this->hasPlayerResource($currentPlayerId, $resource, 1)) {
            throw new BgaUserException(self::_("You have not enough resources to play this action"));
        }
            
        if(!$this->hasResourceAvailability("money", 100)) {
            throw new BgaUserException(self::_("There are not money availability to play this action"));
        }
            
        self::DbQuery("UPDATE player_resources SET $resource = $resource - 1, money = money + 100 WHERE player_id = $currentPlayerId");
        
        self::notifyAllPlayers("resourcesSwapped", "", array(
            'player_id' => self::getActivePlayerId(),
            'incomingResources' => array("money" => 100),
            'outcomingResources' => array($resource => 1),
            'card_counter' => $this->getPlayerHandCount($currentPlayerId)
        ));

        self::notifyAllPlayers("message", clienttranslate('${player_name} goes to the market, sells 1 ${resource} and gets 100 money'), array(
            'player_name' => self::getActivePlayerName(),
            'resource' => $resource
        ));

        $this->gamestate->nextState('actionEnd');

    }

    function acConfirmActionMarketBuy($resource) {
        self::checkAction('confirmActionMarketBuy');
        $currentPlayerId = self::getActivePlayerId();
                        
        if(!$this->hasPlayerResource($currentPlayerId, "money", 100)) {
            throw new BgaUserException(self::_("You have not enough money to play this action"));
        }
            
        if(!$this->hasResourceAvailability($resource, 1)) {
            throw new BgaUserException(self::_("There are not resource availability to play this action"));
        }
            
        self::DbQuery("UPDATE player_resources SET $resource = $resource + 1, money = money - 100 WHERE player_id = $currentPlayerId");
        
        self::notifyAllPlayers("resourcesSwapped", "", array(
            'player_id' => self::getActivePlayerId(),
            'outcomingResources' => array("money" => 100),
            'incomingResources' => array($resource => 1),
            'card_counter' => $this->getPlayerHandCount($currentPlayerId)
        ));

        self::notifyAllPlayers("message", clienttranslate('${player_name} goes to the market, buys 1 ${resource} for 100 money'), array(
            'player_name' => self::getActivePlayerName(),
            'resource' => $resource
        ));

        $this->gamestate->nextState('actionEnd');

    }

    function acConfirmActionMarketTradeSell($resource) {
        self::checkAction('confirmActionMarketTradeSell');
        $currentPlayerId = self::getActivePlayerId();

        if(!$this->hasPlayerResource($currentPlayerId, $resource, 1)) {
            throw new BgaUserException(self::_("You have not enough resources to play this action"));
        }

        self::DbQuery("UPDATE player_resources SET $resource = $resource - 1 WHERE player_id = $currentPlayerId");

        self::setGameStateValue('trade_sell_resource_choice', $this->resourceToInt($resource));

        $this->gamestate->nextState('actionMarketTradeSell2');
    }

    function acConfirmActionMarketTradeSell2($resource) {
        self::checkAction('confirmActionMarketTradeSell2');
        $currentPlayerId = self::getActivePlayerId();

        if(!$this->hasPlayerResource($currentPlayerId, $resource, 1)) {
            throw new BgaUserException(self::_("You have not enough resources to play this action"));
        }

        self::DbQuery("UPDATE player_resources SET $resource = $resource - 1 WHERE player_id = $currentPlayerId");

        self::setGameStateValue('trade_sell2_resource_choice', $this->resourceToInt($resource));

        $this->gamestate->nextState('actionMarketTradeBuy');
    }

    function acConfirmActionMarketTradeBuy($resource) {
        self::checkAction('confirmActionMarketTradeBuy');
        $currentPlayerId = self::getActivePlayerId();

        if(!$this->hasResourceAvailability($resource, 1)) {
            throw new BgaUserException(self::_("There are not resource availability to play this action"));
        }

        $soldResource1 = $this->intToResource(self::getGameStateValue('trade_sell_resource_choice'));
        $soldResource2 = $this->intToResource(self::getGameStateValue('trade_sell2_resource_choice'));

        self::DbQuery("UPDATE player_resources SET $resource = $resource + 1 WHERE player_id = $currentPlayerId");

        if($soldResource1 == $soldResource2) {

            self::notifyAllPlayers("resourcesSwapped", "", array(
                'player_id' => self::getActivePlayerId(),
                'incomingResources' => array($resource => 1),
                'outcomingResources' => array($soldResource1 => 2),
                'card_counter' => $this->getPlayerHandCount($currentPlayerId)
            ));

            self::notifyAllPlayers("message", clienttranslate('${player_name} swaps 2 ${sold_resource} for 1 ${buyed_resource}'), array(
                'player_name' => self::getActivePlayerName(),
                'sold_resource' => $soldResource1,
                'buyed_resource' => $resource,
            ));

        } else {

            self::notifyAllPlayers("resourcesSwapped", "", array(
                'player_id' => self::getActivePlayerId(),
                'incomingResources' => array($resource => 1),
                'outcomingResources' => array($soldResource1 => 1, $soldResource2 => 1),
                'card_counter' => $this->getPlayerHandCount($currentPlayerId)
            ));

            self::notifyAllPlayers("message", clienttranslate('${player_name} swaps 1 ${sold_resource} and 1 ${sold2_resource} for 1 ${buyed_resource}'), array(
                'player_name' => self::getActivePlayerName(),
                'sold_resource' => $soldResource1,
                'sold2_resource' => $soldResource2,
                'buyed_resource' => $resource,
            ));            

        }

        $this->gamestate->nextState('actionEnd');
    }
    
    function acTakeInspiration() {
        self::checkAction('takeInspiration');

        $currentPlayerId = self::getActivePlayerId();

        $drawnCard = self::getUniqueValueFromDB( "SELECT card_id id, location FROM florenza_card WHERE location = 'deck' ORDER BY card_order LIMIT 0, 1");
        $cardId = $drawnCard['id'];
        self::DbQuery("UPDATE florenza_card SET location = 'hand', player_id = " . $currentPlayerId . " WHERE card_id = $cardId");

        $drawnCard = $this->getFlorenzaCard($cardId);

        self::notifyPlayer($currentPlayerId, "cardDrown", "", array(
            "card" => $drawnCard,
        ));

        self::notifyAllPlayers("cardDrownOther", "", array(
            "card" => 1,
            "player_id" => $currentPlayerId
        ));

        self::notifyAllPlayers("message", clienttranslate('${player_name} takes inspiration and draws a new card'), array(
            'player_name' => self::getActivePlayerName()
        ));


        $this->gamestate->nextState('actionEnd');
    }

    function acReserveArtistCard() {
        self::checkAction('reserveArtistCard');
        $this->gamestate->nextState('actionReserveArtist');
    }

    function acReserveMonumentCard() {
        self::checkAction('reserveMonumentCard');
        $this->gamestate->nextState('actionReserveMonument');
    }
    
    function acConfirmArtistReservation($cardId) {
        self::checkAction('confirmArtistReservation');
        $currentPlayerId = self::getActivePlayerId();
        $currentPlayerName = self::getActivePlayerName();
        $artistCard = $this->getArtistCard($cardId);

        if($artistCard['anonymous']) {
            throw new BgaUserException(self::_("You cannot reserve an anonymous artist"));
        }
        
        self::DbQuery("UPDATE artist_card SET location = 'reserved', player_id = $currentPlayerId WHERE card_id = $cardId");
        
        self::notifyAllPlayers("artistReserved", "", array(
            'card' => $artistCard
        ));

        self::notifyAllPlayers("message", clienttranslate('${player_name} reserved an artist: ${card_name}'), array(
            'player_name' => self::getActivePlayerName(),
            'card_name' => $artistCard['title']
        ));
        
        $this->gamestate->nextState('actionEnd');
    }

    function acConfirmMonumentReservation($cardId) {
        self::checkAction('confirmMonumentReservation');
        $currentPlayerId = self::getActivePlayerId();
        $currentPlayerName = self::getActivePlayerName();
        $monumentCard = $this->getMonumentCard($cardId);

        self::DbQuery("UPDATE monument_card SET location = 'reserved', player_id = $currentPlayerId WHERE card_id = $cardId");

        self::notifyAllPlayers("monumentReserved", "", array(
            'card' => $monumentCard
        ));

        self::notifyAllPlayers("message", clienttranslate('${player_name} reserved a monument: ${card_name}'), array(
            'player_name' => self::getActivePlayerName(),
            'card_name' => $monumentCard['title']
        ));
        
        $this->gamestate->nextState('actionEnd');
    }
        
    function acConfirmKeptCard($cardId) {
        self::checkAction('confirmKeptCard');
        $currentPlayerId = self::getCurrentPlayerId(); //use currentPlayerId because is a multiPlayer action
        if(!$this->hasPlayerCardInHandById($currentPlayerId, $cardId)) {
            throw new BgaVisibleSystemException("User $currentPlayerId are trying to keep a card he does not own in his hand");
        }
        $card = $this->getFlorenzaCard($cardId);
        
        self::DbQuery("UPDATE florenza_card SET location = 'discard', player_id = null WHERE player_id = $currentPlayerId AND card_id <> $cardId AND location = 'hand'");
        
        self::notifyPlayer($currentPlayerId, "cardKept", "", array(
            'card_id' => $cardId
        ));

        self::notifyPlayer($currentPlayerId, "message", clienttranslate('You kept ${card_name}'), array(
            'card_name' => $card['titleTr'],
        ));
        
        $this->gamestate->setPlayerNonMultiactive($currentPlayerId, 'captainSet');       
    }
    
    function acConfirmResourceChoice($resource) {
        self::checkAction('confirmResourceChoice');
        
        self::setGameStateValue('captain_resource_choice', $this->resourceToInt($resource));

        self::notifyAllPlayers("message", clienttranslate('${player_name} as captain choose ${resource}'), array(
            'player_name' => self::getActivePlayerName(),
            'resource' => $resource
        ));
        
        $this->gamestate->nextState('mercatantiSet');
    }

    function acConfirmMercatantiChoice($resource) {
        self::checkAction('confirmMercatantiChoice');
        
        self::setGameStateValue('mercatanti_resource_choice', $this->resourceToInt($resource));

        self::notifyAllPlayers("resourceChoice", clienttranslate('${player_name}, as Arte dei Mercatanti owner, choose ${resource}'), array(
            'player_name' => self::getActivePlayerName(),
            'resource' => $resource
        ));
        
        $this->gamestate->nextState('incomeCollection');
    }
    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */
    /*
    function argsNextAction() {
        return array(
            "currentActionNumber" => self::getGameStateValue("current_action_number")
        );
    }
    
    function argsNextTurn() {
        return array(
            "currentTurnNumber" => self::getGameStateValue("current_turn_number")
        );
    }
    
    function argsNextRound() {
        return array(
            "currentRoundNumber" => self::getGameStateValue("current_round_number"),
            "captainId" => $this->getCurrentCaptainId()
        );
    }
    */


//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    function stBeforeAction() {
        $this->giveExtraTime(self::getActivePlayerId());
        $this->gamestate->nextState("action");
    }
 
    function stActionEnd() {
        $currentActionNumber = self::incGameStateValue("current_action_number", 1);
        if($currentActionNumber > self::getPlayersNumber()) $this->gamestate->nextState("turnEnd");
        else $this->gamestate->nextState("nextAction");
    }
    
    function stNextAction() {
        $currentTurnNumber = self::getGameStateValue("current_turn_number", 1);
        $diff = $currentTurnNumber - TURN_TOTAL_NUMBER;
        $this->activeNextPlayer();

        $this->notifyAllPlayers("actionStarted", '', array(
            
        ));

        switch($diff) {
            
            case 1: //palagio
            case 2: //casamento
            case 3: //casa
                $extraCardTypeList = array("PALAGIO", "CASAMENTO", "CASA");
                $extraCardType = $extraCardTypeList[$diff - 1];
                if($this->hasPlayerCardInBoardByType($this->getActivePlayerId(), $extraCardType)) $this->gamestate->nextState("beforeAction");
                else $this->gamestate->nextState("actionEnd");
                break;

            case 4:
                if($this->hasPlayerCardInBoardByClass($this->getActivePlayerId(), "PREDICATORE")) $this->gamestate->nextState("beforeAction");
                else $this->gamestate->nextState("actionEnd");
                break;

            default: 
                $this->gamestate->nextState("beforeAction");
                break;

        }
    }
    
    function stTurnEnd() {
        $currentTurnNumber = self::incGameStateValue("current_turn_number", 1);
        if($currentTurnNumber > TURN_TOTAL_NUMBER + 4) $this->gamestate->nextState("discard");
        else $this->gamestate->nextState("nextTurn");
    }
    
    function stNextTurn() {
        $turnNumber = $this->getGameStateValue("current_turn_number");
        $this->notifyAllPlayers("message", clienttranslate('Turn ${turnnumber} has started!'), array(
            "turnnumber" => $turnNumber
        ));     
        self::setGameStateValue("current_action_number", 1);

        $this->notifyAllPlayers("turnStarted", '', array(
            "turnnumber" => $turnNumber
        ));

        $this->gamestate->nextState("nextAction");
    }
    
    function stRoundEnd() {
        $currentRoundNumber = self::incGameStateValue("current_round_number", 1);
        if($currentRoundNumber > ROUND_TOTAL_NUMBER) $this->gamestate->nextState("scoreCalculation");
        else $this->gamestate->nextState("nextRound");
    }

    function stCaptainSet() {
        $this->gamestate->changeActivePlayer($this->getCurrentCaptainId());
        $this->gamestate->nextState("resourceChoice");
    }

    function stMercatantiSet() {
        $mercatantiPlayer = $this->hasSomePlayerCardInBoardByType("ARTEDEIMERCATANTI");
        if(is_null($mercatantiPlayer)) $this->gamestate->nextState('incomeCollection');
        else {
            $this->gamestate->changeActivePlayer($mercatantiPlayer); 
            $this->gamestate->nextState('mercatantiChoice');
        }
    }
    
    function stDiscard() {
        $currentRoundNumber = self::getGameStateValue("current_round_number", 1);
        if($currentRoundNumber == ROUND_TOTAL_NUMBER) $this->gamestate->nextState("incomeCollection");
        else $this->gamestate->setAllPlayersMultiactive();
    }

    function stIncomeCollection() {
        $this->gamestate->changeActivePlayer($this->getCurrentCaptainId()); //give priority to previous captain
        $playerBasicInfos = self::loadPlayersBasicInfos();
        $playerIdList = array_keys($playerBasicInfos);
        foreach($this->getNaturalOrderedPlayerIdList() as $playerId) {
            self::debug("PLAYER = $playerId");
            $player = $playerBasicInfos[$playerId];
            $income = array(
                "marble" => 1,
                "wood" => 1,
                "metal" => 0,
                "fabric" => 0,
                "gold" => 0,
                "spice" => 0,
                "money" => 200
            );
            $cardList = $this->getFlorenzaCardList("WHERE player_id = $playerId AND location = 'board'");
            foreach($cardList as $card) {
                foreach($card['income'] as $resource => $quantity) {
                    self::debug("FOUND CARD ". $card['type'] . " GIVE $quantity $resource");
                    $income[$resource] = $income[$resource] + $quantity;
                }
            }
            $captainResourceChoice = $this->intToResource(self::getGameStateValue('captain_resource_choice'));
            $income[$captainResourceChoice] = $income[$captainResourceChoice] + 1;

            if($this->hasPlayerCardInBoardByType($playerId, "ARTEDEIMERCATANTI")) {
                $mercatantiResourceChoice = self::getGameStateValue('mercatanti_resource_choice');
                if($mercatantiResourceChoice != -1) {
                    $mercatantiResourceChoice = $this->intToResource($mercatantiResourceChoice);
                    $income[$mercatantiResourceChoice] = $income[$mercatantiResourceChoice] + 1;
                    self::setGameStateValue('mercatanti_resource_choice', -1);
                }
            }

            $resourcesAvailability = $this->getResourcesAvailability();
        
            foreach($income as $resource => $quantity) {
                if($quantity > 0) {
                    if($quantity <= $resourcesAvailability[$resource]) {
                        $message = clienttranslate('${player_name} gets ${quantity} ${resource}');
                    } else {
                        $quantity = $resourcesAvailability[$resource];
                        if($quantity > 0) {
                            $message = clienttranslate('${player_name} gets only ${quantity} ${resource} because there are not availability');
                        } else {
                            $message = clienttranslate('${player_name} should have get some ${resource} but he gets nothing because there is no availability');
                            $quantity = 0;
                        }
                    }
                    self::notifyAllPlayers("resourcesAcquired", '', array(
                        'player_id' => $playerId,
                        'resources' => array($resource => $quantity),
                        'card_counter' => $this->getPlayerHandCount($playerId)
                    ));

                    self::notifyAllPlayers("message", $message, array(
                        'player_name' => $player['player_name'],
                        'resource' => $resource,
                        'quantity' => $quantity
                    ));
                    $income[$resource] = $quantity;
                }
            }

            self::DbQuery("UPDATE player_resources SET marble = marble + ".$income['marble'].", wood = wood + ".$income['wood'].", metal = metal + ".$income['metal'].", fabric = fabric + ".$income['fabric'].", gold = gold + ".$income['gold'].", spice = spice + ".$income['spice'].", money = money + ".$income['money'] . " WHERE player_id = $playerId");
        }
        $this->gamestate->nextState("roundEnd");
    }
        
    function stNextRound() {
    
        $currentRoundNumber = self::getGameStateValue("current_round_number");
                
        if($currentRoundNumber == 2) {
            $sql = "SELECT * FROM florenza_card WHERE round = 2 OR ( round = 1 AND location = 'deck' )";
        } else if($currentRoundNumber == 3) {
            $sql = "SELECT * FROM florenza_card WHERE round = 3 OR ( round = 2 AND location = 'deck' ) OR ( round = 1 AND location = 'deck' )";
        } else if($currentRoundNumber >= 4) {
            $sql = "SELECT * FROM florenza_card WHERE location = 'deck' OR location = 'discard'";
        }
        $cards = self::getObjectListFromDB( $sql );
        shuffle($cards);
        foreach($cards as $index => $card) {        
            $sql = "UPDATE florenza_card SET card_order = $index, location = 'deck' WHERE card_id = " . $card['card_id'];
            self::DbQuery( $sql );
        }
               
        //untap all location
        self::DbQuery("UPDATE location_card SET tapped = 0"); 

        //untap anonymous artist
        self::DbQuery("UPDATE artist_card SET location = 'board' WHERE anonymous = 1");         
        
        //calculate next player
        $captainTokenPlayerId = self::getObjectFromDB("SELECT player_id FROM player_resources WHERE captain_token = 1");
        if($captainTokenPlayerId) {
            self::debug("Player $captainTokenPlayerId has the captain token. He will start next round");
            self::DbQuery("UPDATE player_resources SET captain_token = 0");
            $nextCaptainId = $captainTokenPlayerId['player_id'];
        } else {
            self::debug("No one has the captain token. The player next to the old captain will start next round");
            $nextCaptainId = $this->getPlayerAfter($this->getActivePlayerId());
        }
        $this->gamestate->changeActivePlayer($nextCaptainId);
        self::DbQuery("UPDATE player SET captain = 0");
        self::DbQuery("UPDATE player SET captain = 1 WHERE player_id = $nextCaptainId");
        $captain = $this->loadPlayersBasicInfos();
        $captain = $captain[$nextCaptainId];
        self::notifyAllPlayers("message", clienttranslate('${captainname} is the new captain'), array(
            "captainname" => $captain['player_name']
        )); 

        //give 4+ cards to each player
        foreach($this->getNaturalOrderedPlayerIdList() as $playerId) {
            $cardNumber = 4;
            if($this->hasPlayerCardInBoardByType($playerId, "PALAGIO")) $cardNumber++;
            if($this->hasPlayerCardInBoardByType($playerId, "CASAMENTO")) $cardNumber++;
            if($this->hasPlayerCardInBoardByType($playerId, "CASA")) $cardNumber++;
            $cards = self::getCollectionFromDB( "SELECT card_id id, location FROM florenza_card WHERE location = 'deck' ORDER BY card_order LIMIT 0, $cardNumber", true );
            $ids = implode(array_keys($cards), ",");
            self::DbQuery("UPDATE florenza_card SET location = 'hand', player_id = " . $playerId . " WHERE card_id IN (" . $ids . ")");
            foreach($cards as $cardId => $cardLocation) {
                $card = $this->getFlorenzaCard($cardId);

                self::notifyPlayer($playerId, "cardDrown", "", array(
                    "card" => $card,
                ));     
            }

            self::notifyPlayer($playerId, "message", clienttranslate('You draw ${count} new cards'), array(
                "count" => count($cards),
            )); 
        }

        //refill artist
        $artistCardCount = 2 + 2 * self::getPlayersNumber();
        $artistCards = $this->getArtistCardList( "WHERE location = 'deck' ORDER BY card_order LIMIT 0, $artistCardCount");
        $ids = array();
        foreach($artistCards as $artistCard) {
            $ids[] = $artistCard['id'];
        }
        $ids = implode($ids, ",");
        self::DbQuery("UPDATE artist_card SET location = 'discard' WHERE location = 'board'");
        self::DbQuery("UPDATE artist_card SET location = 'board' WHERE card_id IN (" . $ids . ")");
        self::DbQuery("UPDATE artist_card SET location = 'board' WHERE anonymous = 1" );
        self::notifyAllPlayers("artistsRefilled", "", array(
            "artistCards" => $artistCards,
            "anonymousArtistCard" => $this->getArtistCardList("WHERE anonymous = 1")
        ));
        self::notifyAllPlayers("message", clienttranslate('${number} new artists come in play'), array(
            "number" => count($artistCards)
        ));

        //refill monuments
        $toKeepMonuments = $this->getMonumentCardList("WHERE location = 'board' ORDER BY score_point DESC LIMIT 0, 2");
        $newMonuments = $this->getMonumentCardList("WHERE location = 'deck' ORDER BY card_order ASC LIMIT 0, 5");
        $monumentsCard = array_merge($toKeepMonuments, $newMonuments);
        $ids = array();
        foreach($monumentsCard as $monumentCard) {
            $ids[] = $monumentCard['id'];
        }
        $ids = implode($ids, ",");
        self::DbQuery("UPDATE monument_card SET location = 'discard' WHERE location = 'board'");
        self::DbQuery("UPDATE monument_card SET location = 'board' WHERE card_id IN (" . $ids . ")");
        self::notifyAllPlayers("monumentsRefilled", "", array(
            "monumentCards" => $monumentsCard
        ));
        self::notifyAllPlayers("message", clienttranslate('${number} new monuments come in play'), array(
            "number" => count($newMonuments)
        ));

        $this->notifyAllPlayers("message", clienttranslate('Round ${roundnumber} has started!'), array(
            "roundnumber" => $currentRoundNumber
        ));

        $this->notifyAllPlayers("roundStarted", '', array(
            "roundnumber"   => $currentRoundNumber,
            "captainid"     => $nextCaptainId
        ));        

        //reset counters and play!
        self::setGameStateValue("current_turn_number", 1);
        self::setGameStateValue("current_action_number", 1);
        $this->gamestate->nextState("beforeAction");
    }

    function stScoreCalculation() {
        $players = $this->loadPlayersBasicInfos();
        $resourceGlobalCount = array();
        $moneyGlobalCount = array();
        foreach($players as $player) {        
            $playerId = $player['player_id'];
            $playerName = $player['player_name'];
            
            //checking for sets of private monuments
            $chiesaCount = $this->countPlayerCardInBoardByClass($playerId, "CHIESARIONE");
            $edificioCount = $this->countPlayerCardInBoardByClass($playerId, "EDIFICIORIONE");
            $palazzoCount = $this->countPlayerCardInBoardByClass($playerId, "PALAZZOFAMIGLIA");
            $completedSet = min($chiesaCount, $edificioCount, $palazzoCount);

            if($completedSet > 0) {
                $scorePointEach = COMPLETED_SETS_SCORE_POINT;
                if($this->hasPlayerCardInBoardByType($playerId, "SIGNOREDELORIONE")) $scorePointEach += 2;
                $scorePoint = $completedSet * $scorePointEach;
                self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $playerId");
                self::notifyAllPlayers('scorePointAcquired', "", array(
                    "player_id" => $playerId,
                    "score_point" => $scorePoint
                ));
                self::notifyAllPlayers('message', clienttranslate('${player_name} gets ${score_point} score point (${sets} completed sets)'), array(
                    "player_name" => $playerName,
                    "sets" => $completedSet,
                    "score_point" => $scorePoint
                ));
            }
            
            //checking for majority of money and resources - step 1
            $resourceCount = $this->countResource($playerId);
            $moneyCount = $this->countMoney($playerId);
            if(!array_key_exists($resourceCount, $resourceGlobalCount)) $resourceGlobalCount[$resourceCount] = array();
            $resourceGlobalCount[$resourceCount][] = array("playerId" => $playerId, "playerName" => $playerName, "count" => $resourceCount);
            if(!array_key_exists($moneyCount, $moneyGlobalCount)) $moneyGlobalCount[$moneyCount] = array();
            $moneyGlobalCount[$moneyCount][] = array("playerId" => $playerId, "playerName" => $playerName, "count" => $moneyCount);
            
            //checking for ARTE MAGGIORE
            $scorePointEachBottega = 3;
            $hasArteMaggiore = $this->hasPlayerCardInBoardByType($playerId, "ARTEMAGGIORE"); 
            if($hasArteMaggiore) {
            	$bottegheCount = $this->countPlayerCardInBoardByClass($playerId, "BOTTEGA");
				$scorePoint = $bottegheCount * $scorePointEachBottega;
				self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $playerId");
				self::notifyAllPlayers('scorePointAcquired', "", array(
                    "player_id" => $playerId,
                    "score_point" => $scorePoint
                ));
                self::notifyAllPlayers('message', clienttranslate('${player_name} gets ${score_point} score point (arte maggiore + ${count} workshops)'), array(
                    "player_name" => $playerName,
                    "count" => $bottegheCount,
                    "score_point" => $scorePoint
                ));

            }
            
            //checking for PADRONE
            $scorePointAbitazione = 3;
            $hasPadrone = $this->hasPlayerCardInBoardByType($playerId, "PADRONE");
            if($hasPadrone) {
            	$abitazioneCount = $this->countPlayerCardInBoardByClass($playerId, "ABITAZIONE");
				$scorePoint = $abitazioneCount * $scorePointAbitazione;
				self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $playerId");
				self::notifyAllPlayers('scorePointAcquired', "", array(
                    "player_id" => $playerId,
                    "score_point" => $scorePoint
                ));
                self::notifyAllPlayers('message', clienttranslate('${player_name} gets ${score_point} score point (padrone + ${count} residences)'), array(
                    "player_name" => $playerName,
                    "count" => $abitazioneCount,
                    "score_point" => $scorePoint
                ));
            }

            //checking for MASTROARTISTA
            $hasMastroArtista = $this->hasPlayerCardInBoardByType($playerId, "MASTROARTISTA");
            if($hasMastroArtista) {
                $architectCount = $this->countPlayerArtistInBoardByClass($playerId, "ARCHITECT");
                $sculptorCount = $this->countPlayerArtistInBoardByClass($playerId, "SCULPTOR");
                $painterCount = $this->countPlayerArtistInBoardByClass($playerId, "PAINTER");
                $architectSet = min($architectCount, $sculptorCount, $painterCount);
                if($architectSet > 0) {
                    $scorePoint = $architectSet * 5;
                    self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $playerId");
                    self::notifyAllPlayers('scorePointAcquired', "", array(
                        "player_id" => $playerId,
                        "score_point" => $scorePoint
                    ));
                    self::notifyAllPlayers('message', clienttranslate('${player_name} gets ${score_point} score point because of Mastro Artista (${count} artist sets)'), array(
                        "player_name" => $playerName,
                        "count" => $architectSet,
                        "score_point" => $scorePoint
                    ));
                }
            }

            //checking for PROTETTORE
            $hasProtettore = $this->hasPlayerCardInBoardByType($playerId, "PROTETTORE");
            if($hasProtettore) {
                $monumentCount = $this->countPlayerMonument($playerId);
                if($monumentCount > 0) {
                    $scorePoint = $monumentCount * 3;
                    self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $playerId");
                    self::notifyAllPlayers('scorePointAcquired', "", array(
                        "player_id" => $playerId,
                        "score_point" => $scorePoint
                    ));
                    self::notifyAllPlayers('message', clienttranslate('${player_name} gets ${score_point} score point because of Protettore (${count} monuments built)'), array(
                        "player_name" => $playerName,
                        "count" => $monumentCount,
                        "score_point" => $scorePoint
                    ));
                }
            }

            //removing reserved artist 
            $reservedArtistList = $this->getArtistCardList("WHERE player_id = $playerId AND location = 'reserved'");
            $toRemoveScorePoint = 0;
            foreach($reservedArtistList as $reservedArtist) {
                $toRemoveScorePoint += $reservedArtist['scorePoint'];
            }
            if($toRemoveScorePoint > 0) {
                self::DbQuery("UPDATE player SET player_score = player_score - $toRemoveScorePoint WHERE player_id = $playerId");
                self::notifyAllPlayers('scorePointAcquired', "", array(
                    "player_id" => $playerId,
                    "score_point" => -($toRemoveScorePoint)
                ));
                self::notifyAllPlayers('message', clienttranslate('${player_name} loses ${score_point} score point for reserved (and not played) artists'), array(
                    "player_name" => $playerName,
                    "score_point" => $toRemoveScorePoint
                ));
            }

            //removing reserved monument 
            $reservedMonumentList = $this->getMonumentCardList("WHERE player_id = $playerId AND location = 'reserved'");
            $toRemoveScorePoint = 0;
            foreach($reservedMonumentList as $reservedMonument) {
                $toRemoveScorePoint += $reservedMonument['scorePoint'];
            }
            if($toRemoveScorePoint > 0) {
                self::DbQuery("UPDATE player SET player_score = player_score - $toRemoveScorePoint WHERE player_id = $playerId");
                self::notifyAllPlayers('scorePointAcquired', "", array(
                    "player_id" => $playerId,
                    "score_point" => -($toRemoveScorePoint)
                ));
                self::notifyAllPlayers('message', clienttranslate('${player_name} loses ${score_point} score point for reserved (and not played) monuments'), array(
                    "player_name" => $playerName,
                    "score_point" => $toRemoveScorePoint
                ));
            }
			             
            
        }
        
        //checking for majority of money and resources - step 2
        krsort($resourceGlobalCount);
        krsort($moneyGlobalCount);

/*
        [
            {
                $quantity: [{
                    playerId: $playerId,
                    playerName: $playerName,
                    count: $count
                }]
            }
        ]
        */
                
        for($i = 3; $i > 0;) {
            $resourceMajority = array_shift($resourceGlobalCount);
            if($resourceMajority) {
                $scorePoint = $i == 3 ? 4 : ($i == 2 ? 2 : 1);
                foreach($resourceMajority as $majority) {
                    $playerId = $majority['playerId'];
                    $playerName = $majority['playerName'];
                    $count = $majority['count'];
                    self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $playerId");
                    self::notifyAllPlayers('scorePointAcquired', "", array(
                        "player_id" => $playerId,
                        "score_point" => $scorePoint
                    ));
                    self::notifyAllPlayers('message', clienttranslate('${player_name} gets ${score_point} score point (${count} resources)'), array(
                        "player_name" => $playerName,
                        "count" => $count,
                        "score_point" => $scorePoint
                    ));

                }   
            }
            $i -= max(1, count($resourceMajority));
        }
        
        for($i = 3; $i > 0;) {
            $resourceMajority = array_shift($moneyGlobalCount);
            if($resourceMajority) {
                $scorePoint = $i == 3 ? 4 : ($i == 2 ? 2 : 1);
                foreach($resourceMajority as $majority) {
                    $playerId = $majority['playerId'];
                    $playerName = $majority['playerName'];
                    $count = $majority['count'];
                    self::DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $playerId");
                    self::notifyAllPlayers('scorePointAcquired', "", array(
                        "player_id" => $playerId,
                        "score_point" => $scorePoint
                    ));
                    self::notifyAllPlayers('message', clienttranslate('${player_name} gets ${score_point} score point (${count} money)'), array(
                        "player_name" => $playerName,
                        "count" => $count,
                        "score_point" => $scorePoint
                    ));
                }
            }
            $i -= max(1, count($resourceMajority));
        }   
        

        $this->gamestate->nextState("gameEnd");
        //$this->gamestate->nextState("fakeState");
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
    */

    function zombieTurn( $state, $active_player )
    {
        $statename = $state['name'];
        
        if ($state['type'] == "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                    break;
            }

            return;
        }

        if ($state['type'] == "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $sql = "
                UPDATE  player
                SET     player_is_multiactive = 0
                WHERE   player_id = $active_player
            ";
            self::DbQuery( $sql );

            $this->gamestate->updateMultiactiveOrNextState( '' );
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
}
