<div class="row">
	<!-- Profile Info and Notifications -->
	<div class="col-md-6 col-sm-8 clearfix">
		<ul class="user-info pull-left pull-none-xsm first-tour">
			<!-- Profile Info -->
			<li>
				<a href="{{Session::get('USER_TYPE') != '3' ? url('user/editprofile') : 'javascript:void(0)'}}">
					<img src="{{Session::get('PROFILE_PHOTO')}}" alt="user-avatar"
					class="img-circle user-avatar"/> Hi <span id="user_full_name">{{Session::get('FULL_NAME')}}</span>
				</a>
			</li>
		</ul>
	</div>

	<!-- Raw Links -->
	<div class="col-md-6 col-sm-4 clearfix hidden-xs">
		<ul class="list-inline links-list pull-right">            
            @if(Session::get('USER_TYPE') == '2')
            <li>
				<a href="{{url('dashboard/membersarea')}}"> 
					<i class="entypo-suitcase"></i> Members Area 
				</a>
			</li>
			<li class="sep"></li>
			<li>
				<a href="http://shop.mywifi.io" target="_blank"> 
					<i class="fa fa-cart-arrow-down"></i> Shop Now
				</a>
			</li>
			<li class="sep"></li>
            @endif
            @if(Session::get('AFFILIATE_DASHBOARD_URL') && Session::get('AFFILIATE_DASHBOARD_URL') != "")
            <li>
				<a href="{{url('user/affiliatedashboard')}}"> 
					<i class="fa fa-cubes"></i> Affiliate Dashboard
				</a>
			</li>
			<li class="sep"></li>
            @endif
            @if(Session::get('USER_TYPE') == '2')
            <li><a target="_blank" href="http://support.mywifi.io"> <i
					class="fa fa-bug"></i> Support
			</a></li>
			<li class="sep"></li>
			@endif
            @if(Session::get('USER_TYPE') != '3')
            <li><a href="{{url('user/editprofile')}}"> 
               <i class="entypo-cog"></i> My Account
			</a></li>
			<li class="sep"></li>
            @endif
            <li>
				<a href="{{url('user/logout')}}"> Log Out
					<i class="entypo-logout"></i>
				</a>
			</li>
		</ul>
	</div>
</div>

@if (Session::get('USER_TYPE') != '3' && (Session::get('USER_TIME_ZONE') == "" || Session::get('USER_TIME_ZONE') == '0'))
{{ GenerateConfirmationMessage('danger', 'You did not set your time zone yet. Please click <a href="'.url('user/editprofile').'">here</a> to set time zone.')}}
@endif
