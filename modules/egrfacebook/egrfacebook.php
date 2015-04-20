<?php
/*
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
*  @version  Release: $Revision: 1.1 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of eGrove Systems Corporation
*/

/*   
	  	Project Name : Social Connect Facebook Module 
		Created By 	: egs161
		Created On 	: 1-09-2012
		Updated By 	: egs161
		Updated By 	: egs167, 21-01-2014
		Filename   	: egrfacebook.php (Main class file)
		Purpose    	: Social Connect Facebook Module
		Module Name 	: Social Connect Facebook Module
		Tables Used 	: ps_fbconnect_customer,
		  		  ps_hook_module,
*/	

class EgrFacebook extends Module
{
		/* Initialiaze Default Settings */
		public function __construct()
		{
				$version_mask = explode('.', _PS_VERSION_, 3);
				$version_test = $version_mask[0] > 0 && $version_mask[1] > 3;
				$this->name = 'egrfacebook';
				$this->tab = 'front_office_features';
				if ($version_test)
				$this->author = 'eGrove Systems';
				$this->version = '1.2';
				$this->need_instance = 0;
				
				parent::__construct();
				$this->displayName = $this->l('Social Facebook module');
				$this->description = $this->l('Connect with facebook, automatic registration');
				
		}
		
		/* Installing module */
		function install()
		{
				parent::install();
				if (!$this->createFacebookConfigTbl())
						return false;
				if (!$this->createFacebookCustomersTbl())
						return false;
				if (!$this->registerHook('top'))
						return false;	
				if (!$this->positionHook())
						return false;
				if (!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES ("", "egrfblogin", "facebook login", "facebook login block", "1")'))
						return false;
				if (!Configuration::updateValue('EGR_FB_HOOK_ID',Db::getInstance()->Insert_ID()))
						return false;
				if (!$this->registerHook('fblogin'))
						return false;
				
				if(!$this->moveOverriteFile())
						return false;
				return true;
		}
		
		/* Un installing module */
		function uninstall()
		{
				parent::uninstall();
				if( !$this->dropFacebookConfigTbl() && !$this->unregisterHook('top'))
						return false;	
				if(!$this->unregisterHook('top'))
						return false;
				if(!$this->dropFacebookCustomersTbl())
						return false;
				if(!$this->unregisterHook('egrfblogin'))
						return false;
				$con_id = Configuration::get('EGR_FB_HOOK_ID');
				if (!Db::getInstance()->Execute('DELETE FROM  `'._DB_PREFIX_.'hook` WHERE id_hook = '.$con_id.''))
						return false;				
				//if(!$this->removeOverriteFile())
				//		return false;
				
				Configuration::deleteByName('EGR_FB_HOOK_ID');

				
				return true;
		}
		
		/* Move flogin button to top right */
		function positionHook()
		{
				$db2 = Db::getInstance();
				$query = 'UPDATE '._DB_PREFIX_.'hook_module SET position=2 WHERE id_hook=14 and id_module=(SELECT id_module FROM '._DB_PREFIX_.'module WHERE name=\'egrfacebook\' )';
				$db2->Execute($query);
				return true;
		}
		
		/* Handle module settings in admin panel */
		public function getContent()
		{	
			$this->_html="<h2 class='tab'>".$this->l('Fbconnect Module Settings')."</h2>";

				if (Tools::isSubmit('submitFUOF'))/* Fconnect settings */
				{
						Configuration::updateValue(strtoupper($this->name).'_LOGIN_ACTIVITY', Tools::getValue('loginactivity'));
						Configuration::updateValue(strtoupper($this->name).'_APPID', Tools::getValue('appid'));
						Configuration::updateValue(strtoupper($this->name).'_SECRET', Tools::getValue('secret'));
						
						
						Configuration::updateValue(strtoupper($this->name).'_LIKE_ACTIVITY', Tools::getValue('likeactivity'));
						Configuration::updateValue(strtoupper($this->name).'_SHOW_FACES', Tools::getValue('showfaces'));
						
				
						Configuration::updateValue(strtoupper($this->name).'_SHARE_ACTIVITY', Tools::getValue('shareactivity'));
						
				
						Configuration::updateValue(strtoupper($this->name).'_FIND_PAGE_URL', Tools::getValue('find_pageurl'));
						Configuration::updateValue(strtoupper($this->name).'_FIND_WIDTH', Tools::getValue('find_width'));
						Configuration::updateValue(strtoupper($this->name).'_FIND_HEIGHT', Tools::getValue('find_height'));
						Configuration::updateValue(strtoupper($this->name).'_FIND_SHOW_FACES', Tools::getValue('find_showfaces'));
						Configuration::updateValue(strtoupper($this->name).'_FIND_SHOW_HEADER', Tools::getValue('find_showheader'));
						
						Configuration::updateValue(strtoupper($this->name).'_POST_WALL', Tools::getValue('postwall'));
						Configuration::updateValue(strtoupper($this->name).'_LOGIN_BUTTON', Tools::getValue('login_button'));
						
						if(Tools::getValue('login_button') == 4)
						{
								if(isset($_FILES) AND sizeof($_FILES) AND $_FILES['custom_file']['name'] != NULL)
								{
										$ext = substr(strrchr($_FILES['custom_file']['name'], '.'), 1);
										$custom_file = 'custom_login.'.$ext;

										if(move_uploaded_file($_FILES["custom_file"]["tmp_name"],_PS_ROOT_DIR_.'/modules/egrfacebook/'.$custom_file))
										{
												Configuration::updateValue(strtoupper($this->name).'_LOGIN_CUSTOM', $custom_file);
										}
								}
						}
						
						
						$this->_html.= $this->displayConfirmation($this->l('Fbconnect module settings have been Updated'));
				}
		               

				$this->_displayForm();
				return $this->_html;
		}
		
		/* Forms for module settings in admin panel*/
		public function _displayForm()
		{
				$this->_html.="
				<link type='text/css' rel='stylesheet' href='"._MODULE_DIR_."egrfacebook/css/tabpane.css' />
				<form action='' method='post' style=' float:left;' enctype='multipart/form-data'>
						<div class='tab-pane' id='tab-pane-1' style='width:900px;'>
						<div class='tab-page' id='step1'>
						
						<table class='egr-table'><tr><td>
												
						<tr><td colspan=2><b>".$this->l('Facebook Settings')."</b></td></tr>
						<tr>
							<td style='width:130px;'>".$this->l('App Id')."</td>
							<td><input type='text' name='appid' value='".Tools::getValue('appid', Configuration::get(strtoupper($this->name).'_APPID'))."' /></td>
						</tr>
						<tr>
							<td>".$this->l('Secret Key')."</td>
							<td>
								<input type='password' name='secret' value='".Tools::getValue('secret', Configuration::get(strtoupper($this->name).'_SECRET'))."'   />
							</td>
						</tr>
						
						<tr>
						<td colspan=2 align=left style='padding-left:4px'>"
						.$this->l('Select your login button').
						"
						</td></tr>
						<tr><td colspan=2 align=left style='padding-left:11px'>
						<input onclick=\"$('#custom_file_block').hide();\" type='radio' value='1' id='login_button' name='login_button'
						   ".((Configuration::get(strtoupper($this->name).'_LOGIN_BUTTON')==1) ? 'checked="checked" ' : '').">"
						   ."<img alt='Login with Facebook' src='"._MODULE_DIR_."egrfacebook/flogin1.png'>".
						   "
						</td></tr>
						<tr><td colspan=2 align=left style='padding-left:11px'>
						<input onclick=\"$('#custom_file_block').hide();\" type='radio' value='2' id='login_button' name='login_button'
						   ".((Configuration::get(strtoupper($this->name).'_LOGIN_BUTTON')==2) ? 'checked="checked" ' : '').">"
						   ."<img alt='Login with Facebook' src='"._MODULE_DIR_."egrfacebook/flogin2.png'>".
						   "
						</td></tr>
						<tr><td colspan=2 align=left style='padding-left:11px'>
						<input onclick=\"$('#custom_file_block').hide();\" type='radio' value='3' id='login_button' name='login_button'
						   ".((Configuration::get(strtoupper($this->name).'_LOGIN_BUTTON')==3) ? 'checked="checked" ' : '').">"
						   ."<img alt='Login with Facebook' src='"._MODULE_DIR_."egrfacebook/flogin3.png'>".
						   "
						</td></tr>
						<tr><td colspan=2 align=left style='padding-left:11px'>
						<input onclick=\"$('#custom_file_block').show();\" type='radio' value='4' id='login_button' name='login_button'
						   ".((Configuration::get(strtoupper($this->name).'_LOGIN_BUTTON')==4) ? 'checked="checked" ' : '').">"
						   .$this->l('Custom Upload');
						$this->_html.= "</td></tr>";
						$custom_show = 'none';
						if(Configuration::get(strtoupper($this->name).'_LOGIN_BUTTON')==4)
						{
								$custom_show = 'block';
						}
						   $this->_html.=  "<tr>
								<td colspan=2 align=left style='padding-left:11px;display:".$custom_show."' id='custom_file_block'>";
								
								
										$custom_file = Configuration::get(strtoupper($this->name).'_LOGIN_CUSTOM');
										if(!empty($custom_file))
										{
												$this->_html.=  "<img alt='Login with Facebook' src='"._MODULE_DIR_."egrfacebook/".Configuration::get(strtoupper($this->name).'_LOGIN_CUSTOM')."'>";
										}
								
								
								$this->_html.=  "<br/><input type='file' id='custom_file' name='custom_file'/>
								</td></tr>";
						  $this->_html.= "
						<tr><td colspan=2 align=center><input type='submit' name='submitFUOF' value='".$this->l('Update settings')."' class='button' /></td></tr>
						<tr><td colspan=2 align=center><a class='module_link'href='http://www.modulebazaar.com/en/modules/prestashop.html'>PrestaShop Modules</a></tr>

						</table>
						</div>
						</div>
						</form>";
						
		}
		
		
		/**
		* Returns module content for top position
		*
		* @param array $params Parameters
		* @return string Content
		*/
		public function hookTop($params)
		{
				global $smarty,$cookie;
				
				$fb_user_id = '';
				$fb_user_connected = 0;
				
				/*checks whether user logged in with facebook account */
				if(isset($_COOKIE['fb_user_id']))
				{
						$fb_user_id = $_COOKIE['fb_user_id'];
						$fb_user_connected = 1;
				}
		
				/* define the output for facebook connect along with avatar pic if logged in*/
				$is_logged = isset($params['cookie']->id_customer)?$params['cookie']->id_customer:0;

				$smarty->assign(array(
						'appid' => Configuration::get(strtoupper($this->name).'_APPID'),
						'app_secret' => Configuration::get(strtoupper($this->name).'_SECRET')
				));
				$smarty->assign('islogged', $is_logged);
				$smarty->assign('fb_user_connected', $fb_user_connected);
				$smarty->assign('mod_dir',_MODULE_DIR_);
				$smarty->assign('fimage_no',Configuration::get(strtoupper($this->name).'_LOGIN_BUTTON'));
				$custom_file = Configuration::get(strtoupper($this->name).'_LOGIN_CUSTOM');

				if(Configuration::get(strtoupper($this->name).'_LOGIN_BUTTON') == 4)
				{
						if(empty($custom_file))
						{
								$smarty->assign('fimage_no',1);	
						}
						else
						{
								$smarty->assign('fcustom',Configuration::get(strtoupper($this->name).'_LOGIN_CUSTOM'));
						}
				}

				return $this->display(__FILE__, 'fblogin.tpl');
		}
		
		public function hookEgrfblogin($params)
		{ 
				return $this->hookTop($params);
		}

		public function hookfblogin($params){
			return $this->hookTop($params);
		}
	
		/* Method for getting current url*/
		function curPageURL()
		{
				$pageURL = 'http';
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
				{
						$pageURL .= "s";
				}
				$pageURL .= "://";
				if ($_SERVER["SERVER_PORT"] != "80")
				{
						$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				}
				else
				{
						$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				}
				return $pageURL;
		}
		
		/* Method for creating module table to DB */
		function createFacebookCustomersTbl()
		{
				$db = Db::getInstance();
				$query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'fbconnect_customer (
				  `cust_id` int(10) NOT NULL,
				  `fb_id` bigint(20) NOT NULL,
				  UNIQUE KEY `FB_CUSTOMER` (`cust_id`,`fb_id`)
				  ) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").' DEFAULT CHARSET=utf8';
				$db->Execute($query);
				return true;
		}
		
		/* Method for deleting module table from DB */
		
		function dropFacebookCustomersTbl()
		{
				$db = Db::getInstance();
				$query = 'DROP TABLE '._DB_PREFIX_.'fbconnect_customer';
				$db->Execute($query);
				return true;
		}
		
		/* Method for inserting module settings to DB */
		function createFacebookConfigTbl()
		{
				global $smarty;
				
				
				Configuration::updateValue(strtoupper($this->name).'_APPID', '');
				Configuration::updateValue(strtoupper($this->name).'_SECRET', '');
				Configuration::updateValue(strtoupper($this->name).'_LOGIN_BUTTON', 1);
				
				return true;
		}
		function egrupsshippinglabel_filewrite($destination,$source)
		{
				$data = file_get_contents($source);
				$handle = fopen($destination, "w");
				if(!fwrite($handle, $data))
				{
				    return false;
				}
				fclose($handle);
				return true;
		}
		
		/* Method for deleting module settings from DB */
		function dropFacebookConfigTbl()
		{
				Configuration::deleteByName(strtoupper($this->name).'_APPID');
				Configuration::deleteByName(strtoupper($this->name).'_SECRET');
				Configuration::deleteByName(strtoupper($this->name).'_LOGIN_BUTTON');
				return true;
		}
		function moveOverriteFile()
		{
				$path1 = _PS_ROOT_DIR_.'/override/classes/controller/FrontController.php';
				$path2 = _PS_ROOT_DIR_.'/modules/egrfacebook/egroverride/classes/FrontController.php';
				$path3 = _PS_ROOT_DIR_.'/modules/egrfacebook/egroverride/classes/backup/FrontController.php';
				if(file_exists($path1))
				{
				    $this->egrupsshippinglabel_filewrite($path3,$path1);
				}
				if(!$this->egrupsshippinglabel_filewrite($path1,$path2))
				{
				    return false;
				}
				return true;
		}
		//function removeOverriteFile()
		//{
		//		$path1 = _PS_ROOT_DIR_.'/override/classes/FrontController.php';
		//		$path3 = _PS_ROOT_DIR_.'/modules/egrfacebook/override/classes/backup/FrontController.php';
		//		if(file_exists($path1))
		//		{
		//		    unlink($path1);
		//		}
		//		if(file_exists($path3))
		//		{
		//		    $this->egrupsshippinglabel_filewrite($path3,$path1);
		//		}
		//		return true;
		//}
}
?>