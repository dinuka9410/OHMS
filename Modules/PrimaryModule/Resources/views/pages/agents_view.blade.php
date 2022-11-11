@extends('../layout/home-layout')

@section('title','Agents')

    @section('home-content')
    <div class="grid grid-cols-12 gap-1 mt-1" style="overflow: hidden; padding:15px;">
        <div class="intro-y col-span-12 inline-grid flex-wrap sm:flex-no-wrap items-center mt-2">

        <a href="{{route('add_update_agent_view')}}" class="add-rate-link  mr-2 ml-auto"> <i data-feather="plus-circle" class="w-5 h-5 mr-1"> </i> Add Agent</a>

            <div class="hidden md:block mx-auto text-gray-600"></div>

        </div>
        <!-- BEGIN: Data List -->

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
        <table class="table table-report mt-2" id="dttbl">
            <thead>
                <tr>
                    <th class="text-center whitespace-no-wrap">#</th>
                    <th class="whitespace-no-wrap">Agent Code</th>
                    <th class="whitespace-no-wrap">Agent Name</th>
                    <th class="whitespace-no-wrap">Agent Email</th>
                    <th class="whitespace-no-wrap">Agent Rating</th>
                    <th class="whitespace-no-wrap">Agent Contact Person</th>
                    <th class="text-center whitespace-no-wrap">Status</th>
                    <th class="text-center whitespace-no-wrap"> Action</th>
                </tr>
            </thead>

            <tbody id="data_table">

            </tbody id="body">

        </table>

    </div>

    </div>


    @endsection

    @section('script-area')
        <script>

            $(function(){
                getAgents();
            });

            function getAgents(){

                var table = $('#dttbl').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax :{
                        'url':"{{route('getagents')}}",
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        {data: 'agentCode', name: 'agentCode'},
                        {data: 'agentName', name: 'agentName'},
                        {data: 'agentEmail', name: 'agentEmail'},
                        {data: 'agentRating', name: 'agentRating'},
                        {data: 'agentContactPerson', name: 'agentContactPerson'},
                        {data: 'status', name: 'status'},
                        {data: 'edit-btn', name: 'edit-btn'},

                    ]
                });

            }

            function deleteAgent(id){

                var url = "{{route('agent_delete')}}";
                var param = {
                    'agentId':id,
                    "_token": "{{ csrf_token() }}",
                };

                confirmnotify("do you want to delete this agent?").then(res=>{

                    if(res.isConfirmed){

                        preloader();

                        $.ajax({
                            url:url,
                            type:'POST',
                            data:param,
                            success:function(data){

                                var result = data;

                                if(result.error_status==0){

                                    removeLoader();

                                    notify(0,result.msg);
                                    //location.reload();
                                    $('#dttbl').DataTable().ajax.reload();

                                }else{
                                    removeLoader();
                                    notify(1,result.msg);

                                }

                            },
                            error:function(err){
                                removeLoader();
                                var error = err;
                                notify(1,error.msg);
                            }
                        });

                    }

                });

            }


            function changeStatus(id,status){

                var url = "{{route('change_agent_status')}}";

                if(status==1){

                    new_status = 0;

                }else{

                    new_status = 1;

                }

                var param = {
                    agent_id:id,
                    status:new_status,
                };

                $.ajax({
                    url:url,
                    data:param,
                    success:function(res){
                        var result = res;
                        if(result.error_status==0){

                            notify(0,result.msg);
                            $('#dttbl').DataTable().ajax.reload();
                        }else{

                            notify(1,result.msg);

                        }
                    },
                    error:function(err){
                        notify(1,'unable to change status');
                    }
                });

            }

        </script>
    @endsection
