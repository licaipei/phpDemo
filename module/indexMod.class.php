<?php
class indexMod extends commonMod
{
	public function __construct()
	{
		parent::__construct();
		header('Location: '.__ROOT__.'/system/');

	}
	public function index()
	{
		$this->display('index');
		
	}
}
