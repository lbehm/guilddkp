{include file="header.html"}
	<ul>
		{section name=news_list loop=$news_obj}
		<li class="news{if $news_obj[news_list].STICKY} news_sticky{/if}">
			{if $SHOW_COMMENTS}
				<script type="text/javascript" >
					{literal}
					var last_id = "0";
					function comments_refresh()
					{
						$.getJSON('comments.php?page=news&attach={/literal}{$news_obj[news_list].ID}{literal}&format=json&last_id='+last_id,
							function(data)
							{
								$.each(data.comm,
									function(i, comment)
									{
										var new_post = '<li id="'+comment.id+'" class="comment" style="display:none;">'+'<a name="co_'+comment.id+'" href="#co_'+comment.id+'"><span class="comment_head">'+comment.autor+' @ '+comment.date+'</span></a> <a class="anwser_link" href="javascript:anwser_comment('+comment.id+');">Antworten</a><br />'+'<span class="comment_body">'+comment.msg+'</span>'+'<ul></ul></li>';
										if(!comment.respond_to)
										{
											$("ul#comments_sec").append(new_post);
											if(last_id < comment.id)
											{
												last_id = comment.id;
											}
										}
										else
										{
											$("ul#comments_sec li#"+comment.respond_to+" > ul").append(new_post);
										}
										if(last_id < comment.id)
										{
											last_id = comment.id;
										}
										$("ul#comments_sec li#"+comment.id).show('slide', {direction: 'up'}, 2000, function(){});
									}
								);
							}
						);
					}
					$(document).ready(
						function()
						{
							$.getJSON('comments.php?page=news&attach={/literal}{$news_obj[news_list].ID}{literal}&format=json',
								function(data)
								{
									$.each(data.comm,
										function(i, comment)
										{
											var new_post = '<li id="'+comment.id+'" class="comment" style="display:none;">'+'<a name="co_'+comment.id+'" href="#co_'+comment.id+'"><span class="comment_head">'+comment.autor+' @ '+comment.date+'</span></a> <a class="anwser_link" href="javascript:anwser_comment('+comment.id+');">Antworten</a><br />'+'<span class="comment_body">'+comment.msg+'</span>'+'<ul></ul></li>';
											if(!comment.respond_to)
											{
												$("ul#comments_sec").append(new_post);
												if(last_id < comment.id)
												{
													last_id = comment.id;
												}
											}
											else
											{
												$("ul#comments_sec li#"+comment.respond_to+" > ul").append(new_post);
												if(last_id < comment.id)
												{
													last_id = comment.id;
												}
											}
										}
									);
									$("ul#comments_sec li").show();
									$("ul#comments_sec").show();
									last_id = data.last_id;
								}
							);
						//	var refreshId = setInterval(function(){comments_refresh();}, 10000);
						}
					);
					
					$(function()
					{
						$(".submit_comment_news").click(function()
						{
							var comment = $("#news_comment_text").val();
							{/literal}
							var dataString = 'p_page=news&page=news&p_attach={$news_obj[news_list].ID}&attach={$news_obj[news_list].ID}&comment=' + comment;
							{literal}
							
							if(comment=='')
							{
								alert('Please Give Valid Details');
							}
							else
							{
								$("#comment_progress").show();
								$("#comment_progress").fadeIn(400);
								$("#comment_form").hide();
								$.ajax({
									type: "POST",
									url: "comments.php",
									data: dataString,
									cache: false,
									success: function(html){
										comments_refresh();
									}
								});
							}
							return false;
						});
					})
					
					function anwser_comment(comment_id)
					{
						$("#comments_sec li.comment#"+comment_id).append("<form action=\"#\" method=\"post\" id=\"comment_anwser_form_"+comment_id+"\"><input type=\"text\" id=\"comment_"+comment_id+"\"></input><input type=\"submit\" class=\"submit_comment_comment\" id=\""+comment_id+"\" value=\" Submit Anwser \" /></form>");
						$("li#"+comment_id+" a.anwser_link").remove();
						$("#comment_anwser_form_"+comment_id).submit(function()
						{
							var comment = $("#comment_anwser_form_"+comment_id+" #comment_"+comment_id).val();
							var dataString = 'p_page=comment&p_attach={/literal}{$news_obj[news_list].ID}{literal}&p_respond='+comment_id+'&page=news&attach={/literal}{$news_obj[news_list].ID}{literal}&comment=' + comment;
							
							if(comment=='')
							{
								alert('Please Give Valid Details');
							}
							else
							{
								$("#comment_form").fadeOut('fast', function(){
									$("#comment_progress").fadeIn('fast');
								});
								$.ajax({
									type: "POST",
									url: "comments.php",
									data: dataString,
									cache: false,
									success: function(html){
										comments_refresh();
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
				<form action="#" method="post" id="comment_form">
					<input type="text" id="news_comment_text"></input><input type="submit" class="submit_comment_news" value=" Submit Comment " />
				</form>
			</div>
			{/if}
		</li>
		{/section}
	</ul>
{include file="footer.html"}
