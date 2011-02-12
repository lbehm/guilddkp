{include file="header.html"}
<div class="userPage">
{if $userPage.error == "user"}
User nicht gefunden
{else}
	<div class="user_headline">
		Profil - {$userPage.info.username}
	</div>
	<img class="user_image" src="{$userPage.info.user_icon}" width="160" height="160" alt="" />

	{if $LOGIN}
	<div class="user_contact">
		<table border="0" cellspacing="0" cellpadding="4px">
			<tr><td>Name</td><td>{$userPage.info.firstname} {$userPage.info.lastname}</td></tr>
			{if $userPage.info.gender}<tr><td>Geschlecht</td><td>{if $userPage.info.gender == 1}Männlich{elseif $userPage.info.gender == 2}Weiblich{/if}</td></tr>{/if}
			{if $userPage.info.bday}<tr><td>Geburtstag</td><td>{$userPage.info.bday}</td></tr>{/if}
			{if $userPage.info.town}<tr><td>Wohnort</td><td>{$userPage.info.town}</td></tr>{/if}
			{if $userPage.info.country}<tr><td>Land</td><td>{$userPage.info.country}{if $userPage.info.state} - {$userPage.info.state}{/if}</td></tr>{/if}
			{if $userPage.info.facebook}<tr><td>Facebook</td><td><a href="http://www.facebook.com/profile.php?id={$userPage.info.facebook}" target="_blank">Zum Profil</a></td></tr>{/if}
			{if $userPage.info.icq}<tr><td>ICQ</td><td><img src="http://status.icq.com/online.gif?icq={$userPage.info.icq}&img=5">{$userPage.info.icq}</td></tr>{/if}
			{if $userPage.info.skype}<tr><td>Skype</td><td><a href="skype:{$userPage.info.skype}?call"><img src="http://mystatus.skype.com/smallclassic/{$userPage.info.skype}" style="border: none;" width="114" height="20" alt="{$userPage.info.skype}" /></a></td></tr>{/if}
			{if $userPage.info.msn}<tr><td>MSN</td><td>{$userPage.info.msn}</td></tr>{/if}
		</table>
	</div>
	{/if}
	{if $userPage.error != "char"}
	<div class="user_charinfo">
		<div class="user_charinfo_image">
			<img src="templates/clean/images/race/{$userPage.char_detail.char_race}-{$userPage.char_detail.char_gender}.jpg" width="370" height="225" alt="" />
		</div>
		<div class="user_charinfo_name">
			<span class="charname">{$userPage.char_detail.char_name}</span><br /><span class="charguild">&lt;{$userPage.char_detail.char_guild}&gt;</span>
		</div>
		<span class="user_achiev_points">{$userPage.char_detail.char_achievments}</span>
		<span class="user_char_class" style="background-image: url('{$icon_repo_tiny}{$userPage.char_detail.char_class_icon}.gif');">{$userPage.char_detail.char_class_name}</span>
		<table>
			<tr>
				<td class="user_charinfo_hp">{$userPage.char_detail.char_hp}</td>
				<td class="user_charinfo_profession">
					<div class="user_charinfo_profession_name" style="background-size: {$userPage.char_detail.char_prof1_percent}% 100%;">
						<img src="{$icon_repo}{$userPage.char_detail.char_prof1_image}.jpg"/>
						{$userPage.char_detail.char_prof1_lang}{if $userPage.char_detail.char_prof1_value}<span class="user_charinfo_profession_skill">{$userPage.char_detail.char_prof1_value}</span>{/if}
					</div>
				</td>
			</tr>
			<tr>
				<td class="user_charinfo_bar {$userPage.char_detail.char_bar_k}">{$userPage.char_detail.char_bar_v}</td>
				<td class="user_charinfo_profession">
					<div class="user_charinfo_profession_name" style="background-size: {$userPage.char_detail.char_prof2_percent}% 100%;">
						<img src="{$icon_repo}{$userPage.char_detail.char_prof2_image}.jpg"/>
						{$userPage.char_detail.char_prof2_lang}{if $userPage.char_detail.char_prof2_value}<span class="user_charinfo_profession_skill">{$userPage.char_detail.char_prof2_value}</span>{/if}
					</div>
				</td>
			</tr>
			<tr>
				<td class="user_charinfo_talents1">
					<div class="user_charinfo_talents_image talent_frame">
						<img src="{$icon_repo}{$userPage.char_detail.char_talents1_image}.jpg" width="32" height="32" />
					</div>
					<div class="user_charinfo_talents_name">
						{$userPage.char_detail.char_talents1_name}
					</div>
					<div class="user_charinfo_talents_talents">
						{$userPage.char_detail.char_talents1_talents}
					</div>
				</td>
				<td class="user_charinfo_talents2">
					<div class="user_charinfo_talents_image talent_frame">
						<img src="{$icon_repo}{$userPage.char_detail.char_talents2_image}.jpg" width="32" height="32" />
					</div>
					<div class="user_charinfo_talents_name">
						{$userPage.char_detail.char_talents2_name}
					</div>
					<div class="user_charinfo_talents_talents">
						{$userPage.char_detail.char_talents2_talents}
					</div>        
				</td>
			</tr>
		</table>
	<!--	<div class="user_charinfo_arena two">
			2vs2 <br /> {$userPage.char_detail.char_2vs2}
		</div>
		<div class="user_charinfo_arena three">
			3vs3 <br /> {$userPage.char_detail.char_3vs3}
		</div>    
		<div class="user_charinfo_arena five">
			5vs5 <br /> {$userPage.char_detail.char_5vs5}
		</div>
	-->
	{/if}        
	</div>
	<!--
	<table class="user_loot" cellpadding="4px" border="1px">
		<tr><th align="center" nowrap="nowrap" colspan="5">Neuste Gegenst&auml;nde</th></tr>
		<tr><th width="60px" nowrap="nowrap">Datum</th><th>Gegenstand</th><th width="50px" nowrap="nowrap">ausgegeben</th></tr>
		<tr><td>1.1.1970</td><td><img src="http://static.wowhead.com/images/wow/icons/medium/inv_misc_trophy_argent.jpg" width="20" height="20" /> Klob&uuml;rste des Todes</td><td>150</td></tr>
		<tr><td>1.1.1970</td><td><img src="http://static.wowhead.com/images/wow/icons/medium/inv_misc_trophy_argent.jpg" width="20" height="20" /> Klob&uuml;rste des Todes</td><td>150</td></tr>
		<tr><td>1.1.1970</td><td><img src="http://static.wowhead.com/images/wow/icons/medium/inv_misc_trophy_argent.jpg" width="20" height="20" /> Klob&uuml;rste des Todes</td><td>150</td></tr>
		<tr><td>1.1.1970</td><td><img src="http://static.wowhead.com/images/wow/icons/medium/inv_misc_trophy_argent.jpg" width="20" height="20" /> Klob&uuml;rste des Todes</td><td>150</td></tr>
		<tr><td>1.1.1970</td><td><img src="http://static.wowhead.com/images/wow/icons/medium/inv_misc_trophy_argent.jpg" width="20" height="20" /> Klob&uuml;rste des Todes</td><td>150</td></tr>
	</table>
	<table class="user_raids" cellpadding="4px" border="1px">
		<tr><th align="center" nowrap="nowrap" colspan="5">Letzte Raids</th></tr>
		<tr><th width="60px" nowrap="nowrap">Datum</th><th>Instanz</th><th width="50px" nowrap="nowrap">bekommen</th></tr>
		<tr><td>21.12.2012</td><td><img src="http://requiem.chronotex.de/games/WoW/events/Icon-Onyxia.gif" width="20" height="20" /> Just another Dragon...</td><td>250</td></tr>
		<tr><td>21.12.2012</td><td><img src="http://requiem.chronotex.de/games/WoW/events/Icon-Onyxia.gif" width="20" height="20" /> Just another Dragon...</td><td>250</td></tr>
		<tr><td>21.12.2012</td><td><img src="http://requiem.chronotex.de/games/WoW/events/Icon-Onyxia.gif" width="20" height="20" /> Just another Dragon...</td><td>250</td></tr>
		<tr><td>21.12.2012</td><td><img src="http://requiem.chronotex.de/games/WoW/events/Icon-Onyxia.gif" width="20" height="20" /> Just another Dragon...</td><td>250</td></tr>
		<tr><td>21.12.2012</td><td><img src="http://requiem.chronotex.de/games/WoW/events/Icon-Onyxia.gif" width="20" height="20" /> Just another Dragon...</td><td>250</td></tr>
	</table>
	-->
{/if}
</div>
{include file="footer.html"}"}