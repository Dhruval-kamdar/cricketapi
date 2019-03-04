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
    
    public function registration($postData){
       if(!isset($postData['name']) ||  $postData['name'] == NULL || !isset($postData['emailId']) || $postData['emailId'] == NULL || !isset($postData['userType']) || $postData['userType'] == NULL || $postData['partnerName'] == NULL){
                $result['success'] = false;
                $result['errorMsg']= 'Provide Required Data';
                $result['errorCode']= 400;
       }else{
           $data['table']=TABLE_USERS;
           $data ["where"]=['email_Id'=>$postData['emailId']];           
           $count= $this->countRecords($data);
           if($count > 0){
                $result['success'] = false;
                $result['errorMsg']= 'Email Already Exist.';
                $result['errorCode']= 400;
           }else{
            $data['table']=TABLE_USERS;        
            $data ["insert"]=[
                'name'=>$postData['name'],
                'email_Id'=>$postData['emailId'],
                'user_Type'=>$postData['userType'],
                'android_Id'=>$postData['androidID'],
                'google_Id'=>$postData['googleId'],
                'facebook_Id'=>$postData['facebookId'],
                'google_response_data'=>$postData['googleResponseData'],
                'facebook_response_data'=>$postData['facebookResponseData'],
                'device_Id'=>$postData['deviceId'],
                'device_Ids'=>$postData['deviceIds'][0],
                'partner_Name'=>$postData['partnerName'],
            ];
            $res= $this->insertRecord($data);
                if(!empty($res)){
                    $data['table']=TABLE_USERS_DEVICES;    
                    $data ["where"]=['user_Device_Id'=>$postData['deviceId']];           
                    $count= $this->countRecords($data);
                    if($count == 0){
                        $data['table']=TABLE_USERS_DEVICES; 
                        $data ["insert"]=[
                            'user_Device_Id'=>$postData['deviceId'],
                            'user_Id'=>$res,
                            ];
                        $res_user_devices= $this->insertRecord($data);
                    }
                    $data=[
                        'name'=>$postData['name'],
                        'userid'=>$res,
                        'email'=>$postData['emailId'],
                        'userType'=>$postData['userType'],
                    ];
                    $result['success'] = true;
                    $result['payload']['userProfile'] = $data;

                }else{
                    $result['success'] = false;
                    $result['errorMsg']= 'Something Goes to wrong';
                    $result['errorCode']= 400;
                }            
           }
        }
       return $result;
    }
}

?>