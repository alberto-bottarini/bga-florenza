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
 * states.inc.php
 *
 * FlorenzaCardGame game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => clienttranslate("Game setup"),
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 10 )
    ),

    10 => array(
      "name" => "beforeAction",
      "type" => "game",
      "action" => "stBeforeAction",
      "transitions" => array( "action" => 20 )
    ), 
        
    20 => array(
  		"name" => "action",
  		"description" => clienttranslate('${actplayer} must choose an action'),
  		"descriptionmyturn" => clienttranslate('${you} must choose an action'),
  		"type" => "activeplayer",
      //"action" => "stGameAction",
  		"possibleactions" => array( "playCard", "playLocationCard", "playMonumentCard", "sendWorkers", "reserveArtistCard", "reserveMonumentCard", "takeInspiration", "goToMarket" ),
  		"transitions" => array( "actionEnd" => 30, "actionBaratto" => 21, "actionArtist" => 23, "actionReserveArtist" => 24, "actionReserveMonument" => 25, "actionMarket" => 26 )
    ),
    
    21 => array(
    	"name" => "actionBaratto",
    	"description" => clienttranslate('${actplayer} must choose which resource to sell'),
  		"descriptionmyturn" => clienttranslate('${you} must choose which resource to sell'),
  		"type" => "activeplayer",
  		"possibleactions" => array( "confirmBaratto" ),
  		"transitions" => array( "actionBaratto2" => 22 )
    ),

    22 => array(
      "name" => "actionBaratto2",
      "description" => clienttranslate('${actplayer} must choose which resources take'),
      "descriptionmyturn" => clienttranslate('${you} must choose which resources take'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmBaratto2" ),
      "transitions" => array( "actionEnd" => 30 )
    ),    

    23 => array(
      "name" => "actionArtist",
      "description" => clienttranslate('${actplayer} must choose an available artist'),
      "descriptionmyturn" => clienttranslate('${you} must choose an available artist'),
      "type" => "activeplayer",
      "possibleactions" => array( "playArtistCard" ),
      "transitions" => array( "actionEnd" => 30 )
    ),
    
    24 => array(
      "name" => "actionReserveArtist",
      "description" => clienttranslate('${actplayer} must reserve an available artist'),
      "descriptionmyturn" => clienttranslate('${you} must reserve an available artist'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmArtistReservation" ),
      "transitions" => array( "actionEnd" => 30 )
    ),    

    25 => array(
      "name" => "actionReserveMonument",
      "description" => clienttranslate('${actplayer} must reserve an available monument'),
      "descriptionmyturn" => clienttranslate('${you} must reserve an available monument'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmMonumentReservation" ),
      "transitions" => array( "actionEnd" => 30 )
    ),

    26 => array(
      "name" => "actionMarket",
      "description" => clienttranslate('${actplayer} must take a market action'),
      "descriptionmyturn" => clienttranslate('${you} must take a market action'),
      "type" => "activeplayer",
      "possibleactions" => array( "sellResourceMarket", "buyResourceMarket", "tradeResourceMarket" ),
      "transitions" => array( "actionMarketSell" => 80, "actionMarketBuy" => 81, "actionMarketTradeSell" => 82 )
    ),

    80 => array(
      "name" => "actionMarketSell",
      "description" => clienttranslate('${actplayer} must choose which resource to sell to the market'),
      "descriptionmyturn" => clienttranslate('${you} must choose which resource to sell to the market'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmActionMarketSell" ),
      "transitions" => array( "actionEnd" => 30 )
    ),

    81 => array(
      "name" => "actionMarketBuy",
      "description" => clienttranslate('${actplayer} must choose which resource to buy from the market'),
      "descriptionmyturn" => clienttranslate('${you} must choose which resource to buy from the market'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmActionMarketBuy" ),
      "transitions" => array( "actionEnd" => 30 )
    ),  

    82 => array(
      "name" => "actionMarketTradeSell",
      "description" => clienttranslate('${actplayer} must choose the first resource to sell to the market'),
      "descriptionmyturn" => clienttranslate('${you} must choose the first resource to sell to the market'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmActionMarketTradeSell" ),
      "transitions" => array( "actionMarketTradeSell2" => 83 )
    ),

    83 => array(
      "name" => "actionMarketTradeSell2",
      "description" => clienttranslate('${actplayer} must choose the second resource to sell to the market'),
      "descriptionmyturn" => clienttranslate('${you} must choose the second resource to sell to the market'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmActionMarketTradeSell2" ),
      "transitions" => array( "actionMarketTradeBuy" => 84 )
    ),    

    84 => array(
      "name" => "actionMarketTradeBuy",
      "description" => clienttranslate('${actplayer} must choose which resource to buy from the market'),
      "descriptionmyturn" => clienttranslate('${you} must choose which resource to buy from the market'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmActionMarketTradeBuy" ),
      "transitions" => array( "actionEnd" => 30 )
    ),

    28 => array(
		"name" => "nextAction",
		"type" => "game",
    	"action" => "stNextAction",
    	"updateGameProgression" => true,
    	//"args" => "argsNextAction",
    	"transitions" => array( "beforeAction" => 10, "actionEnd" => 30 )
    ),    
    
    30 => array(
		"name" => "actionEnd",
		"type" => "game",
    	"action" => "stActionEnd",
    	"updateGameProgression" => true,
    	"transitions" => array( "nextAction" => 28, "turnEnd" => 40 )
    ),
    
    35 => array(
		"name" => "nextTurn",
		"type" => "game",
    	"action" => "stNextTurn",
    	"updateGameProgression" => true,
    	//"args" => "argsNextTurn",
    	"transitions" => array( "nextAction" => 28 )
    ),    
    
    40 => array(
		"name" => "turnEnd",
		"type" => "game",
    	"action" => "stTurnEnd",
    	"updateGameProgression" => true,
    	"transitions" => array( "nextTurn" => 35, "discard" => 41 )
    ),
    
    45 => array(
		"name" => "nextRound",
		"type" => "game",
    	"action" => "stNextRound",
    	"updateGameProgression" => true,
    	//"args" => "argsNextRound",
    	"transitions" => array( "beforeAction" => 10 )
    ),   
    
    44 => array(
  		"name" => "resourceChoice",
      "description" => clienttranslate('${actplayer} must choose an extra resource for every players because he is the captain'),
      "descriptionmyturn" => clienttranslate('${you} must choose an extra resource for every players because you are the captain'),		
  		"type" => "activeplayer",
      "possibleactions" => array( "confirmResourceChoice" ),
      "transitions" => array( "incomeCollection" => 42, "mercatantiSet" => 47 )
    ),   

    43 => array(
      "name" => "mercatantiChoice",
      "description" => clienttranslate('${actplayer} must choose an extra resource because he has Arte dei Mercatanti'),
      "descriptionmyturn" => clienttranslate('${you} must choose an extra resource because he has Arte dei Mercatanti'),    
      "type" => "activeplayer",
      "possibleactions" => array( "confirmMercatantiChoice" ),
      "transitions" => array( "incomeCollection" => 42 )
    ),  
    
    42 => array(
      "name" => "incomeCollection",
      "type" => "game",
      "action" => "stIncomeCollection",
      "transitions" => array( "roundEnd" => 50 )
    ),   
    
    41 => array(
		"name" => "discard",
		"type" => "multipleactiveplayer",
		"action" => "stDiscard",
    	"description" => clienttranslate('${actplayer} must choose which card to keep'),
		"descriptionmyturn" => clienttranslate('${you} must choose which card to keep'),
		"possibleactions" => array( "confirmKeptCard" ),
    	"transitions" => array( "incomeCollection" => 42, "captainSet" => 46 )
    ),   

    46 => array(
      "name" => "captainSet",
      "type" => "game",
      "action" => "stCaptainSet",
      "transitions" => array( "resourceChoice" => 44 )
    ),  

    47 => array(
      "name" => "mercatantiSet",
      "type" => "game",
      "action" => "stMercatantiSet",
      "transitions" => array( "mercatantiChoice" => 43, "incomeCollection" => 42 )
    ),      
    
    50 => array(
		"name" => "roundEnd",
		"type" => "game",
    	"action" => "stRoundEnd",
    	"updateGameProgression" => true,
    	"transitions" => array( "nextRound" => 45, "scoreCalculation" => 60 )
    ),

    60 => array(
      "name" => "scoreCalculation",
      "type" => "game",
      "action" => "stScoreCalculation",
      "updateGameProgression" => false,
      "transitions" => array( "gameEnd" => 99, "fakeState" => 100 )
    ),
     
   
    // Final state.
    // Please do not modify.
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ),


    100 => array(
      "name" => "fakeState",
      "description" => clienttranslate('${actplayer} fakeState'),
      "descriptionmyturn" => clienttranslate('${you} fakeState'),
      "type" => "activeplayer",
      "possibleactions" => array( "confirmMonumentReservation" ),
      "transitions" => array( "gameEnd" => 99 )
    ),

);


