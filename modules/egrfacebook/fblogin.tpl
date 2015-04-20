{*
*
* 2012 eGrove Systems Corporation
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade to newer
* versions in the future. If you wish to customize for your
* needs please refer to http://www.egrovesys.com for more information.
*
*  @author eGrove 167 <support@egrovesystems.com>
*  @copyright  2012 eGrove Systems Corporation
*  @version  Release: $Revision: 1.0 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of eGrove Systems Corporation
*}
{assign var='image_file' value="flogin`$fimage_no`.png"}
{if $fimage_no == 4}
{assign var='image_file' value="$fcustom"}
{else}

{/if}
<div id="fb-root"></div>
<div id="egrfbconnect_block" >
<div class="fbconnect_login" style="float:right;">
     <a class="fb_button_ps" onclick="return fblogin();" href="javascript:void(0);">
	  <img alt="Connect to Facebook" title="Connect to Facebook" src="{$mod_dir}egrfacebook/{$image_file}">
     </a>
</div>
<div style="float:right;display:none;" id="fbconnect_logout"> 
     <a class="fb_button_ps" onclick="return fblogout();" href="javascript:void(0);">
	  <img alt="Log out from Facebook" title="Log out from Facebook" src="{$mod_dir}egrfacebook/flogout.png">
     </a>
</div>
<div style="float:right;display:none;" class="fbconnect_loading"> 
     <a class="fb_button_ps" onclick="return fblogout();" href="javascript:void(0);">
	  <img alt="Connecting..." title="Connecting..." src="{$mod_dir}egrfacebook/fload.png">
     </a>
</div>
<div id="fbconnet_pic" style="float:right;"></div>
<div style="clear:both;"></div>
<div style="display:none"><a href="http://www.modulebazaar.com/en/modules/prestashop.html">PrestaShop Modules</a></div>

</div>
<script src="https://connect.facebook.net/en_US/all.js"></script>
<script>
     var base_dir = '{$mod_dir}';
     var islogged = '{$islogged}';
     /* API initialization */
     function init(){
        FB.init({
	       appId: '{$appid}',
	       status: true,
	       cookie: true,
	       xfbml: true,
	       oauth: true
	  });
     }
     init();
     //Check the facebook Login Status

     siteLoginStatus();
     function siteLoginStatus()
     {
	  if(islogged > 0)
	  {
	      	      
	       FB.getLoginStatus(function(response)
	       {
		    if (response.status === 'connected')
		    {
			 var fblogpic = getCookie('fb_user_pic');
			 var fblogcheck = getCookie('fb_user_id');
			 if(fblogcheck != undefined && fblogcheck.length > 0)
			 {
			  $('#fbconnet_pic').html('<img id="belvg-fb-img" style="margin-left:5px" height="24" src="'+fblogpic+'"/>');
			 }
			 $('#fbconnect_logout').show();
			 $('.fbconnect_login').hide();
			 
		    }
	       });
	  }	    
     }

    function fblogin(){
          FB.login(function(response){
	       setCookie('fb_user_id',response.authResponse.userID,1);
	       if(response.status == 'connected'){
		    login(response.authResponse.accessToken);
	       }
          },
	  {
	       scope:'email,user_likes,user_birthday,publish_stream'
	  });
          return false;
     }
     
     function login(token){
	  
	  get_profile_pic();
	   
	  // Start Loading
	  $('.fbconnect_login').hide();
	  $('.fbconnect_loading').show();
	  
	  $.ajax({
	       type: "POST",
	       url: base_dir+'egrfacebook/fbResponseHandler.php',
	       data: "accessToken="+token+"",
	       success: function(msg) {
		    // Stop Loading
		    var current_url = window.location.href;
		    var queryPos = current_url.lastIndexOf('?');
		    queryPos = parseInt(queryPos) + 1;
		    current_url = current_url.substr(queryPos);
		    queryStrings = current_url.split('&');
		    var noBack = 1;
		    if(queryStrings.length > 0)
		    {
			 var params = queryStrings[0].split('=');
			 if(params.length > 0)
			 {
			      if(params[0] == 'back')
			      {
				   var backUrl = decodeURIComponent(params[1]);
				   window.location.href= backUrl;
				   noBack = 0;
			      }
			 }
		    }
		    if(noBack == 1)
		    {
		    	 window.location.href= window.location.href;
		    }
	       }
	  });
     }
     
     function get_profile_pic(){
	  FB.api('/me', function(response) {
	       var src = 'https://graph.facebook.com/'+response.id+'/picture';
	       setCookie('fb_user_pic',src,1);
		
          });
     }
     
     function fblogout(){
	  FB.logout(function(response){
	       deleteCookie('fb_user_id');
	       deleteCookie('fb_user_pic');
	       logout();
	  });
     }
     
     function logout(){
	  window.location.href= '?mylogout';
     }
	  
     /* Post feed to facebook wall */
     function postToFeed(siteUrl,siteName,orderDetails, resultID) {
	  
	  var siteUrl = document.getElementById('post_site_url').value;
	  var siteName = document.getElementById('post_site_name').value;
	  var orderDetails = document.getElementById('post_order_details').value;
	  var resultID = document.getElementById('post_result').value;
	  
	  var obj = {
	       method: 'feed',
	       link: siteUrl,
	       name: 'My new orders',
	       caption: siteName,
	       description: orderDetails
	  };

	  function callback(response) {
	       ;
	  }

	  FB.ui(obj, callback);
     }
     
     /* custom function for setting a cookie */
     function setCookie(c_name,value,expires)
     {
	  document.cookie = c_name + "=" +escape(value);
     }
     
     /* custom function for delete a cookie */
     function deleteCookie(name)
     {
	 document.cookie = name + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
     }
     
     /* custom function for get a cookie */
     function getCookie(name)
     {
	  var i,x,y,ARRcookies=document.cookie.split(";");
	  for (i=0;i<ARRcookies.length;i++)
	  {
	       x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
	       y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
	       x=x.replace(/^\s+|\s+$/g,"");
	       if (x==name)
	       {
		    return unescape(y);
	       }
	  }
     }
</script>

