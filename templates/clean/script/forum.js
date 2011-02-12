$(function() {
	$("#sideFrame .forum span.new_topic").click(function(){
		var forum_id = $(this)[0].id.substr(10);
		if(!$("div#new_topic")[0])
			$("body").append('<div id="new_topic"><span>Titel:</span><input type="text"><span>Text:</span><textarea wrap="soft" rows="5"></textarea><div><button class="submit">Topic erstellen</button><button class="close">Abbrechen</button></div></div>');
		$("div#new_topic")[0].title="Neues Thema";
		$("div#new_topic > span")[0].innerHTML = "Forum: "+this.parentNode.previousElementSibling.lastChild.innerHTML;
		$("div#new_topic > div > button.close").click(function(){$("div#new_topic").dialog( "close" );});
		$("div#new_topic > div > button.submit").unbind('click',false);
		$("div#new_topic > div > button.submit").click(function(){
			if($("body div#new_topic > input").val()!='' && $("body div#new_topic > textarea").val()!='')
				$.ajax({
					type: "POST",
					url: "forum.php",
					data: 'c=execute&set=create_topic&forum='+forum_id+'&title='+escape($("body div#new_topic > input").val())+'&post_text='+escape($("body div#new_topic > textarea").val()),
					cache: false,
					success: function(re){
						var re = jQuery.parseJSON(re);
						if(re.e==1)
							alert("Es ist ein unerwarteter Fehler aufgetreten! Fehler-Code: "+re.m);
						else
							window.location = "topic-"+re.m;
					}
				});
		});
		$("div#new_topic > div").buttonset();
		$("div#new_topic").dialog(
		{
			resizable: false,
			width: 400,
			height: 235,
			closeOnEscape: false,
			modal: false,
		});
		return false;
	});
});