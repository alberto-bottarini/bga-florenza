{OVERALL_GAME_HEADER} 

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- FlorenzaCardGame implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    florenzacardgame_florenzacardgame.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->

<div class="whiteblock" style="display: none">
	<h3>DEBUG:</h3>
	<p>{CURRENT_ACTION_NUMBER_LABEL}: <span id="current-action-container">1</span></p>
	<p>{CURRENT_TURN_NUMBER_LABEL}: <span id="current-turn-container">1</span></p>
	<p>{CURRENT_TURN_NUMBER_LABEL}: <span id="current-round-container">1</span></p>
</div>

<div class="whiteblock">
	<h3>{BOARD_LABEL}</h3>

	<div>
		<div style="float: right; width: 80px;" >
			<h4 style="text-align:center">{ROUND_COUNTER_LABEL}</h4>
			<div id="round-counter" class="round-counter-1"></div>
		</div>

		<div style="float: right; width: 80px;" >
			<h4 style="text-align:center">{TURN_COUNTER_LABEL}</h4>
			<div id="turn-counter" class="turn-counter-1"></div>
		</div>
	
		<h4>{MONUMENT_LIST_LABEL}</h4>
		<div id="monument-card-container">
		</div>
		<div class="clear"></div>
	</div>

	<h4>{LOCATION_LIST_LABEL}</h4>
	<div id="board-location-card-container"></div>
	<div class="clear"></div>

	<h4>{ARTISTI_LIST_LABEL}</h4>
	<div id="artist-card-container"></div>
	<div class="clear"></div>

	<h4>{RESOURCE_AVAILABILITY_LABEL}</h4>
	<div class="resource-container">
		<span class="resource resource-marble"></span>
		<span id="resource-availability-marble"></span>
	</div>
	<div class="resource-container">
		<span class="resource resource-wood"></span>
		<span id="resource-availability-wood"></span>
	</div>
	<div class="resource-container">
		<span class="resource resource-metal"></span>
		<span id="resource-availability-metal"></span>
	</div>
	<div class="resource-container">
		<span class="resource resource-fabric"></span>
		<span id="resource-availability-fabric"></span>
	</div>
	<div class="resource-container">
		<span class="resource resource-gold"></span>
		<span id="resource-availability-gold"></span>
	</div>
	<div class="resource-container">
		<span class="resource resource-spice"></span>
		<span id="resource-availability-spice"></span>
	</div>
	<div class="resource-container">
		<span class="resource resource-money"></span>
		<span id="resource-availability-money"></span>
	</div>
	<div style="clear:both"></div>	
	
</div>

<div class="whiteblock">
	<h3>{MY_HAND_LABEL}</h3>
	<div id="hand-florenza-card-container">
		
	</div>
	<div class="clear"></div>
</div>

<div class="whiteblock">
	<h3>{MY_PLAYER_NAME}</h3>
	<div id="board-florenza-player-{MY_PLAYER_ID}-card-container">
	
	</div>
	<div class="clear"></div>
	<h4>{MONUMENT_LABEL}</h4>
	<div id="board-florenza-player-{MY_PLAYER_ID}-monument-container">
		
	</div>
	<div class="clear"></div>
	<div style="float: right; width: 50%;" >
		<h4>{RESERVED_MONUMENT_LABEL}</h4>
		<div id="board-florenza-player-{MY_PLAYER_ID}-reserved-monument-container">
	
		</div>
	</div>
	<h4>{RESERVED_ARTIST_LABEL}</h4>
	<div id="board-florenza-player-{MY_PLAYER_ID}-reserved-artist-container">
		
	</div>
	<div class="clear"></div>
</div>

<!-- BEGIN playerboard -->
	<div class="whiteblock">
		<h3>{PLAYER_NAME}</h3>
		<div id="board-florenza-player-{PLAYER_ID}-card-container">
		
		</div>
		<div class="clear"></div>
		<h4>{MONUMENT_LABEL}</h4>
		<div id="board-florenza-player-{PLAYER_ID}-monument-container">
			
		</div>
		<div class="clear"></div>

		<div style="float: right; width: 50%;" >
			<h4>{RESERVED_MONUMENT_LABEL}</h4>
			<div id="board-florenza-player-{PLAYER_ID}-reserved-monument-container">
		
			</div>
		</div>

		<h4>{RESERVED_ARTIST_LABEL}</h4>
		<div id="board-florenza-player-{PLAYER_ID}-reserved-artist-container">
		
		</div>
		<div class="clear"></div>
	</div>
<!-- END playerboard -->


<script type="text/javascript">

// Javascript HTML templates

var jstpl_florenza_card = '<div href="#" id="florenza-card-${id}" class="florenza-card-trigger florenza-card-trigger-${type}"></div>';
var jstpl_location_card = '<div href="#" id="location-card-${id}" class="location-card-trigger location-card-trigger-${type} tapped-${tapped}"></div>';
var jstpl_artist_card = '<div href="#" id="artist-card-${id}" class="artist-card-trigger artist-card-trigger-${type}"></div>';
var jstpl_monument_card = '<div href="#" id="monument-card-${id}" class="monument-trigger monument-trigger-${type}"></div>';

var jstpl_artist_over_card = '<span class="artist-card-trigger artist-card-trigger-${type}-${score}"></span>';

var jstpl_captain = '<div id="captain-player"></div>';
var jstpl_card_counter = '<div class="florenza-card-counter" id="florenza-card-counter-${player}">${florenza}</div><div class="resource-card-counter" id="resource-card-counter-${player}">${resource}</div><div class="money-card-counter" id="money-card-counter-${player}">${money}</div><div class="clear"></div>';
var jstpl_my_resource = '<div class="my-resource whiteblock clear"><span class="resource-tooltip" id="resource-marble-tooltip"><span class="resource-small resource-small-marble"></span><span class="resource-value" id="resource-marble"></span></span><span class="resource-tooltip" id="resource-wood-tooltip"><span class="resource-small resource-small-wood"></span><span class="resource-value" id="resource-wood"></span></span><span class="resource-tooltip" id="resource-metal-tooltip"><span class="resource-small resource-small-metal"></span><span class="resource-value" id="resource-metal"></span></span><br/><span class="resource-tooltip" id="resource-fabric-tooltip"><span class="resource-small resource-small-fabric"></span><span class="resource-value" id="resource-fabric"></span></span><span class="resource-tooltip" id="resource-gold-tooltip"><span class="resource-small resource-small-gold"></span><span class="resource-value" id="resource-gold"></span></span><span class="resource-tooltip" id="resource-spice-tooltip"><span class="resource-small resource-small-spice"></span><span class="resource-value" id="resource-spice"></span></span><br/><span class="resource-tooltip" id="resource-money-tooltip"><span class="resource-small resource-small-money"></span><span class="resource-value" id="resource-money"></span></span></div>';


</script>  

{OVERALL_GAME_FOOTER}
