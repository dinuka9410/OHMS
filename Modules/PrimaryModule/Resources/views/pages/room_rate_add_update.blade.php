@extends('../layout/form-layout')

@section('form-name', 'Agent Room Rates')

@section('form-area')

    @php
        
    @endphp

    <form class="validate-form" id="frmdt" method="POST"
        onsubmit="agent_id.disabled = false;  season_id.disabled = false; return true;""
        action="
        {{ route('agent_rates_add_edit') }}">
        @csrf

        <div class="grid grid-cols-12 gap-5 agent-room-rates">
            <div class="col-span-12 ">
                <input hidden class="border" name="symble" id="symble" style="width: 100%; text-align: right"
                    value="{{ $symble }}">
                <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">
                    <div class="intro-y col-span-12 sm:col-span-4 input-form">
                        <label class="flex flex-col sm:flex-row">Agent Code is</label><br>
                        <select name="agent_id" class="input w-full border flex-1 js-example-basic-single" id="agent_id"
                            @if ($details) disabled @endif>
                            @foreach ($agents as $row)
                                <option value="{{ $row->id }}"
                                    <?= isset($details) && $row->id == $details->agent_id ? ' selected="selected"' : '' ?>>
                                    {{ $row->agentName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-4">
                        <label class="flex flex-col sm:flex-row">Season Code</label><br>
                        <select onchange="get_all_room_rate()" name="season_id"
                            class="input w-full border flex-1 js-example-basic-single" id="season_id"
                            @if ($details) disabled @endif>
                            <option value="" < selected="selected">Select</option>
                            @foreach ($seasons as $row)
                                <option value="{{ $row->id }}"
                                    <?= isset($details) && $row->id == $details->season_id ? ' selected="selected"' : '' ?>>
                                    {{ $row->seasonName }}</option>
                            @endforeach
                        </select>
                    </div>






                </div>



                @if (isset($all_rates_get_room_type))
                    @php
                        $index = 0;
                    @endphp
                    @foreach ($all_rates_get_room_type as $all_rates_get_room_type)
                        <br>


                        <div class="col-span-12 lg:col-span-6">
                            <div class="accordion user-permissions-content">
                                <div id="modulediv"
                                    class="user-permissions accordion__pane  border-b border-gray-200 dark:border-dark-5">
                                    <div class="intro-y grid grid-cols-12 gap-6 border-b header-row">
                                        <div class="col-span-12 lg:col-span-9 title-side">
                                            <input hidden class="border" name="roomtypeid[]" id="roomtypeid"
                                                style="width: 100%; text-align: right"
                                                value="{{ $all_rates_get_room_type->get_room_type->room_type_id }}">
                                            <label style="font-size: 20px;"
                                                class="flex flex-col sm:flex-row">{{ $all_rates_get_room_type->get_room_type->room_type_Select }}</label>
                                        </div>
                                        <div class="col-span-12 lg:col-span-3 controler-side">
                                            <a id="togglerbtn' + e.id + '" href="javascript:;"
                                                class="user-permissions-list accordion__pane__toggle view-more-icon font-medium block">
                                                <i style="" class="fas fa-chevron-circle-down"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="accordion__pane__content mt-3 text-gray-700 dark:text-gray-600 leading-relaxed"
                                        id="module' + e.id + '">

                                        <div class="overflow-x-auto">
                                            <table class="table table-report -mt-2" id="dttbl">
                                                <thead>
                                                    <tr>
                                                        <th>Meal plan</th>
                                                        @foreach ($room_cat as $row)
                                                            <th value="{{ $row->room_categories_id }}">
                                                                {{ $row->room_categories_name }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>

                                                <tbody id="data_table">
                                                    @foreach ($mealplan as $row)
                                                        <tr>
                                                            <td>
                                                                {{ $row->mealPlanCode }}

                                                            </td>

                                                            @foreach ($room_cat as $row2)
                                                                <td>
                                                                    @php
                                                                        $cat_count = count($room_cat);
                                                                        $mealplan_count = count($mealplan);
                                                                        $total_count = $mealplan_count * $cat_count;
                                                                        $rate_count = count($all_rates_get);
                                                                        //dd($total_count);
                                                                        $valuve = $all_rates_get
                                                                            ->where('room_category', $row2->room_categories_id)
                                                                            ->where('meal_plan_id', $row->id)
                                                                            ->where('room_type_id', $all_rates_get_room_type->get_room_type->room_type_id)
                                                                            ->first();
                                                                        if ($rate_count == $total_count && isset($valuve->rate) ? $valuve->rate == null || $valuve->rate == '' : '') {
                                                                            $calrate = '';
                                                                        } else {
                                                                            if ($rate_count != $total_count) {
                                                                                $valuve = isset($valuve->rate) ? $valuve->rate : '';
                                                                                $num = (int) $valuve;
                                                                                $calculaterate = doubleval($Cyrate * $num);
                                                                                $calrate = round($calculaterate, 2);
                                                                                if ($calrate == 0) {
                                                                                    $calrate = ' ';
                                                                                }
                                                                            } else {
                                                                                $valuve = isset($valuve->rate) ? $valuve->rate : '';
                                                                                $num = (int) $valuve;
                                                                                $calculaterate = doubleval($Cyrate * $num);
                                                                                $calrate = round($calculaterate, 2);
                                                                            }
                                                                        }
                                                                        
                                                                    @endphp
                                                                    <input hidden class="border" name="room_cat_id[]"
                                                                        id="room_cat_id[]"
                                                                        style="width: 100%; text-align: right"
                                                                        value="{{ $row2->room_categories_id }}">


                                                                    <div class="relative">
                                                                        <div
                                                                            class="absolute rounded-l w-10 h-full flex items-center justify-center bg-gray-100 dark:bg-dark-1 dark:border-dark-4 border text-gray-600">
                                                                            {{ $symble }}</div>

                                                                        <input type="text"
                                                                            class="input pl-12 w-full border col-span-4"
                                                                            name="amount[]" id="amount[]"
                                                                            style="width: 100%"
                                                                            placeholder="{{ $symble }}"
                                                                            data-cat='{{ $row2->room_categories_id }}'
                                                                            data-meal='{{ $row->id }}'
                                                                            value="{{ isset($calrate) ? $calrate : '' }}"
                                                                            type="text" step="0.001">
                                                                    </div>

                                                                </td>
                                                                @php
                                                                    $index++;
                                                                @endphp
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody id="body">
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="container"></div>

                @endif


            </div>


            <input type="hidden" id='form_status' name="form_status"
                value="{{ isset($details) ? $details->status : '1' }}">

            @if (isset($status_info))


                @section('status', $status_info['status'])

            @section('status_button')
                <input class="input input--switch border" type="checkbox" id='status_btn' onchange="changeStatus(this)"
                    @if ($details->status == '1') checked @endif>
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

        @if (isset($status_info))

            <div>

                @section('additional-info')
                    <p>Created By : {{ isset($status_info) ? $status_info['created_by'] : '' }}</p>
                    <p>Created Date : {{ isset($status_info) ? $status_info['created_at'] : '' }}</p>
                    <p>Updated By : {{ isset($status_info) ? $status_info['updated_by'] : '' }}</p>
                    <p>Updated Date : {{ isset($status_info) ? $status_info['updated_at'] : '' }}</p>
                @endsection

            </div>

        @endif


    @endsection





</div>

</form>
@section('script-area')

@if (isset($details))
    <script>
        $(function() {
            $('#status_box').show('fast');
            $('#clear').attr('hidden', true);

        });
    </script>
@endif

<script>
    $(document).ready(function() {

        $('#save').attr('hidden', false);
        $('#cancel').attr('hidden', false);

    });

    function get_all_room_rate() {
        var agent_id = $("#agent_id").val();
        var season_id = $("#season_id").val();
        preloader();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({

            url: "{{ route('get_all_room_rate') }}",
            type: 'POST',
            dataType: "JSON",
            data: {
                'agent_id': agent_id,
                'season_id': season_id
            },
            success: function(response) {

                $('.container').empty();
                $(".container").append(response.top_lable);
                $(".dttbl_tr").append(response.top_header);

                const mealp = response.mealplan;
                const roomc = response.room_cat;
                console.log(mealp);
                for (i = 0; i < mealp.length; i++) {

                    $(".mealplan").append('<tr class="gg' + i + '"><td>' + mealp[i].mealPlanCode + '</td>');

                    for (x = 0; x < roomc.length; x++) {
                        $('.gg' + i).append(
                            '<td><input hidden class="border" name="room_cat_id[]" id="room_cat_id[]" style="width: 100%; text-align: right" value="' +
                            roomc[x].room_categories_id +
                            '"> <div class="relative"><div class="absolute rounded-l w-10 h-full flex items-center justify-center bg-gray-100 dark:bg-dark-1 dark:border-dark-4 border text-gray-600">' +
                            response.symble +
                            '</div><input class="input pl-12 w-full border col-span-4" name="amount[]" id="amount[]" style="width: 100%" value="" placeholder="' +
                            response.symble + '" type="text"></div></td>');
                    }
                    $(".mealplan").append('</tr>');
                }
                removeLoader();
            },
            error: function(xhr) {
                console.log(xhr);
                removeLoader();
                $('.container').empty();
                $(".container").append(
                    '<div style="text-align: center;" class="rounded-md px-5 py-4 mb-2 mt-4 bg-theme-6 text-white">All room rate are alrady added</div>'
                );
                console.log(xhr
                    .responseText);
            }
        });
    }

    $(function() {
        var symble = $("#symble").val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('checkcurrys_rate') }}",
            type: 'POST',
            dataType: "JSON",
            data: {},
            success: function(response) {
                console.log(response);
                var res = response;
                var bsy = res.symble
                if (symble == bsy) {

                } else {
                    preloader();
                    window.location.reload(true);
                }

            },
            error: function(xhr) {
                console.log(xhr
                    .responseText);
            }
        });
    });






    $('#save').click(function() {

        if ($('#frmdt').valid()) {
            preloader();
            $('#frmdt').submit();
        }
        removeLoader();
    });
</script>
@if (isset($all_rates_get_room_type))
    <script>
        $(function() {
            $('#save').text('UPDATE');
        });
    </script>
@endif
@endsection
