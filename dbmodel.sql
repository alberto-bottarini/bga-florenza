
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- FlorenzaCardGame implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- Example 1: create a standard "card" table to be used with the "Deck" tools (see example game "hearts"):

-- CREATE TABLE IF NOT EXISTS `card` (
--   `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `card_type` varchar(16) NOT NULL,
--   `card_type_arg` int(11) NOT NULL,
--   `card_location` varchar(16) NOT NULL,
--   `card_location_arg` int(11) NOT NULL,
--   PRIMARY KEY (`card_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- Example 2: add a custom field to the standard "player" table
-- ALTER TABLE `player` ADD `player_my_custom_field` INT UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `player` ADD `captain` int(1) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `player_resources` (
	`player_id` int(10) unsigned NOT NULL,
	`marble` int(2) NOT NULL,
	`wood` int(2) NOT NULL,
	`metal` int(2) NOT NULL,
	`fabric` int(2) NOT NULL,
	`gold` int(2) NOT NULL,
	`spice` int(2) NOT NULL,
	`money` int(3) NOT NULL,
	`captain_token` int(1) NOT NULL,
	PRIMARY KEY (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `florenza_card` (
	`card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(30) NOT NULL,
	`class` varchar(20) NOT NULL,
	`card_order` int(3) NOT NULL,
	`round` int(1) NOT NULL,
	`location` varchar(10) NOT NULL,
	`player_id` int(10),
	PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `location_card` (
	`card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(20) NOT NULL,
	`tapped` int(1) NOT NULL,
	`card_order` int(2) NOT NULL,
	PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `artist_card` (
	`card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(50) NOT NULL,
	`class` varchar(20) NOT NULL,
	`score_point` int(2) NOT NULL,
	`location` varchar(10) NOT NULL,
	`related_card_id` int(10),
	`related_card_type` varchar(10),
	`player_id` int(10),
	`card_order` int(2) NOT NULL,
	`anonymous` int(1),
	PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `monument_card` (
	`card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(50) NOT NULL,
	`score_point` int(2) NOT NULL,
	`location` varchar(10) NOT NULL,
	`player_id` int(10),
	`card_order` int(2) NOT NULL,
	PRIMARY KEY (`card_id`)	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;