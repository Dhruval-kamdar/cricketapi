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
               $user_id = substr(str_shuffle(str_repeat("0123456789AGLOBMPRSCHGQTZDIUWEKVYFNX", 6)), 0, 6);
            $data['table']=TABLE_USERS;        
            $data ["insert"]=[
                'name'=>$postData['name'],
                'user_id'=>$user_id,
                'email_Id'=>$postData['emailId'],
                'user_Type'=>$postData['userType'],
                'android_Id'=>$postData['androidID'],
                'google_Id'=>$postData['googleId'],
                'facebook_Id'=>$postData['facebookId'],
                'google_response_data'=>$postData['googleResponseData'],
                'facebook_response_data'=>$postData['facebookResponseData'],
                'device_Id'=>$postData['deviceId'],
                
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
                        'userid'=>$user_id,
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
    
    public function teamUpdate($postData){
        print_r(($postData));
        die();
    }
    
    public function coinPacks($postData){
        if(!isset($postData['userId']) || !isset($postData['partnerName'])){
            $result['success'] = false;
            $result['errorMsg']= 'Provide Required Data';
            $result['errorCode']= 400; 
        }else{
            $data['select'] = [TABLE_PRODUCT.'.id as productId',TABLE_PRODUCT.'.productName as name',TABLE_PRODUCT.'.price',TABLE_PRODUCT.'.priceTag',];
            $data['table'] = TABLE_PRODUCT;
            $leagues = $this->selectRecords($data);
            if(count($leagues) > 0){
                $result['success'] = true;
                $result['payload']['coinPacks']= $leagues;

            }else{
                $result['success'] = true;
                $result['payload']['coinPacks']= "No Product Available";
            }
        }
        return $result;
    } 
    
    public function buyCoinPacks($postData){
        
        if(!isset($postData['productId']) || !isset($postData['userId']) || !isset($postData['partnerName'])){
            $result['success'] = false;
            $result['errorMsg']= 'Provide Required Data';
            $result['errorCode']= 400; 
        }else{
         $data['select'] = [TABLE_PRODUCT.'.price'];
         $data['table'] = TABLE_PRODUCT;
         $data['where'] = ['id' => $postData['productId']];
         $leagues = $this->selectRecords($data);
//         
         if($leagues){
            if(count($leagues) > 0){
               $result['success'] = true;
               $result['payload']['wallet']['coins']= $leagues[0];

           }else{
               $result['success'] = true;
               $result['payload']['wallet']['coins']= "No Product Available";
           }
         }else{
            $result['success'] = false;
            $result['errorMsg']= 'Something Goes to wrong';
            $result['errorCode']= 400;
         }
        }
          return $result;
        
    }
    
    public function cricketBagsConfig($postData){
        
        if( !isset($postData['userId']) || !isset($postData['partnerName'])){
            $result['success'] = false;
            $result['errorMsg']= 'Provide Required Data';
            $result['errorCode']= 400; 
        }else{
            $data['select'] = ['CBM.name','CBM.no_of_cards as noOfcards','CBM.price','CBM.common_percentage as commonPercentage','CBM.gold_percentage as goldPercentage'];
            $data['table'] = TABLE_CRICKET_BAG_MASTER . ' as CBM';
            $leagues = $this->selectRecords($data);
            if($leagues){
                if(count($leagues) >0){
                    $result['success'] = true;
                    $result['payload']['cricketBags'] = $leagues;
                }else{
                    $result['success'] = true;
                    $result['payload']['cricketBags'] = "No Cricket Bag Found.";
                }
            }
        }
        return $result;
    }
}

?>