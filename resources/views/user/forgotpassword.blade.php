@extends('template.layout')

@section('content')
<div class="page-body login-page login-form-fall">
    <!-- This is needed when you send requests via Ajax -->
    <script type="text/javascript">
        var baseurl = '';
    </script>    
<style>
	.login-page .login-header {
		padding-top:40px!important;
		padding-bottom:40px!important;
	}
</style>
    <div class="login-container">
        <div class="login-header login-caret">
            <div class="login-content">
                <a href="{{url('/')}}" class="logo">
                    <img src="{{$logo}}" width="180" alt="site-logo" />
                </a>
                <p class="description">Enter your email, and we will send the reset link.</p>
            </div>
        </div>

        <div class="login-form">
            <div class="login-content">               
                    {!! Session::get('status_message') !!}
                    {{Session::forget('status_message')}}                
                <form method="post" role="form" class="validate"  action="{{url('user/forgotpassword')}}">                    
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">@</div>
                            <input type="email" data-validate="required" placeholder="Enter email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <button name="forgot_password" class="btn btn-info btn-lg btn-block">Send Link</button>
                    </div>
                </form>

                <div class="login-bottom-links">
                	<ul class="action-link">
                        <li><a href="{{url('/')}}" class="link">Return to Login Page</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
     <p class="footer">{!! $footer !!}</p>
</div>

    <script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-forgotpassword.js'}}"></script>
    <script src="{{$assets_dir.'/js/jquery.inputmask.bundle.min.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-custom.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-demo.js'}}"></script>
@endsection