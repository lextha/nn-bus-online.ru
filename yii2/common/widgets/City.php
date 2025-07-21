<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace common\widgets;

use Yii;
use yii\base\Widget;

class City extends Widget {
    public $city;
    public function init() {
        parent::init();        
    }
    public function run() {
                    
             /*   $flag=false;
                $url=explode("/", $_SERVER['REQUEST_URI']); //if ($_SERVER['REMOTE_ADDR']=='5.187.68.174') { print_r($url); die();  }
                if (isset($url[1])) {
                    $city_db= City::findOne(['slug'=>$url[1]]);
                    if ($city_db) {
                        $region_db=Region::findOne(['id_region'=>$city_db->region_id]);
                        $city_ip['city']=$city_db->name;
                        $city_ip['region']=$region_db->name;
                    }
                    else { $flag=true; }
                } else { $flag=true; }
                if ($flag){
                    $city_ip['city']='Москва';
                    $city_ip['region']='Москва';
                    $city_db['region_id']='40';
                }
                
        $city_all=City::find()->where('(region_id='.$city_db['region_id'].' AND count_notarius>0) OR count_notarius>2 ORDER BY name')->all();*/
   //  var_dump($this);
        return $this->render(
                'city',
                [
                    'city' => $this->city
                ]
        );
    }
}
