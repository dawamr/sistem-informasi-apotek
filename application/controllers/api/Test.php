<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/API_Controller.php');

class Test extends API_Controller {

	    public function __construct()
    {
        parent::__construct();

    }
	
    public function halo()
    {
        $this->load->helper('api');
        $this->success_response(
            [],
            'API is working!',
            200
        );
    }
}
