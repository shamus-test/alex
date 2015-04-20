<?php
/*
 Created By: EGS 167
 Updated By: EGS 167
 Created on: 28-04-2012
 Updated on: 08-01-2013
*/
class FrontController extends FrontControllerCore
{
    public function initContent()
    {
	parent::initContent();
	$this->context->smarty->assign('HOOK_EGR_FBLOGIN',Hook::exec('egrfblogin'));
    }
}
?>