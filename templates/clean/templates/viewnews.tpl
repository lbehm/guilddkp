{include file="header.html"}
		<ul>
{section name=news_list loop=$news_obj}
			<li class="news{if $news_obj[news_list].STICKY} news_sticky{/if}">
				<span class="news_headline"><a href="viewnews.php?id={$news_obj[news_list].ID}">{$news_obj[news_list].HEADLINE}</a></span><span class="news_date">{$news_obj[news_list].AUTHOR} @ {$news_obj[news_list].TIME}</span>
				<hr />
				<p>{$news_obj[news_list].MESSAGE}</p>
			</li>
		{if $SHOW_COMMENTS}
		<script type="text/javascript" >
			{literal}
			$(document).ready(function() {
				$.ajax({
					type: "GET",
					url: "comments.php",
					data: 'page=news&attach={/literal}{$news_obj[news_list].ID}{literal}',
					cache: false,
					success: function(html){
						$("ul#comments_sec").prepend(html);
						$("ul#comments_sec").fadeIn(2000);
						$("ul#comments_sec li.comment").fadeIn("slow");
					}
				});
	
			
			});
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
								$("ul#comments_sec_tmp").html(html);
								$("ul#comments_sec_tmp").show();
								$("ul#comments_sec").hide();
								$("ul#comments_sec_tmp")[0].id = "comments_sec_temp";
								$("ul#comments_sec")[0].id = "comments_sec_tmp";
								$("ul#comments_sec_temp")[0].id = "comments_sec";
								$("ul#comments_sec_tmp").html("");
								$("ul#comments_sec li").fadeIn("slow");
								$("#comment_progress").fadeOut(400, function(){$("#comment_form").fadeIn(400);});
							}
						});
					}
					return false;
				});
			})
			
			function anwser_comment(comment_id)
			{
				$("#comments_sec li.comment#"+comment_id).append("<form action=\"#\" method=\"post\" id=\"comment_anwser_form\"><input type=\"text\" id=\"comment_"+comment_id+"\"></input><input type=\"submit\" class=\"submit_comment_comment\" id=\""+comment_id+"\" value=\" Submit Anwser \" /></form>");
				$("li#"+comment_id+" a.anwser_link").remove();
				$('#comment_anwser_form').submit(function()
				{
					var comment = $("#comment_anwser_form #comment_"+comment_id).val();
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
								$("ul#comments_sec_tmp").html(html);
								$("ul#comments_sec_tmp").show();
								$("ul#comments_sec").hide();
								$("ul#comments_sec_tmp")[0].id = "comments_sec_temp";
								$("ul#comments_sec")[0].id = "comments_sec_tmp";
								$("ul#comments_sec_temp")[0].id = "comments_sec";
								$("ul#comments_sec_tmp").html("");
								$("ul#comments_sec li").fadeIn("slow");
								$("#news_comment_text").text("");
								$("#comment_progress").fadeOut(400, function(){$("#comment_form").fadeIn(400);});
							}
						});
					}
					return false;
				});
			}
		{/literal}</script>
		<div class="comments_div">
			<div id="comment_progress" style="display:none; height: 24px;">Loading...</div>
			<form action="#" method="post" id="comment_form">
				<input type="text" id="news_comment_text"></input><input type="submit" class="submit_comment_news" value=" Submit Comment " />
			</form>
			<ul id="comments_sec" style="display:none;"></ul>
			<ul id="comments_sec_tmp" style="display:none;"></ul>
		</div>
		{/if}
{/section}
		</ul>
{include file="footer.html"}
