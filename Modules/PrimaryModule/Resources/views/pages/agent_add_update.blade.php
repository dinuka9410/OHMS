@extends('../layout/form-layout')

@section('form-name', 'Agents')

@section('form-area')


<form class="validate-form" id="frmdt" name="frmdt" method="POST" action="{{ route('agent_add_edit') }}">
    @csrf

    <input type="text" id="agent_id" hidden name="agent_id" value="{{ isset($details) ? $details->id : '' }}">

    <div class="grid grid-cols-12 gap-1">

        <div class="col-span-12 md:col-span-3">
            <label class="flex flex-col sm:flex-row">Agent Code</label>
            <input type="text" name="agent_code" id="agent_code" class="input w-full border mt-1"  placeholder="Agent code" value="{{ isset($details) ? $details->agentCode : '' }}">
        </div>

        <div class="col-span-12 md:col-span-3">
            <label class="flex flex-col sm:flex-row">Agent Name</label>
            <input type="text" name="agent_name" id="agent_name" class="input w-full border mt-1"  placeholder="Agent Name" value="{{ isset($details) ? $details->agentName : '' }}">
        </div>

        <div class="col-span-12 md:col-span-3">
            <div class="flex flex-col sm:flex-row">Agent Email</div>
            <input name="agent_email" id="agent_email" type="email" required class="input w-full border mt-1" placeholder="Eg : evolve@travels.com" value="{{ isset($details) ? $details->agentEmail : '' }}">
        </div>

         <div class="col-span-12 md:col-span-3">
            <div class="flex flex-col sm:flex-row">Agent Address</div>
            <input name="agent_address" id="agent_address" type="input" required class="input w-full border mt-1" placeholder="Eg : W.A.D Ramanayake Road" value="{{ isset($details) ? $details->agentAddress : '' }}">
         </div>

         
      
         

        <div class="col-span-12 ">

       

         

            <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">

                
               

                <div class="intro-y col-span-12 md:col-span-3 input-form">
                    <div class="mb-2">Agent Rating</div>
                    <select name="agent_rating" class="input w-full border flex-1" id="agent_rating">
                        <option @if(isset($details)&&$details->agentRating=='1') selected @endif value="1">1</option>
                        <option @if(isset($details)&&$details->agentRating=='2') selected @endif value="2">2</option>
                        <option @if(isset($details)&&$details->agentRating=='3') selected @endif value="3">3</option>
                        <option @if(isset($details)&&$details->agentRating=='4') selected @endif value="4">4</option>
                        <option @if(isset($details)&&$details->agentRating=='5') selected @endif value="5">5</option>
                    </select>
                </div>

                <div class="intro-y col-span-12 md:col-span-3 input-form">
                    <div class="mb-2">Agent Contact Person</div>
                    <input name="agent_contact_person" id="agent_contact_person" type="input" required class="input w-full border flex-1" placeholder="Eg : Mr.Saman" value="{{ isset($details) ? $details->agentContactPerson : '' }}">
                </div>

                <div class="intro-y col-span-12 md:col-span-3 input-form">
                    <div class="mb-2">Contact Number 1</div>
                    <input onkeydown="validateMobileNumber(this)" name="tel_no_1" id="tel_no_1" type="tel" maxlength="15" class="input w-full border flex-1" placeholder="Eg : 081xxxxxxxxx" value="{{ isset($details) ? $details->tel_no_1 : '' }}">
                    <label style="color: red" hidden id="phone_no_error"></label>
                </div>
                <div class="intro-y col-span-12 md:col-span-3 input-form">
                    <div class="mb-2">Contact Number 2</div>
                    <input onkeydown="validateMobileNumber_2(this)" name="tel_no_2" id="tel_no_2" type="tel" class="input w-full 
                    border flex-1" placeholder="Eg : 08122222222" value="{{ isset($details) ? $details->tel_no_2 : '' }}">
                    <label style="color: red" hidden id="phone_no_error_2"></label>
                </div>

            </div>
        </div>
    </div>
    <!-- staus change Active/Inactive -->
    <input type="hidden" id='form_status' name="form_status" value="{{ isset($details) ? $details->status : '1' }}">

</form>
@endsection

@if (isset($status_info))


@section('status', $status_info['status'] )

@section('status_button')
<input class="input input--switch border" type="checkbox" id='status_btn' onchange="changeStatus(this)" @if($details->status=='1') checked @endif >
@endsection

@section('additional-info')
<tr>
    <th>Created By</th>
    <th style="text-align:right">{{ $status_info['created_by'] }}</th>
</tr>
<tr>
    <th>Created Date:</th>
    <th style="text-align:right">{{ $status_info['created_at'] }}</th>
</tr>
<tr>
    @if(isset($status_info['updated_by']))
    <th>Updated By:</th>
    <th style="text-align:right">{{ $status_info['updated_by'] }}</th>
    @endif
</tr>
<tr>
    @if(isset($status_info['updated_by']))
    <th>Updated Date:</th>
    <th style="text-align:right">{{ $status_info['updated_at'] }}</th>
    @endif
</tr>
@endsection

@endif


@section('script-area')

@if(!isset($details))
<script>
    $(function() {
        $('#clear').attr('hidden', false);
    });
</script>
@else
<script>
    $(function() {
        $('#status_box').show('fast');
        $('#save').text('UPDATE');
    });
</script>
@endif

<script>
    $(document).ready(function() {

        $('#save').attr('hidden', false);
        $('#cancel').attr('hidden', false);

    });

    $('#save').click(function() {


        $('#frmdt').validate({
            onfocusout: false,
            onkeyup: false,
            onclick: false,
            rules: {
                agent_code: {
                    required: true,
                    minlength: 3,
                    chk_agent_code:true,
                },
                agent_name: {
                    required: true,
                    minlength: 3,
                    chk_agent_name:true,
                },
                agent_email: {
                    required: true,
                    email: true,
                    chk_agent_email:true,
                },
                agent_address: {
                    required: true,
                    minlength: 3,
                },
                agent_contact_person: {
                    required: true,
                    minlength: 3,
                },
                tel_no_1: {
                    required: true,
                }

            },

            // relavant messages

            messages: {
                agent_address: "please provide a valid address",
                agent_contact_person: "please provide a contact person in the agent",

            }

        });



        jQuery.validator.addMethod("chk_agent_code",function(value,element){

            var agent_id = $('#agent_id').val();
            var agent_code = $('#agent_code').val();

            if(agent_code!=''){

                function valdt(){
                    var temp = 0;
                    $.ajax({
                        type        : "POST",
                        url         : "{{ route('validate_agent_code') }}",
                        async       : false,
                        data        : {"agent_code":agent_code,'agent_id':agent_id},
                        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend  : function(){$("body").css("cursor","wait"); $('#agent_code').addClass('data_loading');},
                        success     : function(msg){ temp=msg; $("body").css("cursor","default"); $('#agent_code').removeClass('data_loading');},
                        error       : function(){ $("body").css("cursor","default"); $('#agent_code').removeClass('data_loading'); console.log("Error");  }
                    });

                    return temp;

                }

                var vlrs = valdt();

                if(vlrs){
                    return false;
                }else {
                    return true;
                }

            }

        },"agent code already exists");


        jQuery.validator.addMethod("chk_agent_name",function(value,element){

            var agent_id = $('#agent_id').val();
            var agent_name = $('#agent_name').val();

            if(agent_name!=''){

                function valdt(){
                    var temp = 0;
                    $.ajax({
                        type        : "POST",
                        url         : "{{ route('validate_agent_name') }}",
                        async       : false,
                        data        : {"agent_name":agent_name,'agent_id':agent_id},
                        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend  : function(){$("body").css("cursor","wait"); $('#agent_name').addClass('data_loading');},
                        success     : function(msg){ temp=msg; $("body").css("cursor","default"); $('#agent_name').removeClass('data_loading');},
                        error       : function(){ $("body").css("cursor","default"); $('#agent_name').removeClass('data_loading'); console.log("Error");  }
                    });

                    return temp;

                }

                var vlrs = valdt();

                if(vlrs){
                    return false;
                }else {
                    return true;
                }

            }

        },"agent name already exists");



        jQuery.validator.addMethod("chk_agent_email",function(value,element){

            var agent_id = $('#agent_id').val();
            var agent_email = $('#agent_email').val();

            if(agent_email!=''){

                function valdt(){
                    var temp = 0;
                    $.ajax({
                        type        : "POST",
                        url         : "{{ route('validate_agent_email') }}",
                        async       : false,
                        data        : {"agent_email":agent_email,'agent_id':agent_id},
                        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend  : function(){$("body").css("cursor","wait"); $('#agent_name').addClass('data_loading');},
                        success     : function(msg){ temp=msg; $("body").css("cursor","default"); $('#agent_name').removeClass('data_loading');},
                        error       : function(){ $("body").css("cursor","default"); $('#agent_name').removeClass('data_loading'); console.log("Error");  }
                    });

                    return temp;

                }

                var vlrs = valdt();

                if(vlrs){
                    return false;
                }else {
                    return true;
                }

            }

        },"agent email already exists");



        if ($('#frmdt').valid()) {

            var agent_id = $('#agent_id').val();
            var agent_code = $('#agent_code').val();

                preloader();

                $('#frmdt').submit();

        }

    });
</script>

@endsection
