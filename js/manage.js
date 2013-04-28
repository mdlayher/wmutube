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
	});
})();
