<?php

class Leagues_model extends My_model {

    public function __construct() {
        parent::__construct();
    }

    public function getAllLeagues($postData){
            $data['select'] = ['leagues.*'];
            $data['table'] = TABLE_LEAGUES . ' as leagues';
            $data['where'] = ['name !=' => ''];
            $leagues = $this->selectRecords($data);
            if(!empty($leagues)){
                $result['success'] = true;
                $result['responseData']['leagueInfo']['leagues'] = $leagues;
                $result['responseData']['leagueInfo']['curentLeagueName'] = 'City League';
            }
            return $result;
    }

    
}

?>