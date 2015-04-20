<?php
if (!defined('_PS_VERSION_'))
  exit;

class AlexProductExtra extends Module{
	public function __construct(){
		$this->name = 'alexproductextra';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Ronnie';
		$this->need_instance = 0;
		$this->ps_version_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Alex Product Extra');
		$this->description = $this->l('Custom extra tab for product in admin and display on front-end using hooks');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		
	}

	public function install(){		
		return (
			parent::install() 			
			&& $this->registerHook('header')
			&& $this->registerHook('alexproductextra')
			&& $this->registerHook('actionAdminControllerSetMedia')
			&& $this->registerHook('actionProductUpdate')
			&& $this->registerHook('displayAdminProductsExtra')
			&& $this->alterTable('add')
		);
	}

	public function uninstall(){
		return (parent::uninstall() && $this->alterTable('remove')); 
	}

	public function alterTable($method){
		switch ($method) {
			case 'add':
				$sql ='ALTER TABLE `'._DB_PREFIX_.'product_lang` 
					ADD editor_note TEXT NOT NULL,
					ADD shippin_and_return TEXT NOT NULL
					';
				break;
			 
			case 'remove':
				$sql = 'ALTER TABLE `'._DB_PREFIX_.'product_lang` 
					DROP editor_note,
					DROP shippin_and_return
					';
				break;
		}
		 
		if(!Db::getInstance()->Execute($sql))
			return false;
		return true;
	}

	public function hookActionAdminControllerSetMedia($params){
		// add necessary javascript to products back office
		if($this->context->controller->controller_name == 'AdminProducts' && Tools::getValue('id_product')){
			$this->context->controller->addJS($this->_path.'views/js/alexproductextra.js');
		}

	}

	public function prepareNewTab(){		
		$alex_data = $this->getCustomField((int)Tools::getValue('id_product')); 
		$id_shop = Shop::getContextListShopID();
		$languages = Language::getLanguages(false, $id_shop);
		$data = array();
		if($alex_data){
			foreach($alex_data as $val){
				$land_id = $val['id_lang'];
				$data[$land_id]['editor_note'] = $val['editor_note'];
				$data[$land_id]['shippin_and_return'] = $val['shippin_and_return'];
			}
		}		
		//echo'<pre>'; print_r($data); echo'</pre>'; die;
		if (!empty($alex_data))
			$this->context->smarty->assign('alex_data', $data);
		else
			$this->context->smarty->assign('alex_data', '');
			
		$this->context->smarty->assign(array(			
			'token' => Tools::getValue('token'),
			'languages' => $languages,
			'default_language' => (int)Configuration::get('PS_LANG_DEFAULT')
		));

	}

	public function hookDisplayAdminProductsExtra($params){
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))){				
			$this->prepareNewTab();			
			return $this->display(__FILE__, 'views/templates/admin/alexproductextra.tpl');
		}
	}

	public function getCustomField($id_product){		
		$id_lang = Context::getContext()->language->id;		
		$id_shop = Context::getContext()->shop->id;
		$result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'product_lang WHERE id_product = "'.(int)$id_product.'" AND id_shop = "'.$id_shop.'" ');
		
		if(empty($result))
			return array();
		return $result;
	}

	public function hookActionProductUpdate($params){
		$id_product = (int)Tools::getValue('id_product');			
		$id_shops = Shop::getContextListShopID();		
		
		foreach($id_shops as $id_shop){
			$languages = Language::getLanguages(false, $id_shop);				
			foreach($languages as $lang){
				$id_lang = $lang['id_lang'];
				$editor_note = addslashes(Tools::getValue('editor_note_'.$id_lang));		
				$shippin_and_return = addslashes(Tools::getValue('shippin_and_return_'.$id_lang));		
				
				$data = array(									
					'editor_note'=> $editor_note,
					'shippin_and_return'=> $shippin_and_return
				);
				$where = "	id_product = '".$id_product."' AND 	id_shop = '".$id_shop."' AND id_lang = '".$id_lang."' ";
				if(!Db::getInstance()->update('product_lang', $data, $where))
					$this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
			}
		}		
	}

	public function hookAlexproductextra($params){
		$key = $params['key'];
		$id_lang = Context::getContext()->language->id;	
		$id_shop = Context::getContext()->shop->id;
		$id_product = $params['id_product'];
		$sql = 'SELECT editor_note, shippin_and_return FROM '._DB_PREFIX_.'product_lang WHERE id_product = "'.(int)$id_product.'" AND id_lang =  "'.$id_lang.'" AND id_shop = "'.$id_shop.'" ';
		$result = Db::getInstance()->getRow($sql);
		if($key==1){			
			return $result['editor_note'];
		}
		if($key==2){
			return $result['shippin_and_return'];
		}
	}
}
?>