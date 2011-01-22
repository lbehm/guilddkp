$(function() {
	$( "#sideFrame" ).accordion(/*{ animated: 'bounceslide' }*/);
	$( "#calendar" ).datepicker({showOtherMonths: true,selectOtherMonths: true});

});

$(document).ready(function(){
	$("#login_btn").click(function(){
		$("div#box")[0].title = "Login";
		$("div#box")[0].innerHTML = '<form class="login_form" action="login.php" methode="Post"><table class="login_data"><tr><td>Benutzername:</td><td><input type="text" name="username" id="name" class="text ui-widget-content ui-corner-all" value="" /></td></tr><tr><td>Passwort:</td><td><input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" /></td></tr><tr><td colspan="2" class="login_btns"><input type="submit" value="Login" /><input type="button" value="Passwort vergessen"/></tr></table></form><div class="login_loading">Loading...</div>';
		$("form.login_form .login_btns").buttonset();
		$("div#box").dialog(
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
		$("div#box")[0].title = "Logut";
		$("div#box")[0].innerHTML = '<div class="confim">Sind Sie sicher, dass Sie sich abmelden wollen?</div>';
		$("div#box").dialog(
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
		$("div#reg_box")[0].title = "Registrieren";
		$("div#reg_box")[0].innerHTML = '<form class="reg_form" action="register.php" methode="Post"><table class="reg_tbl">'+
		'<tr><td>Loginname:</td><td><input type="text" name="username" id="user_name" class="text ui-widget-content ui-corner-all" value="" /></td></tr>'+
		'<tr><td>Name:</td><td><input type="text" name="display_name" id="display_name" class="text ui-widget-content ui-corner-all" value="" /></td></tr>'+
		'<tr><td>Passwort:</td><td><input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" /></td></tr>'+
		'<tr><td>Passwort bestätigen:</td><td><input type="password" name="password_b" id="password_b" value="" class="text ui-widget-content ui-corner-all" /></td></tr>'+
		'<tr><td>E-Mail:</td><td><input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" /></td></tr>'+
		'<tr><td>Geburtsdatum:</td><td><input type="text" name="date" id="birthday_c" value="" class="text ui-widget-content ui-corner-all" /></td></tr>'+
		'<tr><td>Profilbild:</td><td><input type="text" name="pic_url" id="pic_url" value="" class="text ui-widget-content ui-corner-all" /></td></tr>'+
		'<tr><td><input type="submit" value="Registrieren"/></td><td><input type="reset" value="Abbrechen"/></td></tr>'+
		'</table></form><div class="login_loading" />';
		$("#birthday_c").datepicker({showOtherMonths: true,selectOtherMonths: true,dateFormat: 'dd.mm.yy'});
		$("div#reg_box").dialog(
		{
			resizable: false,
			width: 285,
			height: 250,
			closeOnEscape: true,
			modal: true,
		});
		$("form.reg_form #user_name").blur(function(){
			if(this.value != '')
			{
				$.ajax({
					type: "GET",
					url: "register.php",
					data: "a=c&w=un&un="+this.value,
					cache: false,
					success: function(html)
					{
						if(html==false)
						{
								alert("Dieser Benutzername ist leider bereits vergeben!");
						}
					}
				});
			}
		});
		$("form.reg_form #password").blur(function(){
			if(this.value.length < 5)
			{
				alert("Das Passwort, das Sie verwenden wollen ist zu kurtz!");
			}
		});
		$("form.reg_form #password_b").blur(function(){
			if((this.value != $("form.reg_form #password")[0].value) && $("form.reg_form #password")[0].value != '')
			{
				alert("Das wiederholte Passwort ist nicht mit dem ersten identisch!");
			}
		});
		$("form.reg_form").submit(
			function()
			{
				$("table.reg_tbl").hide('slide', {}, 200, function(){
					$("div.login_loading").html("Loading...");
					$("div.login_loading").show('slide', {}, 200, function(){
						$.ajax({
							type: "GET",
							url: "register.php",
							data: "a=r&user_name="+$("form.reg_form #user_name")[0].value+"&display_name="+$("form.reg_form #display_name")[0].value+"&password="+$("form.reg_form #password")[0].value+"&password_b="+$("form.reg_form #password_b")[0].value+"&email="+$("form.reg_form #email")[0].value+"&birthday="+$("form.reg_form #birthday_c")[0].value +"&pic="+$("form.reg_form #pic_url")[0].value,
							cache: false,
							success: function(html)
							{
								if(html=="OK")
								{
									$("div.login_loading").hide('slide', {}, 200, function()
									{
										$("div.login_loading").text("Registrierung erfolgreich erstellt!");
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
										$("div.login_loading").html('Registrierung fehlgeschlagen!<br />Bitte &uuml;berprüfen Sie Ihre Angaben!<br /><div><button id="btn_back">Zurück</button></div>');
										$("div.login_loading > div button").button();
										$("div.login_loading > div button#btn_back").click(function(){
											$("div.login_loading").hide('slide', {}, 200, function()
											{
												$("table.reg_tbl").show('slide', {}, 200, function(){});
											});
										});
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
	/*Modul:forum*/
});
