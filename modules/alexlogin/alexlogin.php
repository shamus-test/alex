<?php
if (!defined('_PS_VERSION_'))
  exit;

class AlexLogin extends Module{
	public function __construct(){
		$this->name = 'alexlogin';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Ronnie';
		$this->need_instance = 0;
		$this->ps_version_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Alex Custom Login');
		$this->description = $this->l('Custom login form in popup');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	}

	public function install(){
		if( !parent::install() || !$this->registerHook('displayTop') || !$this->registerHook('header') )
			return false;

		return true;
	}

	public function uninstall(){
		if( !parent::uninstall() )
			return false;

		return true;
	}

	public function hookDisplayHeader(){
		$this->context->controller->addJS($this->_path.'views/js/alexlogin.js');
		$this->context->controller->addCSS($this->_path.'views/css/alexlogin.css');
	}

	public function hookDisplayTop(){
		return $this->display(__FILE__, 'alexlogin.tpl');
	}

}
?>