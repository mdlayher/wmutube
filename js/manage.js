var manage = (function () {
	$(function () {
		$(".site-section").on("click", ".delete_video", function () {
			// $.post("/ajax/users/delete/" + this.getAttribute("data-userId"), function (data) {
			// 	var resp = JSON.parse(data);
			// 	if (resp["success"] == true) {
			// 		// remove the item from the dom
			// 		 $(this).parent().parent().remove();
			// 	}
			// });
			$(this).parent().parent().remove();
		});

		$("#user_enable_submit").click(function () {
			$.post("/wmutube/ajax/user/enable", "username=" + $("#username").val(), function (data) {
				var resp = JSON.parse(data);
				if (resp["success"] == true) {
					$("#username").val("");	
				}
			});
		});

		$("#user_disable_submit").click(function () {
			$.post("/wmutube/ajax/user/disable", "username=" + $("#username").val(), function (data) {
				var resp = JSON.parse(data);
				if (resp["success"] == true) {
					$("#username").val("");					
				}
			});
		});

		$("#course_delete_submit").click(function () {
			var $del = $($("#course_delete_select").find(":selected").first());
			var id = $del.attr("data-course_id");
			if (id !== undefined) {
				$.post("/wmutube/ajax/course/delete", "id=" + id, function (data) {
					var resp = JSON.parse(data);
					if (resp["success"] == true) {
						$del.remove();
					}
				});
			}
		});

		$("#course_add_submit").click(function (data) {

			var num = $("#course_add_number")[0];
			var title = $("#course_add_title")[0];
			var subject = $("#course_add_subject")[0];
			
			// validate the form
			if (!(num.checkValidity() && title.checkValidity() && subject.checkValidity())) {
				return;
			}

			theObj = {
				"number": parseInt($(num).val()),
				"title": $(title).val(),
				"subject": $(subject).val()
			};

			console.log(JSON.stringify(theObj));

			$.post("/wmutube/ajax/course/create", "courseInfo=" + JSON.stringify(theObj), function (data) {
				var resp = JSON.parse(data);
				if (resp["success"] == true) {
					$(num).val("");
					$(title).val("");
					$(subject).val("");
				}
			});
		});

		$(".show_results").click(function () {

			var top = $(this).parent().parent();

			// if there's already a table, toggle it and return
			var table = top.find("table");
			if (table.length > 0) {
				if (table.first().css("display") != "none") {
					table.hide();
					$(this).text("Show quiz results");
				} else {
					table.slideDown();
					$(this).text("Hide quiz results");
				}

				return;
			}

			$.get("/wmutube/ajax/video/responses/" + $(top).attr("data-video_id"), function (data) {
				var resp = JSON.parse(data);
				console.log(resp);

				// for each question
				$.each(resp["questions"], function (index, item) {
					var answers = item["user_answers"];
					var correctAnswerId;

					// find the correct answer
					$.each(item.answers, function (index, item) {
						if (item.correct == "1") {
							correctAnswerId = item.id;
							console.log("Correct answer is: " + correctAnswerId);
							return false;
						}
					});

					// append results to the dom for this question
					if (item.user_answers.length > 0) {
						// append the top of the table
						top.append("<table><thead><tr><td class='question_text' colspan='3'>" + item.text + "</td></tr><tr><td>Student</td><td>Submitted on</td><td>Correct</td></tr></thead><tbody></tbody></table>");
						$tbody = $($(top).find("tbody")[index]);
						$.each(item.user_answers, function (index, item) {
							var timestamp = new Date(parseInt(item.timestamp) * 1000);
							var date = timestamp.getMonth() + "/" + timestamp.getDate() + "/" + timestamp.getFullYear();
							var correct = "Incorrect";
							console.log(item.user.firstname + " " + item.user.lastname);
							if (item.answerid == correctAnswerId) {
								correct = "Correct";
							}
							$tbody.append("<tr><td>" + item.user.firstname + " " + item.user.lastname + "</td><td>" + date + "</td><td class='" + correct + " + '>" + correct + "</td></tr>");
						});
					} else {
						// top.append("There are no responses yet.");
					}

					top.find("table").slideDown();
					console.log(item["user_answers"]);
				});
			});
		});
	});
})();
