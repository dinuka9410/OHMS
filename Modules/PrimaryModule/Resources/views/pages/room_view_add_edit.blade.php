@extends('../layout/form-layout')

@section('form-name', 'Rooms')

@section('form-area')


    <div class="col-span-12 lg:col-span-8 xxl:col-span-9">
        <!-- BEGIN: Display Information -->
        <div class="p-5" id="form-validation">
            <form class="validate-form" id="frmdt" method="POST" action="{{ route('room_add_update') }} "
                enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-12 gap-5">
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
                                    src=" @if (isset($room)) {{ asset('storage/img/rooms/' . $img_value . '.jpg') }} @else {{ asset('dist/images/') }} @endif">

                            </div>

                        </div>
                    </div>
                    <div class="col-span-12 xl:col-span-8">

                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-4 input-form">
                                <div class="mb-2"><b>Room Name : </b>{{ isset($room) ? $room->room_name : '' }}
                                </div>
                            </div>

                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Room Category :
                                    </b>{{ isset($room) ? $room->RoomCatgoryWithConcat->room_categories_name : '' }}</div>
                            </div>

                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Room Type :
                                    </b>{{ isset($room) ? $room->RoomTypeWithConcat->room_type_Select : '' }}</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Area : </b>{{ isset($room) ? $room->room_area : '' }} Sqrt
                                </div>
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Max residence :
                                    </b>{{ isset($room) ? $room->room_max_recident : '' }} packs</div>
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Default residence :
                                    </b>{{ isset($room) ? $room->room_default_recident : '' }} packs</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Max adults :
                                    </b>{{ isset($room) ? $room->room_max_adults : '' }} packs</div>
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Max children :
                                    </b>{{ isset($room) ? $room->room_max_children : '' }} packs</div>
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Max Additional Beds :
                                    </b>{{ isset($room) ? $room->room_beds : '' }}</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-4">
                                <div class="mb-2"><b>Floor : </b>{{ isset($room) ? $room->room_floor : '' }}
                                </div>
                            </div>

                        </div>
                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-12">
                                <div class="mb-2"><b>Description :
                                    </b>{{ isset($room) ? $room->room_descrption : '' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                            <div class="intro-y col-span-12 sm:col-span-12">
                                <div class="mb-2"><b>Additional Facilities : </b>
                                    @foreach ($Additional_facilities as $Additional_facilities)
                                        /<label class="cursor-pointer select-none" style="margin-top: 2%"
                                            for="horizontal-checkbox-chris-evans">{{ isset($Additional_facilities) ? $Additional_facilities->add_additional_facilites_name : '' }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </div>


                    </div>

            </form>


        </div>
    </div>
    </div>



@endsection

@section('script-area')

    @if (!isset($room))
        <script>
            $(function() {

                $('#clear').attr('hidden', false);

            });
        </script>
    @endif


    <script>
        $(document).ready(function() {

            $('#cancel').attr('hidden', false);

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
            $("#btn2").click(function() {
                $("ol").append(
                    '<li><br><div class="intro-y col-span- sm:col-span-4 input-form"><input type="text" id="facilities" name="facilities[]" class="input border flex-1"placeholder="Additional Facilities"></div></li>'
                );
            });

            $("#btn3").click(function() {
                $("li:last").remove();
            });
        });
    </script>

@endsection
