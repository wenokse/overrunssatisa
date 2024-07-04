$(document).ready(function () {

	load_comment();

	function load_comment()
	{
		$.ajax({
			type: "POST",
			url: "comment_code.php",
			data: {
				'comment_load_data': true
			},
			success: function (response) {
				$('.comment-container').html("");
				// console.log(response);
				$.each(response, function (key, value) { 
					$('.comment-container').
					append('<div class="reply_box border p-2 mb-2">\
							<h6 class="border-bottom d-inline"> '+value.user['firstname'] +' '+value.user['lastname'] +' : '+value.cmt['commented_on'] +' </h6>\
							<p class="para"> '+value.cmt['msg'] +' </p>\
							<button value="'+value.cmt['id'] +'" class="badge btn-warning text-dark reply_btn">Reply</button>\
							<button value="'+value.cmt['id'] +'" class="badge btn-danger view_reply_btn">View Replies</button>\
							<div class="ms-4 reply_section">\
							</div>\
						</div>\
					');
				});
			}
		});
	}

	$(document).on('click', '.reply_btn', function () {

		var thisClicked = $(this);
		var cmt_id = thisClicked;

		$('.reply_section').html("");
		thisClicked.closest('.reply_box').find('.reply_section').
		html('<input type="text" class="reply_msg form-control my-2" placeholder="Reply">\
				<div class="text-end">\
					<button class="btn btn-sm btn-primary reply_add_btn">Reply </button>\
					<button class="btn btn-sm btn-danger reply_cancel_btn">Cancel</button>\
				</div>');
		
	});

	$(document).on('click', '.reply_cancel_btn', function () {
		$('.reply_section').html("");
	});

	$(document).on('click', '.reply_add_btn', function (e) {
		e.preventDefault();
		
		var thisClicked = $(this);
		var cmt_id = thisClicked.closest('.reply_box').find('.reply_btn').val();
		var reply = thisClicked.closest('.reply_box').find('.reply_msg').val();

		var data = {
			'comment_id': cmt_id,
			'reply_msg': reply,
			'add_reply': true
		};
		$.ajax({
			type: "POST",
			url: "comment_code.php",
			data: data,
			success: function (response) {
				alert(response);
				$('.reply_section').html("");
			}
		});

	});

	$(document).on('click', '.view_reply_btn', function (e) {
		e.preventDefault();

		var thisClicked = $(this);
		var cmt_id = thisClicked.val();

		$.ajax({
			type: "POST",
			url: "comment_code.php",
			data: {
				'cmt_id': cmt_id,
				'view_comment_data': true
			},
			success: function (response) {
				// console.log(response);
				$('.reply_section').html("");

				$.each(response, function (key, value) { 
					thisClicked.closest('.reply_box').find('.reply_section').
					append('<div class="sub_reply_box border-bottom p-2 mb-2">\
								<input type="hidden" class="get_username" value="'+value.user['firstname'] +' '+value.user['lastname'] +'" />\
								<h6 class="border-bottom d-inline"> '+value.user['firstname'] +' '+value.user['lastname'] +' : '+ value.cmt['commented_on'] +' </h6>\
								<p class="para"> '+ value.cmt['reply_msg'] +' </p>\
								<button value="'+ value.cmt['comment_id'] +'" class="badge btn-warning text-dark sub_reply_btn">Reply</button>\
								<div class="ms-4 sub_reply_section">\
								</div>\
							</div>');
				});
			}
		});
	});

	$(document).on('click', '.sub_reply_btn', function (e) {
		e.preventDefault();

		var thisClicked = $(this);
		var cmt_id = thisClicked.val();
		var username = thisClicked.closest('.sub_reply_box').find('.get_username').val();

		$('.sub_reply_section').html("");
		thisClicked.closest('.sub_reply_box').find('.sub_reply_section').
			append('<div>\
						<input type="text" value="@'+username+' " class="sub_reply_msg form-control my-2" placeholder="Your Reply" />\
					</div>\
					<div class="text-end">\
						<button class="btn btn-sm btn-primary  sub_reply_add_btn">Reply</button>\
						<button class="btn btn-sm btn-danger  sub_reply_cancel_btn">Cancel</button>\
					</div>\
			');
	});

	$(document).on('click', '.sub_reply_add_btn', function (e) {
		e.preventDefault();

		var thisClicked = $(this);
		var cmt_id = thisClicked.closest('.sub_reply_box').find('.sub_reply_btn').val();
		var reply =  thisClicked.closest('.sub_reply_box').find('.sub_reply_msg').val();

		var data = {
			'cmt_id': cmt_id,
			'reply_msg': reply,
			'add_subreplies': true
		}

		$.ajax({
			type: "POST",
			url: "comment_code.php",
			data: data,
			success: function (response) {
				$('.reply_section').html("");
				alert(response);
			}
		});
		
	});

	$(document).on('click', '.sub_reply_cancel_btn', function (e) {
		e.preventDefault();

		$('.sub_reply_section').html("");
		
	});

	$('.add_comment_btn').click(function (e) { 
		e.preventDefault();

		var msg = $('.comment_textbox').val();
		if($.trim(msg).length == 0)
		{
			error_msg = "Please type comment";
			$('#error_status').text(error_msg);
		}else{
			error_msg = "";
			$('#error_status').text(error_msg);
		}

		if(error_msg != '')
		{
			return false;
		}
		else
		{
			var data = {
				'msg': msg,
				'add_comnment': true,
			};
			$.ajax({
				type: "POST",
				url: "comment_code.php",
				data: data,
				success: function (response) {
					alert(response);
					$('.comment_textbox').val("");
				}
			});
		}
	});

});