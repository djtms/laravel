@extends('template.layout')
@section('content')
<div class="page-container">	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr />
		<div class="row">
			<div class="col-sm-4">
				<h2 style="margin-top: 0px;">
					<i class="entypo-clock"></i>Timeline
				</h2>
			</div>
			<div class="col-sm-8 text-right"></div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
			@if($results)
			<?php $count = 1;?>
				<div id="timeline_data" class="timeline-centered">                  
					@foreach($results as $row)
					<article class="timeline-entry {{$count%2 == 0 ? "left-aligned" : ""}}">
						<div class="timeline-entry-inner">
							<time class="timeline-time">
								<span>{{$row->added_datetime}}</span>
								<span>Today</span>
							</time>
							<div class="timeline-icon">
								<img class="img-circle img-responsive pointer" src="{{$row->picture_url}}" onclick="javascript:GetSocialUserDetail({{$row->id}});" alt="user-avatar">
							</div>
							<div class="timeline-label">
								<h2>
									<a href="javascript:GetSocialUserDetail({{$row->id}});">{{$row->full_name}}</a>
                                    {!! getSocialMediaIcon( $row->social_network) !!}
                                    {!! getOSLogo($row->os_name)!!}
								</h2>
								<p>Campaign:  <a href="{{url("campaign/view?camp_id=".$row->campaign_id)}}">{{$row->campaign}}</a></p>
								<p>Location:  <a href="{{url("location/overview?loca=".$row->location_id)}}">{{$row->location}}</a></p>
								<p>Device:  <a href="{{url('campaign/devicemodal&data=').base64_encode($row->device_id)}}">{{$row->device}}</a></p>
							</div>
						</div>
					</article>	
					<?php $count +=1;?>		
					@endforeach					
				</div>
				@else
					{{GenerateConfirmationMessage('alert alert-danger', 'No social users found.')}}
				@endif
			</div>
		</div>
		<input type="hidden" value="{{$currently_showing}}" id="currently_showing">
		<input type="hidden" value="{{$total_found}}" id="total_found">
	</div>
</div>

<script type="text/javascript">

$(document).ready(function(){
	$(window).scroll(function() {
	    if($(window).scrollTop() == $(document).height() - $(window).height()) {
	    	var total_found = $("#total_found").val();
			var currently_showing = $("#currently_showing").val();
			if(total_found !== currently_showing){
				$("#preloader").removeClass('hide');
				$.ajax({
					url: '{{url("ajax/LoadTimelineData")}}',
					type: 'POST',
					data: {currently_showing:currently_showing},
					success:function(output){
						$("#preloader").addClass('hide');
						var object = jQuery.parseJSON(output);
						$("#currently_showing").val(object.currently_showing);
						$("#timeline_data").append(object.markup);
					}
				});
			}
	    }
	});
});
</script>
@endsection