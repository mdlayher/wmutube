// AJAX login/logout functionality
$(function () {
	// Login functionality
	$("#login_button").click(function() {
		// Capture data from form
		var login = {};
		login.username = $("#login_username").val();
		login.password = $("#login_password").val();

		// Database authentication
		login.method = 0;

		// Attempt login
		$.post("ajax/login", login, function(data) {
			// Reload on successful login
			if (data.status === "success")
			{
				location.reload();
			}
			// Else, display error
			else
			{
				$("#error").text("login error: " + data.status);
				$("#error").show();
			}
		}, "json");
	});

	// Login shortcuts by releasing enter in username/password boxes
	var login_shortcut = function(e) {
		if (e.which === 13)
		{
			$("#login_button").click();
		}
	};
	$("#login_username").keyup(login_shortcut);
	$("#login_password").keyup(login_shortcut);

	// Logout functionality
	$("#logout_button").click(function() {
		$.post("ajax/logout", function() {
			location.reload();
		});
	});
});
