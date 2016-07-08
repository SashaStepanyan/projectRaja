<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Person extends Model
{
    protected $table = "persons";
    protected $fillable = ['firstname', 'lastname', 'job', 'email', 'city', 'country_id', 'key_contact', 'tags', 'notes', 'status'];

    public static function rules($id = 0, $merge = [])
    {
        return array_merge(
            [

                'firstname' => 'required',
                'email' => 'required|email|unique:persons,email' . ($id ? ",$id" : ''),
                'country_id' => 'required',

            ],
            $merge);
    }

    public static function getPersons($params, $colums, $Ids = null)
    {
        $subsql="select ";
        $where = "";
        unset($colums->id);
        unset($colums->action);
        unset($colums->all);
        foreach ($colums as $key => $value){
        if($value == true && $key != 'country_name' && $key != 'id'){
            $subsql .='persons.`'.$key.'`, ';
        }elseif ($value == true && $key == 'country_name' && $key != 'id'){
            $subsql .='country.`name` as country_name, ';
            $where = "INNER JOIN country ON persons.country_id = country.id ";
        }
    }
        $subsql =rtrim($subsql, ', ');

        $sql = $subsql ." from persons ".$where;
        if ($params !== null) {
            $sql = $sql . ' WHERE ';
            $params = explode('%', $params);
            foreach ($params as $k => $v) {

                $d = explode('=', $v);;
                $arr[$d[0]] = $d[1];
            }
            unset($arr['records_per_page']);
            unset($arr['id-like']);
            unset($arr['']);

            foreach ($arr as $k => $v) {
                $col = strtok($k, '-');
                if ($k == 'country_name-eq' && $col == 'country_name') {
                    $sql = $sql . "country.`name` like '" . $v . "%' and ";
                } else {
                    $sql = $sql . 'persons.' . $col . ' like "' . $v . '%" and ';
                }
            }


        }
        if (empty($Ids) && $params != null) {
            $sql = substr($sql, 0, -4);
        }
        if ($params == null && !empty($Ids)) {
            $id = implode(',', $Ids);
            $sql = $sql . ' where persons.id IN(' . $id . ')';
        }
        if (!empty($Ids) && $params != null) {
            $id = implode(',', $Ids);
            $sql = $sql . ' persons.id IN(' . $id . ')';
        }
        
        $persons = DB::select($sql);
        $data = [];
        foreach ($persons as $person) {
            $data[] = (array)$person;
        }

        return $data;
    }
}
