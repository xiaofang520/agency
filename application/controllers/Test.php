<?php

/**
 * Class     IndexController
 *
 */
class TestController extends Base\BaseControllers
{
	public function indexAction()
	{
        var_dump($this->request);
		return false;
	}
}