<?php
include('../../config/config.inc.php');
$callback = Tools::getValue('callback');
$task = Tools::getValue('task');
if($task){
	switch($task){
		case 'register':
			$reg_name = Tools::getValue('reg_name');
			$reg_password = Tools::getValue('reg_password');
			$reg_surname = Tools::getValue('reg_surname');
			$reg_email = Tools::getValue('reg_email');
			$mag = array();

			if (!Validate::isEmail($reg_email) || empty($reg_email))				
				$mag['errors']['email'] = Tools::displayError('Invalid email address');
			elseif (Customer::customerExists($reg_email))
				$mag['errors']['email'] = Tools::displayError('An account using this email address has already been registered');
			elseif (empty($reg_password))
				$mag['errors']['passwd'] = Tools::displayError('Password is required.');
			elseif (!Validate::isPasswd($reg_password))
				$mag['errors']['passwd'] = Tools::displayError('Invalid password.');
			else{
				$customer = new Customer();
				$customer->firstname 	= $reg_name;
				$customer->lastname 	= $reg_surname;
				$customer->email 		= $reg_email;
				$customer->passwd 		= Tools::encrypt($reg_password);
				$customer->active 		= 1;

				$mag['errors'] = $customer->validateController();
				if(empty($mag['errors']))
				{	
					if ($customer->add()){
						//send email
						if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL'))
							return true;

						Mail::Send(
							Context::getContext()->language->id,
							'account',
							Mail::l('Welcome!'),
							array(
								'{firstname}' => $customer->firstname,
								'{lastname}' => $customer->lastname,
								'{email}' => $customer->email,
								'{passwd}' => $reg_password ),
							$customer->email,
							$customer->firstname.' '.$customer->lastname
						);
						
						Context::getContext()->customer = $customer;
						Context::getContext()->smarty->assign('confirmation', 1);
						Context::getContext()->cookie->id_customer = (int)$customer->id;				
						Context::getContext()->cookie->customer_lastname = $customer->lastname;
						Context::getContext()->cookie->customer_firstname = $customer->firstname;
						Context::getContext()->cookie->passwd = $customer->passwd;
						Context::getContext()->cookie->logged = 1;
						// if register process is in two steps, we display a message to confirm account creation
						if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE'))
							Context::getContext()->cookie->account_created = 1;
						$customer->logged = 1;
						Context::getContext()->cookie->email = $customer->email;
						//Context::getContext()->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
						// Update cart address							
						//Context::getContext()->cart->secure_key = $customer->secure_key;
						Context::getContext()->cookie->write();
						$mag['success'] = 1;
					}
				}
			}
			header('Content-Type: application/json');
			echo $callback . '('  . json_encode($mag) . ')';
			exit;
		break;

		case 'login':
			$passwd = trim(Tools::getValue('log_password'));
			$email = trim(Tools::getValue('log_email'));
			$mag = array();

			if (empty($email))
				$mag['errors']['email'] = Tools::displayError('An email address required.');
			elseif (!Validate::isEmail($email))
				$mag['errors']['email'] = Tools::displayError('Invalid email address.');
			elseif (empty($passwd))
				$mag['errors']['passwd'] = Tools::displayError('Password is required.');
			elseif (!Validate::isPasswd($passwd))
				$mag['errors']['passwd'] = Tools::displayError('Invalid password.');
			else
			{
				$customer = new Customer();
				$authentication = $customer->getByEmail(trim($email), trim($passwd));
				if (isset($authentication->active) && !$authentication->active)
					$mag['errors']['active'] = Tools::displayError('Your account isn\'t available at this time, please contact us');
				elseif (!$authentication || !$customer->id)
					$mag['errors']['auth'] = Tools::displayError('Authentication failed.');
				else
				{
					Context::getContext()->cookie->id_compare = isset(Context::getContext()->cookie->id_compare) ? Context::getContext()->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
					Context::getContext()->cookie->id_customer = (int)($customer->id);
					Context::getContext()->cookie->customer_lastname = $customer->lastname;
					Context::getContext()->cookie->customer_firstname = $customer->firstname;
					Context::getContext()->cookie->logged = 1;
					$customer->logged = 1;
					Context::getContext()->cookie->is_guest = $customer->isGuest();
					Context::getContext()->cookie->passwd = $customer->passwd;
					Context::getContext()->cookie->email = $customer->email;

					// Add customer to the context
					Context::getContext()->customer = $customer;
					/*
					if (Configuration::get('PS_CART_FOLLOWING') && (empty(Context::getContext()->cookie->id_cart) || Cart::getNbProducts(Context::getContext()->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart(Context::getContext()->customer->id))
						Context::getContext()->cart = new Cart($id_cart);
					else
					{						
						$id_carrier = (int)Context::getContext()->cart->id_carrier;
						Context::getContext()->cart->id_carrier = 0;
						Context::getContext()->cart->setDeliveryOption(null);
						Context::getContext()->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
						Context::getContext()->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
					}
					Context::getContext()->cart->id_customer = (int)$customer->id;
					Context::getContext()->cart->secure_key = $customer->secure_key;

					Context::getContext()->cart->save();
					Context::getContext()->cookie->id_cart = (int)Context::getContext()->cart->id;					
					Context::getContext()->cart->autosetProductAddress();
					*/
					Hook::exec('actionAuthentication');

					// Login information have changed, so we check if the cart rules still apply
					CartRule::autoRemoveFromCart(Context::getContext());
					CartRule::autoAddToCart(Context::getContext());
					Context::getContext()->cookie->write();

					//print_r($customer);
					$mag['success'] = 1;
				}
			}
			header('Content-Type: application/json');
			echo $callback . '('  . json_encode($mag) . ')';
			exit;
		break;
	}
}

?>
