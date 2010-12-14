$(function() {
	$(".shoutbox").removeClass("ui-accordion-content");
	var sB_last_id = 0;
	function sB_refresh()
	{
		if(sB_last_id)
			var str_last_id = '&li='+sB_last_id;
		else
			var str_last_id = '';
		$.getJSON('comments.php', 'p=shoutBox&a=0'+str_last_id,
			function(j)
			{
				if(!j.e)
				{
					$.each(j.d,function(i, c)
					{
						var new_post = '<li id="'+c.id+'" class="comment" style="display:none;"><img src="'+c.i+'" alt="'+c.u+'" title="'+c.u+'" /><div class="comment_msg"><span class="comment_head">'+c.u+'</span><div>'+c.m+'</div></div></li>';
						if(!c.re)
						{
							if(sB_last_id)
								$("ul.shoutbox ").prepend(new_post);
							else
								$("ul.shoutbox ").append(new_post);
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
	sB_refresh();
	setInterval(function(){sB_refresh();}, 5000);
	$("form#comment_form").submit(function(){
		var comment = escape($(".shoutbox .comment_box input.sBText").val());
		if(comment!='')
		{
			var dataString = 'p=shoutBox&s=pc&a=0&m=' + comment;
			$.ajax({
				type: "POST",
				url: "comments.php",
				data: dataString,
				cache: false,
				success: function(html){
					sB_refresh();
					$(".shoutbox .comment_box input.sBText")[0].value = '';
					$(".shoutbox .comment_box input.sBText")[0].focus();
				}
			});
		}
		return false;
	});
});
