{include file="header.html"}
	<div id="forum_page">
		{if $access}
			<script type="text/javascript">
				var forum_id = {$forum_info.forum_id};
				{literal}$(function(){{/literal}
					$("#sideFrame").accordion( "activate" , forum_id+1);
				{literal}});{/literal}
			</script>
			<ul>
				<li class="headline">{$forum_info.forum_name}</li>
				<!-- Buttons für Seite vor/zurück -->
				{section name=topic_list loop=$forum_topics}
				<li>
					<img class="user" src="{$forum_topics[topic_list].icon}" title="{$forum_topics[topic_list].last_poster}" alt="" />
					<span class="title"><a href="{$domain}/topic-{$forum_topics[topic_list].id}-{$forum_topics[topic_list].cleantitle}">{$forum_topics[topic_list].title}</a></span>
					<span class="time">Letzter Beitrag: {$forum_topics[topic_list].last_poster} - {$forum_topics[topic_list].time}</span>
				</li>
				{/section}
			</ul>
		{else}
			<div class="access_denied">
				<span>Zugriff verweigert</span>
				<div>Sie haben keine ausreichenden Berechtigungen, um auf Das Forum Zugreifen zu können.<br />
				{if $LOGIN}
				Wenden Sie sich für weitere Fragen an den Administrator!
				{else}
				Melden Sie sich an um auf gesperrte Inhalte zuzugreifen!
				{/if}
				</div>
			</div>
		{/if}
	</div>
{include file="footer.html"}
