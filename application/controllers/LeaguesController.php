<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LeaguesController extends CI_Controller {

	function __construct() {
            parent::__construct();
            $this->load->model('Leagues_model', 'Leagues_model');
            
        }
	public function getAllLeagues()
	{
		$loginCheck['result'] = $this->Leagues_model->getAllLeagues($this->input->post());
                echo json_encode($loginCheck);
                exit();
	}
        
}
