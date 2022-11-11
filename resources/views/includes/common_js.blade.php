<script src="{{ asset('dist/js/jquery-3.6.0.js') }}" crossorigin="anonymous"></script>
<script src="{{ asset('dist/js/sweetalerts.js') }}"></script>
<script src="{{ asset('dist/js/toastify.js') }}"></script>

@if (session()->has('status'))

    @php
        
        $data = session('status');
        
    @endphp

    @if (isset($data['error_status']) && $data['error_status'] != null)

        {{-- // if the status is 0 then it means success --}}

        @if ($data['error_status'] == '0')
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'success',
                        text: '{{ $data['msg'] }}',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    })
                });
            </script>

            @php
                session()->forget('status.error_status');
                //Session::forget('status.error_status');
            @endphp

            {{-- // if the status is 1 that means validation error,
          // if the status is 2 thats means a query or exception error --}}
        @elseif($data['error_status'] == '1' || $data['error_status'] == '2')
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '{{ $data['msg'] }}',
                        confirmButtonText: 'close'
                    })
                });
            </script>

            {{-- // the error status is 3 then it means a authorization error --}}
        @elseif($data['error_status'] == '3')
            <script>
                $(document).ready(function() {

                    Swal.fire({
                        title: '<strong>Page Unauthorized</strong>',
                        icon: 'info',
                        html: "You don't have permissions to <b>access</b> this page",
                        showCloseButton: false,
                        showCancelButton: false,
                        focusConfirm: false,
                    })

                })
            </script>
        @endif

    @endif



@endif



@if ($errors->any())


    @foreach ($errors->all() as $error)
        <script>
            $(document).ready(function() {

                Toastify({
                    text: "{{ $error }}",
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "bottom",
                    position: "left",
                    backgroundColor: "#0e2c88",
                    stopOnFocus: true
                }).showToast();

            })
        </script>
    @endforeach

@endif


{{-- javascript based notification trigger --}}


<script>
    /* funtion for handale form status */
    function changeStatus(ele) {
        if (ele.checked) {
            $('#form_status').val('1');
            $('#status').html('Active');
        } else {
            $('#form_status').val('0');
            $('#status').html('Inactive');
        }
    }

    function notify(status, msg) {

        if (status == 0) {

            Swal.fire({
                title: 'success',
                text: msg,
                icon: 'success',
                confirmButtonText: 'Ok'
            });

        }

        if (status == 1 || status == 2) {

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: msg,
                confirmButtonText: 'close'
            });

        }


        // 6 will show the loading alert

        if (status == 6) {

            Swal.fire({
                allowOutsideClick: false,
                title: msg,
            })
            Swal.showLoading()

        }


    }


    function confirmnotify(msg) {
        /* this one used as
        $('#save').click(function() {
            var msg = 'Modules update can affect the database and whole system process. So, do you real want to update modules ? '
            confirmnotify(msg).then((result) => {
                if (result.isConfirmed) {
                    $('#frmdt').submit();
                } else if (result.isDenied) {
                    removeLoader();
                }
            });
        }); */
        return Swal.fire({
            title: msg,
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Yes',
            denyButtonText: `No`,

        });

    }

    


    function preloader() {

        $('.app').append('<div style="" id="loadingDiv"><div class="loader_new"></div></div>');
        $(window).on('load', function() {
            //setTimeout(removeLoader, 2000); //wait for page load PLUS two seconds.
        });


    }


    function removeLoader() {
        $("#loadingDiv").fadeOut(500, function() {
            $("#loadingDiv").remove(); //makes page more lightweight
        });
    }


    function checkIfUniqueField(table, column, value) {

        var url = "{{ route('checkifuniquefield') }}";
        var final_result = false;

        var params = {
            'table': table,
            'column': column,
            'value': value,
        };

        $.ajax({
            url: url,
            data: params,
            async: false,
            //global:false,
            success: function(res) {

                if (res.error_status == 0) {

                    final_result = res.isunique;

                }

            },
            error: function(err) {

                final_result = false;

            }
        });


        return final_result;


    }



    function validateMobileNumber(param) {

        var result = true;

        var regex = /^[0-9,+][\d,\+]{8,15}$/;

        if (param.value == "") {

            result = false;

            $('#phone_no_error').show();

            $('#phone_no_error').text("Invalid phone no");

        } else {

            if (!param.value.match(regex)) {

                result = false;

                $('#phone_no_error').show();

                $('#phone_no_error').text("Invalid phone no");

            } else {

                $('#phone_no_error').hide();

            }

        }


        return result;

    }

    function validateMobileNumber_2(param) {

        var result = true;

        var regex = /^[0-9,+][\d,\+]{8,15}$/;

        if (param.value == "") {

            result = false;

            $('#phone_no_error_2').show();

            $('#phone_no_error_2').text("Invalid phone no");

        } else {

            if (!param.value.match(regex)) {

                result = false;

                $('#phone_no_error_2').show();

                $('#phone_no_error_2').text("Invalid phone no");

            } else {

                $('#phone_no_error_2').hide();

            }

        }


        return result;

    }
</script>
