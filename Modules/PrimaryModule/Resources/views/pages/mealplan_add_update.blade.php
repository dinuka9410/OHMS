@extends('../layout/form-home-layout')

@section('form-name', 'Meal Plan')

@section('form-area')


    <form autocomplete="off" class="validate-form" id="frmdt" method="POST" action="{{ route('meal_add_edit') }}" style="overflow: hidden; padding:15px;">
        @csrf

        <input type="text" id="meal_id" hidden name="meal_id" value="{{ isset($details) ? $details->id : '' }}">

        <div class="grid grid-cols-12 gap-5">
            <div class="col-span-12 ">

                <div class="input-form mt-2">
                    <label class="flex flex-col sm:flex-row">Meal Plan Code</label>
                    <input type="text" style="width: 100%;" name="meal_code" id="meal_plan_code" class="input w-full border mt-1" 
                        placeholder="meal plan code" value="{{ isset($details) ? $details->mealPlanCode : '' }}">
                </div>


                <div class="input-form mt-2">
                    <label class="flex flex-col sm:flex-row">Meal Plan Name</label>
                    <input type="text" style="width: 100%" name="meal_name" id="meal_plan_name" class="input w-full border mt-1"
                        placeholder="Meal Plan Name" value="{{ isset($details) ? $details->mealPlanName : '' }}">
                </div>

            </div>
        </div>

        <button  type="button" class="button w-20 bg-theme-9 text-white mt-3" style="margin-top: 10%;" id="savebtn" >Save</button>
        <button onclick="window.location.replace('add_update_meal_view')"  type="button" class="button w-20 bg-theme-1 text-white mt-3 btn-clear" style="margin-top: 10%;"  >Clear</button>
    </form>



@endsection


@section('table-area')

<div class="p-5" id="basic-table">
    <div class="preview">
        <div class="overflow-x-auto">
            <table class="table" id="dttbl">
                <thead>
                    <tr>
                        <th class="whitespace-no-wrap">#</th>
                        <th class="whitespace-no-wrap">Meal Plan Code</th>
                        <th class="whitespace-no-wrap">Meal Plan Name</th>
                        <th class="text-center whitespace-no-wrap" >Action</th>
                    </tr>
                </thead>
                <tbody id="data_table">
                    @if (count($meals) > 0)
                    @foreach ($meals as $row)
                    <tr class="intro-x">

                        <td>
                            <div class=" whitespace-no-wrap">{{ $row->id }}</div>
                       
                        </td>

                        <td>
                            <div class="whitespace-no-wrap">{{ $row->mealPlanCode }}</div>
                        </td>
                        
                        <td>
                            <div class="whitespace-no-wrap">{{ $row->mealPlanName }}</div>
                        </td>

                        <td><div class="flex justify-center items-center mt-2">
                            <a onclick="preloader()" style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" href="{{route('add_update_meal_view',['id'=>$row->id])}}"><i class="fas fa-edit"></i></a>
                            <a  style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" href="#" onclick="deleteMealPlan({{$row->id}})" ><i class="fas fa-trash"></i></a></div>
                        </td>

                    </tr>
                    @endforeach
                   @endif
                </tbody>
            </table>
       
        </div>
    </div>
  
</div>


@endsection


@if(isset($status_info))

    <div>

        @section('additional-info')

            <p>Created By : {{ isset($status_info) ? $status_info['created_by'] : '' }}</p>
            <p>Created Date : {{ isset($status_info) ? $status_info['created_at'] : '' }}</p>
            <p>Updated By : {{ isset($status_info) ? $status_info['updated_by'] : '' }}</p>
            <p>Updated Date : {{ isset($status_info) ? $status_info['updated_at'] : '' }}</p>

        @endsection

    </div>

@endif


@section('script-area')


    <script>

        $(document).ready(function(){
            $('#save').attr('hidden',true);
            $('#cancel').attr('hidden',false);
            $('#dttbl').dataTable();
        });

        $('#savebtn').click(function() {


            $('#frmdt').validate({

                rules: {
                    meal_code: {
                        required: true,
                        minlength: 3,
                    },
                    meal_name: {
                        required: true,
                        minlength: 3,
                    },

                },

                // relavant messages

                messages: {
                    meal_code: "please enter a valid meal plan code",
                    meal_name: "please enter valid meal plan name",

                }

            });


            if ($('#frmdt').valid()) {

                var meal_id = $('#meal_id').val();
                var meal_code = $('#meal_plan_code').val();
                var isunique = false;

                preloader();

                if(meal_id==""){

                    isunique = checkIfUniqueField('meal_plans','mealPlanCode',meal_code);

                }else{

                    isunique = true;
                }
                

               if(isunique){

                    $('#frmdt').submit();

               }else{

                    removeLoader();
                    notify(1,'meal plan code is not unique');

               }

            }

        });

        function deleteMealPlan(id){

            var url = "{{route('delete_meal_plan')}}";

            var param = {
                'id':id,
                "_token": "{{ csrf_token() }}",
            };

            preloader();

            $.ajax({
                type:'POST',
                url:url,
                data:param,
                success:function(res){
                    var result = res;
                    if(result.error_status==0){

                        notify(0,result.msg);
                        location.reload();

                    }else{

                        removeLoader();
                        notify(1,result.msg);

                    }
                },
                error:function(err){
                    removeLoader();
                    notify(err.msg);
                }
            });

        }

    </script>

    @if(isset($details))
        <script>
            $(function(){
                $('#savebtn').text('UPDATE');
            });
        </script>
    @endif


@endsection
