<div class="settings-pane">
			
	<a href="#" data-toggle="settings-pane" data-animate="true">
		&times;
	</a>
	
	<div class="settings-pane-inner">
		
		<div class="row">
			
			<div class="col-md-4">
				
				<div class="user-info">
					
					<div class="user-image">
						<a href="extra-profile.html">
							<img src="@if($user_profile_image!=''){{ url('uploads/'.$user_profile_image) }} @else {{ asset('/assets/images/user-2.png')}} @endif" class="img-responsive img-circle" width="80px" height="80px;" />
						</a>
					</div>
					
					<div class="user-details">
						
						<h3>
							<a href="{{ url('profile')}}/{{ Auth::user()->id }}">
							@if(isset($userPlusInfo->up_first_name) && isset($userPlusInfo->up_last_name))
								{{$userPlusInfo->up_first_name }}  {{$userPlusInfo->up_last_name }}
							@else
								{{ Auth::user()->name }}
							@endif

							</a>
							
							<!-- Available statuses: is-online, is-idle, is-busy and is-offline -->
							<span class="user-status is-online"></span>
						</h3>
						
						<p class="user-title">
							@if(isset($userPlusInfo->up_designation))
								{{$userPlusInfo->up_designation }} 
							@endif
						</p>
						
						<div class="user-links">
							<a href="extra-profile.html" class="btn btn-primary">Edit Profile</a>
							<a href="extra-profile.html" class="btn btn-success">Upgrade</a>
						</div>
						
					</div>
					
				</div>
				
			</div>
			
			<div class="col-md-8 link-blocks-env">
				
				<div class="links-block left-sep">
					<h4>
						<span>Notifications</span>
					</h4>
					
					<ul class="list-unstyled">
						<li>
							<input type="checkbox" class="cbr cbr-primary" checked="checked" id="sp-chk1" />
							<label for="sp-chk1">Messages</label>
						</li>
						<li>
							<input type="checkbox" class="cbr cbr-primary" checked="checked" id="sp-chk2" />
							<label for="sp-chk2">Events</label>
						</li>
						<li>
							<input type="checkbox" class="cbr cbr-primary" checked="checked" id="sp-chk3" />
							<label for="sp-chk3">Updates</label>
						</li>
						<li>
							<input type="checkbox" class="cbr cbr-primary" checked="checked" id="sp-chk4" />
							<label for="sp-chk4">Server Uptime</label>
						</li>
					</ul>
				</div>
				
				<div class="links-block left-sep">
					<h4>
						<a href="#">
							<span>Help Desk</span>
						</a>
					</h4>
					
					<ul class="list-unstyled">
						<li>
							<a href="#">
								<i class="fa-angle-right"></i>
								Support Center
							</a>
						</li>
						<li>
							<a href="#">
								<i class="fa-angle-right"></i>
								Submit a Ticket
							</a>
						</li>
						<li>
							<a href="#">
								<i class="fa-angle-right"></i>
								Domains Protocol
							</a>
						</li>
						<li>
							<a href="#">
								<i class="fa-angle-right"></i>
								Terms of Service
							</a>
						</li>
					</ul>
				</div>
				
			</div>
			
		</div>
	
	</div>
		
</div>
