@extends('template.layout')

@section('content')
	<div class="login-page login-form-fall" data-url="http://neon.dev">
    <!-- This is needed when you send requests via Ajax -->
    <script type="text/javascript">
        var baseurl = '{{url('user/login')}}';
    </script>
    
    <style>
	
	html, body { 
	 height: 100%;
	}
	
	.login-page{
		min-height:100%;
	}
	.btn-login {
		white-space:normal;
	}
	
	.login-page .login-bottom-links {
		padding-top: 10px;
	}
	
	
	ul.action-link {
		list-style:none;
		display:inline-block;
	}
	
	ul.action-link li {
		float:left;
		padding:0 5px;
		font-size: 14px;
		line-height:18px;
	}
	
	.login-page .login-bottom-links .link {
		line-height:20px;
		margin-bottom:10px;
	}
	
   </style> 

    <div class="login-container">
        <div class="login-header login-caret">
            <div class="login-content">
                <a href="{{url('/')}}" class="logo">                    
                    <img src="{{Session::get('SITE_LOGO')}}" width="180" alt="site-logo" />
                </a>
                <p class="description">Dear user, log in to access your management area!</p>
            </div>
        </div>
       
        <div class="login-form">		
            <div class="login-content">
                {!! Session::get('SESSION_MESSAGE')!!}
                {{Session::forget('SESSION_MESSAGE')}}
                @if(Session::get('status_message') && Session::get('status_message') != "")   
                {{Session::get('status_message')}}
                {{Session::forget('status_message')}}
                @endif

                <form method="post" class="validate" role="form" id="form_login1" action="{{url('/loginPost')}}">
					<input type="hidden" name="_token" value="{{csrf_token()}}"/>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="entypo-user"></i>
                            </div>
                            <input type="email" class="form-control" data-validate="email,required" name="email" id="email" placeholder="Email address" autocomplete="off" value="{{old('email')}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="entypo-key"></i>
                            </div>
                            <input type="password" class="form-control" name="password" id="password" data-validate="required" placeholder="Password" autocomplete="off"  value="{{old('password')}}"/>
                        </div>

                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block btn-login">
                            Log In
                            <i class="entypo-login"></i>
                        </button>
                    </div>

                </form>


                <div class="login-bottom-links">
                    <ul class="action-link">                       
                        <li><a href="{{url('user/forgotpassword')}}" class="link">Forgot your password?</a></li>
                    </ul>                    
                </div>

            </div>            
            <p class="footer">{!! $footer !!}</p>            
        </div>
     </div>
    </div>
    <script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-login.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-custom.js'}}"></script>
    <script src="{{$assets_dir.'/js/neon-demo.js'}}"></script>
@endsection