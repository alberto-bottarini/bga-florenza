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
 * florenzacardgame.action.php
 *
 * FlorenzaCardGame main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/florenzacardgame/florenzacardgame/myAction.html", ...)
 *
 */
  
  
  class action_florenzacardgame extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "florenzacardgame_florenzacardgame";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 
  	
	public function playCard() {
		self::setAjaxMode();     
        $cardId = self::getArg("cardId", AT_posint, true);
        $result = $this->game->acPlayCard($cardId);
        self::ajaxResponse();
	}
	
	public function playLocationCard() {
		self::setAjaxMode();     
        $cardId = self::getArg("cardId", AT_posint, true);
        $result = $this->game->acPlayLocationCard($cardId);
        self::ajaxResponse();
	}

	public function playMonumentCard() {
		self::setAjaxMode();     
        $cardId = self::getArg("cardId", AT_posint, true);
        $result = $this->game->acPlayMonumentCard($cardId);
        self::ajaxResponse();
	}	
	
	public function sendWorkers() {
		self::setAjaxMode();
		$result = $this->game->acSendWorkers();
		self::ajaxResponse();
	}

	public function gotomarket() {
		self::setAjaxMode();
		$result = $this->game->acGoToMarket();
		self::ajaxResponse();
	}

	public function gotomarketsell() {
		self::setAjaxMode();
		$result = $this->game->acGoToMarketSell();
		self::ajaxResponse();
	}

	public function gotomarketbuy() {
		self::setAjaxMode();
		$result = $this->game->acGoToMarketBuy();
		self::ajaxResponse();
	}

	public function gotomarkettrade() {
		self::setAjaxMode();
		$result = $this->game->acGoToMarketTrade();
		self::ajaxResponse();
	}

	public function confirmactionmarketsell() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		$resource = self::getArg("resource", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmActionMarketSell($resource);
		self::ajaxResponse();
	}

	public function confirmactionmarketbuy() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		$resource = self::getArg("resource", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmActionMarketBuy($resource);
		self::ajaxResponse();
	}

	public function confirmactionmarkettradesell() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		$resource = self::getArg("resource", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmActionMarketTradeSell($resource);
		self::ajaxResponse();
	}

	public function confirmactionmarkettradesell2() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		$resource = self::getArg("resource", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmActionMarketTradeSell2($resource);
		self::ajaxResponse();
	}

	public function confirmactionmarkettradebuy() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		$resource = self::getArg("resource", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmActionMarketTradeBuy($resource);
		self::ajaxResponse();
	}
	
	public function reserveArtistCard() {
		self::setAjaxMode();
		$result = $this->game->acReserveArtistCard();
		self::ajaxResponse();
	}

	public function reserveMonumentCard() {
		self::setAjaxMode();
		$result = $this->game->acReserveMonumentCard();
		self::ajaxResponse();
	}	
	
	public function confirmArtistReservation() {
		self::setAjaxMode();
		$cardId = self::getArg("cardId", AT_posint, true);
		$result = $this->game->acConfirmArtistReservation($cardId);
		self::ajaxResponse();
	}

	public function confirmMonumentReservation() {
		self::setAjaxMode();
		$cardId = self::getArg("cardId", AT_posint, true);
		$result = $this->game->acConfirmMonumentReservation($cardId);
		self::ajaxResponse();
	}
	
	public function confirmbaratto() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		//$inResource = self::getArg("in", AT_enum, true, 'marble', $possibleValues);
		$outResource = self::getArg("out", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmBaratto($outResource);
		self::ajaxResponse();
	}

	public function confirmbaratto2() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		$inResource = self::getArg("inin", AT_enum, true, 'marble', $possibleValues);
		//$outResource = self::getArg("out", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmBaratto2($inResource);
		self::ajaxResponse();
	}
	
	public function confirmKeptCard() {
		self::setAjaxMode();     
        $cardId = self::getArg("cardId", AT_posint, true);
        $result = $this->game->acConfirmKeptCard($cardId);
        self::ajaxResponse();
	}

	public function playartistcard() {
		self::setAjaxMode();
		$cardId = self::getArg("cardId", AT_posint, true);
		$result = $this->game->acPlayArtistCard($cardId);
		self::ajaxResponse();
	}

	public function confirmresourcechoice() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		$resource = self::getArg("resource", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmResourceChoice($resource);
		self::ajaxResponse();
	}

	public function confirmmercatantichoice() {
		self::setAjaxMode();
		$possibleValues = array("marble", "wood", "fabric", "gold", "spice", "metal");
		$resource = self::getArg("resource", AT_enum, true, 'marble', $possibleValues);
		$result = $this->game->acConfirmMercatantiChoice($resource);
		self::ajaxResponse();
	}

	public function takeInspiration() {
		self::setAjaxMode();
		$result = $this->game->acTakeInspiration();
		self::ajaxResponse();
	}

	public function cancelAction() {
		self::setAjaxMode();
		$result = $this->game->acCancelAction();
		self::ajaxResponse();
	}

}
  

