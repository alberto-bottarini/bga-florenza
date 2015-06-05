/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * FlorenzaCardGame implementation : © <Alberto Bottarini> <alberto.bottarini@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * florenzacardgame.js
 *
 * FlorenzaCardGame user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {

	dojo.forEachObject = function(obj, f, scope){
		for(var key in obj){
		    if(obj.hasOwnProperty(key)){
		        f.call(scope, key, obj[key]);
		    }
		}
	}

    return declare("bgagame.florenzacardgame", ebg.core.gamegui, {
    
        constructor: function() {
        	this.resourcesList = "marble wood fabric metal spice gold money".split(" ");
            this.florenzaCardClickConnections   = [ ];
            this.locationCardClickConnections   = [ ];
            this.artistCardClickConnections     = [ ];
            this.monumentCardClickConnections       = [ ];
            window.florenza = this;
        },
        
        setup: function(gamedatas) {
            
            var that = this;
            
            if($('player_board_'+this.player_id)) { //spectator
                dojo.place( this.format_block('jstpl_my_resource'), $('player_board_'+this.player_id) );
            }
            dojo.forEach(this.resourcesList, function(resource) {
                if(gamedatas.resources) { //spectator
                   that.updateResource(resource, gamedatas.resources[resource], true);
                }
                that.updateResourceAvailability(resource, gamedatas.resourcesAvailability[resource], true);
                that.createResourceTooltip();
            }); 
            
            for(var i in gamedatas.hand) {	
            	var card = gamedatas.hand[i];
            	this.createFlorenzaCardHandNode(card);
			}
			
            for(var playerId in gamedatas.board) {
            	var playerCardList = gamedatas.board[playerId];
            	for(var i in playerCardList) {
		        	var card = playerCardList[i];
		        	this.createFlorenzaCardBoardNode(playerId, card);
				}
			}

            for(var playerId in gamedatas.card_counter) {
                var playerCardCount = gamedatas.card_counter[playerId];
                playerCardCount.player = playerId;
                var playerBoard = $('player_board_'+playerId);
                dojo.place( this.format_block('jstpl_card_counter', playerCardCount), playerBoard );
            }
            that.createCardCounterTooltip();

            for(var playerId in gamedatas.florenzaArtist) {
                var playerArtistList = gamedatas.florenzaArtist[playerId];
                for(var i in this.sortByClass(playerArtistList)) {
                    var card = playerArtistList[i];
                    that.createArtistCardNodeOnFlorenzaCard(playerId, card);
                }
            }

            for(var playerId in gamedatas.monument) {
                var playerCardList = gamedatas.monument[playerId];
                for(var i in this.sortByScorePoint(playerCardList)) {
                    var card = playerCardList[i];
                    this.createMonumentCardBoardNode(playerId, card);
                }
            }

            for(var playerId in gamedatas.monumentArtist) {
                var playerArtistList = gamedatas.monumentArtist[playerId];
                for(var i in playerArtistList) {
                    var card = playerArtistList[i];
                    that.createArtistCardNodeOnMonumentCard(playerId, card);
                }
            }
           
            for(var playerId in gamedatas.reservedArtist) {
            	var playerArtistList = gamedatas.reservedArtist[playerId];
            	for(var i in this.sortByClass(playerArtistList)) {
                    var card = playerArtistList[i];
                    that.createReservedArtistCardNode(playerId, card);
                }
            }

            for(var playerId in gamedatas.reservedMonument) {
                var playerMonumentList = gamedatas.reservedMonument[playerId];
                for(var i in this.sortByScorePoint(playerMonumentList)) {
                    var card = playerMonumentList[i];
                    that.createReservedMonumentCardNode(playerId, card);
                }
            }
			
			dojo.forEach(gamedatas.locationCard, function(card) {
				that.createLocationCardNode(card);
			});
			
			dojo.forEach(this.sortByClass(gamedatas.artistCard), function(card) {
                that.createArtistCardNode(card);
			});

            dojo.forEach(gamedatas.anonymousArtistCard, function(card) {
                that.createArtistCardNode(card);
            })

            dojo.forEach(this.sortByScorePoint(gamedatas.monumentCard), function(card) {
                that.createMonumentCardNode(card);
            });

			this.updateCaptain(gamedatas.captainId);
			
			//$("current-action-container").innerHTML = gamedatas.currentActionNumber;
			//$("current-turn-container").innerHTML = gamedatas.currentTurnNumber;
			//$("current-round-container").innerHTML = gamedatas.currentRoundNumber;
			this.updateTurn(gamedatas.currentTurnNumber);
            this.updateRound(gamedatas.currentRoundNumber);
			
            this.setupNotifications();
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        onEnteringState: function(stateName, args) {
            
            switch(stateName) {
            
                case 'action':
    	            if(this.isCurrentPlayerActive()) {
    	            
    	            	var that = this;
    	            
    	            	//florenza card handler connect
    		        	var florenzaCardTrigger = this.getFlorenzaCardTriggerList();
    		        	this.florenzaCardClickConnections =  florenzaCardTrigger.map(function(node) { 
    		        		return dojo.connect(node, "onclick", that, "onPlayFlorenzaCard"); 
    		        	});
    		        	this.enablePointerOnFlorenzaCard();
    		        	
    		        	//location card handler connect
    		        	var locationCardTrigger = dojo.query(".location-card-trigger.tapped-0");
    		        	this.locationCardClickConnections =  locationCardTrigger.map(function(node) { 
    		        		return dojo.connect(node, "onclick", that, "onPlayLocationCard"); 
    		        	});
    		        	this.enablePointerOnLocationCard();

                        //monument card handler connect
                        var monumentCardTrigger = this.getMonumentCardTriggerList();
                        this.monumentCardClickConnections = monumentCardTrigger.map(function(node) {
                            return dojo.connect(node, "onclick", that, "onPlayMonumentCard"); 
                        });
                        this.enablePointerOnMonumentCard();
    		        	
    		        }
    		        break;
    		        
    			case 'actionReserveArtist':
    				if(this.isCurrentPlayerActive()) {
                        var that = this;
                        var artistCardTrigger = dojo.query(".artist-card-trigger");
                        this.artistCardClickConnections =  artistCardTrigger.map(function(node) {
                            return dojo.connect(node, "onclick", that, "onConfirmArtistReservation"); 
                        });
                        this.enablePointerOnArtistCard();
                    }
    				break;

                case 'actionReserveMonument':
                    if(this.isCurrentPlayerActive()) {
                        var that = this;
                        var monumentCardTrigger = this.getMonumentCardTriggerList();
                        this.monumentCardClickConnections = monumentCardTrigger.map(function(node) {
                            return dojo.connect(node, "onclick", that, "onConfirmMonumentReservation"); 
                        });
                        this.enablePointerOnMonumentCard();
                    }
                    break;

                case 'actionArtist':                
                    if(this.isCurrentPlayerActive()) {
                        var that = this;
                        var artistCardTrigger = this.getArtistCardTriggerList();
                        this.artistCardClickConnections =  artistCardTrigger.map(function(node) {
                            return dojo.connect(node, "onclick", that, "onPlayArtistCard"); 
                        });
                        this.enablePointerOnArtistCard();
                    }
                    break;
                /*
                case 'nextAction':
               		$("current-action-container").innerHTML = args.args.currentActionNumber;
               		
               		break;	
                           
    			case 'nextTurn':
               		$("current-turn-container").innerHTML = args.args.currentTurnNumber;
               		this.updateTurn(args.args.currentTurnNumber);
               		break;	
               		
               	case 'nextRound':
               		$("current-round-container").innerHTML = args.args.currentRoundNumber;
               		dojo.forEach(dojo.query(".location-card-trigger"), function(cardTrigger) { //untap location card
               			dojo.replaceClass(cardTrigger, "tapped-0", "tapped-1");
               		});
    				this.updateCaptain(args.args.captainId);
    				this.updateTurn(1);
               		break;
               	*/	
               	case 'discard':
    	       		var that = this;
    	       		var florenzaCardTrigger = this.getFlorenzaCardTriggerList();
    	       		this.florenzaCardClickConnections =  florenzaCardTrigger.map(function(node) { 
    		    		return dojo.connect(node, "onclick", that, "onKeepFlorenzaCard"); 
    		    	});
                    this.enablePointerOnFlorenzaCard();
    				this.disablePointerOnLocationCard();		    	
    	        	break;  

            } 	       	
           	
        },

        onLeavingState: function(stateName) {
            
            switch(stateName) {
            
                case 'action':
                	if(this.isCurrentPlayerActive()) {
                		this.removeFlorenzaCardConnections();
    		        	this.removeLocationCardConnections();
                        this.removeMonumentCardConnections();
                	}
                	break;
                	
    			case 'actionReserveArtist':
                    if(this.isCurrentPlayerActive()) {
                        this.removeArtistCardConnections();
                    }
                    break;

                case 'actionReserveMonument':
                    if(this.isCurrentPlayerActive()) {
                        this.removeMonumentCardConnections();
                    }
                    break;

                case 'actionArtist':
                    if(this.isCurrentPlayerActive()) {
                        this.removeArtistCardConnections();
                    }
                    break;
                
                case 'discard':
    	            this.removeFlorenzaCardConnections();
    	            break;

            }
            
        }, 

        onUpdateActionButtons: function(stateName, args) {
            if(this.isCurrentPlayerActive()) {
                switch(stateName) {
                	case "action":
                		this.addActionButton("reserve-artist-button", _("Reserve an artist"), "onReserveArtist");
                        this.addActionButton("reserve-monument-button", _("Reserve a monument"), "onReserveMonument");
                        this.addActionButton("send-worker-button", _("Send workers out (+50 money)"), "onSendWorkers");
                        this.addActionButton("go-market-button", _("Go to the market (buy or sell resources)"), "onGoMarket");
                        this.addActionButton("take-inspiration-button", _("Take inspiration (draw a card)"), "onTakeInspiration");
                		break;

                    case "actionMarket": 
                        this.addActionButton("go-market-sell-button", _("Sell a resource for 100 money"), "onGoMarketSell");
                        this.addActionButton("go-market-buy-button", _("Buy a resource for 100 money"), "onGoMarketBuy");
                        this.addActionButton("go-market-trade-button", _("Trade 2 resource for 1"), "onGoMarketTrade");
                        break;

                    case "actionMarketSell":
                        this.addActionButton("go-market-sell-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmMarketSell");
                        this.addActionButton("go-market-sell-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmMarketSell");
                        this.addActionButton("go-market-sell-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmMarketSell");
                        this.addActionButton("go-market-sell-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmMarketSell");
                        this.addActionButton("go-market-sell-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmMarketSell");
                        this.addActionButton("go-market-sell-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmMarketSell");
                        break;

                     case "actionMarketBuy":
                        this.addActionButton("go-market-buy-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmMarketBuy");
                        this.addActionButton("go-market-buy-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmMarketBuy");
                        this.addActionButton("go-market-buy-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmMarketBuy");
                        this.addActionButton("go-market-buy-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmMarketBuy");
                        this.addActionButton("go-market-buy-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmMarketBuy");
                        this.addActionButton("go-market-buy-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmMarketBuy");
                        break;

                    case 'actionMarketTradeSell':
                        this.addActionButton("go-market-trade-sell-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmMarketTradeSell");
                        this.addActionButton("go-market-trade-sell-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmMarketTradeSell");
                        this.addActionButton("go-market-trade-sell-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmMarketTradeSell");
                        this.addActionButton("go-market-trade-sell-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmMarketTradeSell");
                        this.addActionButton("go-market-trade-sell-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmMarketTradeSell");
                        this.addActionButton("go-market-trade-sell-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmMarketTradeSell");
                        break;

                    case 'actionMarketTradeSell2':
                        this.addActionButton("go-market-trade-sell2-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmMarketTradeSell2");
                        this.addActionButton("go-market-trade-sell2-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmMarketTradeSell2");
                        this.addActionButton("go-market-trade-sell2-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmMarketTradeSell2");
                        this.addActionButton("go-market-trade-sell2-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmMarketTradeSell2");
                        this.addActionButton("go-market-trade-sell2-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmMarketTradeSell2");
                        this.addActionButton("go-market-trade-sell2-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmMarketTradeSell2");
                        break;

                    case 'actionMarketTradeBuy':
                        this.addActionButton("go-market-trade-buy-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmMarketTradeBuy");
                        this.addActionButton("go-market-trade-buy-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmMarketTradeBuy");
                        this.addActionButton("go-market-trade-buy-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmMarketTradeBuy");
                        this.addActionButton("go-market-trade-buy-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmMarketTradeBuy");
                        this.addActionButton("go-market-trade-buy-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmMarketTradeBuy");
                        this.addActionButton("go-market-trade-buy-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmMarketTradeBuy");
                        break;

                    case "resourceChoice":
                        this.addActionButton("resource-choice-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmResourceChoice");
                        this.addActionButton("resource-choice-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmResourceChoice");
                        this.addActionButton("resource-choice-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmResourceChoice");
                        this.addActionButton("resource-choice-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmResourceChoice");
                        this.addActionButton("resource-choice-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmResourceChoice");
                        this.addActionButton("resource-choice-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmResourceChoice");
                        break;

                    case "mercatantiChoice":
                        this.addActionButton("mercatanti-choice-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmMercatantiChoice");
                        this.addActionButton("mercatanti-choice-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmMercatantiChoice");
                        this.addActionButton("mercatanti-choice-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmMercatantiChoice");
                        this.addActionButton("mercatanti-choice-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmMercatantiChoice");
                        this.addActionButton("mercatanti-choice-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmMercatantiChoice");
                        this.addActionButton("mercatanti-choice-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmMercatantiChoice");
                        break;

                    case "actionBaratto":
                        this.addActionButton("baratto-choice-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmBarattoChoice");
                        this.addActionButton("baratto-choice-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmBarattoChoice");
                        this.addActionButton("baratto-choice-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmBarattoChoice");
                        this.addActionButton("baratto-choice-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmBarattoChoice");
                        this.addActionButton("baratto-choice-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmBarattoChoice");
                        this.addActionButton("baratto-choice-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmBarattoChoice");
                        break;

                    case "actionBaratto2":
                        this.addActionButton("baratto2-choice-marble-button", "<span class='resource-small resource-small-marble'></span>", "onConfirmBaratto2Choice");
                        this.addActionButton("baratto2-choice-wood-button", "<span class='resource-small resource-small-wood'></span>", "onConfirmBaratto2Choice");
                        this.addActionButton("baratto2-choice-metal-button", "<span class='resource-small resource-small-metal'></span>", "onConfirmBaratto2Choice");
                        this.addActionButton("baratto2-choice-fabric-button", "<span class='resource-small resource-small-fabric'></span>", "onConfirmBaratto2Choice");
                        this.addActionButton("baratto2-choice-gold-button", "<span class='resource-small resource-small-gold'></span>", "onConfirmBaratto2Choice");
                        this.addActionButton("baratto2-choice-spice-button", "<span class='resource-small resource-small-spice'></span>", "onConfirmBaratto2Choice");
                        break;
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        createFlorenzaCardTooltip: function(card) {
        	var html = "<h4>" + card.title + "</h4><hr />";
            dojo.forEachObject(card.cost, function(resource, quantity) {
                if(resource != 'money') {
                    for(var i = 0; i<quantity; i++)
                        html += "<div class='resource-container-small'><span class='resource-small resource-small-"+resource+"'></span></div>";
                } else if(quantity > 0)
                    html += "<div class='resource-container-small'><span class='resource-small resource-small-"+resource+"'></span>"+quantity+"</div>";
        	});
        	html += "<div class='clear'></div><br/>";
        	if(card.description.length > 0) html += _(card.description) + "<br/>";
        	if(card.scorePoint > 0) html += "Score Point: <b>" + card.scorePoint + "</b><br/>";
        	html += "<br/>";
        	if(card.artists.length > 0) {
		    	dojo.forEach(card.artists, function(artist) {
		    		html += "<span class='artist-small artist-small-"+artist+"'></span>";
		    	});
		    	html += "<div class='clear'></div><br/>";
        	}
			return html;
        },
        
        createFlorenzaCardHandNode: function(card) {
 			var node = dojo.place(this.format_block('jstpl_florenza_card', {
				   id: card.id,
                   type: card.type.toLowerCase()
			}), 'hand-florenza-card-container');
			this.addTooltipHtml(node.id, this.createFlorenzaCardTooltip(card), 100);
		},
        
        createFlorenzaCardBoardNode: function(playerId, card) {
        	var node = dojo.place(this.format_block('jstpl_florenza_card', {
				   id: card.id,
                   type: card.type.toLowerCase()
			}), 'board-florenza-player-'+playerId+'-card-container');
			this.addTooltipHtml(node.id, this.createFlorenzaCardTooltip(card), 100);
        },

        createMonumentCardBoardNode: function(playerId, card) {
            var node = dojo.place(this.format_block('jstpl_monument_card', {
                   id: card.id,
                   type: card.type.toLowerCase()
            }), 'board-florenza-player-'+playerId+'-monument-container');
            this.addTooltipHtml(node.id, this.createMonumentCardTooltip(card), 100);
        },

        removeAllArtistCard: function() {
            dojo.empty('artist-card-container');
        },

        removeAllMonumentCard: function() {
            dojo.empty('monument-card-container');
        },

        createArtistCardNodeOnFlorenzaCard: function(playerId, card) {
            var node = dojo.place(this.format_block('jstpl_artist_over_card', {
                score: card.scorePoint,
                type: card.type.toLowerCase()
            }) , 'florenza-card-' + card.relatedCardId);
        },

        createArtistCardNodeOnMonumentCard: function(playerId, card) {
            var node = dojo.place(this.format_block('jstpl_artist_over_card', {
                score: card.scorePoint,
                type: card.type.toLowerCase()
            }) , 'monument-card-' + card.relatedCardId);
        },

        createReservedArtistCardNode: function(playerId, card) {
            var node = dojo.place(this.format_block('jstpl_artist_card', {
                type: card.type.toLowerCase(),
			   	id: card.id
            }) , 'board-florenza-player-' + playerId + '-reserved-artist-container');
			this.addTooltipHtml(node.id, this.createArtistCardTooltip(card), 100);            
        },

        createReservedMonumentCardNode: function(playerId, card) {
            var node = dojo.place(this.format_block('jstpl_monument_card', {
                type: card.type.toLowerCase(),
                id: card.id
            }) , 'board-florenza-player-' + playerId + '-reserved-monument-container');
            this.addTooltipHtml(node.id, this.createMonumentCardTooltip(card), 100);         
        },
        
        destroyFlorenzaCardHandNode: function(cardId) {
        	this.fadeOutAndDestroy($("florenza-card-" + cardId));
        },

        destroyMonumentCardBoardNode: function(cardId) {
            this.fadeOutAndDestroy($("monument-card-" + cardId));
        },

        destroyArtistCardNode: function(cardId) {
            this.fadeOutAndDestroy($("artist-card-" + cardId));
        },

        changeTappedArtistCardNode: function(cardId, tapped) {
            if(tapped) {
                dojo.addClass("artist-card-" + cardId, 'artist-card-trigger-anonymous');
            } else {
                dojo.removeClass("artist-card-" + cardId, 'artist-card-trigger-anonymous');
            }
        },
        
        createLocationCardTooltip: function(card) {
        	var html = "<h4>" + card.title + "</h4><hr />" + _(card.description);
			return html;
        },        

        createMonumentCardTooltip: function(card) {
            var html = "<h4>" + card.title + " " + card.location + "</h4><hr />";
            dojo.forEachObject(card.cost, function(resource, quantity) {
                if(resource != 'money') {
                    for(var i = 0; i<quantity; i++)
                        html += "<div class='resource-container-small'><span class='resource-small resource-small-"+resource+"'></span></div>";
                } else if(quantity > 0)
                    html += "<div class='resource-container-small'><span class='resource-small resource-small-"+resource+"'></span>"+quantity+"</div>";
            });
            html += "<div class='clear'></div><br/>";
            html += "Score Point: <b>" + card.scorePoint + "</b><br/>";
            html += "<br/>";
            if(card.artists.length > 0) {
                dojo.forEach(card.artists, function(artist) {
                    html += "<span class='artist-small artist-small-"+artist+"'></span>";
                });
                html += "<div class='clear'></div><br/>";
            }
            return html
        },
        
        createArtistCardTooltip: function(card) {
        	var html = "<h4>" + card.title + "</h4><hr />";
        	html += "<span class='artist-small artist-small-"+card['class'].toLowerCase()+"'></span>";
        	html += "<div class='resource-container-small'><span class='resource-small resource-small-money'></span>"+card.cost+"</div>";
        	html += "<div class='clear'></div><br/>";
        	html += "Score Point: <b>" + card.scorePoints.join("|") + "</b><br/>";
        	return html
        },
        
        createLocationCardNode: function(card) {
       		var node = dojo.place(this.format_block('jstpl_location_card', {
			   type: card.type.toLowerCase(),
			   id: card.card_id,
			   tapped: card.tapped
			}) , 'board-location-card-container');
			this.addTooltipHtml(node.id, this.createLocationCardTooltip(card), 100);
		},

        createMonumentCardNode: function(card) {
            var node = dojo.place(this.format_block('jstpl_monument_card', {
               type: card.type.toLowerCase(),
               id: card.id
            }) , 'monument-card-container');
            this.addTooltipHtml(node.id, this.createMonumentCardTooltip(card), 100);
        },        
		
		createArtistCardNode: function(card) {
       		var node = dojo.place(this.format_block('jstpl_artist_card', {
			   type: card.type.toLowerCase(),
			   id: card.id
			}) , 'artist-card-container');
            if(card.anonymous && card.location == "tapped") {
                dojo.addClass(node, 'artist-card-trigger-anonymous');
            }
			this.addTooltipHtml(node.id, this.createArtistCardTooltip(card), 100);
		},
		
		updateAllResource: function(resource, quantity, newValue) {
			this.updateResource(resource, quantity, newValue);
			this.updateResourceAvailability(resource, -quantity, newValue);
		},
        
        updateResource: function(resource, quantity, newValue) {
        	if(!newValue) {
        		var oldValue = parseInt($("resource-" + resource).innerHTML, 10);
        		$("resource-" + resource).innerHTML = oldValue + parseInt(quantity, 10);
	        } else {
	        	$("resource-" + resource).innerHTML = quantity;
	        }
        },
        
        updateResourceAvailability: function(resource, quantity, newValue) {
        	if(!newValue) {
        		var oldValue = parseInt($("resource-availability-" + resource).innerHTML, 10);
        		$("resource-availability-" + resource).innerHTML = oldValue + parseInt(quantity, 10);
	        } else {
	        	$("resource-availability-" + resource).innerHTML = quantity;
	        }
        },

        createResourceTooltip: function() {
            this.addTooltipHtml('resource-marble-tooltip', "<h4>" + _('Marble') + "</h4>", 100);
            this.addTooltipHtml('resource-wood-tooltip', "<h4>" + _('Wood') + "</h4>", 100);
            this.addTooltipHtml('resource-metal-tooltip', "<h4>" + _('Metal') + "</h4>", 100);
            this.addTooltipHtml('resource-fabric-tooltip', "<h4>" + _('Fabric') + "</h4>", 100);
            this.addTooltipHtml('resource-gold-tooltip', "<h4>" + _('Gold') + "</h4>", 100);
            this.addTooltipHtml('resource-spice-tooltip', "<h4>" + _('Spice') + "</h4>", 100);
            this.addTooltipHtml('resource-money-tooltip', "<h4>" + _('Money') + "</h4>", 100);
        },

        createCardCounterTooltip: function() {
            this.addTooltipHtmlToClass('florenza-card-counter', "<h4>" + _('Florenza cards in hand') + "</h4>", 100);
            this.addTooltipHtmlToClass('resource-card-counter', "<h4>" + _('Resource cards in hand') + "</h4>", 100);
            this.addTooltipHtmlToClass('money-card-counter', "<h4>" + _('Money cards in hand') + "</h4>", 100);
        },
        
        getResourceSlidingObject: function(resource) {
        	return "<div class='resource resource-"+resource+"'></div>";
        },
        
        slideResources: function(resource, quantity, from, to) {
        	if(resource == 'money') quantity /= 50;
        	var slidingQuantity = resource == 'money' ? quantity/50 : quantity;
        	for(var c = 0; c < quantity; c++) {
		    	this.slideTemporaryObject(this.getResourceSlidingObject(resource), 
			    		'page-content', from, to, 1000, c*500);
			}
        },
        
        decreaseMyResource: function(resource, quantity) {
        	if(quantity == 0) return;
        	this.slideResources(resource, quantity, "resource-"+resource, "resource-availability-"+resource);	
        	this.updateResource(resource, -1*quantity, false);
        	this.updateResourceAvailability(resource, quantity, false);
        },
        
        decreasePlayerResource: function(playerId, resource, quantity) {
        	if(quantity == 0) return;
        	this.slideResources(resource, quantity, "player_board_"+playerId, "resource-availability-"+resource);
        	this.updateResourceAvailability(resource, quantity, false);
        },
        
        increaseMyResource: function(resource, quantity) {
        	if(quantity == 0) return;  
        	this.slideResources(resource, quantity, "resource-availability-"+resource, "resource-"+resource);
        	this.updateResource(resource, quantity, false);
        	this.updateResourceAvailability(resource, -1*quantity, false);
        },
        
        increasePlayerResource: function(playerId, resource, quantity) {
        	if(quantity == 0) return;
        	this.slideResources(resource, quantity, "resource-availability-"+resource, "player_board_"+playerId);
        	this.updateResourceAvailability(resource, -1*quantity, false);
        },
        
        getMonumentCardTriggerList: function() {
        	var availableMonument = dojo.query("#monument-card-container .monument-trigger"),
                activePlayerId = this.getActivePlayerId(),
                mineMonument = dojo.query("#board-florenza-player-" + activePlayerId + "-reserved-monument-container .monument-trigger");
            dojo.forEach(mineMonument, function(monumentNode) {
                availableMonument.push(monumentNode);
            });
            return availableMonument;
        },

        getArtistCardTriggerList: function() {
            var availableArtist = dojo.query("#artist-card-container .artist-card-trigger"),
                activePlayerId = this.getActivePlayerId(),
                mineArtist = dojo.query("#board-florenza-player-" + activePlayerId + "-reserved-artist-container .artist-card-trigger");
            dojo.forEach(mineArtist, function(artistNode) {
                availableArtist.push(artistNode);
            });
            return availableArtist;
        },
        
        removeFlorenzaCardConnections: function() {
        	dojo.forEach(this.florenzaCardClickConnections, function(connection) {
		    	dojo.disconnect(connection);
		    });
		    this.disablePointerOnFlorenzaCard();
		    this.florenzaCardClickConnections = [];
        },

        getFlorenzaCardTriggerList: function() {
            return dojo.query("#hand-florenza-card-container .florenza-card-trigger");
        },
        
        removeLocationCardConnections: function() {
        	dojo.forEach(this.locationCardClickConnections, function(connection) {
        		dojo.disconnect(connection);
        	});
        	this.disablePointerOnLocationCard();
        	this.locationCardClickConnections = [];
        },

        removeMonumentCardConnections: function() {
            dojo.forEach(this.monumentCardClickConnections, function(connection) {
                dojo.disconnect(connection);
            });
            this.disablePointerOnMonumentCard();
            this.monumentCardClickConnections = [];
        },

        removeArtistCardConnections: function() {
            dojo.forEach(this.artistCardClickConnections, function(connection) {
                dojo.disconnect(connection);
            });
            this.disablePointerOnArtistCard();
            this.artistCardClickConnections = [];
        },
        
        updateCaptain: function(captainId) {
        	dojo.destroy("captain-player");
           	var captainBoard = $('player_board_'+captainId);
           	dojo.place( this.format_block('jstpl_captain' ), captainBoard );
        },

        updateCardCounter: function(playerId, type, quantity) {
            var old = parseInt(dojo.query("#player_board_"+playerId+" ."+type+"-card-counter")[0].innerHTML, 10);
            dojo.query("#player_board_"+playerId+" ."+type+"-card-counter")[0].innerHTML = old + quantity;
        },

        updateCardCounterAll: function(playerId, cardCounter) {
            dojo.forEachObject(cardCounter, function(type, quantity) {
                dojo.query("#player_board_"+playerId+" ."+type+"-card-counter")[0].innerHTML = quantity;
            });
        },
        
        discardFlorenzaCardExcept: function(cardId) {
        	var triggerList = this.getFlorenzaCardTriggerList();
        	var keptCardId = "florenza-card-" + cardId;
            var fadeOutAndDestroy = this.fadeOutAndDestroy;
        	dojo.forEach(triggerList, function(trigger) {
        		if(trigger.id != keptCardId) {
        			fadeOutAndDestroy(trigger);
        		}
        	});
        },
        
        updateTurn: function(turnNumber) {
            $("turn-counter").className='';
        	dojo.addClass("turn-counter", 'turn-counter-' + turnNumber);
        },

        updateRound: function(roundNumber) {
            $("round-counter").className='';
            dojo.addClass("round-counter", 'round-counter-' + roundNumber);
        },
        
        enablePointerOnArtistCard: function() {
            this.getArtistCardTriggerList().addClass("clickable");
        },
        
        disablePointerOnArtistCard: function() {
        	this.getArtistCardTriggerList().removeClass("clickable");
        },
             
        enablePointerOnLocationCard: function() {
        	dojo.query(".location-card-trigger.tapped-0").addClass("clickable");
        },
        
        disablePointerOnLocationCard: function() {
        	dojo.query(".location-card-trigger.tapped-0").removeClass("clickable");        
        },       
        
        enablePointerOnFlorenzaCard: function() {
        	this.getFlorenzaCardTriggerList().addClass("clickable");
        },
        
        disablePointerOnFlorenzaCard: function() {
        	this.getFlorenzaCardTriggerList().removeClass("clickable");        
        },         

        enablePointerOnMonumentCard: function() {
            this.getMonumentCardTriggerList().addClass("clickable");
        },

        disablePointerOnMonumentCard: function() {
            this.getMonumentCardTriggerList().removeClass("clickable");
        },
        
        sortByField: function(items, field) {
            return items.sort(function(a, b) {
                if(a[field] < b[field]) return -1;
                if(a[field] > b[field]) return 1;
                return 1;
            });
        },

        sortByScorePoint: function(items) {
            return this.sortByField(items, 'scorePoint');
        },

        sortByClass: function(items) {
            return this.sortByField(items, 'class');
        },

        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
        onPlayFlorenzaCard: function( evt ) {
        	dojo.stopEvent(evt);
        	if( this.checkAction("playCard")) {
        		this.ajaxcall( '/florenzacardgame/florenzacardgame/playcard.html', { lock: true, 
				   	cardId: evt.target.id.match(/[0-9]+$/)[0]
				}, this, function() { });
        	}
        },
        
        onPlayLocationCard: function( evt ) {
        	dojo.stopEvent(evt);    
        	if( this.checkAction("playLocationCard")) {
        		this.ajaxcall( '/florenzacardgame/florenzacardgame/playlocationcard.html', { lock: true, 
				   	cardId: evt.target.id.match(/[0-9]+$/)[0]
				}, this, function() { });
        	}
        },

        onPlayMonumentCard: function( evt ) {
            dojo.stopEvent(evt); 
            if( this.checkAction("playMonumentCard")) {
                this.ajaxcall( '/florenzacardgame/florenzacardgame/playmonumentcard.html', { lock: true, 
                    cardId: evt.target.id.match(/[0-9]+$/)[0]
                }, this, function() { });
            }
        },
        
        onSendWorkers: function(evt) {
        	dojo.stopEvent(evt);
        	if( this.checkAction("sendWorkers")) {
        		this.ajaxcall( '/florenzacardgame/florenzacardgame/sendworkers.html', { lock: true 
				}, this, function() { });
        	}
        },

        onGoMarket: function(evt) {
            dojo.stopEvent(evt);
            if( this.checkAction("goToMarket")) {
                this.ajaxcall( '/florenzacardgame/florenzacardgame/gotomarket.html', { lock: true 
                }, this, function() { });
            }
        },

        onGoMarketSell: function(evt) {
            dojo.stopEvent(evt);
            if( this.checkAction("sellResourceMarket")) {
                this.ajaxcall( '/florenzacardgame/florenzacardgame/gotomarketsell.html', { lock: true 
                }, this, function() { });
            }
        },

        onGoMarketBuy: function(evt) {
            dojo.stopEvent(evt);
            if( this.checkAction("buyResourceMarket")) {
                this.ajaxcall( '/florenzacardgame/florenzacardgame/gotomarketbuy.html', { lock: true 
                }, this, function() { });
            }
        },

        onGoMarketTrade: function(evt) {
            dojo.stopEvent(evt);
            if( this.checkAction("tradeResourceMarket")) {
                this.ajaxcall( '/florenzacardgame/florenzacardgame/gotomarkettrade.html', { lock: true 
                }, this, function() { });
            }
        },

        onConfirmMarketSell: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;            
            if(this.checkAction("confirmActionMarketSell")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmactionmarketsell.html', { lock: true,
                    resource: target.id.match(/go-market-sell-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },        

        onConfirmMarketBuy: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;            
            if(this.checkAction("confirmActionMarketBuy")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmactionmarketbuy.html', { lock: true,
                    resource: target.id.match(/go-market-buy-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },

        onConfirmMarketTradeSell: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;            
            if(this.checkAction("confirmActionMarketTradeSell")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmactionmarkettradesell.html', { lock: true,
                    resource: target.id.match(/go-market-trade-sell-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },

        onConfirmMarketTradeSell2: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;            
            if(this.checkAction("confirmActionMarketTradeSell2")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmactionmarkettradesell2.html', { lock: true,
                    resource: target.id.match(/go-market-trade-sell2-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },

        onConfirmMarketTradeBuy: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;            
            if(this.checkAction("confirmActionMarketTradeBuy")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmactionmarkettradebuy.html', { lock: true,
                    resource: target.id.match(/go-market-trade-buy-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },
        
        onReserveArtist: function(evt) {
        	dojo.stopEvent(evt);
        	if( this.checkAction("reserveArtistCard")) {
        		this.ajaxcall( '/florenzacardgame/florenzacardgame/reserveartistcard.html', { lock: true 
				}, this, function() { });
        	}
        },

        onReserveMonument: function(evt) {
            dojo.stopEvent(evt);
            if( this.checkAction("reserveMonumentCard")) {
                this.ajaxcall( '/florenzacardgame/florenzacardgame/reservemonumentcard.html', { lock: true 
                }, this, function() { });
            }
        },

        onTakeInspiration: function(evt) {
            dojo.stopEvent(evt);
            if( this.checkAction("takeInspiration")) {
                this.ajaxcall( '/florenzacardgame/florenzacardgame/takeinspiration.html', { lock: true 
                }, this, function() { });
            }
        },
              
        onConfirmArtistReservation: function(evt) {
            dojo.stopEvent(evt);
            if(this.checkAction("confirmArtistReservation")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmartistreservation.html', { lock: true,
                    cardId: evt.target.id.match(/[0-9]+$/)[0]
                }, this, function() { });
            }
        },

        onConfirmMonumentReservation: function(evt) {
            dojo.stopEvent(evt);
            if(this.checkAction("confirmMonumentReservation")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmmonumentreservation.html', { lock: true,
                    cardId: evt.target.id.match(/[0-9]+$/)[0]
                }, this, function() { });
            }
        },

        onPlayArtistCard: function(evt) {
            dojo.stopEvent(evt);
            if(this.checkAction("playArtistCard")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/playartistcard.html', { lock: true,
                    cardId: evt.target.id.match(/[0-9]+$/)[0]
                }, this, function() { });
            }
        },

		onKeepFlorenzaCard: function(evt) {
			dojo.stopEvent(evt);
			if(this.checkAction("confirmKeptCard")) {
				this.ajaxcall('/florenzacardgame/florenzacardgame/confirmkeptcard.html', { lock: true,
        			cardId: evt.target.id.match(/[0-9]+$/)[0]
        		}, this, function() { });
			}
		},
		
        onConfirmResourceChoice: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;            
            if(this.checkAction("confirmResourceChoice")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmresourcechoice.html', { lock: true,
                    resource: target.id.match(/resource-choice-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },

        onConfirmMercatantiChoice: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;            
            if(this.checkAction("confirmMercatantiChoice")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmmercatantichoice.html', { lock: true,
                    resource: target.id.match(/mercatanti-choice-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },

        onConfirmBarattoChoice: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;
            if(this.checkAction("confirmBaratto")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmbaratto.html', { lock: true,
                    out: target.id.match(/baratto-choice-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },

        onConfirmBaratto2Choice: function(evt) {
            dojo.stopEvent(evt);
            var target = evt.target;
            if(target.nodeName.toLowerCase() == "span") target = target.parentNode;
            if(this.checkAction("confirmBaratto2")) {
                this.ajaxcall('/florenzacardgame/florenzacardgame/confirmbaratto2.html', { lock: true,
                    inin: target.id.match(/baratto2-choice-([a-z]+)-button/)[1]
                }, this, function() { });
            }
        },
        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your florenzacardgame.game.php file.
        
        */
        setupNotifications: function() {
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
            
            dojo.subscribe('cardPlayed', this, 'notifCardPlayed');
            dojo.subscribe('monumentPlayed', this, 'notifMonumentPlayed');
            dojo.subscribe('artistCardPlayed', this, 'notifArtistCardPlayed');
            dojo.subscribe('resourcesPaid', this, 'notifResourcesPaid');
            dojo.subscribe('scorePointAcquired', this, 'notifScorePointAcquired');
            dojo.subscribe('resourcesAcquired', this, 'notifResourcesAcquired');
            dojo.subscribe('resourcesSwapped', this, 'notifResourcesSwapped');
            dojo.subscribe('captainTokenAcquired', this, 'notifCaptainTokenAcquired');
            dojo.subscribe('actionCardPlayed', this, 'notifActionCardPlayed');
            dojo.subscribe('cardKept', this, 'notifCardKept');
            dojo.subscribe('cardDrown', this, 'notifCardDrown');
            dojo.subscribe('cardDrownOther', this, 'cardDrownOther');
            dojo.subscribe('artistReserved', this, 'notifArtistReserved');
            dojo.subscribe('artistsRefilled', this, 'notifArtistsRefilled');
            dojo.subscribe('monumentReserved', this, 'notifMonumentReserved');
            dojo.subscribe('monumentsRefilled', this, 'notifMonumentsRefilled');

            dojo.subscribe('actionStarted', this, 'notifActionStarted');
            dojo.subscribe('turnStarted', this, 'notifTurnStarted');
            dojo.subscribe('roundStarted', this, 'notifRoundStarted');
        },  
        
        notifCardPlayed: function(notif) {
        	var card = notif.args.card;
            var playerId = this.getActivePlayerId();
        	if(this.isCurrentPlayerActive()) this.destroyFlorenzaCardHandNode(card.id);
        	this.updateCardCounter(playerId, 'florenza', -1);

            var that = this;
            setTimeout(function() { //avoid id collision for tooltip
                that.createFlorenzaCardBoardNode(playerId, card);    
            }, 1000);
            
		},

        notifMonumentPlayed: function(notif) {
            var card = notif.args.card,
                playerId = this.getActivePlayerId();
            this.destroyMonumentCardBoardNode(card.id);    
            var that = this;
            setTimeout(function() {
                that.createMonumentCardBoardNode(playerId, card);
            }, 1000);
        },

        notifArtistCardPlayed: function(notif) {
            var artistCard = notif.args.card,
                playerId = this.getActivePlayerId();
            if(artistCard.anonymous == 1) { //every args is a string
                this.changeTappedArtistCardNode(artistCard.id, true);
            } else {
                this.destroyArtistCardNode(artistCard.id);
                if(artistCard.relatedCardType == 'florenza') {
                    this.createArtistCardNodeOnFlorenzaCard(playerId, artistCard);
                } else if(artistCard.relatedCardType == 'monument') {
                    this.createArtistCardNodeOnMonumentCard(playerId, artistCard);
                }
            }
        },
		
		notifResourcesPaid: function(notif) {
        	var resources = notif.args.resources,
                cardCounter = notif.args.card_counter;
            var playerId = this.getActivePlayerId();
        	if(this.isCurrentPlayerActive()) {
		    	dojo.forEachObject(resources, function(resource, quantity) {
		    		this.decreaseMyResource(resource, quantity);
		    	}, this);
		    } else {
		    	dojo.forEachObject(resources, function(resource, quantity) {
		    		this.decreasePlayerResource(playerId, resource, quantity);
		    	}, this);
		    }
            this.updateCardCounterAll(playerId, cardCounter);
		},
		
		notifScorePointAcquired: function(notif) {
			var scorePoint = notif.args.score_point,
        		playerId = notif.args.player_id || this.getActivePlayerId();
			this.scoreCtrl[playerId].incValue(scorePoint);
		},
		
		notifResourcesAcquired: function(notif) {
			var resources = notif.args.resources,
                playerId = notif.args.player_id,
                cardCounter = notif.args.card_counter;
        	if(playerId == this.player_id) {
		    	dojo.forEachObject(resources, function(resource, quantity) {
		    		this.increaseMyResource(resource, quantity);
		    	}, this);
		    } else {
		    	dojo.forEachObject(resources, function(resource, quantity) {
		    		this.increasePlayerResource(playerId, resource, quantity);
		    	}, this);
		    }
            this.updateCardCounterAll(playerId, cardCounter);
		},
		
		notifResourcesSwapped: function(notif) {
			this.notifResourcesPaid({ args: { resources: notif.args.outcomingResources, player_id : notif.args.player_id, card_counter : notif.args.card_counter } });
			this.notifResourcesAcquired({ args: { resources: notif.args.incomingResources, player_id : notif.args.player_id, card_counter : notif.args.card_counter } });
		},
		
		notifCaptainTokenAcquired: function(notif) {
			var playerId = this.getActivePlayerId();
			//TODO devo scalare le risorse disponibili
		},
		
		notifActionCardPlayed: function(notif) {
			var cardId = notif.args.card.card_id;
			dojo.replaceClass("location-card-" + cardId, "tapped-1", "tapped-0");
		},

        notifCardKept: function(notif) {
            var cardId = notif.args.card_id;
            this.discardFlorenzaCardExcept(cardId);
        },

        notifCardDrown: function(notif) {
            var card = notif.args.card;
            this.createFlorenzaCardHandNode(card);
        },

        cardDrownOther: function(notif) {
            var quantity = notif.args.card,
                playerId = notif.args.player_id;
            this.updateCardCounter(playerId, 'florenza', quantity);
        },
        
        notifArtistReserved: function(notif) {
        	var card = notif.args.card;
        	var playerId = this.getActivePlayerId();
            this.destroyArtistCardNode(card.id);
            var that = this;
            setTimeout(function() {
        	   that.createReservedArtistCardNode(playerId, card);
            }, 1000)
        },

        notifArtistsRefilled: function(notif) {
            var artistCards = notif.args.artistCards,
                anonymousArtistCard = notif.args.anonymousArtistCard,
                that = this;
            this.removeAllArtistCard();
            dojo.forEach(this.sortByClass(artistCards), function(card) {
                that.createArtistCardNode(card);
            });
            dojo.forEach(anonymousArtistCard, function(card) {
                that.createArtistCardNode(card);
            })
        },

        notifMonumentReserved: function(notif) {
            var card = notif.args.card;
            var playerId = this.getActivePlayerId();
            this.destroyMonumentCardBoardNode(card.id);
            var that = this;
            setTimeout(function() {
                that.createReservedMonumentCardNode(playerId, card);
            }, 1000);
        },

        notifMonumentsRefilled: function(notif) {
            var monumentsCards = notif.args.monumentCards,
                that = this;
            this.removeAllMonumentCard();
            dojo.forEach(this.sortByScorePoint(monumentsCards), function(card) {
                that.createMonumentCardNode(card);
            });
        },

        notifActionStarted: function(notif) {

        },

        notifTurnStarted: function(notif) {
            var turnNumber = notif.args.turnnumber;
            this.updateTurn(turnNumber);
        },

        notifRoundStarted: function(notif) {
            var roundNumber = notif.args.roundnumber,
                captainId   = notif.args.captainid;
            //untap location card
            dojo.forEach(dojo.query(".location-card-trigger"), function(cardTrigger) { 
                dojo.replaceClass(cardTrigger, "tapped-0", "tapped-1");
            });
            this.updateCaptain(captainId);
            this.updateTurn(1);
            this.updateRound(roundNumber);
        }

   });             
});
