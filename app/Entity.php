<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Entity extends Model
{
    protected $fillable =  [ 'name', 'country_id', 'existing_type', 'currency_id','credit_days_id','notes','status'];
    public static function rules ($id=0, $merge=[]) {
        return array_merge(
            [

                'applicant' => 'required',
                'entity.name' => 'required',

            ],
            $merge);
    }
    

    public function Currency(){
        return $this->belongsTo('App\Currency');
    }
    
    public function CreditDays(){
        return $this->belongsTo('App\CreditDays');
    }

    public static function getEntitis($params, $columns, $Ids=null)
    {
        $subsql="select applicant.name, ";
        $where = "";
        unset($columns->id);
        unset($columns->action);
        unset($columns->all);

        foreach ($columns as $key => $value){
            if($value == true && $key == 'applicants'){
                $subsql .= 'GROUP_CONCAT(applicant.name) as Applicant, ';
                $where .= "left join `entity_applicant` on `entities`.`id` = `entity_applicant`.`entities_id` ";
                $where .= "left join `applicant` on `applicant`.`id` = `entity_applicant`.`applicant_id` ";
            }elseif ($value == true && $key == 'name'){
                $subsql .='entities.`name` as "Entyty Name", ';
            }elseif ($value == true && $key == 'existing_type'){
                $subsql .='entities.`existing_type` "Existing Type", ';
            }elseif ($value == true && $key == 'currency.name'){
                $subsql .='`currency`.`name` as "Currency", ';
                $where .= "left join `currency` on `entities`.`currency_id` = `currency`.`id` ";
            }elseif ($value == true && $key == 'day'){
                $subsql .='`credit_days`.`day` as "Credit Days", ';
                $where .= "left join `credit_days` on `entities`.`credit_days_id` = `credit_days`.`id` ";
            }elseif ($value == true && $key == 'default_address'){
                $subsql .="(GROUP_CONCAT(entitiy_address.default,', ',entitiy_address.state,IF(entitiy_address.state !='',', ',''),entitiy_address.city,IF(entitiy_address.city !='',', ',''),entitiy_address.street SEPARATOR '/')) as 'Address', ";
                $where .= "left join `entitiy_address` on `entitiy_address`.`entity_id` = `entities`.`id` ";
            }elseif ($value == true && $key == 'notes'){
                $subsql .='entities.`notes` as Notes, ';
            }
            if($value == true && $key == 'default_address'){
                //$whereDef = "where `entitiy_address`.`default` = 1 ";
            }

        }
        $subsql =rtrim($subsql, ', ');


        $sql = $subsql . " from `entities` " . $where;


        if ($params !== null) {

            $sql = $sql . ' WHERE ';
            $params = explode('*', $params);
            foreach ($params as $k => $v) {

                $d = explode('=', $v);;
                $arr[$d[0]] = $d[1];
            }

            unset($arr['records_per_page']);
            unset($arr['id-like']);
            unset($arr['']);

            foreach ($arr as $k => $v) {

                if ($k == 'aplicants-like' ) {
                    $sql = $sql . 'applicant.`name` like "%' . $v . '%" and ';
                }
                elseif($k == 'name-like') {
                    $sql = $sql . ' "Entyty Name" like "%' . $v . '%" and ';
                }
                elseif($k == 'existing_type-like') {
                    $sql = $sql . ' "Existing Type" like "%' . $v . '%" and ';
                }
                elseif($k == 'currency.name-like') {
                    $sql = $sql . ' "Currency" like "%' . $v . '%" and ';
                }
                elseif($k == 'day-like') {
                    $sql = $sql . ' "Credit Days" like "%' . $v . '%" and ';
                }
                elseif($k == 'default_address-like') {
                    $Val = explode(',', $v);
                    $sql = $sql . 'entitiy_address.state like "%' . trim($Val[0]," ")  .'%" and entitiy_address.state like "%'
                        . trim($Val[1]," ")  .'%" and entitiy_address.state like "%'. trim($Val[2]," ")  .'%" and ';
                }
                elseif($k == 'notes-like') {
                    $sql = $sql . 'Notes like "%' . $v . '%" and ';
                }
            }


        }
        if (empty($Ids) && $params != null) {
            $sql = substr($sql, 0, -4);
        }
        if($params == null && !empty($Ids)){
            $id = implode(',', $Ids);
            $sql=$sql.' where entities.id IN('.$id.') ';
        }
        if (!empty($Ids) && $params != null) {
            $id = implode(',', $Ids);
            $sql = $sql . ' persons.id IN(' . $id . ')';
        }
        $sql = $sql.'group by `entity_applicant`.`entities_id`';

        $persons = DB::select($sql);
        $data = [];
        foreach ($persons as $person) {
            $addresses=explode('/', $person->Address);
            $v = '';
            foreach($addresses as $key=>$address){
                $status = explode(',',$address);
                $address = substr($address,2,strlen($address));
                if($key==0 && $status[0] == '1' && $address != ' ') {
                    $v ='Default Address: '.$address.' / ';
                } elseif ($key !=0 && $status[0] == '1'){
                } elseif($address !=' ' && $address) {
                    $v .= 'Address '.$key.': ' . $address. ' / ';
                }
            }
            $v = rtrim($v,' / ');
            $person->Address = $v;
            unset($person->name);
            $data[] = (array)$person;
        }
        return $data;
    }
    
}
