@extends('../layout/form-layout')

@section('form-name', 'Rooms')

@section('form-area')


    <div class="col-span-12 lg:col-span-8 xxl:col-span-9">
        <!-- BEGIN: Display Information -->
        <div class="p-5" id="form-validation">
            <form class="validate-form" id="frmdt" method="POST" action="{{ route('room_add_update') }} "
                enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-12 gap-5 room-img-up">
                    <div class="col-span-12 xl:col-span-4">
                        <div class="border border-gray-200 dark:border-dark-5 rounded-md p-5">
                            <div class="w-40 h-40 relative image-fit cursor-pointer zoom-in mx-auto">
                                @php
                                    if (isset($room)) {
                                        $img_value = $room->room_id;
                                    }
                                @endphp
                                <img id="img_review" name="img_review" class="rounded-md"
                                    alt=""
                                    src=" @if (isset($room)) {{ $img_path }} @else {{asset('dist/images/room_defult.jpg')}} @endif">

                            </div>
                            <div class="w-40 mx-auto cursor-pointer relative mt-5 room-img-up-btn">
                                <button type="button" class="button w-full bg-theme-1 text-white">Upload
                                    Image</button>
                                <input onchange="loadPreview(this);" type="file" id="room_img" name="room_img"
                                    class="w-full h-full top-0 left-0 absolute opacity-0">
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 xl:col-span-8">
                        <div class="input-form">
                            <input hidden type="text" name="room_type_id" id="room_type_id" class="input w-full border mt-2"
                                value="{{ isset($room) ? $room->room_type_id : '' }}">
                            <input hidden type="text" name="room_id" value="{{ isset($room) ? $room->room_id : '' }}"
                                id="room_id" class="input w-full border mt-2">
                        </div>
                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-6 input-form">
                                <div class="mb-2">Room Name (Number)</div>
                                <input type="text" id="room_name" name="room_name" class="input w-full border flex-1"
                                    placeholder="201" value="{{ isset($room) ? $room->room_name : '' }}">
                                <span style="color:red;" id="validatetxt" name="validatetxt"></span>
                            </div>

                            <div class="intro-y col-span-12 sm:col-span-6">
                                <div class="mb-2">Room Category</div>
                                <select class="input w-full border flex-1" id="room_cat" name="room_cat">
                                    @foreach ($room_categories as $room_categories)
                                        <option
                                            value="{{ isset($room_categories) ? $room_categories->room_categories_id : '' }}"
                                            @if(isset($rooms)) @if($room_categories->room_categories_id == $room->RoomCatgoryWithConcat->room_categories_id) selected="selected" @endif @endif
                                            data-Area="{{ $room_categories->area }}"
                                            data-Max-residence="{{ $room_categories->max_recident }}"
                                            data-Default-residence="{{ $room_categories->default_recident }}"
                                            data-Max-adults="{{ $room_categories->max_adults }}"
                                            data-Max-children="{{ $room_categories->max_children }}">
                                            {{ isset($room_categories) ? $room_categories->room_categories_name : '' }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                        </div>
                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-6">
                                <div class="mb-2">Room Type</div>
                                <select class="input w-full border flex-1" name="rt_id" id="rt_id">
                                    @if (count($room_type) > 0)
                                        @foreach ($room_type as $room_type)
                                            <option value="{{ $room_type->room_type_id }}"
                                                @if(isset($rooms))@if ($room_type->room_type_id == $room->RoomTypeWithConcat->room_type_id) selected="selected" @endif @endif>
                                                {{ $room_type->room_type_Select }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-6 input-form">
                                <div class="mb-2">Area</div>
                                <input type="number" id="room_area" name="room_area" class="input w-full border flex-1"
                                    placeholder="sqft" required value="{{ isset($room) ? $room->room_area : '' }}">
                            </div>
                        </div>


                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-6 input-form">
                                <div class="mb-2">Max residence</div>
                                <input type="number" id="room_max_rec" name="room_max_rec"
                                    class="input w-full border flex-1" placeholder="0"
                                    value="{{ isset($room) ? $room->room_max_recident : '' }}">
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-6 input-form">
                                <div class="mb-2">Default residence</div>
                                <input type="number" id="room_def_red" name="room_def_red"
                                    class="input w-full border flex-1" placeholder="0"
                                    value="{{ isset($room) ? $room->room_default_recident : '' }}">
                            </div>

                        </div>
                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5 input-form">
                            <div class="intro-y col-span-12 sm:col-span-6">
                                <div class="mb-2">Max adults</div>
                                <input type="number" id="room_max_adult" name="room_max_adult"
                                    class="input w-full border flex-1" placeholder="0"
                                    value="{{ isset($room) ? $room->room_max_adults : '' }}">
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-6 input-form">
                                <div class="mb-2"> Max children</div>
                                <input type="number" id="room_max_child" name="room_max_child"
                                    class="input w-full border flex-1" placeholder="0"
                                    value="{{ isset($room) ? $room->room_max_children : '' }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5 input-form">
                            <div class="intro-y col-span-12 sm:col-span-6">
                                <div class="mb-2">Max Additional Beds</div>
                                <input type="number" id="room_max_addi_beds" name="room_max_addi_beds"
                                    class="input w-full border flex-1" placeholder="0"
                                    value="{{ isset($room) ? $room->room_beds : '' }}">
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-6 input-form">
                                <div class="mb-2">Floor</div>
                                <input type="number" id="room_floor" name="room_floor" class="input w-full border flex-1"
                                    placeholder="0" value="{{ isset($room) ? $room->room_floor : '' }}">
                            </div>
                        </div>


                        <div class="mt-3">
                            <label>Description</label>
                            <textarea class="input w-full border mt-2 bg-gray-100" id="room_descr" name="room_descr"
                                placeholder="">{{ isset($room) ? $room->room_descrption : '' }}</textarea>
                        </div>

                        <br><br>
                        <div class="intro-y col-span-12 sm:col-span-6 input-form">
                            <label>Additional Facilities</label> &nbsp; &nbsp;
                            <a type="button" id="btn2" class="mt-2" style=""
                                class=" mr-1 mb-2 inline-block bg-theme-1 text-white">
                                <span class="w-5 h-5 flex items-center justify-center">
                                    <i data-feather="plus" class="w-4 h-4"></i>
                                </span></a>
                            <a type="button" id="btn3" class="mt-2" style=""
                                class=" mr-1 mb-2 inline-block bg-theme-6 text-white">
                                <span class="w-5 h-5 flex items-center justify-center">
                                    <i data-feather="minus" class="w-4 h-4"></i>
                                </span></a>
                            <br>
                        </div>

                        <div class="intro-y col-span-4 sm:col-span-4 input-form">
                            <ol>

                            </ol>
                        </div>
                        <div class=" grid grid-cols-12 gap-4 row-gap-5 mt-5 room-facil-slct">

                            @foreach ($Additional_facilities as $Additional_facilities)
                                <div
                                    class=" flex items-center text-gray-700 dark:text-gray-500 mr-2 col-span-12 sm:col-span-6">

                                    <input style="margin-top: 2%" type="checkbox" class="input input--switch border" --}}
                                        id="room_pvt_ent" name="checked_box[]"
                                        value="{{ $Additional_facilities->add_additional_facilites_id }}"
                                        @if (isset($room_add_fac) && in_array($Additional_facilities->add_additional_facilites_id, $room_add_fac) > 0) checked @endif>&nbsp;&nbsp;

                                    <label class="cursor-pointer select-none" style="margin-top: 2%"
                                        for="horizontal-checkbox-chris-evans">{{ isset($Additional_facilities) ? $Additional_facilities->add_additional_facilites_name : '' }}</label>
                                </div>
                            @endforeach

                        </div>

                    </div>
                    
                    <input type="hidden" id='form_status' name="form_status"
                        value="{{ isset($details) ? $details->Status : '1' }}">
            </form>


        </div>
    </div>
    </div>




@endsection

@if (isset($status_info))


    @section('status', $status_info['status'])

    @section('status_button')
        <input class="input input--switch border" type="checkbox" id='status_btn' onchange="changeStatus(this)"
            @if ($details->Status == '1') checked @endif>
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



@section('script-area')

    @if (isset($room))
        <script>
            $(function() {

                $('#update').attr('hidden', false);
                $('#cancel').attr('hidden', false);
                $('#save').attr('hidden', true);
                $('#clear').attr('hidden', true);
                $('#status_box').show('fast');
            });
        </script>
    @else
        <script>
            $('#update').attr('hidden', true);
            $('#save').attr('hidden', false);
            $('#clear').attr('hidden', false);
            $('#cancel').attr('hidden', false);
        </script>
    @endif


    <script>
        $(document).ready(function() {

            //jquery script
            $("#username").change(function() {
                var username = $(this).val();
                //now sending this username to ajax page for geting img saved against this username.
                $.ajax({
                    url: "ajaxpage.php",
                    data: {
                        data: username
                    }
                }).done(function(result) {
                    //now assign result to its related place
                    $(".img").html(result);
                })
            });

        });


        $(document).ready(function() {
            $('#savfm').click(function(e) {
                var frmdt = $('#frmdt').valid();
                if (frmdt == true) {
                    $("body").css("cursor", "wait");
                    $(".loader").fadeIn('slow');
                    document.forms["frmdt"].submit();
                } else {
                    console.error('Validation Error');
                }
            });

            $('#addtional_facility').click(function(e) {
                var frmdt = $('#frmadd').valid();
                if (frmdt == true) {
                    $("body").css("cursor", "wait");
                    $(".loader").fadeIn('slow');
                    document.forms["frmadd"].submit();
                } else {
                    console.error('Validation Error');
                }
            });
        });

        $('#room_cat').change(function() {
            var link = $("#room_cat option:selected").attr('data-Area');
            var link1 = $("#room_cat option:selected").attr('data-Max-residence');
            var link2 = $("#room_cat option:selected").attr('data-Default-residence');
            var link3 = $("#room_cat option:selected").attr('data-Max-adults');
            var link4 = $("#room_cat option:selected").attr('data-Max-children');



            $('#room_area').val(link);
            $('#room_max_rec').val(link1);
            $('#room_def_red').val(link2);
            $('#room_max_adult').val(link3);
            $('#room_max_child').val(link4);

        });

        function loadPreview(input, id) {
            id = id || '#img_review';
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $(id)
                        .attr('src', e.target.result)
                        .width(200)
                        .height(150);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }


        $("#room_name").blur(function() {
            var room_name = $('#room_name').val();
            var room_id = $('#room_id').val();
            $("#validatetxt").text("");
            var isunique = checkIfUniqueField('rooms', 'room_name', room_name);
            if (isunique == false) {
                $("#validatetxt").append("room name already exsits");
            }


        });

        $('#save').click(function() {


            $('#frmdt').validate({

                rules: {

                    room_name: {
                        required: true,
                        minlength: 1,
                    },
                    room_area: {
                        required: true,
                        minlength: 1,
                    },
                    room_max_rec: {
                        required: true,
                        minlength: 1,
                    },
                    room_def_red: {
                        required: true,
                        minlength: 1,
                    },
                    room_max_adult: {
                        required: true,
                        minlength: 1,
                    },
                    room_max_child: {
                        required: true,
                        minlength: 1,
                    },
                    room_max_addi_beds: {
                        required: true,
                        minlength: 1,
                    },
                    room_floor: {
                        required: true,
                        minlength: 1,
                    },

                },

                // relavant messages

                messages: {

                    room_type_area: "please enter a room name or id",
                    room_type_select: "please enter a room type",
                    room_type_area: "please enter a room type area",
                    room_type_max_reci: "please enter a max residence",
                    room_type_defa_rec: "please enter a default residence",
                    room_type_max_adults: "please enter a max adults",
                    room_type_max_child: "please enter a max children's",
                    room_max_addi_beds: "please enter a additional bed",
                    room_floor: "please enter a floor number",
                }

            });



            if ($('#frmdt').valid()) {
                preloader();
                var room_id = $('#room_id').val();
                var room_name = $('#room_name').val();
                var isunique = false;

                if (room_type_id == '') {

                    var isunique = checkIfUniqueField('rooms', 'room_name', room_name);

                } else {
                    isunique = true;

                }

                if (isunique) {

                    $('#frmdt').submit();

                } else {
                    removeLoader();
                    notify(1, 'the room categories name is not unique');
                }
            }
            //removeLoader();
        });

        $('#clear').click(function() {

            $('#room_name').val("");
            $('#room_area').val("");
            $('#room_max_rec').val("");
            $('#room_def_red').val("");
            $('#room_max_adult').val("");
            $('#room_max_child').val("");
            $('#room_max_addi_beds').val("");
            $('#room_floor').val("");

        });

        $('#update').click(function() {


            $('#frmdt').validate({

                rules: {

                    room_name: {
                        required: true,
                        minlength: 1,
                    },
                    room_area: {
                        required: true,
                        minlength: 1,
                    },
                    room_max_rec: {
                        required: true,
                        minlength: 1,
                    },
                    room_def_red: {
                        required: true,
                        minlength: 1,
                    },
                    room_max_adult: {
                        required: true,
                        minlength: 1,
                    },
                    room_max_child: {
                        required: true,
                        minlength: 1,
                    },
                    room_max_addi_beds: {
                        required: true,
                        minlength: 1,
                    },
                    room_floor: {
                        required: true,
                        minlength: 1,
                    },

                },

                // relavant messages

                messages: {

                    room_type_area: "please enter a room name or id",
                    room_type_select: "please enter a room type",
                    room_type_area: "please enter a room type area",
                    room_type_max_reci: "please enter a max residence",
                    room_type_defa_rec: "please enter a default residence",
                    room_type_max_adults: "please enter a max adults",
                    room_type_max_child: "please enter a max children's",
                    room_max_addi_beds: "please enter a additional bed",
                    room_floor: "please enter a floor number",
                }

            });



            if ($('#frmdt').valid()) {
                preloader();
                var room_id = $('#room_id').val();
                var room_name = $('#room_name').val();
                var isunique = false;

                if (room_type_id == '') {

                    var isunique = checkIfUniqueField('rooms', 'room_name', room_name);

                } else {
                    isunique = true;

                }

                if (isunique) {

                    $('#frmdt').submit();

                } else {
                    removeLoader();
                    notify(1, 'the room categories name is not unique');
                }
            }
            //removeLoader();
        });

        $(document).ready(function() {
            $("#btn2").click(function() {
                $("ol").append(
                    '<li><br><div class="intro-y col-span- sm:col-span-4 input-form"><input type="text" id="facilities" name="facilities[]" class="input border flex-1"placeholder="Additional Facilities " required></div></li>'
                );
            });

            $("#btn3").click(function() {
                $("li:last").remove();
            });
        });
    </script>

@endsection
