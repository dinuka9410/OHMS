@extends('../layout/form-home-layout')

@section('form-name', 'Room Type')

@section('form-area')

    <form class="validate-form" id="frmdt" method="POST" action="{{ route('add_update_room_type') }}" style="overflow: hidden; padding:15px;">
        @csrf
        <input hidden type="text" name="room_type_id" id="room_type_id" placeholder="Luxury Penthouse" minlength="5"
            value="{{ isset($room_type) ? $room_type->room_type_id : '' }}">

        <div class="grid grid-cols-12 gap-5">
            <div class="col-span-12 ">

                <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">

                    <div class="intro-y col-span-12 sm:col-span-6 input-form">
                        <div class="mb-2">Room Type</div>
                        <input type="text" name="room_type_select" id="room_type_select" class="input w-full border mt-2"
                            placeholder="Luxury Penthouse"
                            value="{{ isset($room_type) ? $room_type->room_type_Select : '' }}" required>
                    </div>
                </div>

                <div class="input-form">
                    <br>
                    <label class="flex flex-col sm:flex-row">Description</label><br>
                    <textarea name="room_type_discription" id="room_type_discription" class="input w-full border mt-2 bg-gray-100"
                        placeholder="">{{ isset($room_type) ? $room_type->room_type_descrption : '' }}</textarea>
                </div>
            </div>
        </div>
        <input type="hidden" id='form_status' name="form_status"
        value="{{ isset($details) ? $details->room_type_status : '1' }}">
        <button type="button" class="button w-20 bg-theme-9 text-white mt-3" style="margin-top: 10%;"
            id="savebtn">Save</button>

    </form>

@endsection


@section('table-area')
    <div class="p-5" id="basic-table">
        <div class="preview">
            <div class="overflow-x-auto">

                <table class="table table-report -mt-2" id="dttbl">
                    <thead>
                        <tr>
                            <th class="whitespace-no-wrap">#</th>
                            <th class="whitespace-no-wrap">Type</th>
                            <th class="text-center whitespace-no-wrap"> Action</th>
                        </tr>
                    </thead>
                    <tbody id="data_table">
                        @if (count($room_types) > 0)
                        @php
                            $index_count = 0;
                        @endphp

                            @foreach ($room_types as $room_type)
                            @php
                                $index_count = $index_count+1;
                            @endphp

                                <tr class="intro-x">
                                    <td>
                                        <div class="font-medium whitespace-no-wrap ">{{ $room_type->room_type_id }}</div>
                                    </td>
                                    <td>
                                        <div class="font-medium whitespace-no-wrap">{{ $room_type->room_type_Select }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex justify-center items-center">
                                        @if ($room_type->room_type_status == '1')
                                        <div><div style="margin: auto;"  class="onoffswitch">
                                            <input onclick="change_status_room_type({{$room_type->room_type_id}});"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="roomtypenumber{{$index_count}}" tabindex="0" checked >
                                            <label class="onoffswitch-label" for="roomtypenumber{{$index_count}}">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div></div>

                                        @else
                                        <div><div style="margin: auto;" class="onoffswitch">
                                            <input onclick="change_status_room_type({{$room_type->room_type_id}});"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="roomtypenumber{{$index_count}}" tabindex="0" >
                                            <label class="onoffswitch-label" for="roomtypenumber{{$index_count}}">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div></div>
                                        @endif
                                        @if ($room_type->room_type_id != 0)

                                        <div style="margin-left: 10%;" class="flex justify-center items-center mt-2">
                                            <a onclick="preloader()" style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" href="{{route('room_type_add_edit',['id'=> $room_type->room_type_id])}}"><i class="fas fa-edit"></i></a>
                                            <a  style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" href="{{route('room_type_delete',['id'=> $room_type->room_type_id])}}" ><i class="fas fa-trash"></i></a>
                                        </div>
{{--
                                            <form style="margin-left: 10%;" action="{{ route('room_type_add_edit') }}" method="get">
                                                <input hidden value="{{ $room_type->room_type_id }}" name="id">
                                                <button onclick="preloader()"
                                                    class="flex items-center mr-3 text-theme-1" type="submit"><i
                                                        data-feather="check-square"
                                                        class="w-4 h-4 mr-1"></i>Edit</button>
                                            </form>
                                            <form action="{{ route('room_type_delete') }}" method="get">
                                                <input hidden value="{{ $room_type->room_type_id }}" name="id">
                                                <button onclick="preloader()"
                                                    class="flex items-center mr-3 text-theme-6" type="submit"><i
                                                        data-feather="trash-2"
                                                        class="w-4 h-4 mr-1"></i>Delete</button>
                                            </form> --}}

                                    @endif
                                </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody id="body">
                </table>

            </div>
        </div>
    </div>



@endsection


@section('script-area')

    @if (isset($room_catogory_withid))
        <script>
            $(function() {
                $('#savebtn').text('UPDATE');
                
            });
        </script>
    @endif

    @if (!isset($room_type))
        <script>
            $(function() {

                $('#clear').attr('hidden', false);

            });
        </script>
    @endif

    @if (isset($status_info))
    @section('status', $status_info['status'])

    @section('status_button')
        <input class="input input--switch border" type="checkbox" id='status_btn' onchange="changeStatus(this)"
            @if ($details->room_type_status == '1') checked @endif>
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
            @if (isset($status_info['updated_by']))
                <th>Updated By:</th>
                <th style="text-align:right">{{ $status_info['updated_by'] }}</th>
            @endif
        </tr>
        <tr>
            @if (isset($status_info['updated_by']))
                <th>Updated Date:</th>
                <th style="text-align:right">{{ $status_info['updated_at'] }}</th>
            @endif
        </tr>
    @endsection

@endif


    <script>
        function change_status_room_type(id) {

            var url = "{{ route('change_status_room_type') }}";

            var params = {
                'id': id,
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: params,
                success: function(res) {


                    notify(0, 'Room type status has been changed !');

                },
                error: function(err) {

                    notify(1, 'Unable to changed status!');

                }
            });


        }

        $(document).ready(function() {

            $('#save').attr('hidden', true);
            $('#cancel').attr('hidden', false);

        });

        $('#savebtn').click(function() {

            $('#frmdt').validate({

                rules: {
                    room_type_select: {
                        required: true,
                        minlength: 1,
                    },
                },

                // relavant messages

                messages: {

                    room_type_select: "please enter a room type",

                    room_type_discription: "please enter a room discription",
                }

            });

            if ($('#frmdt').valid()) {
                preloader();
                var room_type_select = $('#room_type_select').val();
                var room_type_id = $('#room_type_id').val();
                var isunique = false;

                if (room_type_id == '') {

                    var isunique = checkIfUniqueField('room_types', 'room_type_Select', room_type_select);

                } else {
                    isunique = true;

                }

                if (isunique) {

                    $('#frmdt').submit();

                } else {
                    removeLoader();
                    notify(1, 'the room type name is not unique');
                }
            }


        });
    </script>

@endsection
