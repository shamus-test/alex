<?php
class BlockWishListOverride extends BlockWishList
{
	public function hookDisplayRightColumnProduct($params)
	{
		return $this->hookProductActions($params);
	}
}
