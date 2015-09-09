@extends('template.layout')
@section('content')
<div class="page-container">
	<!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr />
		<div class="row">
			<div class="col-sm-3">
				<h2 style="margin-top: 0px;">
					<i class="entypo-location"></i>Locations
				</h2>
			</div>
			<div class="col-sm-9 text-right">
                @if(Session::get('USER_TYPE') != '3')
                    <button type="button"
					class="btn btn-red btn-icon icon-left btn-lg third-tour"
					id="load_new"
					onclick="javascript:$('#add_new_location').modal('show', {backdrop: 'static'});">
					Add New Location <i class="entypo-plus-circled"></i>
				</button>
                @endif
            </div>
		</div>
		@if(Session::get('message') && Session::get('message') != '')        
            <div class="row" style="padding-top: 10px;">
			<div class="col-md-12">
				<div
					class="{{Session::get('class')}}{{ Session::forget('class')}} alert-dismissible fade in">
					<button data-dismiss="alert" class="close" type="button">
						<span aria-hidden="true">x</span><span class="sr-only">Close</span>
					</button>
                     {!! Session::get('message')!!}
                      {{ Session::forget('message')}}
                    </div>
			</div>
		</div>
		@endif
		<div class='row'>
			<div class='col-md-12'>
				<div class="btn-group">
		        	<button type="button" id="all_locations" data-status="" class="location_status btn btn-white active"></button>
					<button type="button" id="active_locations" data-status="1" class="location_status btn btn-white"></button>
					<button type="button" id="inactive_locations" data-status="0" class="location_status btn btn-white"></button>
					<input type="hidden" id="hdn_location_status" value=""/>
				</div>
			</div>
		</div>
		@if(Session::get('USER_TYPE') != '3')        
        <div class="row">
			<div class="col-md-12 search-box">
				<input id="location_search_value" type="text"
					class="form-control input-lg"
					placeholder="Search your location here by location name, location address, location country, location user or location username and hit ENTER">
			</div>
		</div>
        @endif
        <br />
		<div id="locations"></div>
		{!! $footer !!}
    </div>
	<!-- /.main-content-->
</div>

<!-- Modal 6 (Long Modal)-->
<div class="modal" id="add_new_location">
	<div class="modal-dialog" style="width: 65%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h3 class="modal-title">Add New Location</h3>
			</div>

			<form id="rootwizard-2" autocomplete="off" method="post"
				action="{{url('location/create')}}"
				class="form-wizard validate form-horizontal">
				<div class="modal-body">

					<div class="form-group">
						<div class="col-md-12">
							<label class="control-label" for="company map">Search Your Location</label>
							<input type="text" class="form-control" onclick="load_map();"
								onmouseout="load_text();" id="geocomplete" name="company_name"
								placeholder="Enter Your Business Address or Company Name" />
						</div>
					</div>


					<div class="map_new">
						<div class="panel-title"
							style="padding-right: 0; padding-bottom: 0;">
							<h3></h3>
							<p></p>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<div class="map_canvas" style='height: 110px;'></div>
							</div>
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12">
							<label class="control-label" for="company">Company Name</label> <input
								type="text" class="form-control" name="name"
								data-validate="required" placeholder="Name" />
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label " for="company">Address 1</label> <input
										type="text" class="form-control" name="formatted_address"
										data-validate="required" placeholder="Formatted Address" />
								</div>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label " for="venue_address2">Address 2</label>
									<input type="text" class="form-control" name="venue_address2"
										placeholder="(Optional) Secondary  Address " />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="company">State/Province </label>
									<input type="text" class="form-control"
										name="administrative_area_level_1"
										placeholder="State/Province" />
								</div>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="company">Town/City </label> <input
										type="text" class="form-control" name="locality"
										placeholder="Locality" />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label " for="postal_code">Zip/Postal </label>
									<input class="form-control" name="postal_code" id="postal_code"
										type="text" value="" placeholder="Zip/Postal" />
								</div>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label " for="company">Country </label> 
									<input type="text" class="form-control" name="country" placeholder="Country" data-validate="required" /> 
									<input type="hidden" class="form-control" name="country_short" placeholder="Country Code" />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="company">Phone Number </label>
									<input type="text" class="form-control"
										name="international_phone_number" data-validate="required"
										placeholder="International Phone Number" />
								</div>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="location">Time Zone</label>                                
                                <select id="time_zone" name="time_zone"
										class="form-control required">
									@foreach($timezones as $key =>$value)                                    
                                        <option	value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
								</div>
							</div>
						</div>
					</div>


					<div class="row hide">
						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="country_code">Country Code</label>
									<input class="form-control" name="country_code"
										id="country_code" placeholder="Country Code" type="text"
										value="" />
								</div>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="company">Sub Locality</label>
									<input type="text" class="form-control" name="sublocality"
										placeholder="Sub Locality" />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="company">URL </label> <input
										type="text" class="form-control" name="url" placeholder="URL" />
								</div>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<div class="col-md-12">
									<label class="control-label" for="company">Website</label> <input
										type="text" class="form-control" name="website"
										placeholder="Website" />
								</div>
							</div>
						</div>
					</div>


				</div>

				<div class="modal-footer">
					<input type="hidden" value="" name="location" />
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-info" name="submit"
						value="Save">
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal" id="location_alert_modal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form action="{{url('location/delete')}}"
				method="POST">
				<div id="modal_body" class="modal-body"
					style="font-weight: bold; color: black;"></div>
				<div id="modal_footer" class="modal-footer text-center"></div>
			</form>
		</div>
	</div>
</div>



<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/jquery.geocomplete.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
<script type="text/javascript">
$(document).ready(function () {
    $("div.c_hide").addClass('hide');    
    
    getAllLocations();
    $('#location_search_value').bind('keypress', function(e) {
    	var code = e.keyCode || e.which;
    	 if(code == 13) {
    		 getAllLocations();
    	 }
    });
    $("#locations").on( "click", ".pagination a", function (e){
        e.preventDefault();
        var page = $(this).attr("data-page"); //get page number from link
        var search_value = $('#location_search_value').val();
        if(page){
        	getAllLocations(page)
        }
	});
    $(".location_status").click(function(){
        var status = $(this).attr("data-status");
        $("#hdn_location_status").val(status);
        //var element_id = $(this).attr("id");
        $(".location_status").removeClass('active');
        $(this).addClass("active");
        
        getAllLocations();
    });
});
function getAllLocations(page){
	$("#preloader").removeClass('hide');
	var search_value = $('#location_search_value').val();
	var status = $("#hdn_location_status").val();
    $.ajax({
        url: '{{url("ajax/GetAllLocations")}}',
        type: 'POST',
        data: {search_value:search_value, status:status, page:page,_token:'{{csrf_token()}}'},
        success:function(output){
        	var objData = $.parseJSON( output );
        	$("#preloader").addClass('hide');
        	$("#locations").html(objData.markup);
        	$("#all_locations").text('All ('+objData.location_count.all_locations+')');
        	$("#active_locations").text('Active ('+objData.location_count.active_locations+')');
        	$("#inactive_locations").text('Inactive ('+objData.location_count.inactive_locations+')');
        	$(".boots-switch").bootstrapSwitch();
        	$('[data-toggle="tooltip"]').tooltip();
        }
        
    });
}
                                       function load_map() {
                                           $("#geocomplete").geocomplete({
                                               map: ".map_canvas",
                                               details: "form",
                                               types: ["geocode", "establishment"]
                                           });
                                       };
function removeLocation(id){
        $("#modal_title").html('Delete Confirmation');
        $("#modal_body").html('<i class="entypo-attention"></i> Are you sure you want to delete this location?');
        $("#modal_footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '<button type="submit" class="btn btn-red">Yes</button>' +
                '<input type="hidden" name="location_id" value="' + id + '"/>');
        $("#location_alert_modal").modal('show');
}
                                       function load_text() {
                                        name = $("#rootwizard-2 input[name='name']").val();
                                        add = $("#rootwizard-2 input[name='formatted_address']").val();
                                        $(".map_new p").text(add);
                                        $(".map_new h3").text(name);
                                        }
                                       /*$("#find").click(function () {
                                        $("#geocomplete").geocomplete({
                                        map: ".map_canvas",
                                        details: "form",
                                        types: ["geocode", "establishment"]
                                        });
                                        $("#geocomplete").trigger("geocode");
                                        setTimeout(function () {
                                        name = $("#rootwizard-2 input[name='name']").val();
                                        add = $("#rootwizard-2 input[name='formatted_address']").val();
                                        $(".map_new p").text(add);
                                        $(".map_new h3").text(name);
                                        }, 500);
                                        });*/
                                       function changeStatus(id) {
                                            $.ajax({
                                                url: '{{url("ajax/ChangeStatusLocation")}}',
                                                type: 'POST',
                                                data: {id:id},
                                                success:function(result){
                                                    result = result.trim('\n');
                                                    if(result == 'success'){
                                                    	getAllLocations();
                                                    }
                                                }
                                            });
                                           /* $.post(
                                                   '{{url("ajax/ChangeStatusLocation")}}',
                                                   {
                                                       submit: "edit",
                                                       id: id
                                                   },
                                           function (data, status) {
                                               if (status === 'success') {
                                                   if (status_s === 'off') {
                                                       $("div#loca_on div#loca_on_" + id + "").addClass("hide");
                                                       $("div#loca_off div#loca_off_" + id + "").removeClass("hide");
                                                   }
                                                   if (status_s === 'on') {
                                                       $("div#loca_on div#loca_on_" + id + "").removeClass("hide");
                                                       $("div#loca_off div#loca_off_" + id + "").addClass("hide");
                                                   }
                                               } else {
                                                   alert("Edit " + status + "!");
                                               }
                                           },
                                                   "json"
                                                   );*/
                                       }
</script>
@endsection