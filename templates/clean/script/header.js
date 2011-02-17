$(function() {
	$( "#calendar" ).datepicker({showOtherMonths: true,selectOtherMonths: true});
	$( "#sideFrame" ).accordion({ animated: 'bounceslide' });

});

$(document).ready(function(){
	$("#login_btn").click(function(){
		if(!$("div#login_box")[0])
			$("body").append('<div id="login_box" />');
		$("div#login_box")[0].title = "Login";
		$("div#login_box")[0].innerHTML = '<form class="login_form" action="login.php" methode="Post"><table class="login_data"><tr><td>Benutzername:</td><td><input type="text" name="username" id="name" class="text ui-widget-content ui-corner-all" value="" /></td></tr><tr><td>Passwort:</td><td><input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" /></td></tr><tr><td colspan="2" class="login_btns"><input type="submit" value="Login" /><input type="button" value="Passwort vergessen"/></td></tr></table></form><div class="login_loading">Loading...</div>';
		$("form.login_form .login_btns").buttonset();
		$("div#login_box").dialog(
		{
			resizable: false,
			width: 260,
			height: 125,
			closeOnEscape: true,
			modal: true,
		});
		$("form.login_form").submit(
			function()
			{
				$("table.login_data").hide('slide', {}, 200, function(){
					$("div.login_loading").html("Loading...");
					$("div.login_loading").show('slide', {}, 200, function(){
						$.ajax({
							type: "POST",
							url: "login.php",
							data: "login=1&username="+$("form.login_form table.login_data #name").val()+"&password="+$("form.login_form table.login_data #password").val(),
							cache: false,
							success: function(html)
							{
								if(html=="OK")
								{
									$("div.login_loading").hide('slide', {}, 200, function()
									{
										$("div.login_loading").text("Login erfolgreich");
										$("div.login_loading").show('slide', {}, 200, function()
										{
											window.location = window.location.href;
										});
									});
								}
								else
								{
									$("div.login_loading").hide('slide', {}, 200, function()
									{
										$("div.login_loading").html('<div style="padding:3px;" class="ui-state-error ui-corner-all">Login fehlgeschlagen!<br />Bitte &uuml;berprüfen Sie Ihre Logindaten!</div><div class="loginfailbtns"><button width="100%" id="btn_back">Zurück</button><button id="btn_lostpwd">Passwort vergessen</button></div>');
										$("div.login_loading > div.loginfailbtns button").button();
										$("div.login_loading > div.loginfailbtns button#btn_back").click(function(){
											$("div.login_loading").hide('slide', {}, 200, function()
											{
												$("table.login_data").show('slide', {}, 200, function(){});
											});
										});
										$("div.login_loading > div.loginfailbtns button#btn_lostpwd").click(function(){});
										$("div.login_loading > div.loginfailbtns").buttonset();
										$("div.login_loading").show('slide', {}, 200, function(){});
									});
								}
							}
						});
					});
				});
				return false;
			}
		);
		return false;
	});
	$("#logout_btn").click(function(){
		if(!$("div#logout_box")[0])
			$("body").append('<div id="logout_box" />');
		$("div#logout_box")[0].title = "Logut";
		$("div#logout_box")[0].innerHTML = '<div class="confim">Sind Sie sicher, dass Sie sich abmelden wollen?</div>';
		$("div#logout_box").dialog(
		{
			resizable: false,
			closeOnEscape: true,
			modal: true,
			buttons: {
				"Ja, ausloggen": function() {
					$.ajax({
							type: "GET",
							url: "login.php",
							data: "logout=1",
							cache: false,
							success: function(h)
							{
								window.location = window.location.href;
							}
					});
				},
				"Abbrechen!": function() {
					$( this ).dialog( "close" );
				}
			}
		});
		return false;
	});
	$("#reg_btn").click(function(){
		if(!$("div#reg_box")[0])
			$("body").append('<div id="reg_box" />');
		FB.init({
			appId: '',
			status: true,
			cookie: true,
			xfbml: true
		});
		$("div#reg_box")[0].title = "Registrieren";
		$("div#reg_box")[0].innerHTML = '<div id="reg_step_1"><span class="title">Willkommen!</span><div>Requiem bietet Ihnen weitaus mehr, als Sie bisher sehen können!<br />Werden auch Sie ein Mitglied unserer Comunity und und genießen Sie es!<br />In den folgenden Schritten können Sie sich mit unserer Platform im Web verbinden und die Gilde neu erleben!</div><span class="btns"><button class="btn_next">Weiter</button></span></div>';
		$("div#reg_box btns").buttonset();
		$("div#reg_box").dialog(
		{
			resizable: false,
			width: 380,
			height: 230,
			closeOnEscape: false,
			modal: true,
		});
		$("div#reg_box").append('<div id="reg_step_fb" style="display:none;overflow:hidden;height:450px;"><fb:registration fields="[{\'name\':\'name\'},{\'name\':\'first_name\'},{\'name\':\'last_name\'},{\'name\':\'accname\',\'description\':\'Login-Name\',\'type\':\'text\'},{\'name\':\'birthday\'},{\'name\':\'email\'},{\'name\':\'password\'},{\'name\':\'captcha\'}]" redirect-uri="http://requiem-gilde.tk/register.php?a=r&fb=1" width="620px" height="450px" onvalidate="valid_reg"></fb:registration></div>');
		$("div#reg_box > div#reg_step_fb").append('<script>function valid_reg(f,c){$.getJSON("register.php","a=c&w=un&un="+f.accname,function(re){if(re.r){c({accname:"Dieser Accountname ist bereits vergeben!"});}else{c();}});}</script>');
		$("div#reg_box .btns .btn_next").click(function(){
			$("div#reg_box").dialog('close');
			$("div#reg_box #reg_step_1").hide();
			$("div#reg_box #reg_step_fb").show();
			$("div#reg_box").dialog(
			{
				resizable: false,
				width: 620,
				height: 480,
				closeOnEscape: false,
				modal: true,
			});
			return false;
		});
		return false;
	});

	$(".bb_item").append('<div class="tooltip"><img src="http://static.wowhead.com/images/wow/icons/large/inv_misc_questionmark.jpg" /><span class="title">Server wird abgefragt...</span></div>');
	$(".bb_item").hover(function(){
		$(this).children().css('display', 'block');
	},function(){
		$(this).children().css('display', 'none');
	});
});
