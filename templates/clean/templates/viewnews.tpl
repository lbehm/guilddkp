{include file="header.html"}
	<div id="news_page">
		{section name=news_list loop=$news_obj}
		{if $SHOW_COMMENTS}
		<script type="text/javascript" >
			{literal}
			var news_last_id = 0;
			var news_busy = false;
			function comments_refresh()
			{
				if(news_busy)
					return;
				news_busy = true;
				if(news_last_id)
					var str_news_last_id = '&li='+news_last_id;
				else
					var str_news_last_id = '';
				$.getJSON('comments.php', 'p=news&a={/literal}{$news_obj[news_list].ID}{literal}'+str_news_last_id,
					function(j){
						if(!j.e)
							$.each(j.d,
								function(i, c)
								{
									var new_post = '<li id="'+c.id+'" class="comment" style="display:none;"><img class="user" src="'+c.i+'" alt="'+c.u+'" /><div class="comment_msg"><a class="comment_head" name="co_'+c.id+'" href="#co_'+c.id+'">'+c.u+'<span>'+c.d+'</span></a>{/literal}{if $LOGIN}<button class="anwser_link ui-widget ui-state-default" onClick="javascript:anwser_comment('+c.id+');">Antworten</button>{/if}{literal}<div>'+c.m+'</div></div>'+'<ul></ul></li>';
									if(!c.re)
										$("ul#comments_sec").append(new_post);
									else
										$("ul#comments_sec li#"+c.re+" > ul").append(new_post);
									if(news_last_id < parseInt(c.id))
										news_last_id = parseInt(c.id);
									$("ul#comments_sec").show();
									$("ul#comments_sec li#"+c.id).show('slide', {direction: 'up'}, 500, function(){});
								}
							);
						
					}
				);
				news_busy = false;
			}
			$(function()
			{
				$(".submit_comment_news").click(function()
				{{/literal}
					var comment = $("#news_comment_text")[0].value;
					$("#news_comment_text")[0].value = '';
					var dataString = 'p=news&s=pc&a={$news_obj[news_list].ID}&m=' + comment;{literal}
					$.ajax({
						type: "POST",
						url: "comments.php",
						data: dataString,
						cache: false,
						success: function(html){
							comments_refresh();
							$("#news_comment_text")[0].focus();
						}
					});
					return false;
				});
			});
			function anwser_comment(comment_id)
			{
				$("#comments_sec li.comment#"+comment_id).append('<form style="display:none;" action="#" method="post" id="comment_anwser_form_'+comment_id+'" class="comment_box comment">{/literal}<img class="user" src="{$user_icon}" alt="" />{literal}<div><textarea type="text" id="comment_'+comment_id+'"></textarea><input type="submit" class="submit_comment_news ui-widget ui-state-default" id="'+comment_id+'" value="Jetzt Antworten!" /><input type="button" class="comment_form_esc ui-widget ui-state-default" onClick="close_comment_form('+comment_id+');" value="Abbrechen" /></div></form>');
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
						data: 'p=comment&op=news&s=pc&a={/literal}{$news_obj[news_list].ID}{literal}&r='+comment_id+'&m='+comment,
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
		{/if}
		<div class="news{if $news_obj[news_list].STICKY} news_sticky{/if}">
			<span class="news_headline"><a href="{$domain}/news-{$news_obj[news_list].ID}-{$news_obj[news_list].CLEANTITLE}">{$news_obj[news_list].HEADLINE} >></a></span><br />
			<span class="news_date">{$news_obj[news_list].AUTHOR} @ {$news_obj[news_list].TIME}</span>
			{if $FB}<span class="fb_like"><fb:like href="{$domain}/news-{$news_obj[news_list].ID}-{$news_obj[news_list].CLEANTITLE}" layout="button_count" font="lucida grande"></fb:like></span>{/if}
			<div>{$news_obj[news_list].MESSAGE}</div>
			{if $SHOW_COMMENTS}
			<div class="comments_div">
				<span class="comments_headline">Kommentare:</span>
				<ul id="comments_sec" style="display:none;"></ul>
				{if $LOGIN}
				<form action="#" method="post" id="comment_form" class="comment_box comment">
					<img class="user" src="{$user_icon}" alt="" />
					<div><span class="comments_headline">Auf "{$news_obj[news_list].HEADLINE}" antworten:</span><br />
						<textarea type="text" id="news_comment_text"></textarea>
						<input type="submit" class="submit_comment_news ui-widget ui-state-default" value="Beitrag senden!" />
					</div>
				</form>
				{/if}
			</div>
			{/if}
		</div>
		{/section}
	</div>
{include file="footer.html"}
