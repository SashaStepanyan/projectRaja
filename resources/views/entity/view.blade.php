<div class=" bs-component">

        <div class="caption">
            <div class="tablecont">
                <table class="table table-striped  table-bordered table-condensed table-responsive viewTable" >
                    <thead>
                    <tr>
                        <td class="text-left view">Applicant :</td>
                        <td class="text-left view">{{$entity->applicants}}</td>
                    </tr>
                    </thead>
                    <tbody class="table-hover">
                    <tr>
                        <td class="text-left view">Entity Name:</td>
                        <td class="text-left view">{{$entity->name}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Existing Type:</td>
                        <td class="text-left view">{{$entity->existing_type}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Currency:</td>
                        <td class="text-left view">{{$entity->currency_name}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Credit Day:</td>
                        <td class="text-left view">{{$entity->credit_day}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Addresses:</td>
                        <td class="text-left view"><?php

                            $addresses=explode('/', $entity->addresses);
                            foreach($addresses as $key=>$address){
                                $status = explode(',',$address);
                                $address = substr($address,2,strlen($address));
                                if($key==0 && $status[0] == '1') {
                                     echo '<b>Default Address</b>: '.$address.'<br>';
                                } elseif ($key !=0 && $status[0] == '1'){
                                } else {
                                    echo 'Address '.$key.': ' . $address. '<br>';
                                }
                                //$addr=($key==0 && $status[0] == '1')?'<b>Default Address</b>':'Address '.$key;
                                //echo $addr.': '.$address.'<br>';
                            }

                            ?></td>
                    </tr>
                    <tr>
                        <td class="text-left view">Notes:</td>
                        <td class="text-left view"><p>{{$entity->notes}}</p></td>
                    </tr>
                    <tr>
                        <td class="text-left view">Status:</td>
                        <td class="text-left view">{!!(($entity->status=='0')?'Disable':'Enable')!!}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

