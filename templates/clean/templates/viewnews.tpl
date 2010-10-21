{include file="header.html"}
	<ul>
		{section name=news_list loop=$news_obj}
		<li class="news{if $news_obj[news_list].STICKY} news_sticky{/if}">
			{if $SHOW_COMMENTS}
				<script type="text/javascript" >
					{literal}
					var last_id = 0;
					function comments_refresh()
					{
						if(last_id)
							var str_last_id = '&li='+last_id;
						else
							var str_last_id = '';
						$.getJSON('comments.php', 'p=news&a={/literal}{$news_obj[news_list].ID}{literal}'+str_last_id,
							function(j)
							{
								if(!j.e)
								{
									$.each(j.d,
										function(i, c)
										{
											var new_post = '<li id="'+c.id+'" class="comment" style="display:none;">'+'<a name="co_'+c.id+'" href="#co_'+c.id+'"><span class="comment_head">'+c.u+' @ '+c.d+'</span></a> <a class="anwser_link" href="javascript:anwser_comment('+c.id+');">Antworten</a><br />'+'<span class="comment_body">'+c.m+'</span>'+'<ul></ul></li>';
											if(!c.re)
											{
												$("ul#comments_sec").append(new_post);
												if(last_id < c.id)
												{
													last_id = c.id;
												}
											}
											else
											{
												$("ul#comments_sec li#"+c.re+" > ul").append(new_post);
											}
											if(last_id < c.id)
											{
												last_id = c.id;
											}
											$("ul#comments_sec").show();
											$("ul#comments_sec li#"+c.id).show('slide', {direction: 'up'}, 2000, function(){});
										}
									);
								}
							}
						);
					}
					$(document).ready(
						function()
						{
							$.getJSON('comments.php?p=news&a={/literal}{$news_obj[news_list].ID}{literal}',
								function(j)
								{
									$.each(j.d,
										function(i, c)
										{
											var new_post = '<li id="'+c.id+'" class="comment" style="display:none;">'+'<a name="co_'+c.id+'" href="#co_'+c.id+'"><span class="comment_head">'+c.u+' @ '+c.d+'</span></a> <a class="anwser_link" href="javascript:anwser_comment('+c.id+');">Antworten</a><br />'+'<span class="comment_body">'+c.m+'</span>'+'<ul></ul></li>';
											if(!c.re)
											{
												$("ul#comments_sec").append(new_post);
												if(last_id < c.id)
												{
													last_id = c.id;
												}
											}
											else
											{
												$("ul#comments_sec li#"+c.re+" > ul").append(new_post);
												if(last_id < c.id)
												{
													last_id = c.id;
												}
											}
										}
									);
									$("ul#comments_sec li").show();
									$("ul#comments_sec").show();
									//last_id = j.li;
								}
							);
							var refreshId = setInterval(function(){comments_refresh();}, 10000);
						}
					);
					
					$(function()
					{
						$(".submit_comment_news").click(function()
						{
							if(comment=='')
							{
								alert('Please Give Valid Details');
							}
							else
							{
								var comment = $("#news_comment_text").val();{/literal}
								var dataString = 'p=news&a={$news_obj[news_list].ID}&m=' + comment;{literal}
								$("#comment_form").hide('slide', {direction: 'up'}, 500, function(){
									$("#comment_progress").show('slide', {direction: 'up'}, 500, function(){
										$.ajax({
											type: "POST",
											url: "comments.php",
											data: dataString,
											cache: false,
											success: function(html){
												$("#comment_progress").hide('slide', {direction: 'up'}, 500, function(){
													$("#comment_form").show('slide', {direction: 'up'}, 500, function(){});
												});
												comments_refresh();
											}
										});
									});
								});
								
							}
							return false;
						});
					});
					
					function anwser_comment(comment_id)
					{
						$("#comments_sec li.comment#"+comment_id).append('<form style="display:none;" action="#" method="post" id="comment_anwser_form_'+comment_id+'" class="comment_box"><input type="text" id="comment_'+comment_id+'"></input><input type="submit" class="submit_comment_comment" id="'+comment_id+'" value=" Submit Anwser " style="display:none;" /></form>');
						$("li#"+comment_id+" > a.anwser_link").fadeOut(500, function(){
							$("#comments_sec li.comment#"+comment_id+" > form").show('slide', {direction:'up'}, 500, function(){});
						});
						$("#comment_anwser_form_"+comment_id).submit(function()
						{
							if(comment=='')
							{
								alert('Please Give Valid Details');
							}
							else
							{
								var comment = $("#comment_anwser_form_"+comment_id+" #comment_"+comment_id).val();
								$("#comment_anwser_form_"+comment_id).hide('slide', {direction: 'up'}, 500, function(){
									$("#comment_anwser_form_"+comment_id).remove();
								});
								$.ajax({
									type: "POST",
									url: "comments.php",
									data: 'p=comment&a={/literal}{$news_obj[news_list].ID}{literal}&r='+comment_id+'&m='+comment,
									cache: false,
									success: function(html){
										comments_refresh();
										$("li#"+comment_id+" > a.anwser_link").fadeIn(500, function(){});
									}
								});
							}
							return false;
						});
					}
				{/literal}</script>
			{/if}
			<span class="news_headline"><a href="viewnews.php?id={$news_obj[news_list].ID}">{$news_obj[news_list].HEADLINE}</a></span><span class="news_date">{$news_obj[news_list].AUTHOR} @ {$news_obj[news_list].TIME}</span>
			<hr />
			<p>{$news_obj[news_list].MESSAGE}</p>
			{if $SHOW_COMMENTS}
			<div class="comments_div">
				<ul id="comments_sec" style="display:none;"></ul>
				<div id="comment_progress" style="display:none; height: 24px;">Loading...</div>
				<form action="#" method="post" id="comment_form" class="comment_box">
					<input type="text" id="news_comment_text" /><input type="submit" class="submit_comment_news" value=" Submit Comment " style="display:none;" />
				</form>
			</div>
			{/if}
		</li>
		{/section}
	</ul>
{include file="footer.html"}
