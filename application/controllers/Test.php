<?php

/**
 * Class     IndexController
 *
 */
class TestController extends Base\BaseControllers
{
	public function indexAction()
	{
		$userModel = new UserModel();

		var_dump($userModel->getAccount());

		return false;
	}
}