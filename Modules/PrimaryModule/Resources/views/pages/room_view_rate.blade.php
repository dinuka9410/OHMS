@extends('../layout/form-layout')

@section('form-name', 'Agent Room Rates')

@section('form-area')


    <form class="validate-form" id="frmdt" method="POST"
        onsubmit="agent_id.disabled = false;  season_id.disabled = false; return true;""
        action="
        {{ route('agent_rates_add_edit') }}">
        @csrf

        <div class="grid grid-cols-12 gap-5">
            <input hidden class="border" name="symble" id="symble" style="width: 100%; text-align: right"
                value="{{ $symble }}">
            <div class="col-span-12 ">
                <div class="grid grid-cols-12 gap-4 row-gap-5 mt-5">

                    @php
                        
                        $valuve = $agents->where('id', $details->agent_id)->first();
                        $valuve2 = $seasons->where('id', $details->season_id)->first();
                    @endphp
                    <div class="intro-y col-span-12 sm:col-span-3 input-form">
                        <div class="mb-2"><b>Agent Code : </b>{{ $valuve->agentName }}</div>
                    </div>

                    <div class="intro-y col-span-12 sm:col-span-3">
                        <div class="mb-2"><b>Agent Code : </b>{{ $valuve->agentCode }}</div>
                    </div>
                    <div class="intro-y col-span-12 sm:col-span-3">
                        <div class="mb-2"><b> Seasons : </b>{{ $valuve2->seasonName }}</div>
                    </div>


                </div>

                <br>
                @if (isset($all_rates_get_room_type))
                    @php
                        $index = 0;
                    @endphp
                    @foreach ($all_rates_get_room_type as $all_rates_get_room_type)
                        <br>
                        <input hidden class="border" name="roomtypeid" id="roomtypeid"
                            style="width: 100%; text-align: right"
                            value="{{ $all_rates_get_room_type->get_room_type->room_type_id }}">
                        <div class="col-span-12 lg:col-span-6">
                            <div class="accordion user-permissions-content">
                                <div id="modulediv"
                                    class="user-permissions accordion__pane  border-b border-gray-200 dark:border-dark-5">
                                    <div class="intro-y grid grid-cols-12 gap-6 border-b header-row">
                                        <div class="col-span-12 lg:col-span-9 title-side">
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
                                                                        if($calrate ==''){$valcal='';}else {$valcal=$symble.':'.$calrate;}
                                                                    @endphp

                                                                    <label style="text-align:right;">
                                                                        {{ $valcal }}</label>
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
                    @php
                        $index = 0;
                    @endphp
                    @foreach ($room_types as $row)
                        <br>
                        <div class="col-span-12 lg:col-span-6">
                            <div class="accordion user-permissions-content">
                                <div id="modulediv"
                                    class="user-permissions accordion__pane  border-b border-gray-200 dark:border-dark-5">
                                    <div class="intro-y grid grid-cols-12 gap-6 border-b header-row">
                                        <div class="col-span-12 lg:col-span-9 title-side">
                                            <label style="font-size: 20px;"
                                                class="flex flex-col sm:flex-row">{{ $row->room_type_Select }}</label>
                                        </div>
                                    </div>
                                    <div class="accordion__pane__content mt-3 text-gray-700 dark:text-gray-600 leading-relaxed"
                                        id="module">
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

                                                            @foreach ($room_cat as $row)
                                                                <td>
                                                                    <input class="border input w-full border mt-2"
                                                                        name="amount[]" id="amount[]" style="width: 100%"
                                                                        value="{{ isset($all_rates_get) ? $all_rates_get[$index] : '' }}"
                                                                        type="text">
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
                @endif


            </div>

        </div>

    </form>

@endsection



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


@section('script-area')

    <script>
        $(document).ready(function() {
            $('#save').attr('hidden', true);
            $('#cancel').attr('hidden', false);
            $('#clear').attr('hidden', true);
        });

        $('#save').click(function() {


            $('#frmdt').validate({

                rules: {

                },
                messages: {
                }

            });


            if ($('#frmdt').valid()) {
                $('#frmdt').submit();
            }

        });

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
                    var res = response;
                    var bsy = res.symble
                    if (symble == bsy) {

                    } else {
                        location.reload();
                    }

                },
                error: function(xhr) {
                    console.log(xhr
                        .responseText);
                }
            });
        });
    </script>

@endsection
