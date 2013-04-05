$(function () {
	var questionAnswers = (function () {
		console.log("Hello world!");
		
	}());

	$(document).on("mousedown", ".answer", function () {
			console.log("clicked!");
			$(".answer>.radio").removeClass("selected");
			$(this).children(".radio").addClass("selected");
		});

	var quizzing = (function () {

		var quizQuestions = [
			{
				question: "What is the diameter of the sun?",
				presentationTime: 7,
				answers: [
					{ text: "5km", correct: false, answerId: 1 },
					{ text: "20km", correct: false, answerId: 2 },
					{ text: "500km", correct: false, answerId: 3 },
					{ text: "Larger than all of these combined", correct: true, answerId: 4 }
				]
			},
			{
				question: "Another question!",
				presentationTime: 15,
				answers: [
					{ text: "yes", correct: false, answerId: 5 },
					{ text: "no", correct: false, answerId: 6 },
					{ text: "pz", correct: false, answerId: 7 }
				]
			},
		];

		var newAnswerDiv = function (answerText, answerId) {
			// create the div that contains the answer
			var ans = 	"<div class='answer' data-answer_id='" + answerId + "'>\
							<div class='radio'></div>\
							<div class='answer_text'>" + answerText + "</div>\
						</div>";

			return ans;
		};

		var publicMembers = {
			PresentQuizIfNecessary: function (currentTime) {
				var presentIndex = undefined;
				$.each(quizQuestions, function (index, item) {
					if (item.presentationTime <= currentTime && typeof(item.presented) === "undefined") {
						console.log("Q" + index + ": We should now present this item. ");
						item.presented = true;
						presentIndex = index;
						return false;
					}
				});

				if (presentIndex !== undefined) {

					// remove all the answers from the dom
					$(".answer").remove();

					var question = quizQuestions[presentIndex];

					$(".question>div").html("<p>" + question.question + "</p>");
					$.each(question.answers, function (index, item) {
						$(".answers>div").append(newAnswerDiv(item.text, item.answerId));
					});

					$('.body_fade').css("display", "block");
					$('.quiz_container').css("display", "block");

					return true;
				} else {
					return false;
				}
			}
		};

		$(function () {
			// on ready
			$("#quiz_submit").click(function () {
				$('.body_fade').css("display", "none");
				$('.quiz_container').css("display", "none");
				player.play();
			});
		});
		return publicMembers;

	})();

	var player = (function () {

		var videoPlayer = null;
		_V_("video_player").ready(function () {
			videoPlayer = this;

			$("#" + this.id).css("width", "100%");
			$("#" + this.id).height("100%");

			videoPlayer.addEvent("timeupdate", function (e) {

				var currentTime = videoPlayer.currentTime();
				// check if it's time to show another quiz.
				if (quizzing.PresentQuizIfNecessary(currentTime) === true) {
					console.log("Aaaaaaaand we should pause");
					videoPlayer.pause();
				}
				console.log(currentTime);
			});
		});

		var publicMembers = {
			play: function () {
				videoPlayer.play();
			},
			pause: function () {
				videoPlayer.pause();
			}
		};

		return publicMembers;
	}());
});