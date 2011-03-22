{include file="header.html"}
<div class="userPage">
{if $userPage.error == "user"}
User nicht gefunden
{else}
	<img class="user icon user_image" src="{$userPage.info.user_icon}" alt="" />
	<div class="user_headline">
		Profil - {$userPage.info.username}
	</div>

	{if $LOGIN}
	<div class="user_contact">
		<table border="0" cellspacing="0" cellpadding="4px">
			<tr><th>Name</th><td>{$userPage.info.firstname} {$userPage.info.lastname}</td></tr>
			{if $userPage.info.gender}<tr><th>Geschlecht</th><td>{if $userPage.info.gender == 1}M&auml;nnlich{elseif $userPage.info.gender == 2}Weiblich{/if}</td></tr>{/if}
			{if $userPage.info.bday}<tr><th>Geburtstag</th><td>{$userPage.info.bday}</td></tr>{/if}
			{if $userPage.info.town}<tr><th>Wohnort</th><td>{$userPage.info.town}</td></tr>{/if}
			{if $userPage.info.country}<tr><th>Land</th><td>{$userPage.info.country}{if $userPage.info.state} - {$userPage.info.state}{/if}</td></tr>{/if}
			{if $userPage.info.facebook}<tr><th>Facebook</th><td><a class="fb" href="http://www.facebook.com/profile.php?id={$userPage.info.facebook}" target="_blank">Zum Profil</a></td></tr>{/if}
			{if $userPage.info.icq}<tr><th>ICQ</th><td><img src="http://status.icq.com/online.gif?icq={$userPage.info.icq}&img=5">{$userPage.info.icq}</td></tr>{/if}
			{if $userPage.info.skype}<tr><th>Skype</th><td><a href="skype:{$userPage.info.skype}?call"><img src="http://mystatus.skype.com/smallclassic/{$userPage.info.skype}" style="border: none;" width="114" height="20" alt="{$userPage.info.skype}" /></a></td></tr>{/if}
			{if $userPage.info.msn}<tr><th>MSN</th><td>{$userPage.info.msn}</td></tr>{/if}
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
	</div>
	{/if}        
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
	<div class="comments_div">
		<script type="text/javascript" >
			{literal}
			var user_last_id = 0;
			var user_busy = false;
			function comments_refresh()
			{
				if(user_busy)
					return;
				user_busy = true;
				if(user_last_id)
					var str_user_last_id = '&li='+user_last_id;
				else
					var str_user_last_id = '';
				$.getJSON('comments.php', 'p=user&a={/literal}{$userPage.info.id}{literal}'+str_user_last_id,
					function(j){
						if(!j.e)
							$.each(j.d,
								function(i, c)
								{
									var new_post = '<li id="'+c.id+'" class="comment" style="display:none;"><img class="user icon" src="'+c.i+'" alt="'+c.u+'" /><div class="comment_msg"><a class="comment_head" name="co_'+c.id+'" href="#co_'+c.id+'">'+c.u+'<span>'+c.d+'</span></a>{/literal}{if $LOGIN}<button class="anwser_link ui-widget ui-state-default" onClick="javascript:anwser_comment('+c.id+');">Antworten</button>{/if}{literal}<div>'+c.m+'</div></div>'+'<ul></ul></li>';
									if(!c.re)
										$("ul#comments_sec").append(new_post);
									else
										$("ul#comments_sec li#"+c.re+" > ul").append(new_post);
									if(user_last_id < parseInt(c.id))
										user_last_id = parseInt(c.id);
									$("ul#comments_sec").show();
									$("ul#comments_sec li#"+c.id).show('slide', {direction: 'up'}, 500, function(){});
								}
							);
						
					}
				);
				user_busy = false;
			}
			$(function()
			{
				$(".submit_comment_user").click(function()
				{{/literal}
					var comment = $("#user_comment_text")[0].value;
					$("#user_comment_text")[0].value = '';
					var dataString = 'p=user&s=pc&a={$userPage.info.id}&m=' + comment;{literal}
					$.ajax({
						type: "POST",
						url: "comments.php",
						data: dataString,
						cache: false,
						success: function(html){
							comments_refresh();
							$("#user_comment_text")[0].focus();
						}
					});
					return false;
				});
			});
			function anwser_comment(comment_id)
			{
				$("#comments_sec li.comment#"+comment_id).append('<form style="display:none;" action="#" method="post" id="comment_anwser_form_'+comment_id+'" class="comment_box comment">{/literal}<img class="user icon" src="{$user_icon}" alt="" />{literal}<div><textarea type="text" id="comment_'+comment_id+'"></textarea><input type="submit" class="submit_comment_user ui-widget ui-state-default" id="'+comment_id+'" value="Jetzt Antworten!" /><input type="button" class="comment_form_esc ui-widget ui-state-default" onClick="close_comment_form('+comment_id+');" value="Abbrechen" /></div></form>');
				$("#comments_sec li.comment#"+comment_id+" > form").show('slide', {direction:'up'}, 500, function(){$("#comment_anwser_form_"+comment_id+" #comment_"+comment_id)[0].focus();});
				$("li#"+comment_id+" > div > button")[0].disabled = true;
				$("#comment_anwser_form_"+comment_id).submit(function()
				{
					var comment = $("#comment_anwser_form_"+comment_id+" #comment_"+comment_id).val();
					$("#comment_anwser_form_"+comment_id).hide('slide', {direction: 'up'}, 500, function(){
						$("#comment_anwser_form_"+comment_id).remove();
						$("li#"+comment_id+" > div > button")[0].disabled = false;
					});
					$.ajax({
						type: "POST",
						url: "comments.php",
						data: 'p=comment&op=user&s=pc&a={/literal}{$userPage.info.id}{literal}&r='+comment_id+'&m='+comment,
						cache: false,
						success: function(html){
							comments_refresh();
							$("li#"+comment_id+" > a.anwser_link").fadeIn(500, function(){});
						}
					});
					return false;
				});
			}
			function close_comment_form(comment_id)
			{
				$("form#comment_anwser_form_"+comment_id).hide('slide', {direction: 'up'}, 500, function(){
					$("form#comment_anwser_form_"+comment_id).remove();
				});
				$("li#"+comment_id+" > div > button")[0].disabled = false;
				return false;
			}
			comments_refresh();
			setInterval(function(){comments_refresh();}, 5000);
		{/literal}</script>
		<span class="comments_headline">Pinnwand:</span>
		<ul id="comments_sec"></ul>
		{if $LOGIN}
		<form action="#" method="post" id="comment_form" class="comment_box comment">
			<img class="user icon" src="{$user_icon}" alt="" />
			<div><span class="comments_headline">{$userPage.info.username} etwas schreiben:</span><br />
				<textarea type="text" id="user_comment_text"></textarea>
				<input type="submit" class="submit_comment_user ui-widget ui-state-default" value="Beitrag senden!" />
			</div>
		</form>
		{/if}
	</div>
{/if}
</div>
{include file="footer.html"}