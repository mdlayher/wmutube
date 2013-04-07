// AJAX login/logout functionality
$(function () {
	const ROOT = "/wmutube";

	// Login functionality
	$("#login_button").click(function() {
		// Capture data from form
		var login = {};
		login.username = $("#login_username").val();
		login.password = $("#login_password").val();

		// LDAP authentication
		login.method = 3;

		// Display status
		$("#login_status").text("logging in...");

		// Attempt login
		$.post(ROOT + "/ajax/login", login, function(data) {
			// Reload on successful login
			if (data.status === "success")
			{
				$("#login_status").text("success!");
				location.reload();
			}
			// Else, display error
			else
			{
				// Print error
				$("#login_status").text("failure!");
				$("#error").html("<p class=\"error\">login error: " + data.status + "</p>");
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
		$.post(ROOT + "/ajax/logout", function() {
			location = ROOT;
		});
	});
});
