var $jQ = jQuery.noConflict();

$jQ(document).ready(function(){

	// Run the init method on document ready:
	chat.init();

});

var chatStop = false;

var chat = {

	// data holds variables for use in the class:

	data : {
		lastID 		: 0,
		noActivity	: 0
	},

	// Init binds event listeners and sets up timers:

	init : function(){

		// Converting the #chatLineHolder div into a jScrollPane,
		// and saving the plugin's API in chat.data:

		chat.data.jspAPI = $jQ('#chatLineHolder').jScrollPane({
			verticalDragMinHeight: 12,
			verticalDragMaxHeight: 12,
			autoReinitialise: true
		}).data('jsp');

		// We use the working variable to prevent
		// multiple form submissions:

		var working = false;
		// Submitting a new chat entry:

		$jQ('#submitForm').submit(function(){

			var text = $jQ('#chatText').val();

			if(text.length == 0){
				return false;
			}

			if(working) return false;
			working = true;

			// Assigning a temporary ID to the chat:
			var tempID = 't'+Math.round(Math.random()*1000000),
				params = {
					id			: tempID,
					author		: chat.data.name,
					avatar	: chat.data.avatar,
					text		: text.replace(/</g,'&lt;').replace(/>/g,'&gt;')
				};

			// Using our addChatLine method to add the chat
			// to the screen immediately, without waiting for
			// the AJAX request to complete:

			chat.addChatLine($jQ.extend({},params));

			// Using our tzPOST wrapper method to send the chat
			// via a POST AJAX request:

			$jQ.tzPOST('submitChat',$jQ(this).serialize(),function(r){
				working = false;

				$jQ('#chatText').val('');
				$jQ('div.chat-'+tempID).remove();

				params['id'] = r.insertID;
				chat.addChatLine($jQ.extend({},params));
			});

			return false;
		});


		// Checking whether the user is already logged (browser refresh)

		$jQ.tzGET('checkLogged',function(r){
			if(r.logged){
				chat.login(r.loggedAs.name,r.loggedAs.avatar);
			}
		});

		// Self executing timeout functions

		(function getChatsTimeoutFunction(){
			chat.getChats(getChatsTimeoutFunction);
		})();

		(function getUsersTimeoutFunction(){
			chat.getUsers(getUsersTimeoutFunction);
		})();

	},

	// The login method hides displays the
	// user's login data and shows the submit form

	login : function(name,avatar){

		chat.data.name = name;
		chat.data.avatar = avatar;
	//	$jQ('#chatTopBar').html(chat.render('loginTopBar',chat.data));

		$jQ('#loginForm').fadeOut(function(){
			$jQ('#submitForm').fadeIn();
			$jQ('#chatText').focus();
		});

	},

	// The render method generates the HTML markup
	// that is needed by the other methods:

	render : function(template,params){

		var arr = [];
		switch(template){
			case 'loginTopBar':
				arr = [
				'<span><img src="',params.avatar,'" width="23" height="23" alt="',params.name,'" title="',params.name,'"/>',
				'<span class="name">',params.name,
				'</span>'];
			break;

			case 'chatLine':
				arr = [
					'<div class="chat chat-',params.id,' rounded"><span class="avatar"><img src="',params.avatar,
					'" width="23" height="23" onload="this.style.visibility=\'visible\'" />','</span><span class="author">',params.author,
					':</span><span class="text">',params.text,'</span><span class="time">',params.time,'</span></div>'];
			break;

			case 'user':
				arr = [
					'<div class="user" title="',params.name,'"><img src="',
					params.avatar,'" width="30" height="30" alt="',params.name,'" title="',params.name,'" onload="this.style.visibility=\'visible\'" /></div>'
				];
			break;
		}

		// A single array join is faster than
		// multiple concatenations

		return arr.join('');

	},

	// The addChatLine method ads a chat entry to the page

	addChatLine : function(params){

		// All times are displayed in the user's timezone

		var d = new Date();
		if(params.time) {

			// PHP returns the time in UTC (GMT). We use it to feed the date
			// object and later output it in the user's timezone. JavaScript
			// internally converts it for us.

			d.setUTCHours(params.time.hours,params.time.minutes);
		}

		params.time = (d.getHours() < 10 ? '0' : '' ) + d.getHours()+':'+
					  (d.getMinutes() < 10 ? '0':'') + d.getMinutes();

		var markup = chat.render('chatLine',params),
			exists = $jQ('#chatLineHolder .chat-'+params.id);

		if(exists.length){
			exists.remove();
		}

		if(!chat.data.lastID){
			// If this is the first chat, remove the
			// paragraph saying there aren't any:

			$jQ('#chatLineHolder p').remove();
		}

		// If this isn't a temporary chat:
		if(params.id.toString().charAt(0) != 't'){
			var previous = $jQ('#chatLineHolder .chat-'+(+params.id - 1));
			if(previous.length){
				previous.after(markup);
			}
			else chat.data.jspAPI.getContentPane().append(markup);
		}
		else chat.data.jspAPI.getContentPane().append(markup);

		// As we added new content, we need to
		// reinitialise the jScrollPane plugin:

		chat.data.jspAPI.reinitialise();
		chat.data.jspAPI.scrollToBottom(true);

	},

	// This method requests the latest chats
	// (since lastID), and adds them to the page.

	getChats : function(callback){
		$jQ.tzGET('getChats',{lastID: chat.data.lastID},function(r){

			for(var i=0;i<r.chats.length;i++){
				chat.addChatLine(r.chats[i]);
			}

			if(r.chats.length){
				chat.data.noActivity = 0;
				chat.data.lastID = r.chats[i-1].id;
			}
			else{
				// If no chats were received, increment
				// the noActivity counter.

				chat.data.noActivity++;
			}

			if(!chat.data.lastID){
				chat.data.jspAPI.getContentPane().html('<p class="noChats">No chats yet</p>');
			}

			if(r.disconnect){
				chat.data.jspAPI.getContentPane().html('<p class="noChats">Disconnected: '+ r.disconnect + '</p>');
				$jQ(".videowhisper_htmlvideo").get(0).pause();
				$jQ(".videowhisper_htmlvideo").get(0).src ='';
				//$jQ("#streamContainer").css("visibility", "hidden");
				$jQ("#streamContainer").remove();
				chatStop = true;
			}
			// Setting a timeout for the next request,
			// depending on the chat activity:

			var nextRequest = 1000;

			// 2 seconds
			if(chat.data.noActivity > 3){
				nextRequest = 2000;
			}

			if(chat.data.noActivity > 10){
				nextRequest = 5000;
			}

			// 15 seconds
			if(chat.data.noActivity > 20){
				nextRequest = 15000;
			}

			if (!chatStop) setTimeout(callback,nextRequest);
		});
	},

	// Requesting a list with all the users.

	getUsers : function(callback){
		$jQ.tzGET('getUsers',function(r){

			var users = [];

			for(var i=0; i< r.users.length;i++){
				if(r.users[i]){
					users.push(chat.render('user',r.users[i]));
				}
			}

			var message = '';

			if(r.total<1){
				message = 'No viewer is online';
			}
			else {
				message = r.total+' '+(r.total == 1 ? 'viewer':'viewers')+' online';
			}

			users.push('<p class="count">'+message+'</p>');

			$jQ('#chatUsers').html(users.join(''));

			if (!chatStop) setTimeout(callback,15000);
		});
	},

	// This method displays an error message on the top of the page:

	displayError : function(msg){
		var elem = $jQ('<div>',{
			id		: 'chatErrorMessage',
			html	: msg
		});

		elem.click(function(){
			$jQ(this).fadeOut(function(){
				$jQ(this).remove();
			});
		});

		setTimeout(function(){
			elem.click();
		},5000);

		elem.hide().appendTo('body').slideDown();
	}
};

// Custom GET & POST wrappers:

$jQ.tzPOST = function(action,data,callback){
	$jQ.post(vwChatAjax + '&task=' + action,data,callback,'json');
}

$jQ.tzGET = function(action,data,callback){
	$jQ.get(vwChatAjax + '&task=' + action,data,callback,'json');
}

// A custom jQuery method for placeholder text:

$jQ.fn.defaultText = function(value){

	var element = this.eq(0);
	element.data('defaultText',value);

	element.focus(function(){
		if(element.val() == value){
			element.val('').removeClass('defaultText');
		}
	}).blur(function(){
		if(element.val() == '' || element.val() == value){
			element.addClass('defaultText').val(value);
		}
	});

	return element.blur();
}