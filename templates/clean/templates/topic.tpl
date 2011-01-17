{include file="header.html"}
	<div id="topic_page">
		{if $access}
			<script type="text/javascript" src="templates/clean/script/forum.js"></script>
			<script type="text/javascript">
				var topic_id = {$forum_info.topic_id};
				var forum_id = {$forum_info.forum_id};
				var forum_last_post_id = {$forum_info.last_post_id};
				{literal}$(function(){{/literal}
					$("#sideFrame").accordion( "activate" , forum_id+1);
				{literal}});{/literal}
			</script>
			<ul>
				<li class="headline"><a href="{$domain}/forum-{$forum_info.forum_id}-{$forum_info.forum_cleantitle}">{$forum_info.forum_name}</a> &gt;&gt; {$forum_info.topic_title}</li>
				<!-- Buttons für Seite vor/zurück -->
				{section name=post_list loop=$forum_posts}
				<li>
					<img src="{$forum_posts[post_list].POST_ICON}" title="{$forum_posts[post_list].POST_AUTOR}" alt="{$forum_posts[post_list].POST_AUTOR}" />
					<span class="title">{$forum_posts[post_list].POST_AUTOR}
					<a class="controls fixlink" name="{$forum_posts[post_list].POST_ID}" href="{$domain}/topic-{$forum_info.topic_id}-{$forum_info.topic_cleantitle}#{$forum_posts[post_list].POST_ID}">¶</a>
					{if $FB}<span class="controls fb_like"><fb:like href="{$domain}/topic-{$forum_info.topic_id}-{$forum_info.topic_cleantitle}" layout="button_count" font="lucida grande"></fb:like></span>{/if}
					</span>
					<span class="time">{$forum_posts[post_list].POST_DATE}</span>
					{if $forum_posts[post_list].EDIT_POST}<span class="controls edit">&lt; Bearbeiten &gt;</span>{/if}
					<div class="text">{$forum_posts[post_list].POST_TEXT}</div>
				</li>
				{/section}
			</ul>
			<div class="new_post">
				<form action="" method="post" id="post_form">
					<img src="{$user_icon}" alt="Sie!" />
					<div>
						<span>Auf "{$forum_info.topic_title}" antworten:</span><br />
						<textarea type="text" id="post_text"></textarea>
						<input type="submit" class="ui-widget ui-state-default" value="Beitrag senden!" />
					</div>
				</form>
			</div>
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
