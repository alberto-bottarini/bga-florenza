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
 * florenzacardgame.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in florenzacardgame_florenzacardgame.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
  require_once( APP_BASE_PATH."view/common/game.view.php" );
  
  class view_florenzacardgame_florenzacardgame extends game_view
  {
    function getGameName() {
        return "florenzacardgame";
    }    
  	function build_page( $viewArgs )
  	{		
  	    // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );
        
	  	global $g_user;
		$current_player_id = $g_user->get_id(); 

        /*********** Place your code below:  ************/

        $this->tpl['BOARD_LABEL'] = self::_("Board");
        $this->tpl['CURRENT_ACTION_NUMBER_LABEL'] = self::_("Current Action Number");
        $this->tpl['CURRENT_TURN_NUMBER_LABEL'] = self::_("Current Turn Number");
        $this->tpl['CURRENT_ROUND_NUMBER_LABEL'] = self::_("Current Round Number");
        $this->tpl['LOCATION_LIST_LABEL'] = self::_("Location List");
        $this->tpl['MONUMENT_LIST_LABEL'] = self::_("Monument List");
        $this->tpl['TURN_COUNTER_LABEL'] = self::_("Turn");
        $this->tpl['ROUND_COUNTER_LABEL'] = self::_("Round");
        $this->tpl['ARTISTI_LIST_LABEL'] = self::_("Available Artist List");
        $this->tpl['MY_RESOURCE_LABEL'] = self::_("My Resources");
        $this->tpl['RESOURCE_AVAILABILITY_LABEL'] = self::_("Resources Availability");
        $this->tpl['MARBLE_LABEL'] = self::_("Marble");
        $this->tpl['WOOD_LABEL'] = self::_("Wood");
        $this->tpl['METAL_LABEL'] = self::_("Metal");
        $this->tpl['FABRIC_LABEL'] = self::_("Fabric");
        $this->tpl['GOLD_LABEL'] = self::_("Gold");
        $this->tpl['SPICE_LABEL'] = self::_("Spice");
        $this->tpl['MONEY_LABEL'] = self::_("Money");
        $this->tpl['MY_HAND_LABEL'] = self::_("Cards in my hand");
        $this->tpl['MY_BOARD_LABEL'] = self::_("Cards in my board");
        $this->tpl['MONUMENT_LABEL'] = self::_("Built Monuments");
        
        $this->tpl['BARATTO_LABEL'] = self::_("Baratto");
        $this->tpl['BARATTO_SUBLABEL_1'] = self::_("Exchange 1");
        $this->tpl['BARATTO_SUBLABEL_2'] = self::_("for 1");
        $this->tpl['BARATTO_CONFIRM_LABEL'] = self::_("Confirm!");
        
        $this->tpl['RESOURCE_CHOICE_LABEL'] = self::_("Choose a resource");
        
        $this->tpl['RESERVED_ARTIST_LABEL'] = self::_("Reserved artists");
        $this->tpl['RESERVED_MONUMENT_LABEL'] = self::_("Reserved monuments");
        
        foreach($players as $player) {
        	if($player['player_id'] == $current_player_id) {
		   		$this->tpl['MY_PLAYER_ID'] = $current_player_id;
		    	$this->tpl['MY_PLAYER_NAME'] = $player['player_name'];
		    }
		}
        
		$this->page->begin_block("florenzacardgame_florenzacardgame", "playerboard");
		foreach($players as $player) {
			if($player['player_id'] != $current_player_id) {
				$this->page->insert_block("playerboard", array(
					"PLAYER_ID" => $player['player_id'],
					"PLAYER_NAME" => $player['player_name']
				));
			}
		}


        /*
        
        // Examples: set the value of some element defined in your tpl file like this: {MY_VARIABLE_ELEMENT}

        // Display a specific number / string
        $this->tpl['MY_VARIABLE_ELEMENT'] = $number_to_display;

        // Display a string to be translated in all languages: 
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::_("A string to be translated");

        // Display some HTML content of your own:
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::raw( $some_html_code );
        
        */
        
        /*
        
        // Example: display a specific HTML block for each player in this game.
        // (note: the block is defined in your .tpl file like this:
        //      <!-- BEGIN myblock --> 
        //          ... my HTML code ...
        //      <!-- END myblock --> 
        

        $this->page->begin_block( "florenzacardgame_florenzacardgame", "myblock" );
        foreach( $players as $player )
        {
            $this->page->insert_block( "myblock", array( 
                                                    "PLAYER_NAME" => $player['player_name'],
                                                    "SOME_VARIABLE" => $some_value
                                                    ...
                                                     ) );
        }
        
        */
        

        /*********** Do not change anything below this line  ************/
  	}
  }
  

