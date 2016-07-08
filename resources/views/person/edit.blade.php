
    <div class="">
        <div class=" bs-component">
            <div class="hasError"></div>
                {{ Form::model($person, array('method' => 'POST', 'route' => array('admin.person.update', $person->id),'class'=>'form-horizontal','id'=>'update_person')) }}

                <div class="form-group">
                    <div class="col-lg-4 col-lg-offset-8 col-md-4 col-md-offset-8 col-sm-4 col-sm-offset-8  marginBottom">
                        <div class="checkbox paddingLeft">
                            <label>
                                {{ Form::checkbox('status',!$person->status,false,['class'=>'checkbox_size']) }} {!!(($person->status=='1')?'Disable':'Enable')!!} Person
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-5 col-lg-offset-1 col-md-6 marginBottom">
                        {{ Form::text('firstname', old('firstname'), array('class' => 'form-control','placeholder'=>'First name*')) }}
                    </div>
                    <div class="col-lg-5 col-md-6">
                        {{ Form::text('lastname', old('lastname'), array('class' => 'form-control','placeholder'=>'Last name')) }}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-1">
                        {{ Form::text('job',old('job') , array('class' => 'form-control','placeholder'=>'Job Title / Designation')) }}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-1">
                        {{ Form::email('email', old('email'), array('class' => 'form-control','placeholder'=>'Email address*')) }}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-5 col-lg-offset-1 col-md-6 marginBottom">
                        {{ Form::text('city', old('city'), array('class' => 'form-control','placeholder'=>'City')) }}
                    </div>
                    <div class="col-lg-5 col-md-6">
                        {!! Form::select('country_id', $country, old('country_id'), ['class' => 'form-control selectpicker show-tick','data-live-search'=>'true','title'=>'Country']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-5 col-lg-offset-1 col-md-6 marginBottom">
                        <div class="checkbox">
                            <label>

                                {{ Form::checkbox('key_contact','yes',($person->key_contact=='yes')?true:false,['class'=>'checkbox_size']) }} Key Contact


                            </label>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6">
                        {{ Form::text('tags', old('tags'), array('class' => 'form-control','placeholder'=>'Tags')) }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-1">
                        {{ Form::textarea('notes', old('notes'), ['placeholder' => 'Notes','class'=>'form-control','size' => '3x5']) }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-2 col-lg-offset-9 col-md-2 col-md-offset-10 col-sm-2 col-sm-offset-10 floatinginUpdate ">
                        {{ Form::submit('Update', array('class' => 'btn btn-sm btn-success updateMarginLeft')) }}
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>