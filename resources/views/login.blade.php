<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="DexignLab">
    <meta name="robots" content="" >
    <meta name="description" content="Brightling App">

    <!-- Mobile Specific -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- PAGE TITLE HERE -->
    <title>Praisy | Admin Login</title>

    <!-- Favicon icon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('storage/logo/logo.png') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

</head>

<body class="vh-100">
<div class="authincation h-100">
    <div class="container h-100">
        <div class="row justify-content-center h-100 align-items-center">
            <div class="col-md-6">
                <div class="authincation-content">
                    <div class="row no-gutters">
                        <div class="col-xl-12">
                            <div class="auth-form" style="border-radius: 20px">
                                <div class="text-center mb-3">
                                    <a href="javascript:void(0)" class="brand-logo">
                                        <img src="{{ asset('storage/logo/logo.png') }}" style="width:300px" />
                                    </a>
                                </div>
                                <h4 class="text-center mb-4">Sign in your account</h4>
                                <form id="adminLogin">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="" class="mb-1"><strong>Email</strong></label>
                                        <input id="" type="email" class="form-control" value="" name="email" placeholder="Email">
                                        <div class="error-email"></div>
                                    </div>
                                    <div class="mb-3 position-relative">
                                        <label for="password" class="mb-1" ><strong>Password</strong></label>
                                        <input  id="password" type="password" class="form-control" value="" name="password" placeholder="*********">
                                        <button class="btn btn-sm border-0 position-absolute end-0" style="top: 50%;transform: translate(0,-25%);" type="button" id="toggleIcon">
                                            <i class="login-icon bi bi-eye fs-5"></i>
                                        </button>
                                        <div class="error-password"></div>
                                    </div>
                                    <div class="text-center">
                                        <button id="submitBtn" type="submit" class="btn btn-primary btn-block">Sign In</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>

<script>

    let notyf = new Notyf({
        duration: 3000,
        position: {
            x: 'right',
            y: 'top',
        },
    });

    $("#adminLogin").on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData($("#adminLogin")[0]);
        const btn = $('#submitBtn');
        $.ajax({
            type: "POST",
            url: "{{ route('admin_login') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            data: formData,
            beforeSend: function () {
                $('.error-message').html('');
                btn.prop('disabled', true);
                btn.html('Processing');
            },
            success: function (res) {
                if (res.success === true) {
                    btn.prop('disabled', false);
                    btn.html('Sign In');
                    notyf.success({
                        message: res.message,
                        duration: 3000
                    });
                    setTimeout(function () {
                        window.location.href = "{{ route('dashboard') }}";
                    }, 3500);

                } else {
                    btn.prop('disabled', false);
                    btn.html('Sign In');
                    notyf.error({
                        message: res.message,
                        duration: 3000
                    });
                }
            },
            error: function (e) {
                btn.prop('disabled', false);
                btn.html('Sign In');
                if (e.responseJSON.errors['email']) {
                    $('.error-email').html('<small class=" error-message text-danger">' + e.responseJSON.errors['email'][0] + '</small>');
                }
                if (e.responseJSON.errors['password']) {
                    $('.error-password').html('<small class=" error-message text-danger">' + e.responseJSON.errors['password'][0] + '</small>');
                }
            }
        });
    });

    $(document).ready(function() {
        $("#toggleIcon").click(function() {
            let input = $("#password");
            let icon = $("#toggleIcon i");

            if (input.attr("type") === "password") {
                input.attr("type", "text");
                icon.removeClass("bi-eye").addClass("bi-eye-slash");
            } else {
                input.attr("type", "password");
                icon.removeClass("bi-eye-slash").addClass("bi-eye");
            }
        });
    });


</script>

</body>
</html>
