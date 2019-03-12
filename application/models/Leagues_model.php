<?php

class Leagues_model extends My_model {

    public function __construct() {
        parent::__construct();
    }

    
    
    public function registration($postData){
        $object = json_decode($postData['requestData']);
        
       if(!isset($object->name) ||  $object->name == NULL || !isset($object->emailId) || $object->emailId == NULL || !isset($object->userType) || $object->userType == NULL || $postData['partnerName'] == NULL){
                $result['success'] = false;
                $result['errorMsg']= 'Provide Required Data';
                $result['errorCode']= 400;
       }
       else{
           $data['table']=TABLE_USERS;
           $data ["where"]=['email_Id'=>$object->emailId];           
           $count= $this->countRecords($data);
           if($count > 0){
                $result['success'] = false;
                $result['errorMsg']= 'Email Already Exist.';
                $result['errorCode']= 400;
           }else{
               $user_id = substr(str_shuffle(str_repeat("0123456789AGLOBMPRSCHGQTZDIUWEKVYFNX", 6)), 0, 6);
            $data['table']=TABLE_USERS;        
            $data ["insert"]=[
                'name'=>$object->name,
                'user_id'=>$user_id,
                'email_Id'=>$object->emailId,
                'user_Type'=>$object->userType,
                'android_Id'=>$object->androidID,
                'google_Id'=>$object->googleId,
                'facebook_Id'=>$object->facebookId,
               'google_response_data'=>$object->googleResponseData,
                'facebook_response_data'=>$object->facebookResponseData,
                'device_Id'=>$object->deviceId,
                'partner_Name'=>$postData['partnerName'],
            ];
            $res= $this->insertRecord($data);
                if(!empty($res)){
                    $data['table']=TABLE_USERS_DEVICES;    
                    $data ["where"]=['user_Device_Id'=>$object->deviceId,];           
                    $count= $this->countRecords($data);
                    if($count == 0){
                        $data['table']=TABLE_USERS_DEVICES; 
                        $data ["insert"]=[
                            'user_Device_Id'=>$object->deviceId,
                            'user_Id'=>$res,
                            ];
                        $res_user_devices= $this->insertRecord($data);
                    }
                    $data=[
                         
                        'name'=>$object->name,
                        'userid'=>$user_id,
                        'email'=>$object->emailId,
                        'userType'=>$object->emailId,
                        'teamSkillPoints'=>0,
                        "userType"=>$object->userType,
                        "faceBookId"=> $object->facebookId,
//                        "xpPoints"=>10,
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
    
    public function cardCollection($postData){
        if(!isset($postData['partnerName']) ||  $postData['partnerName'] == NULL || !isset($postData['userId']) || $postData['userId'] == NULL){
                $result['success'] = false;
                $result['errorMsg']= 'Provide Required Data';
                $result['errorCode']= 400;
       }else{
           $data['select']=['*'];
           $data['table'] = TABLE_CARD_MASTER  ;
           $cardMasterId = $this->selectRecords($data);
           for($i=0 ; $i <count($cardMasterId) ; $i++){
              
               $data['table'] = TABLE_USERS_CARD  ;
               $data ["where"]=['user_id'=>$postData['userId'],'card_id'=>$cardMasterId[$i]->id]; 
               $count= $this->countRecords($data);
               if($count == 0){
                   $data['table'] = TABLE_USERS_CARD ;
                   $data ["insert"]=[
                       'user_id'=>$postData['userId'],
                       'card_id'=>$cardMasterId[$i]->id,
                       'keyname'=>$cardMasterId[$i]->keyname,
                       'level'=>$cardMasterId[$i]->level,
                       'cardType'=>$cardMasterId[$i]->cardType,
                       'upgradeClass'=>$cardMasterId[$i]->upgradeClass,
                       'battingAccuracy'=>$cardMasterId[$i]->battingAccuracy,
                       'bowlingAccuracy'=>$cardMasterId[$i]->bowlingAccuracy,
                       'minPower'=>$cardMasterId[$i]->minPower,
                       'maxPower'=>$cardMasterId[$i]->maxPower,
                       'minBowlingSpeed'=>$cardMasterId[$i]->minBowlingSpeed,
                       'maxBowlingSpeed'=>$cardMasterId[$i]->maxBowlingSpeed,
                       'minTurn'=>$cardMasterId[$i]->minTurn,
                       'maxTurn'=>$cardMasterId[$i]->maxTurn,
                       'skillPoints'=>$cardMasterId[$i]->skillPoints,
                    ];
                   $res=$this->insertRecord($data);
               }
           }
           
           $data['select']=['keyname','level','cardType','upgradeClass','battingAccuracy','bowlingAccuracy','minPower','maxPower','minBowlingSpeed','maxBowlingSpeed','minTurn','maxTurn','skillPoints'];
           $data['table'] = TABLE_USERS_CARD;
           $data ["where"]=['user_id'=>$postData['userId']];
           $Usercard = $this->selectRecords($data);
           
           for($i=0 ; $i <count($Usercard) ; $i++){
                // $Usercard[$i]->level
                 $Usercard[$i]->battingAccuracy=array_slice(explode(',',$Usercard[$i]->battingAccuracy),(($Usercard[$i]->level)-1),2);
                 $Usercard[$i]->bowlingAccuracy=array_slice(explode(',',$Usercard[$i]->bowlingAccuracy),(($Usercard[$i]->level)-1),2);
                 $Usercard[$i]->minPower=array_slice(explode(',',$Usercard[$i]->minPower),(($Usercard[$i]->level)-1),2);
                 $Usercard[$i]->maxPower=array_slice(explode(',',$Usercard[$i]->maxPower),(($Usercard[$i]->level)-1),2);
                 $Usercard[$i]->minBowlingSpeed=array_slice(explode(',',$Usercard[$i]->minBowlingSpeed),(($Usercard[$i]->level)-1),2);
                 $Usercard[$i]->maxBowlingSpeed=array_slice(explode(',',$Usercard[$i]->maxBowlingSpeed),(($Usercard[$i]->level)-1),2);
                 $Usercard[$i]->minTurn=array_slice(explode(',',$Usercard[$i]->minTurn),(($Usercard[$i]->level)-1),2);
                 $Usercard[$i]->maxTurn=array_slice(explode(',',$Usercard[$i]->maxTurn),(($Usercard[$i]->level)-1),2);
            }
       }
         $result['success'] = true;
         $result['payload']['cards']= $Usercard;
        return $result;
    }
    
    public function getAllLeagues($postData){
        if(!isset($postData['partnerName']) ||  $postData['partnerName'] == NULL || !isset($postData['userId']) || $postData['userId'] == NULL){
            $result['success'] = false;
            $result['errorMsg']= 'Provide Required Data';
            $result['errorCode']= 400;
       }else{
            $data['table']=TABLE_USER_LEAGUES;
            $data['where']=['user_id'=>$postData['userId']];
            $resCount= $this->countRecords($data);
            if($resCount == 0){
                $data=[];
                $data['select']=['id'];
                $data['table']=TABLE_LEAGUES_MASTER;
                $result= $this->selectRecords($data);
                
                for($i = 0; $i <count($result);$i++){
                    $data['table']=TABLE_USER_LEAGUES;
                    if($i == 0){
                        $data ["insert"]=[
                            'user_id'=>$postData['userId'],
                            'leagues_id'=>$result[$i]->id,
                            'is_unblocked'=>'true'
                        ];
                    }else{
                        $data ["insert"]=[
                            'user_id'=>$postData['userId'],
                            'leagues_id'=>$result[$i]->id,
                            'is_unblocked'=>'false'
                        ];
                    }
                    $insertRecord= $this->insertRecord($data);
                    }
                }
                $data['select']=['LM.name','LM.reward','LM.promoteAt','LM.demoteAt','LM.coinsPerMatch','LM.cardsToBeUnlocked','LM.starsLose','LM.starsLose','LM.starsWin','LM.starsTie','LM.energyToBeDeducted','UL.is_unblocked as isUnlocked'];
                $data['join'] = [
                    TABLE_LEAGUES_MASTER . ' as LM' => [
                        'LM.id = UL.leagues_id',
                        'LEFT',
                    ],
                ];
                $data['table']=TABLE_USER_LEAGUES.' as UL';
                $data['where']=['user_id'=>$postData['userId']];
                $resultRecord= $this->selectFromJoin($data);
                
                $result['success'] = true;
                $result['payload']['leagueInfo']['leagues']= $resultRecord;
                return $result;
            }
       }
       
    public function teamUpdate($postData){
        $object = json_decode($postData);
       
        $user_id=$object->userId;
        
        $requestData=$object->requestData;
        $strategyData=$requestData->strategy;
        
        $team=$strategyData->team;
        
        $bowlingOrder=$strategyData->bowlingOrder;
        if(!isset($team) ||  $team == NULL || !isset($bowlingOrder) || $bowlingOrder == NULL){
            $result['success'] = false;
            $result['errorMsg']= 'Provide Required Data';
            $result['errorCode']= 400;
        }else{
            $data=[];
            $data['table']=TABLE_USER_TEAM;
            $data['where']=['user_id'=>$user_id];
            $count= $this->countRecords($data);
            if($count == 0 ){
                    for($i = 0; $i <count($team) ;$i++  ){
                        $data['table']=TABLE_USER_TEAM;
                        $data['insert']=['user_id'=>$user_id,'player_Name'=>$team[$i]];
                        $res= $this->insertRecord($data);
                    }

                    for($i = 0; $i <count($bowlingOrder) ;$i++  ){
                        $data['table']=TABLE_USER_BOWLING_ORDER;
                        $data['insert']=['user_id'=>$user_id,'player_Name'=>$bowlingOrder[$i]];
                        $res= $this->insertRecord($data);
                    }
            }else{
                $data=[];
                $data['table']=TABLE_USER_TEAM;
                $data['where']=['user_id'=>$user_id];
                $delete= $this->deleteRecords($data);

                $data=[];
                $data['table']=TABLE_USER_BOWLING_ORDER;
                $data['where']=['user_id'=>$user_id];
                $delete= $this->deleteRecords($data);

                for($i = 0; $i <count($team) ;$i++  ){
                        $data['table']=TABLE_USER_TEAM;
                        $data['insert']=['user_id'=>$user_id,'player_Name'=>$team[$i]];
                        $res= $this->insertRecord($data);
                    }

                    for($i = 0; $i <count($bowlingOrder) ;$i++  ){
                        $data['table']=TABLE_USER_BOWLING_ORDER;
                        $data['insert']=['user_id'=>$user_id,'player_Name'=>$bowlingOrder[$i]];
                        $res= $this->insertRecord($data);
                    }
            }
        
            $result['success'] = true;
            $result['payload']['strategy']['bowlingOrder']= $bowlingOrder;
            $result['payload']['strategy']['team']= $team;
            return $result;
        }
    }
}

?>