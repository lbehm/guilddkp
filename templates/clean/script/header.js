$(function() {
	$( "#calendar" ).datepicker();
});
$(document).ready(
	function()
	{
		$("#login_btn").click(function(){
			$("div#box")[0].title = "Login";
			$("div#box")[0].innerHTML = '<form class="login_form" action="login.php" methode="Post"><table class="login_data"><tr><td>Benutzername:</td><td><input type="text" name="username" id="name" class="text ui-widget-content ui-corner-all" value="" /></td></tr><tr><td>Passwort:</td><td><input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" /></td></tr></table><input type="submit" style="display:none;" /></form><div class="login_loading">Loading...</div>';
			$("div#box").dialog(
			{
				resizable: false,
				width: 260,
				height: 100,
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
								data: "external=yes&username="+$("form.login_form table.login_data #name").val()+"&password="+$("form.login_form table.login_data #password").val(),
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
											$("div.login_loading").html('Login fehlgeschlagen!<br />Bitte &uuml;berprüfen Sie Ihre Logindaten!<br /><div><button id="btn_back">Zurück</button><button id="btn_lostpwd">Passwort vergessen</button></div>');
											$("div.login_loading > div button").button();
											$("div.login_loading > div button#btn_back").click(function(){
												$("div.login_loading").hide('slide', {}, 200, function()
												{
													$("table.login_data").show('slide', {}, 200, function(){});
												});
											});
											$("div.login_loading > div button#btn_lostpwd").click(function(){});
											$("div.login_loading > div").buttonset();
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
						window.location = "login.php?logout&re=viewnews.php";
					},
					"Abbrechen!": function() {
						$( this ).dialog( "close" );
					}
				}
			});
			return false;
		});
	}
);
