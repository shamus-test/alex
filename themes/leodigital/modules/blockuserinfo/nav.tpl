<script type="text/javascript">
/* Blockusreinfo */
	
$(document).ready( function(){
	$(".leo-groupe").each( function(){
		var content = $(".groupe-content");
		$(".groupe-btn", this ).click( function(){
			content.toggleClass("eshow");
		}) ;
	} );
	
	$(window).resize(function() {
		if( $(window).width() > 600 ){
			 $(".groupe-content").removeClass('eshow');
		}
	});
});
</script>

<!-- Block user information module NAV  -->
<div class="leo-groupe g-dropdown">
	<a class="groupe-btn visible-sm" alt=""><span class="icon-user"></span></a>
	<div id="header_user_info" class="groupe-content hidden-xs hidden-sm">
		<ul class="links">
			
			{if $logged}
				<li class="link_topbar"><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}"  rel="nofollow">{l s='Welcome'} <span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span></a></li>
				<li class="link_topbar"><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" title="{l s='Log me out' mod='blockuserinfo'}" rel="nofollow"><i class="icon-power-off"></i> {l s='Sign out' mod='blockuserinfo'}</a></li>
				<li class="link_topbar"><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow"><i class="icon-user"></i> {l s='Your Account' mod='blockuserinfo'}</a></li>
				<li class="link_topbar"><a href="{$link->getModuleLink('blockwishlist', 'mywishlist')}" title="{l s='My wishlists' mod='blockuserinfo'}"><i class="icon-heart"></i> {l s='My wishlists' mod='blockuserinfo'}</a></li>
			{else}
				<li class="link_topbar" ><a href="javascript:void(0);" title="{l s='Login to your customer account' mod='blockuserinfo'}" rel="nofollow" onclick="showLoginPopup();"><i class="icon-lock"></i> {l s='Log in' mod='blockuserinfo'}</a></li>
				<!--<li class="link_topbar"><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='Register account' mod='blockuserinfo'}" rel="nofollow"><i class="icon-user"></i> {l s='Register' mod='blockuserinfo'}</a></li>-->
				<li class="link_topbar"><a href="{$link->getModuleLink('blockwishlist', 'mywishlist')}" title="{l s='My wishlists' mod='blockuserinfo'}"><i class="icon-heart"></i> {l s='My wishlists' mod='blockuserinfo'}</a></li>
			{/if}
		</ul>
	</div>
</div>
<!-- /Block usmodule NAV -->