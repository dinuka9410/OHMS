@extends('../layout/home-layout')

@section('title', 'Agent Room Rates')

@section('home-content')

    <div class="grid grid-cols-12 gap-6 mt-5" style="overflow: hidden; padding:15px;">
        <div class="intro-y col-span-12 inline-grid flex-wrap sm:flex-no-wrap mt-2">

            <a href="{{ route('add_update_room_rate_view') }}" class="add-rate-link  mr-2 ml-auto"> <i
                    data-feather="plus-circle" class="w-5 h-5 mr-1"> </i> Add Rate</a>

            <div class="hidden md:block mx-auto text-gray-600"></div>

        </div>
        <!-- BEGIN: Data List -->

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2" id="dttbl">
                <thead>
                    <tr>
                        <th class="whitespace-no-wrap">#</th>
                        <th class="text-center whitespace-no-wrap">Travel Agent</th>
                        <th class="text-center whitespace-no-wrap">Season</th>
                        <th class="text-center whitespace-no-wrap">Action</th>
                    </tr>
                </thead>

                <tbody id="data_table">

                </tbody id="body">

            </table>

        </div>

    </div>

    <script>
        function deleterate(agent_id,season_id) {

            var url = "{{ route('deleterate') }}";

            var params = {
                'agent_id': agent_id,
                'season_id': season_id,
            }
            $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                        });
            $.ajax({
                type: 'GET',
                url: url,
                data: params,
                success: function(res) {

                    location.reload();
                    
                    notify(0, 'Delete successful');

                },
                error: function(err) {

                    notify(1, 'Unable to delete room rate! (This room rate already used in reservation) ');

                }
            });


        }

        function change_status(agent_id, season_id) {

            var url = "{{ route('checkcurrys_rate_agent_room') }}";

            var params = {
                'agent_id': agent_id,
                'season_id': season_id,
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: params,
                success: function(res) {

                    loadtable();
                    notify(0, 'Agent rate status has been changed !');

                },
                error: function(err) {

                    notify(1, 'Agent rate to changed status!');

                }
            });


        }


        $(function() {

            loadtable();


        });

        function loadtable() {
            var table = $('#dttbl').DataTable({
                processing: true,
                serverSide: false,
                bDestroy: true,
                ajax: {
                    'url': "{{ route('get_all_roomrate') }}"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'agent',
                        name: 'agent'
                    },
                    {
                        data: 'season',
                        name: 'season'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    }

                ]
            });

        }
    </script>
@endsection
