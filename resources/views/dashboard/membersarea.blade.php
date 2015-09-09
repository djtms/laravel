@extends('template.layout')
@section('content')
<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
   @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        <div id="my-element"></div>
        @include('template.headermenu')
        <hr/>
        <div class="row">
            <div class="col-sm-4">
                <h2 style="margin-top: 0px;" class="last-tour">
                    <i class="entypo-gauge"></i> Members Area
                </h2>
            </div>
            <div class="col-sm-8 text-right">
            </div>
        </div>



        <div class="row">
            <div class="col-md-12">
             {!! $members_area_content !!}   
            </div>
        </div>


            {!! $footer !!}
    </div><!-- /.main-content-->    
</div>
@endsection