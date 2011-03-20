{include file="header.html"}
<div class="raidPage">
{if $raidPage.error == "raid"}
Raid nicht gefunden
{else}
	<img class="raid_image" src="{$icon_repo_large}{$raidPage.info.icon}.jpg" alt="" />
	<div class="raid_headline">{$raidPage.info.title}</div>
	<span class="raid_date">Raidleader: {$raidPage.info.leader_name} - Start: {$raidPage.info.start} Ende: {$raidPage.info.end}</span>

	<div class="raid_kills">
	{section name=kills_list loop=$raidPage.kills}
		<div class="boss_name">{$raidPage.kills[kills_list].name}</div>
		<div class="boss_details">
			<span class="boss_dkp">Erhalten: {$raidPage.kills[kills_list].dkp} DKP</span>
			<div class="loot_list">
			{section name=loot_list loop=$raidPage.kills[kills_list].loot}
				<div class="loot_item">
					<img class="item_image" src="{$icon_repo_large}{$raidPage.kills[kills_list].loot[loot_list].item_icon}.jpg" alt="" />
					<a href="item={$raidPage.kills[kills_list].loot[loot_list].item_id}">{$raidPage.kills[kills_list].loot[loot_list].item_name}</a>
					<span class="loot_details">Für <span class="loot_dkp">{$raidPage.kills[kills_list].loot[loot_list].dkp}</span> DKP @ <a class="loot_looter" href="char-{$raidPage.kills[kills_list].loot[loot_list].looter_id}-{$raidPage.kills[kills_list].loot[loot_list].looter}">{$raidPage.kills[kills_list].loot[loot_list].looter}</a></span>
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
								<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.kills[kills_list].attendees.dd[baDD_list].icon}.jpg');" href="char-{$raidPage.kills[kills_list].attendees.dd[baDD_list].id}-{$raidPage.kills[kills_list].attendees.dd[baDD_list].name}">{$raidPage.kills[kills_list].attendees.dd[baDD_list].name}</li>
							{/section}
							</ul>
						</td>
						<td>
							<ul>
							{section name=baTANK_list loop=$raidPage.kills[kills_list].attendees.tank}
								<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.kills[kills_list].attendees.tank[baTANK_list].icon}.jpg');" href="char-{$raidPage.kills[kills_list].attendees.tank[baTANK_list].id}-{$raidPage.kills[kills_list].attendees.tank[baTANK_list].name}">{$raidPage.kills[kills_list].attendees.tank[baTANK_list].name}</li>
							{/section}
							</ul>
						</td>
						<td>
							<ul>
							{section name=baHEAL_list loop=$raidPage.kills[kills_list].attendees.heal}
								<li class="attendees bb_class" style="background-image:url('{$icon_repo}{$raidPage.kills[kills_list].attendees.heal[baHEAL_list].icon}.jpg');" href="char-{$raidPage.kills[kills_list].attendees.heal[baHEAL_list].id}-{$raidPage.kills[kills_list].attendees.heal[baHEAL_list].name}">{$raidPage.kills[kills_list].attendees.heal[baHEAL_list].name}</li>
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
		$("div.raidPage > .raid_kills > .boss_name").click(function()
		{
			$(this).next().toggle('slide', {direction:'up'}, 200);
			return false;
		}).next().hide();
	{/literal}</script>
	<!-- 
		layout like news
		accordion für bosse
			aufklappen für loot
	-->
{/if}
</div>
{include file="footer.html"}