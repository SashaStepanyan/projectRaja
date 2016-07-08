
    <div class=" bs-component">

        <div class="caption">
            <div class="tablecont">
                <table class="table table-striped  table-bordered table-condensed table-responsive viewTable">
                    <thead>
                    <tr>
                        <td class="text-left view" style="border: none !important; font-weight: normal !important; width: 123px">First Name:</td>
                        <td class="text-left view" style="border: none !important; font-weight: normal !important;">{{$person->firstname}}</td>
                    </tr>
                    </thead>
                    <tbody class="table-hover">
                    <tr>
                        <td class="text-left view">Last Name:</td>
                        <td class="text-left view">{{$person->lastname}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Job:</td>
                        <td class="text-left view">{{$person->job}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Email:</td>
                        <td class="text-left view">{{$person->email}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">City:</td>
                        <td class="text-left view">{{$person->city}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Country:</td>
                        <td class="text-left view">{{$country->name}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Tags:</td>
                        <td class="text-left view">{{$person->tags}}</td>
                    </tr>
                    <tr>
                        <td class="text-left view">Notes:</td>
                        <td class="text-left view"><p>{{$person->notes}}</p></td>
                    </tr>
                    <tr>
                        <td class="text-left view">Status:</td>
                        <td class="text-left view">{!!(($person->status=='0')?'Disable':'Enable')!!}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



