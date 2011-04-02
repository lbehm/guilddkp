<?php
	if(!defined('intern'))
	{
		header('HTTP/1.0 404 Not Found');
		exit;
	}
	/**
	 * includes/wow_convert.php
	 * by devimplode
	 */
	$skill = array(
		171=>array(
			'title'=>'Alchemy',
			'lang'=>'Alchemie',
			'type'=>1,
			'icon'=>'trade_alchemy',
			'special'=>array(
				1=>'Meister der Elixiere',
				2=>'Meister der Tr&auml;nke',
				3=>'Meister der Transmutation'
			)
		),
		356=>array(
			'title'=>'Fishing',
			'lang'=>'Angeln',
			'type'=>0,
			'icon'=>'trade_fishing',
			'special'=>0
		),
		794=>array(
			'title'=>'Archaeology',
			'lang'=>'Arch&auml;ologie',
			'type'=>0,
			'icon'=>'trade_archaeology'
		),
		186=>array(
			'title'=>'Mining',
			'lang'=>'Bergbau',
			'type'=>1,
			'icon'=>'trade_mining'
		),
		129=>array(
			'title'=>'First Aid',
			'lang'=>'Erste Hilfe',
			'type'=>0,
			'icon'=>'spell_holy_sealofsacrifice'
		),
		202=>array(
			'title'=>'Engineering',
			'lang'=>'Ingenieurskunst',
			'type'=>1,
			'icon'=>'trade_engineering',
			'special'=>array(
				20219=>'Gnomeningenieur',
				20222=>'Gobliningenieur'
			)
		),
		773=>array(
			'title'=>'Inscription',
			'lang'=>'Inschriftenkunde',
			'type'=>1,
			'icon'=>'inv_inscription_tradeskill01'
		),
		755=>array(
			'title'=>'Jewelcrafting',
			'lang'=>'Juwelenschleifen',
			'type'=>1,
			'icon'=>'inv_misc_gem_01'
		),
		185=>array(
			'title'=>'Cooking',
			'lang'=>'Kochkunst',
			'type'=>0,
			'icon'=>'inv_misc_food_15'
		),
		182=>array(
			'title'=>'Herbalism',
			'lang'=>'Kr&auml;uterkunde',
			'type'=>1,
			'icon'=>'spell_nature_naturetouchgrow'
		),
		393=>array(
			'title'=>'Skinning',
			'lang'=>'K&uuml;rschnerei',
			'type'=>1,
			'icon'=>'inv_misc_pelt_wolf_01'
		),
		165=>array(
			'title'=>'Leatherworking',
			'lang'=>'Lederverarbeitung',
			'type'=>1,
			'icon'=>'inv_misc_armorkit_17',
			'special'=>array(
				10656=>'Drachenschuppenlederverarbeitung',
				10658=>'Elementarlederverarbeitung',
				10660=>'Stammeslederverarbeitung'
			)
		),
		164=>array(
			'title'=>'Blacksmithing',
			'lang'=>'Schmiedekunst',
			'type'=>1,
			'icon'=>'trade_blacksmithing',
			'special'=>array(
				9787=>'Waffenschmied',
				9788=>'R&uuml;stungsschmied',
				17039=>'Schwertschmiedemeister',
				17040=>'Hammerschmiedemeister',
				17041=>'Axtschmiedemeister'
			)
		),
		197=>array(
			'title'=>'Tailoring',
			'lang'=>'Schneiderei',
			'type'=>1,
			'icon'=>'trade_tailoring',
			'special'=>array(
				26797=>'Zauberfeuerschneiderei',
				26798=>'Mondstoffschneiderei',
				26801=>'Schattenzwirnschneiderei'
			)
		),
		333=>array(
			'title'=>'Enchanting',
			'lang'=>'Verzauberkunst',
			'type'=>1,
			'icon'=>'trade_engraving'
		)
	);
	$classes = array(
		1 => array(
			'name'=>'Krieger',
			'icon'=>'class_warrior',
			'roles'=>array(
				'tank'=>true,
				'dd'=>true,
				'melee'=>true,
				'range'=>false,
				'heal'=>false
			),
			1=>array(
				'name'=>'Waffen',
				'icon'=>'ability_rogue_eviscerate'
			),
			2=>array(
				'name'=>'Furor',
				'icon'=>'ability_warrior_innerrage'
			),
			3=>array(
				'name'=>'Schutz',
				'icon'=>'inv_shield_06'
			)
		),
		2 => array(
			'name'=>'Paladin',
			'icon'=>'class_paladin',
			'roles'=>array(
				'tank'=>true,
				'dd'=>true,
				'melee'=>true,
				'range'=>false,
				'heal'=>true
			),
			1=>array(
				'name'=>'Heilig',
				'icon'=>'spell_holy_holybolt'
			),
			2=>array(
				'name'=>'Schutz',
				'icon'=>'spell_holy_devotionaura'
			),
			3=>array(
				'name'=>'Vergelter',
				'icon'=>'spell_holy_auraoflight'
			)
		),
		3 => array(
			'name'=>'J&auml;ger',
			'icon'=>'class_hunter',
			'roles'=>array(
				'tank'=>false,
				'dd'=>true,
				'melee'=>false,
				'range'=>true,
				'heal'=>false
			),
			1=>array(
				'name'=>'Tierherrschaft',
				'icon'=>'ability_hunter_beasttaming'
			),
			2=>array(
				'name'=>'Treffsicherheit',
				'icon'=>'ability_marksmanship'
			),
			3=>array(
				'name'=>'&Uuml;berleben',
				'icon'=>'ability_hunter_swiftstrike'
			)
		),
		4 => array(
			'name'=>'Schurke',
			'icon'=>'class_rogue',
			'roles'=>array(
				'tank'=>false,
				'dd'=>true,
				'melee'=>true,
				'range'=>false,
				'heal'=>false
			),
			1=>array(
				'name'=>'Meucheln',
				'icon'=>'ability_rogue_eviscerate'
			),
			2=>array(
				'name'=>'Kampf',
				'icon'=>'ability_backstab'
			),
			3=>array(
				'name'=>'T&auml;uschung',
				'icon'=>'ability_stealth'
			)
		),
		5 => array(
			'name'=>'Priester',
			'icon'=>'class_priest',
			'roles'=>array(
				'tank'=>false,
				'dd'=>true,
				'melee'=>false,
				'range'=>true,
				'heal'=>true
			),
			1=>array(
				'name'=>'Disziplin',
				'icon'=>'spell_holy_holybolt'
			),
			2=>array(
				'name'=>'Heilig',
				'icon'=>'spell_holy_wordfortitude'
			),
			3=>array(
				'name'=>'Schatten',
				'icon'=>'spell_shadow_shadowwordpain'
			)
		),
		6 => array(
			'name'=>'Todesritter',
			'icon'=>'class_deathknight',
			'roles'=>array(
				'tank'=>true,
				'dd'=>true,
				'melee'=>true,
				'range'=>false,
				'heal'=>false
			),
			1=>array(
				'name'=>'Blut',
				'icon'=>'spell_deathknight_bloodpresence'
			),
			2=>array(
				'name'=>'Frost',
				'icon'=>'spell_deathknight_frostpresence'
			),
			3=>array(
				'name'=>'Unheilig',
				'icon'=>'spell_deathknight_unholypresence'
			)
		),
		7 => array(
			'name'=>'Schamane',
			'icon'=>'class_shaman',
			'roles'=>array(
				'tank'=>false,
				'dd'=>true,
				'melee'=>true,
				'range'=>true,
				'heal'=>true
			),
			1=>array(
				'name'=>'Elementar',
				'icon'=>'spell_nature_lightning'
			),
			2=>array(
				'name'=>'Verst&auml;rker',
				'icon'=>'spell_nature_lightningshield'
			),
			3=>array(
				'name'=>'Wiederherstellung',
				'icon'=>'spell_nature_magicimmunity'
			)
		),
		8 => array(
			'name'=>'Magier',
			'icon'=>'class_mage',
			'roles'=>array(
				'tank'=>false,
				'dd'=>true,
				'melee'=>false,
				'range'=>true,
				'heal'=>false
			),
			1=>array(
				'name'=>'Arkan',
				'icon'=>'spell_holy_magicalsentry'
			),
			2=>array(
				'name'=>'Feuer',
				'icon'=>'spell_fire_firebolt02'
			),
			3=>array(
				'name'=>'Frost',
				'icon'=>'spell_frost_frostbolt02'
			)
		),
		9 => array(
			'name'=>'Hexenmeister',
			'icon'=>'class_warlock',
			'roles'=>array(
				'tank'=>false,
				'dd'=>true,
				'melee'=>false,
				'range'=>true,
				'heal'=>false
			),
			1=>array(
				'name'=>'Gebrechen',
				'icon'=>'spell_shadow_deathcoil'
			),
			2=>array(
				'name'=>'D&auml;monologie',
				'icon'=>'spell_shadow_metamorphosis'
			),
			3=>array(
				'name'=>'Zerst&ouml;rung',
				'icon'=>'spell_shadow_rainoffire'
			)
		),
		11 => array(
			'name'=>'Druide',
			'icon'=>'class_druid',
			'roles'=>array(
				'tank'=>true,
				'dd'=>true,
				'melee'=>true,
				'range'=>true,
				'heal'=>true
			),
			1=>array(
				'name'=>'Gleichgewicht',
				'icon'=>'spell_nature_starfall'
			),
			2=>array(
				'name'=>'Wilder Krampf',
				'icon'=>'ability_racial_bearform'
			),
			3=>array(
				'name'=>'Wiederherstellung',
				'icon'=>'spell_nature_healingtouch'
			)
		)
	);
	$races = array(
		1=>array(
			'title'=>'Human',
			'lang'=>'Mensch',
			'side'=>0,
			'faction'=>72,
			'icon_m'=>'race_human_male',
			'icon_w'=>'race_human_female',
			'classes'=>array(
				1=>&$classes[1],
				2=>&$classes[2],
				3=>&$classes[3],
				4=>&$classes[4],
				5=>&$classes[5],
				6=>&$classes[6],
				9=>&$classes[9]
			)
		)
	);
	$reputation = array(
		0=>"Hasserf&uuml;llt",
		1=>"Feindselig",
		2=>"Unfreundlich",
		3=>"Neutral",
		4=>"Freundlich",
		5=>"Wohlwollend",
		6=>"Respektvoll",
		7=>"Ehrf&uuml;rchtig"
	);
	$factions = array(
		47 => "Eisenschmiede",
		54 => "Gnomeregangnome",
		59 => "Thoriumbruderschaft",
		67 => "Horde",
		68 => "Unterstadt",
		69 => "Darnassus",
		72 => "Sturmwind",
		76 => "Orgrimmar",
		81 => "Donnerfels",
		270 => "Stamm der Zandalari",
		529 => "Argentumd&auml;mmerung",
		530 => "Dunkelspeertrolle",
		576 => "Holzschlundfeste",
		609 => "Zirkel des Cenarius",
		910 => "Brut Nozdormus",
		911 => "Silbermond",
		922 => "Tristessa",
		930 => "Die Exodar",
		932 => "Die Aldor",
		933 => "Das Konsortium",
		934 => "Die Seher",
		935 => "Die Sha'tar",
		941 => "Die Mag'har",
		942 => "Expedition des Cenarius",
		946 => "Ehrenfeste",
		947 => "Thrallmar",
		967 => "Das Violette Auge",
		970 => "Sporeggar",
		978 => "Kurenai",
		989 => "H&uuml;ter der Zeit",
		990 => "Die W&auml;chter der Sande",
		1011 => "Unteres Viertel",
		1012 => "Die Todesh&ouml;rigen",
		1015 => "Netherschwingen",
		1031 => "Himmelswache der Sha'tari",
		1037 => "Vorposten der Allianz",
		1038 => "Ogri'la",
		1052 => "Expedition der Horde",
		1073 => "Die Kalu'ak",
		1077 => "Offensive der Zerschmetterten Sonne",
		1090 => "Kirin Tor",
		1091 => "Der Wyrmruhpakt",
		1098 => "Ritter der Schwarzen Klinge",
		1104 => "Stamm der Wildherzen",
		1105 => "Die Orakel",
		1106 => "Argentumkreuzzug",
		1119 => "Die S&ouml;hne Hodirs",
		1156 => "Das &auml;scherne Verdikt"
	);
	$zones=array(
		'Icecrown Citadel'=>array(
			'name'=>'Eiskronenzitadelle',
			'zone_id'=>'4812',
			'difficulty'=>array(
				1=>array(
					'name'=>'Eiskronenzitadelle',
					'short'=>'ICC10',
					'creatures'=>array(
						36612=>array(
							'name'=>'Lord Marrowgar',
							'name_de'=>'Lord Marrowgar',
							'type'=>'boss',
							'dkp'=>'10'
						),
						36855=>array(
							'name'=>'Lady Deathwhisper',
							'name_de'=>'Lady Deathwhisper',
							'type'=>'boss',
							'dkp'=>'10'
						),
						37813=>array(
							'name'=>'Deathbringer Saurfang',
							'name_de'=>'Deathbringer Saurfang',
							'type'=>'boss',
							'dkp'=>'10'
						),
					)
				),
				2=>array(
					'name'=>'Eiskronenzitadelle - Heroisch',
					'short'=>'ICC10-HM',
					'creatures'=>array(
						36612=>array(
							'name'=>'Lord Marrowgar',
							'name_de'=>'Lord Marrowgar',
							'type'=>'boss',
							'dkp'=>'15'
						),
						36855=>array(
							'name'=>'Lady Deathwhisper',
							'name_de'=>'Lady Deathwhisper',
							'type'=>'boss',
							'dkp'=>'15'
						),
						37813=>array(
							'name'=>'Deathbringer Saurfang',
							'name_de'=>'Deathbringer Saurfang',
							'type'=>'boss',
							'dkp'=>'15'
						),
					)
				),
				3=>array(
					'name'=>'Eiskronenzitadelle - 25',
					'short'=>'ICC25',
					'creatures'=>array(
						36612=>array(
							'name'=>'Lord Marrowgar',
							'name_de'=>'Lord Marrowgar',
							'type'=>'boss',
							'dkp'=>'25'
						),
						36855=>array(
							'name'=>'Lady Deathwhisper',
							'name_de'=>'Lady Deathwhisper',
							'type'=>'boss',
							'dkp'=>'25'
						),
						37813=>array(
							'name'=>'Deathbringer Saurfang',
							'name_de'=>'Deathbringer Saurfang',
							'type'=>'boss',
							'dkp'=>'25'
						),
					)
				),
				4=>array(
					'name'=>'Eiskronenzitadelle - 25 Heroisch',
					'short'=>'ICC25-HM',
					'creatures'=>array(
						36612=>array(
							'name'=>'Lord Marrowgar',
							'name_de'=>'Lord Marrowgar',
							'type'=>'boss',
							'dkp'=>'25'
						),
						36855=>array(
							'name'=>'Lady Deathwhisper',
							'name_de'=>'Lady Deathwhisper',
							'type'=>'boss',
							'dkp'=>'25'
						),
						37813=>array(
							'name'=>'Deathbringer Saurfang',
							'name_de'=>'Deathbringer Saurfang',
							'type'=>'boss',
							'dkp'=>'25'
						),
					)
				),
			),
		),
		'Trial of the Crusader'=>array(
			'name'=>'Pr&uuml;fung des Kreuzfahrers',
			'zone_id'=>'4722',
			'difficulty'=>array(
				1=>array(
					'name'=>'Pr&uuml;fung des Kreuzfahrers',
					'short'=>'PdK10',
					'creatures'=>array(
						34797=>array(
							'name'=>'Icehowl',
							'name_de'=>'Icehowl',
							'type'=>'boss',
							'dkp'=>'0'
						),
						34780=>array(
							'name'=>'Lord Jaraxxus',
							'name_de'=>'Lord Jaraxxus',
							'type'=>'boss',
							'dkp'=>'0'
						),
						34459=>array(
							'name'=>'Erin Misthoof',//Faction-Champions
							'name_de'=>'Erin Misthoof',
							'type'=>'boss',
							'dkp'=>'0'
						),
						34497=>array(
							'name'=>'Fjola Lightbane',
							'name_de'=>'Fjola Lightbane',
							'type'=>'boss',
							'dkp'=>'0'
						),
						34564=>array(
							'name'=>'Anub\'arak',
							'name_de'=>'Anub\'arak',
							'type'=>'boss',
							'dkp'=>'0'
						),
					)
				),
				2=>array(
					'name'=>'Pr&uuml;fung des obersten Kreuzfahrers',
					'short'=>'PdoK10',
					'creatures'=>array(
						34797=>array(
							'name'=>'Icehowl',
							'name_de'=>'Icehowl',
							'type'=>'boss',
							'dkp'=>'5'
						),
						34780=>array(
							'name'=>'Lord Jaraxxus',
							'name_de'=>'Lord Jaraxxus',
							'type'=>'boss',
							'dkp'=>'5'
						),
						34459=>array(
							'name'=>'Erin Misthoof',//Faction-Champions
							'name_de'=>'Erin Misthoof',
							'type'=>'boss',
							'dkp'=>'5'
						),
						34497=>array(
							'name'=>'Fjola Lightbane',
							'name_de'=>'Fjola Lightbane',
							'type'=>'boss',
							'dkp'=>'5'
						),
						34564=>array(
							'name'=>'Anub\'arak',
							'name_de'=>'Anub\'arak',
							'type'=>'boss',
							'dkp'=>'5'
						),
					)
				),
				3=>array(
					'name'=>'Pr&uuml;fung des Kreuzfahrers 25',
					'short'=>'PdK25',
					'creatures'=>array(
						34797=>array(
							'name'=>'Icehowl',
							'name_de'=>'Icehowl',
							'type'=>'boss',
							'dkp'=>'0'
						),
						34780=>array(
							'name'=>'Lord Jaraxxus',
							'name_de'=>'Lord Jaraxxus',
							'type'=>'boss',
							'dkp'=>'0'
						),
						34459=>array(
							'name'=>'Erin Misthoof',//Faction-Champions
							'name_de'=>'Erin Misthoof',
							'type'=>'boss',
							'dkp'=>'0'
						),
						34497=>array(
							'name'=>'Fjola Lightbane',
							'name_de'=>'Fjola Lightbane',
							'type'=>'boss',
							'dkp'=>'0'
						),
						34564=>array(
							'name'=>'Anub\'arak',
							'name_de'=>'Anub\'arak',
							'type'=>'boss',
							'dkp'=>'0'
						),
					)
				),
				4=>array(
					'name'=>'Pr&uuml;fung des obersten Kreuzfahrers 25',
					'short'=>'PdoK25',
					'creatures'=>array(
						34797=>array(
							'name'=>'Northrend Beasts',
							'name_de'=>'Icehowl',
							'type'=>'boss',
							'dkp'=>'15'
						),
						34780=>array(
							'name'=>'Lord Jaraxxus',
							'name_de'=>'Lord Jaraxxus',
							'type'=>'boss',
							'dkp'=>'15'
						),
						34459=>array(
							'name'=>'Erin Misthoof',//Faction-Champions
							'name_de'=>'Erin Misthoof',
							'type'=>'boss',
							'dkp'=>'15'
						),
						34497=>array(
							'name'=>'Fjola Lightbane',
							'name_de'=>'Fjola Lightbane',
							'type'=>'boss',
							'dkp'=>'15'
						),
						34564=>array(
							'name'=>'Anub\'arak',
							'name_de'=>'Anub\'arak',
							'type'=>'boss',
							'dkp'=>'15'
						),
					)
				),
			),
		),
		'Vault of Archavon'=>array(
			'name'=>'Archavons Kammer',
			'zone_id'=>'4603',
			'difficulty'=>array(
				1=>array(
					'name'=>'Archavons Kammer',
					'short'=>'AK10',
					'creatures'=>array(
						31125=>array(
							'name'=>'Archavon the Stone Watcher',
							'name_de'=>'Archavon the Stone Watcher',
							'type'=>'boss',
							'dkp'=>'0'
						),
						33993=>array(
							'name'=>'Emalon the Storm Watcher',
							'name_de'=>'Emalon the Storm Watcher',
							'type'=>'boss',
							'dkp'=>'0'
						),
						35013=>array(
							'name'=>'Koralon the Flame Watcher',
							'name_de'=>'Koralon the Flame Watcher',
							'type'=>'boss',
							'dkp'=>'0'
						),
						38433=>array(
							'name'=>'Toravon the Ice Watcher',
							'name_de'=>'Toravon the Ice Watcher',
							'type'=>'boss',
							'dkp'=>'0'
						),
					)
				),
				2=>array(
					'name'=>'Archavons Kammer 25',
					'short'=>'AK25',
					'creatures'=>array(
						31125=>array(
							'name'=>'Archavon the Stone Watcher',
							'name_de'=>'Archavon the Stone Watcher',
							'type'=>'boss',
							'dkp'=>'0'
						),
						33993=>array(
							'name'=>'Emalon the Storm Watcher',
							'name_de'=>'Emalon the Storm Watcher',
							'type'=>'boss',
							'dkp'=>'0'
						),
						35013=>array(
							'name'=>'Koralon the Flame Watcher',
							'name_de'=>'Koralon the Flame Watcher',
							'type'=>'boss',
							'dkp'=>'5'
						),
						38433=>array(
							'name'=>'Toravon the Ice Watcher',
							'name_de'=>'Toravon the Ice Watcher',
							'type'=>'boss',
							'dkp'=>'25'
						),
					)
				),
			),
		)
	);
?>
