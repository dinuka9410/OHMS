@extends('../layout/form-home-layout')

@section('form-name', 'Room Category ')

@section('form-area')

    <style>
        .img-uploded-delete-msg {
            color: red;
            text-align: center;
        }

        .img-upload .rounded-full {
            display: none;
        }
    </style>
    <div class="mt-3">
        <label>Upload Image</label>

        {{-- back end eken hadala ewanna --}}
        <div class="border-2 border-dashed dark:border-dark-5 rounded-md mt-3 pt-4 img-upload">
            <div>
                @if (isset($room_catogory_withid))
                    <div class="flex flex-wrap px-4" id="uplord_img_saved" name="uplord_img_saved"></div>
                @endif

                <div class="flex flex-wrap px-4" id="uplord_img" name="uplord_img">

                </div>
                <span class="img-uploded-delete-msg" id="img_delete_msg"></span>
            </div>
        </div>
    </div>


    <div class="preview" id="btnCreate" name="btnCreate">
        <form action="{{ route('img_uplord') }}" class="dropzone border-gray-200 border-dashed">
            @csrf

            <div class="fallback">
                <input name="file" id="file" type="file" multiple />
            </div>
            <div class="dz-message" data-dz-message>
                <div class="text-lg font-medium">Drop files here or click to upload.</div>
                <div class="text-gray-600">

                </div>
            </div>
        </form>
    </div>


    <div class="p-5" id="form-validation">
        <form class="validate-form" id="frmdt" method="POST" action="{{ route('Room_Category_Add_Update') }}">
            @csrf
            <input hidden type="text" name="cat_id" id="cat_id" placeholder="Luxury Penthouse"
                value="{{ isset($room_catogory_withid) ? $room_catogory_withid->room_categories_id : '' }}">

            {{-- <div class="border-gray-200 border-dashed">
                    <div class="fallback"> <input name="file" type="file" multiple /> </div>
                    <div class="dz-message" data-dz-message>
                        <div class="text-lg font-medium">Drop files here or click to upload.</div>
                        <div class="text-gray-600"> This is just a demo dropzone. Selected files are <span class="font-medium">not</span> actually uploaded. </div>
                    </div>
                </div> --}}
            {{-- <label>Upload Image</label>
            <div class="border-2 border-dashed dark:border-dark-5 rounded-md mt-3 pt-4">
                <div class="flex flex-wrap px-4">
                    <div class="w-24 h-24 relative image-fit mb-5 mr-5 cursor-pointer zoom-in">

                    </div>
                    <div class="images-preview-div zoom-in cursor-pointer mb-5 mr-5 relative w-24 "> </div>
                </div>
                <div class="px-4 pb-4 flex items-center cursor-pointer relative">
                    <i data-feather="image" class="w-4 h-4 mr-2"></i> <span
                        class="text-theme-1 dark:text-theme-10 mr-1">Upload a file</span>

                    <input type="file" name="images[]"  id="images" placeholder="Choose images" multiple="true">
                </div>
            </div> --}}


            <div class="grid grid-cols-12 gap-5">
                <div class="col-span-12 ">

                    <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">

                        <div class="intro-y col-span-12 sm:col-span-6 input-form">
                            <div class="mb-2">Catagory Name</div>
                            <input type="text" name="cat_name" id="cat_name" class="input w-full border mt-2"
                                placeholder="Luxury Penthouse" minlength="2"
                                value="{{ isset($room_catogory_withid) ? $room_catogory_withid->room_categories_name : '' }}"
                                required>
                        </div>
                        <div class="intro-y col-span-12 sm:col-span-6 input-form">
                            <div class="mb-2">Area</div>
                            <input type="number" class="input w-full border mt-2"
                                value="{{ isset($room_catogory_withid) ? $room_catogory_withid->area : '' }}"
                                name="area" id="area" required placeholder="sqft">
                        </div>

                    </div>
                    <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">

                        <div class="intro-y col-span-12 sm:col-span-6 input-form">
                            <div class="mb-2">Max residence</div>
                            <input name="max_reci" id="max_reci" type="number" class="input w-full border flex-1"
                                placeholder="1" required
                                value="{{ isset($room_catogory_withid) ? $room_catogory_withid->max_recident : '1' }}">
                        </div>
                        <div class="intro-y col-span-12 sm:col-span-6 input-form">
                            <div class="mb-2">Default residence</div>
                            <input name="defa_rec" id="defa_rec" type="number" class="input w-full border flex-1"
                                placeholder="1" required
                                value="{{ isset($room_catogory_withid) ? $room_catogory_withid->default_recident : '1' }}">
                        </div>

                    </div>
                    <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">

                        <div class="intro-y col-span-12 sm:col-span-6 input-form">
                            <div class="mb-2">Max adults</div>
                            <input name="max_adults" id="max_adults" type="number" class="input w-full border flex-1"
                                placeholder="1" required
                                value="{{ isset($room_catogory_withid) ? $room_catogory_withid->max_adults : '1' }}">
                        </div>
                        <div class="intro-y col-span-12 sm:col-span-6 input-form">
                            <div class="mb-2">Max children</div>
                            <input name="max_child" id="max_child" type="number" class="input w-full border flex-1"
                                placeholder="1" required
                                value="{{ isset($room_catogory_withid) ? $room_catogory_withid->max_children : '1' }}">
                        </div>

                    </div>

                </div>
            </div>

            <button type="button" class="button w-20 bg-theme-9 text-white mt-3" style="margin-top: 10%;"
                id="savebtn">Save</button>

                <input type="hidden" id='form_status' name="form_status"
                value="{{ isset($details) ? $details->status : '1' }}">
        </form>
    </div>

@endsection


@section('table-area')
    <div class="p-5" id="basic-table">
        <div class="preview">
            <div class="overflow-x-auto">

                <table class="table table-report -mt-2" id="dttbl">
                    <thead>
                        <tr>
                            <th class="whitespace-no-wrap">#</th>
                            <th class="whitespace-no-wrap">Name</th>
                            <th class="text-center whitespace-no-wrap">Residence</th>
                            <th class="text-center whitespace-no-wrap"> Action</th>
                        </tr>
                    </thead>
                    <tbody id="data_table">
                        @if (count($room_catogory) > 0)
                            @php
                                $index_count = 0;
                            @endphp
                            @foreach ($room_catogory as $room_catogory)
                                @php
                                    $index_count = $index_count + 1;
                                @endphp
                                <tr class="intro-x">
                                    <td>
                                        <div class="font-medium whitespace-no-wrap ">
                                            {{ $room_catogory->room_categories_id }}</div>
                                    </td>
                                    <td>
                                        <div class="font-medium whitespace-no-wrap ">
                                            {{ $room_catogory->room_categories_name }}</div>
                                    </td>
                                    <td>
                                        <div class="font-medium whitespace-no-wrap text-center ">
                                            {{ $room_catogory->max_recident }}</div>
                                    </td>
                                    <td>
                                        <div class="flex justify-center items-center">
                                        @if ($room_catogory->status == '1')
                                            <div>
                                                <div style="margin: auto;" class="onoffswitch">
                                                    <input
                                                        onclick="change_status_room_category({{ $room_catogory->room_categories_id }});"
                                                        type="checkbox" name="onoffswitch" class="onoffswitch-checkbox"
                                                        id="roomtypenumber{{ $index_count }}" tabindex="0" checked>
                                                    <label class="onoffswitch-label"
                                                        for="roomtypenumber{{ $index_count }}">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <div style="margin: auto;" class="onoffswitch">
                                                    <input
                                                        onclick="change_status_room_category({{ $room_catogory->room_categories_id }});"
                                                        type="checkbox" name="onoffswitch" class="onoffswitch-checkbox"
                                                        id="roomtypenumber{{ $index_count }}" tabindex="0">
                                                    <label class="onoffswitch-label"
                                                        for="roomtypenumber{{ $index_count }}">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($room_catogory->room_categories_id != 0)
                                        <div style="margin-left: 10%;" class="flex justify-center items-center mt-2">
                                            <a onclick="preloader()" style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" href="{{route('room_category_view_add_update',['id'=>$room_catogory->room_categories_id])}}"><i class="fas fa-edit"></i></a>
                                            <a  style="margin-top: 0%" class="flex items-center mr-3 text-theme-1" href="{{route('room_category_delete',['id'=>$room_catogory->room_categories_id])}}" ><i class="fas fa-trash"></i></a>
                                        </div>

                                            {{-- <form style="margin-left: 10%;" action="{{ route('room_category_view_add_update') }}"
                                                method="get">
                                                <input hidden value="{{ $room_catogory->room_categories_id }}"
                                                    name="id">

                                                <button onclick="preloader()"
                                                    class="flex items-center mr-3 text-theme-1" type="submit"><i
                                                        data-feather="check-square"
                                                        class="w-4 h-4 mr-1"></i>Edit</button>

                                            </form>
                                            <form action="{{ route('room_category_delete') }}" method="get">
                                                <input hidden value="{{ $room_catogory->room_categories_id }}"
                                                    name="id">
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
               // $('#status_box').show('fast');
            });
        </script>
    @endif
    @if (!isset($room_catogory_withid))
        <script>
            $(function() {

                $('#clear').attr('hidden', false);


            });
        </script>
    @endif



    <script>
        function change_status_room_category(id) {

            var url = "{{ route('change_status_room_category') }}";

            var params = {
                'id': id,
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: params,
                success: function(res) {


                    notify(0, 'Room category status has been changed !');

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
                    cat_name: {
                        required: true,
                        minlength: 2,
                    },
                    area: {
                        required: true,
                        minlength: 1,
                    },
                    max_reci: {
                        required: true,
                        minlength: 1,
                    },
                    defa_rec: {
                        required: true,
                        minlength: 1,
                    },
                    max_adults: {
                        required: true,
                        minlength: 1,
                    },
                    max_child: {
                        required: true,
                        minlength: 1,
                    }
                },

                // relavant messages

                messages: {

                    room_type_select: "Category Name Required",
                    room_type_area: "Area Required",
                    room_type_max_reci: "Max Residence Required",
                    room_type_defa_rec: "Default  Residence Required",
                    room_type_max_adults: "Max adults Required",
                    room_type_max_child: "Max Child Required"
                }

            });



            if ($('#frmdt').valid()) {
                preloader();
                var cat_name = $('#cat_name').val();
                var cat_id = $('#cat_id').val();
                var isunique = false;

                if (cat_id == '') {

                    var isunique = checkIfUniqueField('room_categories', 'room_categories_name', cat_name);

                } else {

                    isunique = true;

                }

                if (isunique) {

                    $('#frmdt').submit();

                } else {
                    removeLoader();
                    notify(1, 'the entered agent code is not unique');
                }

            }


        });

        $(document).ready(function() {
            $('#btnCreate').click(function() {
                $('div.dz-success').remove();

            });

        });

        function calling_fun() {
            $.ajax({
                url: "{{ route('fetch_img') }}",
                type: 'GET',
                success: function(data) {
                    console.log(data);
                    $('#uplord_img').html(data);

                },
                error: function(xhr) {
                    console.log(xhr
                        .responseText);
                }

            });
            document.getElementById("img_delete_msg").innerHTML = "Click on image to delete";
        }

        $(document).ready(function() {
            $('#btnCreate').click(function() {
                calling_fun();


            });
            edit_calling_fun();
        });

        $(document).on('click', '.remove_img', function() {
            var name = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('remove_fetch_img') }}",
                type: 'POST',
                data: {
                    name: name
                },
                success: function(data) {
                    calling_fun();

                },
                error: function(xhr) {
                    console.log(xhr
                        .responseText);
                }

            });


        })

        $(document).on('click', '.remove_img_saved', function() {
            var name = $(this).attr('id');
            var folder_id = $('#cat_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('edit_remove_fetch_img') }}",
                type: 'POST',
                data: {
                    name: name,
                    folder_id: folder_id
                },
                success: function(data) {
                    edit_calling_fun();

                },
                error: function(xhr) {
                    console.log(xhr
                        .responseText);
                }

            });


        })

        function edit_calling_fun() {
            var folder_id = $('#cat_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('edit_fetch_img') }}",
                type: 'POST',
                data: {
                    data: folder_id,
                },
                success: function(data) {
                    console.log(data);
                    $('#uplord_img_saved').html(data);

                },
                error: function(xhr) {
                    console.log(xhr
                        .responseText);
                }

            });

        }
    </script>

@endsection
