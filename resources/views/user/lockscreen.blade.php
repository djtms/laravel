@extends('template.layout')
@section('content')
<div
	class="login-page is-lockscreen login-form-fall login-form-fall-init"
	data-url="http://neon.dev">
	<div class="login-container">

		<div class="login-header">

			<div class="login-content">

				<a href="#" class="logo"> <img
					src="{{$logo == '' ? $assets_dir.'/images/logo@2x.png' :$logo}}"
					width="180" alt="site-logo" />
				</a>

				<p class="description">Dear {{$name}}, enter your password to log back in.</p>
			</div>

		</div>

		<div class="login-form">

			<div class="login-content">
				<form method="post" action="{{url('/user/lockscreen')}}">
                    <input type="hidden" name="_token" value="{{$_token}}"/>
					<div class="form-group lockscreen-input">
                        
						<div class="lockscreen-thumb">
							<img src="{{$photo}}"
								class="img-circle thumbnail" />
						</div>

						<div class="lockscreen-details">
							<h4 style="color: #51555d">{{$name}}</h4>
							<a href="{{url('user/logout')}}" class="link">logged
								off <i class="entypo-right-open"></i>
							</a>
						</div>

					</div>                   
				     {!! Session::get('SESSION_MESSAGE') !!}
                     {{ Session::forget('SESSION_MESSAGE')}}                   
                    <div class="form-group">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="entypo-key"></i>
							</div>
							<input type="hidden" name="myhidden" value="{{base64_encode($email)}}"/>
							<input type="hidden" name="rurl" value="{{$redirect_by}}"/>
							<input type="password" class="form-control" name="password" id="password" placeholder="Password" required />
						</div>
					</div>

					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-block btn-login">
							Log in <i class="entypo-login"></i>
						</button>
					</div>
				</form>

				<div class="login-bottom-links">
                	<ul class="action-link">
                        <li><a href="{{url('user/logout')}}" class="link">Sign in
						using different account <i class="entypo-right-open"></i></a></li>
                    </ul>
                </div>
			</div>
			<p class="footer">{!! $footer !!}</p>
		</div>
	</div>
</div>
@endsection