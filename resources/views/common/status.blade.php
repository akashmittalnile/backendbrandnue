@if(isset($status))
	@switch($status)
		@case(config('constant.status.active'))
			<div class="badge bg-success p-1">Active</div>
			@break
		@case(config('constant.status.in_active'))
			<div class="badge bg-danger  p-1">Inactive</div>
			@break
		@default
			<div class="badge bg-danger  p-1">Pending</div>
	@endswitch
@elseif(isset($notify))
	@switch($notify)
		@case(config('constant.status.active'))
			<div class="badge bg-success p-1">Published</div>
			@break
		@case(config('constant.status.in_active'))
			<div class="badge bg-danger  p-1">Created</div>
			@break
		@default
			<div class="badge bg-danger  p-1">Pending</div>
	@endswitch
@endif