<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModal');
	}

	public function index()
	{
		$this->load->view('home.php');
	}

}
