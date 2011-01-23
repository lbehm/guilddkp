{include file="header.html"}
<div class="user">
{if $error == "user"}
User nicht gefunden
{else}
	<div class="user_headline">
    	Profil - {$username}
    </div>
    <div class="user_image">
    	<img src="{$user_icon}" width="160" height="160" alt="" />
    </div>
    
    <div class="user_charinfo">
    {if $error == "char"}
    Kein Charakter gefunden
    {else}
    	<div class="user_charinfo_image">
        	<img src="templates/clean/images/race/{$char_race}-{$char_gender}.jpg" width="370" height="225" alt="" />
        </div>
       	<div class="user_charinfo_name">
        	{$char_name}<br /> &lt;{$char_guild}&gt;
        </div>
        <div class="user_charinfo_hp">
        	{$char_hp}
        </div>
        <div class="user_charinfo_profession">
        	<table border="0" cellspacing="0" cellpadding="0" height="24px">
            <td width="90%" nowrap="nowrap">
            <div class="user_charinfo_profession_skill_image" style="width:{$char_prof1_percent}%">
           		<div class="user_charinfo_profession_image">
        			<img src="templates/clean/images/profession/{$char_prof1_image}.jpg" width="24" height="24" />
        		</div>
                <span class="user_charinfo_profession_name">{$char_prof1_name}</span>
     	  	</div>
            
            </td>
            <td width="10%">
            <div class="user_charinfo_profession_skill">
        			{$char_prof1_value}
        	</div>
            </td>
         </table>
         </div>
		 {if $char_bar_k == "mana"} 
     	 <div class="user_charinfo_mana">
         {/if}
         {if $char_bar_k == "rage"}
         <div class="user_charinfo_rage">
         {/if}
         {if $char_bar_k == "energy"}
         <div class="user_charinfo_energy">
         {/if}
         {if $char_bar_k == "runepower"}
         <div class="user_charinfo_runepower">
         {/if}
         {if $char_bar_k == "focus"}
         <div class="user_charinfo_focus">
         {/if}
     	   	{$char_bar_v}
    	</div>
		
        <div class="user_charinfo_profession">
        	<table border="0" cellspacing="0" cellpadding="0" height="24px">
            <td width="90%" nowrap="nowrap">
            <div class="user_charinfo_profession_skill_image" style="width:{$char_prof2_percent}%">
           		<div class="user_charinfo_profession_image">
        			<img src="templates/clean/images/profession/{$char_prof2_image}.jpg" width="24" height="24" />
        		</div>
                   <span class="user_charinfo_profession_name">{$char_prof2_name}</span>
     	  	</div>
            </td>
            <td width="10%">
            <div class="user_charinfo_profession_skill">
        			{$char_prof2_value}
        	</div>
            </td>
         </table>
       	</div>
        <div class="user_charinfo_talents1">
        	<div class="user_charinfo_talents_image">
            	<img src="templates/clean/images/talents/{$char_talents1_image}.png" width="32"	height="32" />
            </div>
            <div class="user_charinfo_talents_name">
            	{$char_talents1_name}
            </div>
            <div class="user_charinfo_talents_talents">
            	{$char_talents1_talents}
            </div>
        </div>
        <div class="user_charinfo_talents2">
        	<div class="user_charinfo_talents_image">
            	<img src="templates/clean/images/talents/{$char_talents2_image}.png" width="32"	height="32" />
            </div>
            <div class="user_charinfo_talents_name">
            	{$char_talents2_name}
            </div>
            <div class="user_charinfo_talents_talents">
            	{$char_talents2_talents}
            </div>        
        </div>
        <div class="user_charinfo_arena" style="margin-left:4px;float:left;">
        	2vs2 <br /> {$char_2vs2}
        </div>
        <div class="user_charinfo_arena" style="margin-right:4px;float:right;">
        	5vs5 <br /> {$char_3vs3}
        </div> 
        <div class="user_charinfo_arena">
        	3vs3 <br /> {$char_5vs5}
        </div>    
        {/if}        
    </div>
    <div class="user_contact">
    	<table border="0" cellspacing="0" cellpadding="4px">
        <colgroup><col width="50%" /><col width="50%" /></colgroup>
    		<tr><td>Email-Adresse</td><td>{$user_email}</td></tr>
       		<tr><td>Geschlecht</td><td>{$user_gender}</td></tr>
       	 	<tr><td>Geburtstag</td><td>{$user_bday}</td></tr>
      	  	<tr><td>Name</td><td>{$user_firstname} {$user_lastname}</td></tr>
        	<tr><td>Wohnort</td><td>{$user_town}</td></tr>
        	<tr><td>Bundesland</td><td>{$user_state}</td></tr>
    		<tr><td>Land</td><td>{$user_country}</td></tr>
        	<tr><td>Facebook</td><td>{$user_facebook}</td></tr>
        	<tr><td>ICQ</td><td>{$user_icq}</td></tr>
        	<tr><td>Skype</td><td>{$user_skype}</td></tr>
        	<tr><td>MSN</td><td>{$user_msn}</td></tr>
       </table>
    </div>
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
{/if}
</div>
{include file="footer.html"}"}