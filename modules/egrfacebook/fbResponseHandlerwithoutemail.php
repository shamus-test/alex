<?php
/*   
	  	Project Name 	: Social Connect Facebook Module 
		Created By 	: egs167
		Created On 	: 21-02-2012
		Updated By 	: egs167
		Updated On 	: 21-02-20122-2012
		Filename   	: fbResponseHandler.php (supporting handler file )
		Purpose    	: Social Connect Facebook Module
		Module Name 	: Social Connect Facebook Module
		Tables Used 	: ps_fbconnect_customer,
		  		  ps_customer,
				  ps_customer_group
*/
include(dirname(__FILE__).'/../../config/config.inc.php');

global $cookie,$smarty,$cart;
$cont = new FrontController();
$cont->init();

$context = Context::getContext();
$logged = $context->customer->isLogged();

if (isset($_POST['fbrequest']) && !empty($_POST['email']))
{
	try
	{		
		$user_json_encoded_result = $_POST['fbrequest'];
		
		if($user_json_encoded_result)
		{
			$user = json_decode($user_json_encoded_result);
			if($user)
			{
				//$addresses1= array();
				$fb_id=$user->id;
				$firstname=$user->first_name;
				$lastname=$user->last_name;
				$email=$_POST['email'];
				$gender = ($user->gender == 'male')?1:2;			
				
				// generate passwd
				srand((double)microtime()*1000000);
				$passwd = substr(uniqid(rand()),0,12);
				$real_passwd = $passwd; 
				$passwd = md5(pSQL(_COOKIE_KEY_.$passwd)); 
				
				$date_add = date('Y-m-d H:i:s'); 
				$date_updated = $date_add;
				$last_pass_gen=$date_updated;
				$active=1;
				$secure_key = md5(uniqid(rand(), true));
				
				$fbirthday=$user->birthday;
				$nBirthday = '';
				$birth_year = '';
				$birth_month = '';
				$birth_day = '';
				
				/*format db as it fit to prestashop dob field */
				if(!empty($fbirthday))
				{
					$birth_date = explode('/',$fbirthday);
					$birth_year = $birth_date[2];
					$birth_month = $birth_date[0];
					$birth_day = $birth_date[1];
					$nBirthday = $birth_year.'-'.$birth_month.'-'.$birth_day;
				}
				
				if($logged)/* Check user already logged with site */
				{
					if (Customer::customerExists($email))/* Check if facebook email exists in database, if use update the dob */
					{
						$customer = new Customer();
						
						$authentication = $customer->getByEmail(trim($email));
						if (!$authentication OR !$customer->id)
						{
							//AUTHENTICATION FAILED
						}
						else
						{
							$birthday =  $customer->birthday;
							if( (empty($birthday) || $birthday == '0000-00-00' ) && !empty($fbirthday) )
							{
								$customer->birthday = $nBirthday;
								$customer->update();
							}
						}
					}
					else
					{
						//'NO ACTION';
					}
				}
				else
				{
					
					if (Customer::customerExists($email))
					{
						//This customer exist and login automactically with prestashop acc and syn the data
						$customer = new Customer();
						$authentication = $customer->getByEmail(trim($email));
						if (!$authentication OR !$customer->id)
						{
							//AUTHENTICATION FAILED
						}
						else
						{
							//login automatically with prestashop user account
							$cookie->id_compare = '';
							if(isset($cookie->id_compare))
							{
								$cookie->id_compare = $cookie->id_compare;
							}
							else
							{
								if(class_exists('CompareProduct'))
								{
									if(method_exists('CompareProduct','getIdCompareByIdCustomer'))
									{
										$cookie->id_compare = CompareProduct::getIdCompareByIdCustomer($customer->id);
									}
								}
							}
							$cookie->id_customer = (int)($customer->id);
							$cookie->customer_lastname = $customer->lastname;
							$cookie->customer_firstname = $customer->firstname;
							$cookie->logged = 1;
							$cookie->is_guest = $customer->isGuest();
							$cookie->passwd = $customer->passwd;
							$cookie->email = $customer->email;
							
							
							if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart) OR Cart::getNbProducts($cookie->id_cart) == 0))
							{
								$cookie->id_cart = (int)(Cart::lastNoneOrderedCart((int)($customer->id)));
							}
							/* Update cart address */
							$cart->id_carrier = 0;
							$cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
							$cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));
							
							// If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
							$cart->secure_key = $customer->secure_key;
							$cart->update();
							
							//syn the data prestashop data
							$birthday =  $customer->birthday;
							if( (empty($birthday) || $birthday == '0000-00-00' ) && !empty($fbirthday) )
							{
								$customer->birthday = $nBirthday;
								$customer->update();
							}
							
							Hook::exec('authentication');
						}
					}
					else
					{
						$id_default_group = 1;
												
						$sql = 'insert into `'._DB_PREFIX_.'customer` SET 
									   id_gender = '.$gender.', id_default_group = '.$id_default_group.',
									   firstname = \''.$firstname.'\', lastname = \''.$lastname.'\',
									   email = \''.$email.'\', passwd = \''.$passwd.'\',
									   last_passwd_gen = \''.$last_pass_gen.'\', birthday=\''.$nBirthday.'\',
									   secure_key = \''.$secure_key.'\', active = '.$active.',
									   date_add = \''.$date_add.'\', date_upd = \''.$date_updated.'\' ';
						
						defined('_MYSQL_ENGINE_')?$result = Db::getInstance()->Execute($sql):$result = Db::getInstance()->Execute($sql);
						$insert_id = Db::getInstance()->Insert_ID();
				
						// insert record in customer group
						$id_group = 1;
						$sql = 'INSERT into `'._DB_PREFIX_.'customer_group` SET 
									   id_customer = '.$insert_id.', id_group = '.$id_group.' ';
						defined('_MYSQL_ENGINE_')?$result = Db::getInstance()->Execute($sql):$result = Db::getInstance()->Execute($sql);
						
				
						// insert record into customerXfacebook table
						$sql = 'INSERT into `'._DB_PREFIX_.'fbconnect_customer` SET
									   cust_id = '.$insert_id.', fb_id = '.$fb_id.' ';
						defined('_MYSQL_ENGINE_')?$result = Db::getInstance()->Execute($sql):$result = Db::getInstance()->Execute($sql);
						
						$customer = new Customer();
						$authentication = $customer->getByEmail(trim($email));
						if (!$authentication OR !$customer->id)
						{
							//AUTHENTICATION FAILED
						}
						else
						{
							$smarty->assign('confirmation', 1);
							$cookie->id_compare = '';
							if(isset($cookie->id_compare))
							{
								$cookie->id_compare = $cookie->id_compare;
							}
							else
							{
								if(class_exists('CompareProduct'))
								{
									if(method_exists('CompareProduct','getIdCompareByIdCustomer'))
									{
										$cookie->id_compare = CompareProduct::getIdCompareByIdCustomer($customer->id);
									}
								}
							}
							$cookie->id_customer = (int)($customer->id);
							$cookie->customer_lastname = $customer->lastname;
							$cookie->customer_firstname = $customer->firstname;
							$cookie->logged = 1;
							$cookie->is_guest = 0;
							$cookie->passwd = $customer->passwd;
							$cookie->email = $customer->email;
							if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart) OR Cart::getNbProducts($cookie->id_cart) == 0))
								$cookie->id_cart = (int)(Cart::lastNoneOrderedCart((int)($customer->id)));
							/* Update cart address */
							$cart->id_carrier = 0;
							$cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
							$cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));
							
							// If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
							$cart->secure_key = $customer->secure_key;
							$cart->update();
							Hook::exec('authentication');
		
						//	Mail::Send(intval($cookie->id_lang), 'account', 'Welcome!', 
						//			array('{firstname}' => $customer->firstname, 
						//			  '{lastname}' => $customer->lastname, 
						//			  '{email}' => $customer->email, 
						//			  '{passwd}' => $real_passwd), 
						//			  $customer->email,
						//			  $customer->firstname.' '.$customer->lastname);
						}
						
					}
					
					if(!empty($fcookie['access_token']))
					{
						if(!$logged)
						{
							Tools::redirect($_SERVER['PHP_SELF']);
						}
					}
				}
			}
		}
	}
	catch (FacebookApiException $e)
	{
		echo 'Error in Appl';
	}
}
?>				 	  
