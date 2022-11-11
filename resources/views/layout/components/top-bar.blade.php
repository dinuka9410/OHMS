<!-- BEGIN: Top Bar -->

<!-- Get Auth -->
@php $user = Auth::user(); @endphp
<div class="top-bar">
    <!-- BEGIN: Breadcrumb -->
    <div class="-intro-x breadcrumb mr-auto hidden sm:flex">
        <a href="/" class="">Dashboard</a>
        @if(isset($pagenames))
        @foreach($pagenames as $row)

        <i data-feather="chevron-right" class="breadcrumb__icon"></i>
        <a href="{{$row['routename']}}" class="breadcrumb--active">{{$row['displayname']}}</a>

        @endforeach
        @endif
    </div>
    <!-- END: Breadcrumb -->

    <!-- Curuncy change -->
    <!-- <div class="intro-x relative mr-3 sm:mr-6 curuncy-change">
        <span class="currency-change"><select id="currency_selector" class="w-full js-example-basic-single" onchange="set_currency_rate()"></select></span>
    </div> -->
    <!--  Curuncy change  End -->

    <!-- BEGIN: Notifications -->
    <!-- <div class="notification-area intro-x dropdown mr-auto sm:mr-6">
        <div class="dropdown-toggle notification notification--bullet cursor-pointer">
            <i data-feather="bell" class="notification__icon dark:text-gray-300"></i>
        </div>
        <div class="notification-content pt-2 dropdown-box">
            <div class="notification-content__box dropdown-box__content box dark:bg-dark-6">
                <div class="notification-content__title">Notifications</div>
                <div id="notification_content">
                    @if(COUNT($user->unreadNotifications)>0)
                    <div class="notifications-list">
                    @foreach ($user->unreadNotifications()->paginate(5) as $notification)
                    <div class="notification-item cursor-pointer relative flex items-center mt-1">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <img alt="Midone Tailwind HTML Admin Template" class="rounded-full" src="{{ user_image($notification->create_user_id) }}">
                            <div class="w-3 h-3 bg-theme-9 absolute right-0 bottom-0 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <a href="{{ $notification->url }}" class="font-medium truncate mr-5">{{ $notification->permission_name }}</a>
                            </div>
                            <div class="notification-msg w-full truncate text-gray-600">{{ $notification->msg }}</div>
                        </div>
                    </div>
                    @endforeach
                    </div>
                    <button class="button notification_cleat_btn" onclick="clearNotification()">
                        <span class="text-side">Clear All</span>
                        <span class="icon-side">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x mx-auto"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </span>
                    </button>
                    @else
                   
                    <div class="w-full truncate text-gray-600">You don't have any notifications</div>
                    @endif
                </div>
            </div>
        </div>
    </div> -->
    <!-- END: Notifications -->

    <!-- BEGIN: Account Menu -->
    <div class="intro-x dropdown w-8 h-8">
        <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in">
            <img alt="" src="{{ user_image($user->id) }}">
        </div>
        <div class="dropdown-box w-56">
            <div class="dropdown-box__content box bg-theme-38 dark:bg-dark-6 text-white">
                <div class="p-4 border-b border-theme-40 dark:border-dark-3">
                    <div class="font-medium">{{$user->name}}</div>

                </div>
                <div class="p-2 border-t border-theme-40 dark:border-dark-3">
                    <a href="{{ url('logout') }}" class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md">
                        <i data-feather="toggle-right" class="w-4 h-4 mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Account Menu -->
</div>
<!-- END: Top Bar -->

<script src="{{asset('dist/js/currency_rates.js')}}"></script>
<link href="{{asset('dist/css/select2.min.css')}}" rel="stylesheet" />
<script src="{{asset('/dist/js/select2.min.js')}}"></script>
<script>
    $('#currency_selector').select2();

    function clearNotification(user_id) {
        console.log(user_id);
        $.ajax({
            type: "POST",
            url: "{{ route('clear_notification') }}",
            async: false,
            data: {},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                $('#notification_content').html("<div class='w-full truncate text-gray-600'>You don't have any notifications</div>");
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
</script>
