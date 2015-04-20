{*
* 2007-2014 PrestaShop
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
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<section id="social_block">
	<ul>
		<li>
			<div class="social">
				{if $facebook_url != ''}
						<a  class="facebook" target="_blank" href="{$facebook_url|escape:html:'UTF-8'}">
							<i class="icon-facebook"></i><span>{l s='Facebook' mod='blocksocial'}</span>
						</a>
				{/if}
				{if $twitter_url != ''}
					
						<a class="twitter" target="_blank" href="{$twitter_url|escape:html:'UTF-8'}">
							<i class="icon-twitter"></i> <span>{l s='Twitter' mod='blocksocial'}</span>
						</a>
					
				{/if}
				{if $pinterest_url != ''}        	
        		<a class="pinterest" target="_blank" href="{$pinterest_url|escape:html:'UTF-8'}">
        			<i class="icon-pinterest"></i><span>{l s='Pinterest' mod='blocksocial'}</span>
        		</a>
        	
		        {/if}
				
				{if $google_plus_url != ''}
		        	
		        		<a class="google-plus" target="_blank" href="{$google_plus_url|escape:html:'UTF-8'}">
		        			<i class="icon-google-plus"></i><span>{l s='Google Plus' mod='blocksocial'}</span>
		        		</a>
		        	
		        {/if}
				
				{if $rss_url != ''}
						<a class="rss" target="_blank" href="{$rss_url|escape:html:'UTF-8'}">
							<i class="icon-rss"></i> <span>{l s='RSS' mod='blocksocial'}</span>
						</a>
					
				{/if}
		        {if $youtube_url != ''}
		        	
		        		<a class="youtube" target="_blank"  href="{$youtube_url|escape:html:'UTF-8'}">
		        			<i class="icon-youtube"></i> <span>{l s='Youtube' mod='blocksocial'}</span>
		        		</a>
		        	
		        {/if}
		        
    	</div>
    </li>
	</ul>
</section>
<div class="clearfix"></div>
