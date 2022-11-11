@extends('../layout/form-home-layout')

@section('form-name','Seasons')

@section('form-area')


<form autocomplete="off" class="validate-form" id="frmdt" method="POST" action="{{route('season_add_edit')}}">
    @csrf
    <input type="text" hidden name="season_id" id="season_id_holder" value="{{ isset($details) ? $details->id : ''}}">

    <div class="grid grid-cols-12 gap-5 mt-5 mb-5" style="overflow: hidden; padding:15px;">

    <div class="col-span-6">
        <label class="flex flex-col sm:flex-row">Season Code </label><br>
        <input type="text" name="s_code" id="s_code" class="input w-full border mt-1" style="-webkit-fill-available" placeholder="Season code" value="{{isset($details) ? $details->seasonCode : old('s_code') }}">
    </div>

    <div class="col-span-6">
        <label class="flex flex-col sm:flex-row">Season Name</label><br>
        <input type="text" name="s_name" id="s_name" class="input w-full border mt-1" style="-webkit-fill-available" placeholder="Summer season" value="{{isset($details) ? $details->seasonName : old('s_name') }}">
    </div>

        <div class="col-span-12">
            <div class="grid grid-cols-12 gap-4 row-gap-5 mt-3">
                <div class="intro-y col-span-12 sm:col-span-6 input-form">
                    <div class="mb-2">Start Date</div>
                    <input name="s_start_date" id="s_start_date" type="date" required class="input w-full border flex-1" placeholder="0" value="{{isset($details) ? $details->start_date : old('s_start_date') }}">
                </div>
                <div class="intro-y col-span-12 sm:col-span-6 input-form">
                    <div class="mb-2">End Date</div>
                    <input name="s_end_date" id="s_end_date" type="date" required class="input w-full border flex-1" placeholder="0" value="{{isset($details) ? $details->end_date : old('s_end_date') }}">
                </div>

            </div>

        </div>

    </div>

    <div class="col-span-12 text-right mt-3" style="overflow: hidden; padding:15px;">
        <button type="button" class="button w-20 bg-theme-9 text-white "  id="savebtn">Save</button>
        <button onclick="window.location.reload()" type="button" class="button w-20 bg-theme-1 text-white mt-3 btn-clear">Clear</button>
    </div>

</form>


@endsection


@section('table-area')

<div class="p-1 mt-5" id="basic-table" style="overflow: hidden; padding:15px;">
    <div class="preview">
        <div class="overflow-x:auto">

            <table class="table table-report mt-2" id="dttbl">
                <thead>
                    <tr>
                        <th class="text-center whitespace-no-wrap">Season Code</th>
                        <th class=" text-center whitespace-no-wrap">Start Date</th>
                        <th class="text-center whitespace-no-wrap"> End Date</th>
                        <th class=" text-center whitespace-no-wrap">Status</th>
                        <th class=" text-center whitespace-no-wrap"> Action</th>
                    </tr>
                </thead>
                <tbody id="data_table"></tbody id="body">
            </table>

        </div>
    </div>
</div>

@endsection

@section('script-area')

<script>
    var table;

    $(document).ready(function() {
        $('#save').attr('hidden', true);
        $('#cancel').attr('hidden', false);
        loadTable();
    });


    function loadTable() {
        $('#dttbl').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                'url': "{{route('get_all_seasons')}}",
            },
            columns: [
                {
                    data: 'seasonCode',
                    name: 'seasonCode'
                },
                {
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                {
                    render: function(data, type, row, meta) {
                        return '<div class="flex justify-center items-center mt-2"><div class="onoffswitch"><input onclick="changeStatus(' + row.id + ',' + row.status + ')"  type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="room' + row.id + '" tabindex="0" ' + (row.status == 1 ? 'checked' : '') + ' ><label class="onoffswitch-label" for="room' + row.id + '"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div></div>';
                    }
                },
                {
                    render: function(data, type, row, meta) {
                        return '<div class="flex items-center" ><button type="button" style="margin-top: 0%;"  class="text-theme-1" onclick="get_season_by_id('+row.id+')" ><i class="fas fa-edit"></i></button> <button type="button" onclick="delete_season('+row.id+')" ><i class="fas fa-trash"></i></button></div>';
                    }
                }

            ]
        });
    }

    $('#savebtn').click(function() {

        $('#frmdt').validate({
            onfocusout: false,
            onkeyup: false,
            onclick: false,
            rules: {
                s_code: {
                    required: true,
                    minlength: 3,
                    chk_season_code: true,
                },
                s_name: {
                    required: true,
                    chk_season_name: true,
                }
            },



        });


        jQuery.validator.addMethod("chk_season_code", function(value, element) {

            var season_id = $('#season_id_holder').val();
            var s_code = $('#s_code').val();

            if (s_code != '') {

                function valdt() {
                    var temp = 0;
                    $.ajax({
                        type: "POST",
                        url: "{{ route('validate_season_code') }}",
                        async: false,
                        data: {
                            "s_code": s_code,
                            'season_id': season_id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            $("body").css("cursor", "wait");
                            $('#s_code').addClass('data_loading');
                        },
                        success: function(msg) {
                            temp = msg;
                            $("body").css("cursor", "default");
                            $('#s_code').removeClass('data_loading');
                        },
                        error: function() {
                            $("body").css("cursor", "default");
                            $('#s_code').removeClass('data_loading');
                            console.log("Error");
                        }
                    });

                    return temp;

                }

                var vlrs = valdt();

                if (vlrs) {
                    return false;
                } else {
                    return true;
                }

            }

        }, "season code already exists");



        jQuery.validator.addMethod("chk_season_name", function(value, element) {

            var season_id = $('#season_id_holder').val();
            var s_name = $('#s_name').val();

            if (s_name != '') {

                function valdt() {
                    var temp = 0;
                    $.ajax({
                        type: "POST",
                        url: "{{ route('validate_season_name') }}",
                        async: false,
                        data: {
                            "s_name": s_name,
                            'season_id': season_id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            $("body").css("cursor", "wait");
                            $('#s_name').addClass('data_loading');
                        },
                        success: function(msg) {
                            temp = msg;
                            $("body").css("cursor", "default");
                            $('#s_name').removeClass('data_loading');
                        },
                        error: function() {
                            $("body").css("cursor", "default");
                            $('#s_name').removeClass('data_loading');
                            console.log("Error");
                        }
                    });

                    return temp;

                }

                var vlrs = valdt();

                if (vlrs) {
                    return false;
                } else {
                    return true;
                }

            }

        }, "season name already exists");



        if ($('#frmdt').valid()) {

            preloader();

            var startdate = $('#s_start_date').val();
            var enddate = $('#s_end_date').val();
            var s_code = $('#s_code').val();
            var s_id = $('#season_id_holder').val();

            if (startdate < enddate) {
                $('#frmdt').submit();
            } else {
                removeLoader();
                notify(1, 'Please check the start date and end date of the season');
            }
        }
    });


    // this function is used when edit season;
    function get_season_by_id(id) {

        preloader();

        var url = "{{route('get_season_by_id')}}";
        var params = {
            'id': id,
            "_token": "{{ csrf_token() }}",
        }

        $.ajax({
            url: url,
            type: "POST",
            data: params,
            success: function(data) {

                removeLoader();

                var res = data;

                if (res.status == 0) {

                    $('#savebtn').text('UPDATE');

                    $('#s_code').val('');
                    $('#season_id_holder').val('');
                    $('#s_name').val('');
                    $('#s_start_date').val('');
                    $('#s_end_date').val('');

                    $('#s_code').val(res.data.seasonCode);
                    $('#season_id_holder').val(res.data.id);
                    $('#s_name').val(res.data.seasonName);
                    $('#s_start_date').val(res.data.start_date);
                    $('#s_end_date').val(res.data.end_date);

                    if (!res.canEditDate) {

                        $('#s_start_date').attr('readonly', true);
                        $('#s_end_date').attr('readonly', true);

                    } else {

                        $('#s_start_date').attr('readonly', false);
                        $('#s_end_date').attr('readonly', false);

                    }

                } else {

                    notify(1, res.msg);

                }


            },
            error: function(err) {
                removeLoader();
                console.log(err);
            }
        });

    }


    function delete_season(id) {

        var season_id = id;

        var url = "{{route('delete_season')}}";

        var param = {
            "season_id": season_id
        }

        confirmnotify("do you want to delete this season?").then(res => {

            if (res.isConfirmed) {

                preloader();

                $.ajax({

                    url: url,
                    type: "POST",
                    data: param,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

                        var res = data;

                        if (res.error_status == 0) {
                            removeLoader();
                            notify(0, res.msg);
                            $('#dttbl').DataTable().ajax.reload();
                        } else {
                            removeLoader();
                            notify(1, res.msg);
                        }
                    },
                    error: function(err) {
                        removeLoader();
                        notify(1, err.msg);

                    }

                });

            }

        });


    }


    function changeStatus(id, status) {

        var url = "{{route('change_season_status')}}";

        if (status == 1) {

            new_status = 0;

        } else {

            new_status = 1;

        }

        var param = {
            id: id,
            status: new_status,
        };

        $.ajax({
            url: url,
            data: param,
            success: function(res) {
                var result = res;
                if (result.error_status == 0) {

                    notify(0, result.msg);
                    $('#dttbl').DataTable().ajax.reload();
                } else {

                    notify(1, result.msg);

                }
            },
            error: function(err) {
                notify(1, 'unable to change status');
            }
        });

    }
</script>

@endsection
