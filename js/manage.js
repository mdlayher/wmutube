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

		$("#user_lookup_submit").click(function () {
			$.post("/ajax/users/find/" + $("#user_lookup_text").val(), function (data) {
				var resp = JSON.parse(data);
				if (resp["success"] == true) {
					// add users to the dom
				}
			});
		});

		$("#dept_delete_submit").click(function () {
			var $del = $($("#dept_delete_select").find(":selected").first());
			var id = $del.attr("data-dept_id");
			if (id !== undefined) {
				$.post("/ajax/departments/delete/" + id, function (data) {
					var resp = JSON.parse(data);
					if (resp["success"] == true) {
						$del.remove();
					}
				});
			}
		});

		$("#course_delete_submit").click(function () {
			var $del = $($("#course_delete_select").find(":selected").first());
			var id = $del.attr("data-course_id");
			if (id !== undefined) {
				$.post("/ajax/courses/delete/" + id, function (data) {
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

		$("#dept_add_submit").click(function (data) {
			var $add = $("#dept_add_text");
			if ($add.val().length > 0) {
				$.post("/ajax/departments/add/" + $add.val(), function (data) {
					var resp = JSON.parse(data);
					if (resp["success"] == true) {
						// add this to the course delete select box
						$("#dept_delete_select").append("<option>" + $add.val() + "</option>");
						$add.val("");
					}
				});
			}
		});
	});
})();
