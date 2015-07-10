<?php

Class UserModel
{
	private $read_instance;

	private $write_instance;

	public function __construct()
	{
		$this->read_instance = \Base\MysqlFactory::getReadInstance('yiyu_agency');
		$this->write_instance = \Base\MysqlFactory::getWriteInstance('yiyu_account');
	}

	public function test()
	{
		return $this->write_instance->query("select * from bbl_agenter")->fetchAll();
	}

	public function getAccount()
	{
		return $this->write_instance->query("select * from bbl_user")->fetchAll();
	}
}
