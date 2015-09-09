@extends('template.layout')
@section('content')
<style type="text/css">
    .social-part {
        position:relative;
    }

    .social-part .social-icon-btns {
        position: absolute; 
        top:6px;
        right: 6px;
        z-index: 10;
        color:#FFF;
    }
</style>

<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        <div id="my-element"></div>        
        @include('template.headermenu')
        <hr />
        <div class="row">
            <div class="col-sm-4">
                <h2 style="margin-top: 0px;" class="last-tour">
                    <i class="entypo-gauge"></i> Dashboard
                </h2>
            </div>
            <div class="col-sm-8 text-right">
            </div>
        </div>        
        <br />
        <div class="row">
            <div class="col-sm-2 social-part">
                <div class="row">
                    <div class="col-xs-12 text-right social-icon-btns"><i class="fa fa-facebook-square" style="color: #fff;"></i></div>
                </div>
                <div class="tile-stats" style="background-color: #3b5998;">
                    <div class="icon">
                        <i class="entypo-users"></i>
                    </div>
                    <div class="num" data-start="0" data-end="{{$total_social_user['fb']}}" data-postfix="" data-duration="1500" data-delay="0">0</div>

                    <h3>Users</h3>
                    <p>
                        <strong style="font-size: 15px;">{{$returning_user['fb']}}</strong>
                        Returning.
                    </p>
                </div>
            </div>

            <div class="col-sm-2 social-part">
                <div class="row">
                    <div class="col-xs-12 text-right social-icon-btns"> <i class="fa fa-twitter-square" style="color: #fff;"></i></div>
                </div>

                <div class="tile-stats" style="background-color: #55acee;">
                    <div class="icon">
                        <i class="entypo-users"></i>
                    </div>
                    <div class="num" data-start="0" data-end="{{$total_social_user['tw']}}" data-postfix="" data-duration="1500" data-delay="600">0</div>

                    <h3>Users</h3>
                    <p>
                        <strong style="font-size: 15px;">{{$returning_user['tw']}}</strong>
                        Returning.
                    </p>
                </div>
            </div>
            
            <div class="col-sm-2 social-part">
                <div class="row">
                    <div class="col-xs-12 text-right social-icon-btns"><i class="fa fa-google-plus-square" style="color: #fff;"></i></div>
                </div>
                <div class="tile-stats" style="background-color: #dd4b39;">
                    <div class="icon">
                        <i class="entypo-users"></i>
                    </div>					
                    <div class="num" data-start="0" data-end="{{$total_social_user['gp']}}" data-postfix="" data-duration="1500" data-delay="1200">0</div>

                    <h3>Users</h3>
                    <p>
                        <strong style="font-size: 15px;">{{$returning_user['gp']}}</strong>
                        Returning.
                    </p>
                </div>
            </div>

            <div class="col-sm-2 social-part">
                <div class="row">
                    <div class="col-xs-12 text-right social-icon-btns"><i class="fa fa-linkedin-square" style="color: #fff;"></i></div>
                </div>
                <div class="tile-stats" style="background-color: #0976b4;">
                    <div class="icon">
                        <i class="entypo-users"></i>
                    </div>
                    <div class="num" data-start="0" data-end="{{$total_social_user['li']}}" data-postfix="" data-duration="1500" data-delay="1800">0</div>

                    <h3>Users</h3>
                    <p>
                        <strong style="font-size: 15px;">{{$returning_user['li']}}</strong>
                        Returning.
                    </p>
                </div>

            </div>
            
            <div class="col-sm-2 social-part">
                <div class="row">
                    <div class="col-xs-12 text-right social-icon-btns"><i class="fa fa-instagram" style="color: #fff;"></i></div>
                </div>
                <div class="tile-stats" style="background-color:#3f729b;">
                    <div class="icon">
                        <i class="entypo-users"></i>
                    </div>
                    <div class="num" data-start="0" data-end="{{$total_social_user['ig']}}" data-postfix="" data-duration="1500" data-delay="2400">0</div>

                    <h3>Users</h3>
                    <p>
                        <strong style="font-size: 15px;">{{$returning_user['ig']}}</strong>
                        Returning.
                    </p>
                </div>
            </div>
            <div class="col-sm-2 social-part">
                <div class="row">
                    <div class="col-xs-12 text-right social-icon-btns"><i class="fa fa-envelope-o" style="color: #fff;"></i></div>
                </div>
                <div class="tile-stats" style="background-color:#F7931E;">
                    <div class="icon">
                        <i class="entypo-users"></i>
                    </div>
                    <div class="num" data-start="0" data-end="{{$total_social_user['cuser']}}" data-postfix="" data-duration="1500" data-delay="3000">0</div>

                    <h3>Users</h3>
                    <p>
                        <strong style="font-size: 15px;">{{$returning_user['cuser']}}</strong>
                        Returning.
                    </p>
                </div>

            </div>
        </div>

        <br />

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong><i class="entypo-map"></i> Location Map</strong>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="location-map" style="width: 100%; height: 300px"></div>
                    </div>
                </div>
            </div>
        </div>

        <br />

        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong><i class="entypo-users"></i> Recently Connected Users</strong>
                        </div>
                    </div>

                    <!-- panel body -->
                    <div class="panel-body">
                        <div class="row">
                            @if (count($lastest_users) > 0)
                                @foreach ($lastest_users as $user)
                                    <div class="col-xs-4 col-sm-4 col-md-3" style="padding:0 5px;">
                                        <div class="thumbnail" style="min-height:140px;">
                                            <img width="50" onclick="javascript:GetSocialUserDetail({{$user->id}});" src="{{$user->picture_url }}" class="img-responsive img-circle thumbnail pointer" alt="user-pic" />
                                            <div class="caption text-center" style="padding:4px;">
                                                <p>{{$user->name == "" ? "Unknown" : $user->name }}</p>
                                                @if (strlen($user->location) > 16)
                                                    <a href="{{url('location/overview?loca='.$user->location_id)}}" title="{{$user->location}}" class='btn btn-info btn-xs'>{{substr($user->location, 0, 14)}}...</a>
                                                @else 
                                                    <a href="{{url('location/overview?loca='.$user->location_id)}}" title="{{$user->location}}" class='btn btn-info btn-xs'>{{$user->location}}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-10 col-md-offset-1">
                                    <div class="alert alert-warning"><strong><i class="fa fa-frown-o"></i> No user(s) has been found!</strong></div>
                                </div>
                            @endif
                        </div><!-- row -->
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong><i class="entypo-network"></i> WIFI users</strong>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <i class="fa fa-male" style="font-size: 60px; color: #47639E;"></i>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>{{$male_female['male']}}</h3>
                                        <p>Male WIFI users.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <i class="fa fa-female" style="font-size: 60px; color: #E4007D;"></i>
                                    </div>
                                    <div class="col-md-9">
                                        <h3>{{$male_female['female']}}</h3>
                                        <p>Female WIFI users.</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div><!-- panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong><i class="entypo-target"></i> Location Connections</strong>
                        </div>
                    </div>
                    <div class="panel-body" style="padding-top:0px;">
                        <table class="table responsive">
                            <thead>
                                <tr>
                                    <th style="font-size:14px;">Location</th>
                                    <th><i class="fa fa-facebook-square"></i></th>
                                    <th><i class="fa fa-twitter-square"></i></th>
                                    <th><i class="fa fa-google-plus-square"></i></th>
                                    <th><i class="fa fa-linkedin-square"></i></th>
                                    <th><i class="fa fa-instagram"></i></th>
                                    <th><i class="fa fa-envelope-square"></i></th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (count($top_locations) > 0)
                                    @foreach ($top_locations as $location)
                                        <tr>
                                            <td><a href="{{url('location/overview&loca=' . $location->location_id)}}">{{$location->name}}</a></td>
                                            <td>{{$location->fb}}</td>
                                            <td>{{$location->tw}}</td>
                                            <td>{{$location->gp}}</td>
                                            <td>{{$location->li}}</td>
                                            <td>{{$location->ig}}</td>
                                            <td>{{$location->cu}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="6" class="empty_row">No Location(s) Found</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="my-other-element"></div>
        </div>
			{!! $footer !!}
    </div><!-- /.main-content-->    
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {

        
        var locations = [{!!$location_graph_data!!}];
        
        
        var map = new google.maps.Map(document.getElementById('location-map'), {
            zoom: 4
            //center: new google.maps.LatLng(locations)
        });
        
        var bounds = new google.maps.LatLngBounds();
        var infowindow = new google.maps.InfoWindow();    

        for (var i = 0; i < locations.length; i++) {  
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
          });

          //extend the bounds to include each marker's position
          bounds.extend(marker.position);

          google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
              infowindow.setContent(locations[i][0]);
              infowindow.open(map, marker);
            }
          })(marker, i));
        }

        //now fit the map to the newly inclusive bounds
        map.fitBounds(bounds);
    });





</script>
<link rel="stylesheet" href="{{$assets_dir.'/js/jvectormap/jquery-jvectormap-1.2.2.css'}}">
<script src="{{$assets_dir.'/js/jvectormap/jquery-jvectormap-1.2.2.min.js'}}"></script>
<script src="{{$assets_dir.'/js/jvectormap/jquery-jvectormap-world-mill-en.js'}}"></script>
@endsection