/*
	JS: Forum-API
*/
$(function() {
	var topic_page = 1;
	function topic_refresh()
	{
		$.getJSON('forum.php', 'c=api_topic&p='+topic_page+'&id='+topic_id+'&p_id='+forum_last_post_id,
			function(j)
			{
				if(!j.e)
				{
					$.each(j.d,function(i, c)
					{
						var new_post = '';
						if(!c.re)
						{
							$("div#topic_page ul").append(new_post);
							if(sB_last_id < parseInt(c.id))
							{
								sB_last_id = parseInt(c.id);
							}
						}
						else
						{
							$("ul.shoutbox li#"+c.re+" > ul").append(new_post);
						}
						if(sB_last_id < c.id)
						{
							sB_last_id = parseInt(c.id);
						}
						$("ul.shoutbox").show();
						$("ul.shoutbox li#"+c.id).show('slide', {direction: 'up'}, 500, function(){});
					});
				}
			}
		);
	}
	$("div#topic_page .new_post > form").submit(function(){
		var p_text = escape($("div#topic_page .new_post > form > div > textarea#post_text").val());
		$.ajax({
			type: "POST",
			url: "forum.php",
			data: 'c=execute&set=create_post&topic='+topic_id+'&post_text='+p_text,
			cache: false,
			success: function(html){
				$("div#topic_page .new_post > form > div > textarea#post_text")[0].value = '';
				$("div#topic_page .new_post > form > div > textarea#post_text")[0].focus();
				topic_refresh();
			}
		});
		
		return false;
	});
});