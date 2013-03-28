$(function()
{
	var timeout;	
	var drawer_open = function() {
		// on mouse in
		timeout = setTimeout(function() {
			$('#browse_drawer').transition({ height: '250px' }, 300, 'snap', function() {
				// callback
				$('browse_drawer').addClass('shadow');
			});
		}, 300);
	};
	var drawer_close = function() {
		// on mouse out
		clearTimeout(timeout);
		$('#browse_drawer').css('height', 0);
	};

	$("#videos_link").hover(drawer_open, drawer_close);
	$("#manage_link").hover(drawer_open, drawer_close);
});
