/*
	JS: Forum-API
*/
$(function() {
	var topic_page = 1;
	var last_page = 1;
	var f_ud_idle = false;
	function topic_refresh()
	{
		if((topic_page != last_page) || (!f_ud_idle))
			return false;
		f_ud_idle = false;
		$.getJSON('forum.php', 'c=api_topic&p='+topic_page+'&id='+topic_id+'&p_id='+forum_last_post_id,
			function(j)
			{
				if(!j.e)
				{
					$.each(j.d,function(i, c)
					{
						var new_post = '<li><img src="'+c.ICON+'" alt="'+c.AUTOR+'" title="'+c.AUTOR+'" /><span class="title">'+c.AUTOR+'</span><span class="time">'+c.DATE+'</span><div class="text">'+c.TEXT+'</div></li>';
						$("div#topic_page ul").append(new_post);
						if(forum_last_post_id < parseInt(c.ID))
						{
							forum_last_post_id = parseInt(c.ID);
						}
					});
				}
			}
		);
		f_ud_idle = true;
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
	setInterval(function(){topic_refresh();}, 60000);
	f_ud_idle = true;
});
