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
        
        public function registration(){
                $response['result'] = $this->Leagues_model->registration($this->input->post());
                echo json_encode($response);
                exit();
        }
        
        public function cardCollection(){
            $response['result'] = $this->Leagues_model->cardCollection($this->input->post());
            echo json_encode($response);
            exit();
        }
        
        public function teamUpdate(){
            $response['result'] = $this->Leagues_model->teamUpdate($this->input->post());
            echo json_encode($response);
            exit();
        }
        
        public function coinPacks(){
            $response['result'] = $this->Leagues_model->coinPacks($this->input->post());
            echo json_encode($response);
            exit();
        }
 }
   
