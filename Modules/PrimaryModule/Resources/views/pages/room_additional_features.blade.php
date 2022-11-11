@extends('../layout/form-home-layout')

@section('form-name','Room Facilities')

@section('form-area')

    <form autocomplete="off" class="validate-form" id="frmdt" method="POST" action="{{'add_update_facilities'}}" style="overflow: hidden; padding:15px;">
        @csrf
        <input type="text" hidden name="facility_id_holder" id="facility_id_holder" >
        <div class="input-form ">
            <br>
            <label class="flex flex-col sm:flex-row">Facility Name</label><br>
            <input type="text" name="facility_name" required id="facility_name" class="input w-full border mt-1" style="width:100%"
                placeholder="Facility name" >
        </div>

        <button  type="button" class="button w-20 bg-theme-9 text-white mt-3" style="margin-top: 10%;" id="savebtn" >Save</button>
        <button onclick="window.location.replace('room_facilities_view')"  type="button" class="button w-20 bg-theme-1 text-white mt-3" style="margin-top: 10%;"  >Clear</button>
    </form>

@endsection


@section('table-area')

    <div style="overflow-y: scroll; height:400px" class="p-5" id="basic-table">
        <div class="preview">
            <div class="overflow-x-auto">

        <table class="table table-report -mt-2" id="dttbl">
            <thead>
                <tr>
                    <th class="whitespace-no-wrap">#</th>
                    <th class="whitespace-no-wrap">Room facilites name</th>
                    <th class="text-center whitespace-no-wrap">ACTION</th>
                </tr>
            </thead>
            <tbody id="data_table">
            </tbody id="body">
        </table>

            </div>
        </div>
    </div>

@endsection


@section('script-area')

<script>

    $(document).ready(function(){

        $('#save').attr('hidden',true);
        $('#cancel').attr('hidden',false);

        loadTable();

    });


    function loadTable(){

            $('#dttbl').DataTable({
                    processing: true,
                    serverSide: false,
                    bDestroy: true,
                    ajax :{
                        'url':"{{route('get_all_room_facilities')}}",
                    },

                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        {data: 'add_additional_facilites_name', name: 'add_additional_facilites_name'},
                        {
                            data: 'edit-btn',
                            name: 'edit-btn',
                        },


                    ]

            });

    }

    $('#savebtn').click(function(){


       $('#frmdt').validate({

            onfocusout: false,
            onkeyup: false,
            onclick: false,

            rules:{

                facility_name:{
                    required:true,
                    chk_room_facility:true,
                }
            },

            // relavant messages

            messages:{

                facility_name:"Please enter a valid facility name / Already in use"
            }

       });


       jQuery.validator.addMethod("chk_room_facility",function(value,element){

            var facility_id = '';
            var gfacility = $('#facility_name').val();

            if(gfacility!=''){

                function valdt(){
                    var temp = 0;
                    $.ajax({
                        type        : "POST",
                        url         : "{{ route('validate_facility_name') }}",
                        async       : false,
                        data        : {"gfacility":gfacility,'facility_id':facility_id},
                        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend  : function(){$("body").css("cursor","wait"); $('#facility_name').addClass('data_loading');},
                        success     : function(msg){ temp=msg; $("body").css("cursor","default"); $('#facility_name').removeClass('data_loading');},
                        error       : function(){ $("body").css("cursor","default"); $('#facility_name').removeClass('data_loading'); console.log("Error");  }
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

        },"room facility already exists");


       if($('#frmdt').valid()){
           preloader();
           $('#frmdt').submit();
       }

    });


    function get_facility(id){

        //preloader();

        var url = "{{route('get_facility')}}";

        var data = {
            'facility_id':id,
        };

        $.ajax({
            url:url,
            data:data,
            success:function(res){

                var result = res;

                console.log(result);

                if(result.error_status==0){

                    $('#savebtn').text('UPDATE');

                    $('#facility_id_holder').val('');
                     $('#facility_name').val('');

                     $('#facility_id_holder').val(result.data.add_additional_facilites_id);
                     $('#facility_name').val(result.data.add_additional_facilites_name);

                }


            },
            error:function(err){

                console.log(err);

            }
        });

        removeLoader();
    }


    function deleteFacility(id){

        var url = "{{route('deletefacility')}}";

        var param = {
            'facility_id':id,
        };


        confirmnotify("do you want to delete this season?").then(res=>{

            if(res.isConfirmed){
                preloader();
                $.ajax({
                    url:url,
                    data:param,
                    success:function(res){
                        var data = res;

                        if(data.error_status==0){
                            removeLoader();
                            notify(0,data.msg);
                            loadTable();
                        }else{

                            removeLoader();
                            notify(1,data.msg);

                        }

                    },
                    error:function(err){

                        notify(1,error.msg);
                    },
                });

            }

        });


    }

</script>


@endsection
