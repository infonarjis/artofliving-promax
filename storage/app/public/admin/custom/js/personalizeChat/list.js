let lastChatMessageId = null;

$(document).ready(function () {
	$("#toggle_chatdata").on("click", function () {
		$(".left_chatlistpanel").toggleClass("hide-chatlistpanel");
		$(".chats-listingschats-box").toggleClass("full-size");
	});

	$('body').on('keyup', "#searchKeyword", function (e) {
		// if ($(this).val() == '') {
		getChatMemberList();
		// }
	});

	setInterval(function () {
		getChatMemberList();
	}, 2000);

	$('body').on('click', '.btn_chat_refresh', function (e) {
		e.preventDefault();
		var formData = new FormData();
		let conversationMemberId = $('#adminChatDiv').attr('data-member_id');
		let conversationMatriId = $('#adminChatDiv').attr('data-matri_id');
		formData.append('id', conversationMemberId);
		formData.append('matri_id', conversationMatriId);
		var url = 'custom-chat-messages';
		ajaxRequest($(this), formData, url, 'responseconversationList');

		getChatMemberList();
	});

	$('body').on('click', ".createConversation", function (e) {
		e.preventDefault();
		var formData = new FormData();
		let conversationMemberId = $(this).attr('member-id');
		let conversationMatriId = $(this).attr('member-matriId');
		formData.append('id', conversationMemberId);
		formData.append('matri_id', conversationMatriId);
		var url = 'custom-chat-messages';
		ajaxRequest($(this), formData, url, 'responseconversationList');
	});

	$(document).on('keydown', function (e) {
		if (e.key === 'Enter') {
			$('#sendMessage').html('<i class="bx bx-loader bx-spin"></i>');
			e.preventDefault(); // STOP new line
			$('#sendMessage').trigger('click');
		}
	});


	$('body').on('click', "#sendMessage", function (e) {
		e.preventDefault();
		var formData = new FormData($('#sendMessageForm')[0]);
		var url = $('#sendMessageForm').attr('action');
		ajaxRequest($(this), formData, url, 'responseSendMessage');
	});

	var interValTimer = setInterval(
		function () {
			if ($('body').find('#adminChatDiv').length > 0) {
				clearInterval(interValTimer);
				$('body').find('#adminChatDiv').on('scroll', function () {
					if ($(this).scrollTop() == ($(this).height() - $(this).height())) {
						if ($(this).attr('data-is-more') == 1) {
							$('.loader_chat__more').removeClass('d-none');
							var formData = new FormData();
							formData.append('page', $(this).attr('data-page'));
							formData.append('member_id', $(this).attr('data-member_id'));
							var url = $('#base_url').val() + '/personalize-chat/more-message';
							ajaxRequest($(this), formData, url, 'getMoreMessageResponse');
						}
					}
				});
			}
		}
		, 1000);
});

function responseSendMessage(_this, response) {
	if (response.status == 'success') {
		$('.right_bydefault_chatbg').remove();
		$('#textareaincrease').val('');
		$('#textareaincrease').focus();
		var scrollHeight = $('#adminChatDiv')[0].scrollHeight;
		$('#adminChatDiv').animate({ scrollTop: scrollHeight }, 'fast');
		$('#sendMessage').html('<i class="bx bx-send"></i>');
		$('#messageListAdmin').append("<div class='send_messages-div my-md-3 my-2 d-flex align-items-sm-start justify-content-end gap-2 gap-lg-3'><div class='send-messages'><div class='send-single-msg'>" + response.data.sendMessage + "<span class='send_msg_time'>" + response.data.sendOn + "</span></div></div></div>");
	} else {
		$("html, body").animate({ scrollTop: 0 }, "fast");
		showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.message, "Failed");
	}
}

function responseconversationList(_this, response) {
	if (response.status == 'success') {
		$('#unreadMsgCount' + response.data.memberId).addClass('d-none');
		$("#chatConversation").html(response.data.html);
		var scrollHeight = $('#adminChatDiv')[0].scrollHeight;
		$('#textareaincrease').focus();
		$('#adminChatDiv').animate({ scrollTop: scrollHeight }, 'fast');
		const textarea = document.getElementById("textareaincrease");
		setListner(textarea);
		setHeight(textarea);
	}
}

function getChatMemberList() {

	var formData = new FormData();

	formData.append('searchKeyword', $('#searchKeyword').val());
	formData.append('type', $(".dataListTab.active").attr('data-list'));

	let memberId = $('#adminChatDiv').attr('data-member_id');

	if (memberId) {
		formData.append('member_id', memberId);
	}

	var url = 'custom-chat-list';

	ajaxRequest($(this), formData, url, 'responseChatMemberList');
}

function responseChatMemberList(_this, response) {
	if (response.status == 'success') {
		$('#recentChatsList').html(response.data.recentHtml);
		let latestMessageId = response.data.latestMessageId;
		let latestSenderType = response.data.latestSenderType;

		// First request
		if (lastChatMessageId === null) {
			lastChatMessageId = latestMessageId;
			return;
		}
		// New message received from MEMBER
		if (latestMessageId && latestMessageId != lastChatMessageId && latestSenderType == 2) {
			lastChatMessageId = latestMessageId;
			if ($('.btn_chat_refresh').length > 0) {
				$('.btn_chat_refresh').trigger('click');
			}
		}
	}
}

// get More Message Response : Date : 18-11-2021
function getMoreMessageResponse(_this, response) {
	$('.loader_chat__more').addClass('d-none');
	if (response.status == 'success') {
		var page = parseInt($(_this).attr('data-page'));
		$(_this).attr('data-page', page + 1);
		$(_this).find('#messageListAdmin').prepend(response.data.html);
	} else if (response.status == 'no_message') {
		$(_this).attr('data-is-more', '0');
	} else {
		alert(response.msg);
	}
}

function setListner(textarea) {
	textarea.addEventListener("input", (e) => {
		// setHeight(e.target);
	});
}

function setHeight(elem) {
	const style = getComputedStyle(elem, null);
	const verticalBorders = Math.round(parseFloat(style.borderTopWidth) + parseFloat(style.borderBottomWidth));
	const maxHeight = parseFloat(style.maxHeight) || 75;
	elem.style.height = "9px";
	const newHeight = elem.scrollHeight + verticalBorders;
	elem.style.overflowY = newHeight > maxHeight ? "auto" : "hidden";
	elem.style.height = Math.min(newHeight, maxHeight) + "px";
}
