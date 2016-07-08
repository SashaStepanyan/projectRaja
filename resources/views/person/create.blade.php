<div class="">
    <div class="bs-component">
        <div class="hasError"></div>

        {{ Form::open(array('route' => 'admin.person.store','class'=>'form-horizontal','id'=>'create_new_person')) }}


        <div class="form-group">
            <div class="col-lg-5 col-lg-offset-1 col-md-6 marginBottom">
                {{ Form::text('firstname', '', array('class' => 'form-control','placeholder'=>'First name*')) }}
            </div>
            <div class="col-lg-5 col-md-6">
                {{ Form::text('lastname', '', array('class' => 'form-control','placeholder'=>'Last name')) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-1">
                {{ Form::text('job', '', array('class' => 'form-control','placeholder'=>'Job Title / Designation')) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-1">
                {{ Form::email('email', '', array('class' => 'form-control','placeholder'=>'Email address*')) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-5 col-lg-offset-1 col-md-6 marginBottom">
                {{ Form::text('city', '', array('class' => 'form-control','placeholder'=>'City')) }}
            </div>
            <div class="col-lg-5 col-md-6">
                {!! Form::select('country_id',   $country, '', ['class' => 'form-control selectpicker show-tick','data-live-search'=>'true','title'=>'Country']) !!}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-5 col-lg-offset-1 col-md-6 marginBottom">
                <div class="checkbox">
                    <label>

                        {{Form::checkbox('key_contact', 'yes', false,['class'=>'checkbox_size']) }} Key Contact

                    </label>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                {{ Form::text('tags', '', array('class' => 'form-control','placeholder'=>'Tags')) }}
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-1">
                {{ Form::textarea('notes', null, ['placeholder' => 'Notes','class'=>'form-control','size' => '3x5']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-2 col-lg-offset-9 col-md-2 col-md-offset-10 col-sm-2 col-sm-offset-10 floatinginUpdate">
                {{ Form::submit('Save', array('class' => 'btn btn-sm btn-success marginLeft','id'=>'creating_user')) }}
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>