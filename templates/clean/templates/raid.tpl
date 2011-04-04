{include file="header.html"}
<div class="raidPage">
{if $raidPage.error == "raid"}
Raid nicht gefunden
{else}
	<img class="icon" src="./templates/clean/images/raid_icons/{$raidPage.info.icon}.png" alt="" />
	<div class="raid_headline">
		<div>{$raidPage.info.title}</div>
		{if $LOGIN}{if not $raidPage.past}{if $quickDKP}<div class="raidPlanerBar">
			<select id="charSelect">
				{section name=char loop=$quickDKP}
				<option value="{$quickDKP[char].id}">{$quickDKP[char].name}</option>
				{/section}
			</select>
			<select id="roleSelect">
			</select>
			<button class="agree">Ich nehme teil</button><button class="maybe">Ersatzbank</button><button class="disagree">Nein</button>
		</div>
		<script type="text/javascript">{literal}
			$(function(){
				function updateRoleSelector(e){
					{/literal}{section name=char_id loop=$quickDKP}
					if($(e).val()=={$quickDKP[char_id].id}){literal}
					{
						$("div.raidPage > .raid_headline > .raidPlanerBar > select#roleSelect").get(0).innerHTML='{/literal}{if $quickDKP[char_id].roles.tank}<option value="tank">Tanken</option>{/if}{if $quickDKP[char_id].roles.heal}<option value="heal">Healer</option>{/if}{if $quickDKP[char_id].roles.melee}<option value="melee">Nahkampf</option>{/if}{if $quickDKP[char_id].roles.range}<option value="range">Fernkampf</option>{/if}{literal}';
					}
					{/literal}{/section}{literal}
				}
				updateRoleSelector($("div.raidPage > .raid_headline > .raidPlanerBar > select#charSelect").get(0));
				$("div.raidPage > .raid_headline > .raidPlanerBar > select#charSelect").change(function(){updateRoleSelector(this);});
				$("div.raidPage .raid_headline .raidPlanerBar button").click(function(){
					$.ajax({
						type:'GET',
						url:'api.php',
						data:'p=raidAttend&raid={/literal}{$raidPage.info.id}{literal}&status='+$(this).get(0).className+'&char='+$("div.raidPage > .raid_headline > .raidPlanerBar > select#charSelect option:selected").val()+'&role='+$("div.raidPage > .raid_headline > .raidPlanerBar > select#roleSelect > option:selected").val(),
						dataType:'json',
						success:function(re){if(re.url)window.location=re.url;}
					});
				});
		});
		{/literal}</script>
		{/if}{/if}{/if}
	</div>
	<span class="raid_date">Raidleader: {$raidPage.info.leader_name} - Start: {$raidPage.info.start} Ende: {$raidPage.info.end}</span>

	{if $raidPage.past}
	<div class="raid_kills">
	{section name=kills_list loop=$raidPage.kills}
		<div class="boss_name">{$raidPage.kills[kills_list].name}<span>( More )</span></div>
		<div class="boss_details">
			<span class="boss_dkp">Erhalten: {$raidPage.kills[kills_list].dkp} DKP</span>
			<div class="loot_list">
			{section name=loot_list loop=$raidPage.kills[kills_list].loot}
				<div class="loot_item">
					<img class="icon" src="{$icon_repo_large}{$raidPage.kills[kills_list].loot[loot_list].item_icon}.jpg" alt="" />
					<a href="item={$raidPage.kills[kills_list].loot[loot_list].item_id}">{$raidPage.kills[kills_list].loot[loot_list].item_name}</a>
					<span class="loot_details">F&uuml;r <span class="loot_dkp">{$raidPage.kills[kills_list].loot[loot_list].dkp}</span> DKP @ <a class="loot_looter" href="char-{$raidPage.kills[kills_list].loot[loot_list].looter_id}-{$raidPage.kills[kills_list].loot[loot_list].looter}">{$raidPage.kills[kills_list].loot[loot_list].looter}</a></span>
				</div>
			{/section}
			</div>
			<div class="boss_attendees">
				<div class="attendees_headline">Teilnehmer:</div>
				<table>
					<tr>
						<td>Damagedealer</td>
						<td>Tanks</td>
						<td>Healer</td>
					</tr>
					<tr>
						<td>
							<ul>
							{section name=baDD_list loop=$raidPage.kills[kills_list].attendees.dd}
								<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.kills[kills_list].attendees.dd[baDD_list].icon}.jpg');">{$raidPage.kills[kills_list].attendees.dd[baDD_list].name}</li>
							{/section}
							</ul>
						</td>
						<td>
							<ul>
							{section name=baTANK_list loop=$raidPage.kills[kills_list].attendees.tank}
								<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.kills[kills_list].attendees.tank[baTANK_list].icon}.jpg');">{$raidPage.kills[kills_list].attendees.tank[baTANK_list].name}</li>
							{/section}
							</ul>
						</td>
						<td>
							<ul>
							{section name=baHEAL_list loop=$raidPage.kills[kills_list].attendees.heal}
								<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.kills[kills_list].attendees.heal[baHEAL_list].icon}.jpg');">{$raidPage.kills[kills_list].attendees.heal[baHEAL_list].name}</li>
							{/section}
							</ul>
						</td>
					</tr>
				</table>
			</div>
		</div>
	{/section}
	</div>
	<script type="text/javascript">{literal}
		$("div.raidPage > .raid_kills > .boss_name").click(function(){$(this).next().toggle('slide', {direction:'up'}, 200);return false;}).next().hide();
	{/literal}</script>
	{else}
	<!--Raidplaner-->
	<div class="attendees">
		<div class="attendees_headline">Best&auml;tigt:</div>
		<table class="sign">
			<tr>
				<td>Tanks</td>
				<td>Healer</td>
				<td>Nahk&auml;mpfer</td>
				<td>Fernk&auml;mpfer</td>
			</tr>
			<tr>
				<td class="tank">
					<ul>
					{section name=baTANK loop=$raidPage.plan.sign.tank}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.sign.tank[baTANK].icon}.jpg');">{$raidPage.plan.sign.tank[baTANK].name}<div></div></li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="heal">
					<ul>
					{section name=baHEAL loop=$raidPage.plan.sign.heal}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.sign.heal[baHEAL].icon}.jpg');">{$raidPage.plan.sign.heal[baHEAL].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="melee">
					<ul>
					{section name=baMELEE loop=$raidPage.plan.sign.melee}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.sign.melee[baMELEE].icon}.jpg');">{$raidPage.plan.sign.melee[baMELEE].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="range">
					<ul>
					{section name=baRANGE loop=$raidPage.plan.sign.range}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.sign.range[baRANGE].icon}.jpg');">{$raidPage.plan.sign.range[baRANGE].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
			</tr>
		</table>
		<div class="attendees_headline">Angemeldet:</div>
		<table class="agree">
			<tr>
				<td>Tanks</td>
				<td>Healer</td>
				<td>Nahk&auml;mpfer</td>
				<td>Fernk&auml;mpfer</td>
			</tr>
			<tr>
				<td class="tank">
					<ul>
					{section name=baTANK loop=$raidPage.plan.agree.tank}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.agree.tank[baTANK].icon}.jpg');">{$raidPage.plan.agree.tank[baTANK].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="heal">
					<ul>
					{section name=baHEAL loop=$raidPage.plan.agree.heal}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.agree.heal[baHEAL].icon}.jpg');">{$raidPage.plan.agree.heal[baHEAL].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="melee">
					<ul>
					{section name=baMELEE loop=$raidPage.plan.agree.melee}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.agree.melee[baMELEE].icon}.jpg');">{$raidPage.plan.agree.melee[baMELEE].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="range">
					<ul>
					{section name=baRANGE loop=$raidPage.plan.agree.range}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.agree.range[baRANGE].icon}.jpg');">{$raidPage.plan.agree.range[baRANGE].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
			</tr>
		</table>
		<div class="attendees_headline">Ersatzbank:</div>
		<table class="maybe">
			<tr>
				<td>Tanks</td>
				<td>Healer</td>
				<td>Nahk&auml;mpfer</td>
				<td>Fernk&auml;mpfer</td>
			</tr>
			<tr>
				<td class="tank">
					<ul>
					{section name=baTANK loop=$raidPage.plan.maybe.tank}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.maybe.tank[baTANK].icon}.jpg');">{$raidPage.plan.maybe.tank[baTANK].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="heal">
					<ul>
					{section name=baHEAL loop=$raidPage.plan.maybe.heal}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.maybe.heal[baHEAL].icon}.jpg');">{$raidPage.plan.maybe.heal[baHEAL].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="melee">
					<ul>
					{section name=baMELEE_list loop=$raidPage.plan.maybe.melee}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.maybe.melee[baMELEE].icon}.jpg');">{$raidPage.plan.maybe.melee[baMELEE].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="range">
					<ul>
					{section name=baRANGE loop=$raidPage.plan.maybe.range}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.maybe.range[baRANGE].icon}.jpg');">{$raidPage.plan.maybe.range[baRANGE].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
			</tr>
		</table>
		<div class="attendees_headline">Abgemeldet:</div>
		<table class="disagree">
			<tr>
				<td>Tanks</td>
				<td>Healer</td>
				<td>Nahk&auml;mpfer</td>
				<td>Fernk&auml;mpfer</td>
			</tr>
			<tr>
				<td class="tank">
					<ul>
					{section name=baTANK loop=$raidPage.plan.disagree.tank}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.disagree.tank[baTANK].icon}.jpg');">{$raidPage.plan.disagree.tank[baTANK].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="heal">
					<ul>
					{section name=baHEAL loop=$raidPage.plan.disagree.heal}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.disagree.heal[baHEAL].icon}.jpg');">{$raidPage.plan.disagree.heal[baHEAL].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="melee">
					<ul>
					{section name=baMELEE loop=$raidPage.plan.disagree.melee}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.disagree.melee[baMELEE].icon}.jpg');">{$raidPage.plan.disagree.melee[baMELEE].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
				<td class="range">
					<ul>
					{section name=baRANGE loop=$raidPage.plan.disagree.range}
						<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.plan.disagree.range[baRANGE].icon}.jpg');">{$raidPage.plan.disagree.range[baRANGE].name}</li>
					{/section}
						<li class="placeholder"></li>
					</ul>
				</td>
			</tr>
		</table>
	</div>
	{if $raidPage.admin}
	<script>{literal}
	$(function() {
		$( "li.attendees" ).draggable({
			helper: "clone",
			revert: "invalid",
			cursor: "move",
			containment: "div.attendees"
		});
		$(".attendees ul").droppable({
			accept: "li.attendees",
			activeClass: "activedrop",
			drop: function( event, ui ) {
				ui.draggable.prependTo( this );
				$.ajax({
					url:'api.php',
					data:'p=raidModAttend&raid={/literal}{$raidPage.info.id}{literal}&status='+$(this.parentNode.parentNode.parentNode.parentNode).get(0).className+'&char='+ui.draggable.text()+'&role='+$(this.parentNode).get(0).className,
					dataType:'json',
					success:function(re){if(re.url)window.location=re.url;}
				});
			}
		});
	/*	$(".attendees .agree ul").droppable({
			accept: "li.attendees",
			activeClass: "activedrop",
			drop: function( event, ui ) {
				ui.draggable.prependTo( this );
				$.ajax({
					url:'api.php',
					data:'p=raidModAttend&raid={/literal}{$raidPage.info.id}{literal}&status=agree&char='+ui.draggable.text()+'',
					dataType:'json',
					success:function(re){if(re.url)window.location=re.url;}
				});
			}
		});
		$(".attendees .maybe ul").droppable({
			accept: "li.attendees",
			activeClass: "activedrop",
			drop: function( event, ui ) {
				ui.draggable.prependTo( this );
				$.ajax({
					url:'api.php',
					data:'p=raidModAttend&raid={/literal}{$raidPage.info.id}{literal}&status=maybe&char='+ui.draggable.text()+'',
					dataType:'json',
					success:function(re){if(re.url)window.location=re.url;}
				});
			}
		});
		$(".attendees .disagree ul").droppable({
			accept: "li.attendees",
			activeClass: "activedrop",
			drop: function( event, ui ) {
				ui.draggable.prependTo( this );
				$.ajax({
					url:'api.php',
					data:'p=raidModAttend&raid={/literal}{$raidPage.info.id}{literal}&status=disagree&char='+ui.draggable.text()+'',
					dataType:'json',
					success:function(re){if(re.url)window.location=re.url;}
				});
			}
		});
	*/});
	{/literal}</script>
	{/if}
	{/if}
{/if}
</div>
{include file="footer.html"}