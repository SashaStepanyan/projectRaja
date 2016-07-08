<div class="">
    <div class="bs-component">
        <div class="hasError"></div>

        {{ Form::open(array('route' => 'admin.entity.store','class'=>'form-horizontal','id'=>'create_new_entity')) }}
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-1">
                {!! Form::select('applicant[]',   $applicant, '', ['class' => 'form-control selectpicker','multiple'=>'multiple','data-hide-disabled'=>true,'id'=>'first-disabled','title'=>'Applicant / Client / Foreign Counsel*']) !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-1">
                {{ Form::text("entity[name]", '', array('class' => 'form-control','placeholder'=>'Entity name*')) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-1">
                {{ Form::text("entity[existing_type]", '', array('class' => 'form-control','placeholder'=>'Existing type')) }}
            </div>
        </div>
        <div class="addresses">
            <div id="address1" class="every_address col-md-10 col-md-offset-1">
                <span aria-hidden="true" class="close close_address checkHidden">×</span>

            <div class="form-group">
                <div class="col-lg-12">
                    {{ Form::text("address[street][]", '', array('class' => 'form-control','placeholder'=>'Street')) }}
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-6  col-md-6 marginBottom">
                    {{ Form::text("address[city][]", '', array('class' => 'form-control','placeholder'=>'City')) }}
                </div>
                <div class="col-lg-6 col-md-6">
                    {{ Form::text("address[state][]", '', array('class' => 'form-control','placeholder'=>'State')) }}
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-6 col-md-6 marginBottom">
                    {{ Form::text("address[zip][]", '', array('class' => 'form-control','placeholder'=>'Zip', 'id'=>'zip')) }}
                </div>
                <div class="col-lg-6 col-md-6">
                    {!! Form::select("address[country_id][]",   [null => 'Country'] +$country, '', ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-12 ">
                    {{ Form::text("address[tmo][]", '', array('class' => 'form-control','placeholder'=>'TMO code' , 'id'=>'tmo')) }}
                </div>
            </div>

            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-5 col-lg-offset-1 col-md-6 marginBottom">
                {!! Form::select("entity[currency_id]" ,  [null => 'Billing Currency'] +$currency, '', ['class' => 'form-control']) !!}
            </div>
            <div class="col-lg-5 col-md-6">
                {!! Form::select("entity[credit_days_id]",   [null => 'Credit Days'] +$credit_days, '', ['class' => 'form-control']) !!}
            </div>
        </div>


        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-1">
                {{ Form::textarea("entity[notes]", null, ['placeholder' => 'Notes','class'=>'form-control','size' => '3x5']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-2 col-lg-offset-1 col-md-2 col-sm-3 ">
                {{ Form::button('Add Address', array('class' => 'btn btn-sm btn-success','id'=>'add_second_address')) }}
            </div>
            <div class="col-lg-2 col-lg-offset-6 col-md-2 col-md-offset-7 col-sm-2 col-sm-offset-7 floatingDiv">
                {{ Form::submit('Save', array('class' => 'btn btn-sm btn-success','id'=>'add_new_address')) }}
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>