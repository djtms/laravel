@extends('template.layout')

@section('content')
<div class="page-body login-page login-form-fall">

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
                <form name="frm-resetpassword" method="POST" class="validate">
                    <input type="hidden" name="act" value="resetpassword"/>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="entypo-key"></i>
                            </div>
                            <input type="password" placeholder="Enter strong password" data-validate="required" id="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="entypo-cw"></i>
                            </div>
                            <input type="password" placeholder="Confirm password" data-message-equal-to="Passwords doesn't match." data-validate="required,equalTo[#password]" id="confirm_password" name="confirm_password" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <button name="reset_password" type="submit" class="btn btn-info btn-lg btn-block">Reset Password</button>
                    </div>
                </form>
                
                <div class="login-bottom-links">
                	<ul class="action-link">
                        <li><a href="{{url('/')}}" class="link"><i class="entypo-lock"></i>
                        Return to Login Page</a></li>
                    </ul>
                </div>
            </div>
            <p class="footer">{!! $footer !!}</p>
        </div>
    </div>
</div>
    <script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-forgotpassword.js'}}"></script>
    <script src="{{$assets_dir.'/js/jquery.inputmask.bundle.min.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-custom.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-demo.js'}}"></script>
    @endsection
