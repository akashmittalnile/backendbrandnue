@extends('layouts.login')
@section('col',12)
@section('content')
	<div class="text-justify page-content">
		<h1 class="mt-3 font-weight-bold">Steps to Delete Account</h1>
		<p>1. Open the navigation menu or the side menu from the left side of the mobile app.</p>
		<p>2. Select My profile option</p>
		<p>3. Click on Edit Profile button</p>
		<p>4. Select the Delete Account option</p>
		<p>This will delete the user data from the application and user will not be able to login into the application.</p>
		<hr>
		
	</div>
@endsection
@push('css')
	<style>
		.page-content p{ text-align:left !important;margin-top: 12px; } 
		.page-content h2{ text-align:left !important; }
		.page-content h3{ font-size: 18px; font-weight: 600; padding: 0; color: #6c7383;}
		.page-content ul li{font-size: 14px;color: #6c7383;}
		.page-content ul ol{font-size: 14px;color: #6c7383;list-style-type: circle;}
		.page-content b{font-size: 16px;}
		.auth-section{display: flow-root;}

	</style>
@endpush