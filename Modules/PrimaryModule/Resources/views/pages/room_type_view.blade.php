@extends('../layout/home-layout')

@section('title','Room Type')

@section('home-content')

           <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-no-wrap items-center mt-2">

            <a href="{{route('room_type_add_edit')}}"
                class="button text-white bg-theme-1 shadow-md mr-2">Add room type</a> 
            <div class="dropdown">
                <button class="dropdown-toggle button px-2 box text-gray-700 dark:text-gray-300">
                    <span class="w-5 h-5 flex items-center justify-center">
                        <i class="w-4 h-4" data-feather="plus"></i>
                    </span>
                </button>

            </div>
        </div>
        <!-- BEGIN: Data List -->

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2" id="dttbl">
                <thead>
                    <tr>
                        <th class="whitespace-no-wrap">#</th>
                        <th class="whitespace-no-wrap">Type</th>
                        <th class=" whitespace-no-wrap">Area</th>
                        <th class=" text-center  whitespace-no-wrap">Max adults</th>
                        <th class=" text-center  whitespace-no-wrap">Max children</th>
                        <th class="text-center whitespace-no-wrap"> Status</th>
                        <th class="text-center whitespace-no-wrap"> Action</th>
                    </tr>
                </thead>
                <tbody id="data_table">
                    @if (count($room_types) > 0)
                        @foreach ($room_types as $room_type)
                            <tr class="intro-x">
                                <td>
                                    <div class="font-medium whitespace-no-wrap ">{{ $room_type->room_type_id }}</div>
                                </td>
                                <td>
                                    <div class="font-medium whitespace-no-wrap">{{ $room_type->room_type_Select }}</div>
                                </td>
                                <td>
                                    <div class="font-medium whitespace-no-wrap">{{ $room_type->room_type_area }}</div>
                                </td>
                                <td>
                                    <div class="font-medium whitespace-no-wrap text-center ">{{ $room_type->room_type_max_adults }}</div>
                                </td>
                                <td>
                                    <div class="font-medium whitespace-no-wrap text-center ">{{ $room_type->room_type_max_children }}</div>
                                </td>
                                <td class="w-40">
                                    @if ($room_type->room_type_status == '1')
                                        <div class="flex items-center justify-center text-theme-9">
                                            <i data-feather="check-square" class="w-4 h-4 mr-2"> </i>
                                            Active
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center text-theme-6">
                                            <i data-feather="check-square" class="w-4 h-4 mr-2"> </i>
                                            Inactive
                                        </div>
                                    @endif

                                </td>

                                <td class="table-report__action w-56">
                                    @if ($room_type->room_type_id != 0)
                                        <div class="flex justify-center items-center">
                                            <form action="{{route('room_type_add_edit')}}" method="get" >

                                                <input hidden value="{{ $room_type->room_type_id }}" name="id" >
                                                <button style="" class="flex items-center mr-3 text-theme-1" type="submit" ><i data-feather="eye" class="w-4 h-4 mr-1"></i>View</button>
        

                                            </form>

                                            <form action="{{route('room_type_add_edit')}}" method="get" >

                                                <input hidden value="{{ $room_type->room_type_id }}" name="id" >
                                                <button style="" class="flex items-center mr-3 text-theme-1" type="submit" ><i data-feather="check-square" class="w-4 h-4 mr-1"></i>Edit</button>
                                                
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody id="body">
            </table>

        </div>
    </div>

    @extends('includes.links')
    @if($errors->any())
    @foreach($errors->all() as $error)

     <script>
         
           $(document).ready(function(){
            Toastify({
                text:
                "{{$error}}",
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






