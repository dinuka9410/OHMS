@extends('../layout/home-layout')

@section('title', 'Rooms')

@section('home-content')

    <div class="grid grid-cols-12 gap-1 mt-1">
        <div class="intro-y col-span-12 inline-grid flex-wrap sm:flex-no-wrap items-center mt-1">


            <a href="{{ route('room_add_edit') }}" class="add-rate-link  mr-2 ml-auto"> <i data-feather="plus-circle"
                    class="w-5 h-5 mr-1"> </i> Add room</a>
        </div>
        <!-- BEGIN: Data List -->

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2" id="dttbl">
                <thead>
                    <tr>
                        <th class=" whitespace-no-wrap">#</th>
                        <th class=" text-center whitespace-no-wrap">Name</th>
                        <th class=" text-center whitespace-no-wrap">Image</th>
                        <th class=" text-center whitespace-no-wrap">Area</th>
                        <th class=" text-center whitespace-no-wrap">Category</th>
                        <th class=" text-center whitespace-no-wrap">Type</th>
                        <th class=" text-center whitespace-no-wrap">Floor</th>
                        <th class=" text-center whitespace-no-wrap">Action</th>
                    </tr>
                </thead>
                <tbody id="data_table">


                </tbody>
            </table>
        </div>

        <div class="modal" id="datepicker-modal-preview">
            <div class="modal__content">
                <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto">
                        Rooms Details
                    </h2>
                </div>
                <br>
                <div class="w-40 h-40 relative image-fit cursor-pointer zoom-in mx-auto">
                    @php
                        $img_value = 1;
                    @endphp
                    <img id="img_review" name="img_review" class="rounded-md" alt=""
                        src=" @if (isset($room)) {{ asset('storage/img/rooms/' . $img_value . '.jpg') }} @else {{ asset('dist/images/room_defult.jpg') }} @endif">

                </div>
                <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                    <div class="col-span-12 sm:col-span-6">
                        <b>Room Name : </b> <label id="Room_Name" name="Room_Name"></label>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <b>Room Category :</b> <label id="Room_Category" name="Room_Category"></label>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                    <div class="col-span-12 sm:col-span-6">
                        <b>Room Type : </b> <label id="Room_Type" name="Room_Type"></label>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <b>Area :</b> <label id="Area" name="Area"></label>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                    <div class="col-span-12 sm:col-span-6">
                        <b>Max residence : </b> <label id="Max_residence" name="Max_residence"></label>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <b>Default residence :</b> <label id="Default_residence" name="Default_residence"></label>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                    <div class="col-span-12 sm:col-span-6">
                        <b>Max adults : </b> <label id="Max_adults" name="Max_adults"></label>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <b>Max children :</b> <label id="Max_children" name="Max_children"></label>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                    <div class="col-span-12 sm:col-span-6">
                        <b>Max Additional Beds : </b> <label id="Max_Additional_Beds" name="Max_Additional_Beds"></label>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <b>Floor :</b> <label id="Floor" name="Floor"></label>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                    <div class="col-span-12 sm:col-span-12">
                        <b>Description : </b> <label id="Description" name="Description"></label>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                    <div class="col-span-12 sm:col-span-12">
                        <b>Additional Facilities : </b> <label id="Additional_Facilities"
                            name="Additional_Facilities"></label>
                    </div>
                </div>
                <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                    <button type="button" data-dismiss="modal" class="button w-20 bg-theme-6 text-white mt-3">Close</button>
                </div>
            </div>
        </div>
    </div>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                $(document).ready(function() {
                    Toastify({
                        text: "{{ $error }}",
                        duration: 5000,
                        newWindow: true,
                        close: true,
                        gravity: "bottom",
                        position: "left",
                        backgroundColor: "#0e2c88",
                        stopOnFocus: true
                    }).showToast();

                });
            </script>
        @endforeach
    @endif
    <script type="text/javascript">
        $(function() {

            loadtable();

        });

        function loadtable() {
            var table = $('#dttbl').DataTable({
                processing: true,
                serverSide: false,
                bDestroy: true,
                ajax: {
                    'url': "{{ route('room_view_ajax') }}"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'img',
                        name: 'img'
                    },
                    {
                        data: 'area',
                        name: 'area'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'floor',
                        name: 'floor'
                    },
                    {
                        data: 'sts',
                        name: 'sts',
                    },



                ]
            });

            table.clear()

        }

        function change_status(id) {

            var url = "{{ route('change_status') }}";

            var params = {
                'id': id,
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: params,
                success: function(res) {

                    loadtable();
                    notify(0, 'Room status has been changed !');

                },
                error: function(err) {

                    notify(1, 'Unable to changed status!');

                }
            });


        }


        function deleteRoom(id) {

            var url = "{{ route('deleteRoom') }}";

            var params = {
                'id': id,
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: params,
                success: function(res) {

                    location.reload();
                    notify(0, 'Delete successful');

                },
                error: function(err) {

                    notify(1, 'Unable to delete room! (This room already used in reservation) ');

                }
            });


        }

        function getroomdeatils(id) {


            $('#Room_Name').text('');
            $('#Room_Category').text('');
            $('#Room_Type').text('');
            $('#Area').text('');
            $('#Max_residence').text('');
            $('#Default_residence').text('');
            $('#Max_adults').text('');
            $('#Max_children').text('');
            $('#Max_Additional_Beds').text('');
            $('#Floor').text('');
            $('#Description').text('');
            $('#Additional_Facilities').text('');


            var url = "{{ route('getroomdeatils') }}";

            var params = {
                'id': id,
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: params,
                success: function(res) {


                    $('#Room_Name').text(res.room.room_name);
                    $('#Room_Category').text(res.Room_Categories.room_categories_name);
                    $('#Room_Type').text(res.Room_type.room_type_Select);
                    $('#Area').text(res.room.room_area + ' Sqft');
                    $('#Max_residence').text(res.room.room_max_recident + ' Packs');
                    $('#Default_residence').text(res.room.room_default_recident + ' Packs');
                    $('#Max_adults').text(res.room.room_max_adults + ' Packs');
                    $('#Max_children').text(res.room.room_max_children + ' Packs');
                    $('#Max_Additional_Beds').text(res.room.room_beds + ' Packs');
                    $('#Floor').text(res.room.room_floor + ' Floor');
                    $('#Description').text(res.room.room_descrption);


                    if (typeof(res.room_add_fac) != "undefined" && res.room_add_fac !== null) {
                        $.each(res.room_add_fac, function(index) {
                            const valuve = res.room_add_fac[index];
                            $("#Additional_Facilities").append('<label>' + valuve + " | </label>");
                        });
                    }


                },
                error: function(err) {

                    notify(1, 'Unable to load room details');

                }
            });


        }


        $(document).ready(function() {
            $("#Search_Input").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#data_table tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        $('#data_table').on('click', '.dltbtn', function(e) {
            var id = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('/delete_room_type') }}",
                type: 'POST',
                dataType: "JSON",
                data: {
                    "id": id
                },
                success: function(response) {



                },
                error: function(xhr) {
                    console.log(xhr
                        .responseText);
                }
            });
        });
    </script>
@endsection
