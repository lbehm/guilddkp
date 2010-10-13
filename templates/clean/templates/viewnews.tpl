{include file="header.html"}
		<ul>
{section name=news_list loop=$news_obj}
			<li class="news{if $news_obj[news_list].STICKY} news_sticky{/if}">
				<span class="news_headline"><a href="viewnews.php?id={$news_obj[news_list].ID}">{$news_obj[news_list].HEADLINE}</a></span><span class="news_date">{$news_obj[news_list].AUTHOR} @ {$news_obj[news_list].TIME}</span>
				<hr />
				<p>{$news_obj[news_list].MESSAGE}</p>
			</li>
			{if $news_obj[news_list].DETAIL}
			<script type="text/javascript" >
				{literal}
				$(document).ready(function() {
					$.ajax({
						type: "GET",
						url: "comments.php",
						data: 'page=news&attach={/literal}{$news_obj[news_list].ID}{literal}',
						cache: false,
						success: function(html){
							$("ul#comments_sec").append(html);
							$("ul#comments_sec").fadeIn(2000);
							$("ul#comments_sec li").fadeIn("slow");
							$("ul#comments_sec li:last").hide();
							
						}
					});
		
				
				});
				$(function() {
					$(".submit").click(function()
					{
						var comment = $("#comment").val();
						var last_id = $(".last_id:last")[0].value;
						alert('ID:' + last_id);
						var dataString = 'page=news&attach={/literal}{$news_obj[news_list].ID}{literal}&last_id='+ last_id +'&comment=' + comment;
						
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
									$("ul#comments_sec").append(html);
									$("ul#comments_sec li").fadeIn("slow");
									$(".last_id").hide();
									$("#comment_progress").hide();
									$("#comment_form").fadeIn(400);
								}
							});
						}return false;
					});
				});
			{/literal}</script>
			<li class="comments_sec">
				<ul id="comments_sec" style="display:none">
					<li class="new_comment">
						<div id="comment_progress" style="display:none">Loading...</div>
						<form action="#" method="post" id="comment_form">
							<textarea id="comment"></textarea><br />
							<input type="submit" class="submit" value=" Submit Comment " />
						</form>
					</li>
				</ul>
			</li>
			{/if}
{/section}
		</ul>
{include file="footer.html"}
