@extends('../app')
@section('content')

<div class="row">
	@if($permit->module_product_process_gen)
	<div class="col-sm-12"> 
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ $dictionary['validate_pdn_files'] }}</h3>
				
			</div>
			<div class="panel-body">

			<script type="text/javascript">
			jQuery(document).ready(function($)
			{
			   var t = $("#example-2").dataTable({
			        "oLanguage": {
			                        "sSearch": "{{ $dictionary['dtable_search'] }}",
			                        "sLengthMenu": "{{ $dictionary['dtable_length_menu_show'] }}"+'&nbsp;<select class="form-control input-sm" aria-controls="example-2" name="example-2_length"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>&nbsp;'+"{{ $dictionary['dtable_length_menu_entries'] }}"
			                     }

			    });

			     // Replace checkboxes when they appear
			});
			</script>
				
				<table class="table table-bordered table-striped" id="example-2">
					<thead>
						<tr class="bg-info">
							<th>{{ $dictionary['fprwd_folder'] }}</th>
							<th>{{ $dictionary['fprwd_gen_new'] }}</th>
							<th>{{ $dictionary['fprwd_all_gen'] }}</th>
							<th>{{ $dictionary['fprwd_ref_count'] }}</th>
							<th>{{ $dictionary['fprwd_images'] }}</th>
							<th>{{ $dictionary['fprwd_action'] }}</th>
						</tr>
					</thead>
					
					<tbody class="middle-align">
					@if(!empty($gen))
					@foreach($gen as $ruv)
						  <tr>
						  	<td>{{ $ruv->name }}</td>
						  	<td> 
						  		@if($ruv->new>0)
						  		<a href="{{ url() }}/{{$ruv->download}}/new" class="btn btn-success btn-sm btn-icon icon-left" target="_blank">
									{{ $ruv->new }}							
								</a>
								@else
									{{ $ruv->new }}							
								
								@endif
						  	</td>
						  	<td>
						  		@if($ruv->gen>0)
						  		
						  		<a href="{{ url() }}/{{$ruv->download}}/all " class="btn btn-info btn-sm btn-icon icon-left" >{{ $ruv->gen }}
						  		</a> 
						  		@else
									{{ $ruv->gen }}							
								
								@endif
						  	</td>
						  	<td>
						  		<a href="{{ url('') }}" class="btn btn-blue btn-sm btn-icon icon-left">{{ $ruv->total_ref }}
						  		</a> 
						   </td>
						  	<td>
						  	<a href="{{ url('') }}" class="btn btn-purple btn-sm btn-icon icon-left">	{{ $ruv->images }}
						  		</a> 
						   </td>
						  	<td>
						  		
						  		<a href="{{ url() }}/{{$ruv->route}}" class="btn btn-icon btn-primary" title="Generate">
									<i class="fa-cog"></i>
								</a>
								<a href="{{ url() }}/{{$ruv->url}}" target="_blank" class="btn btn-icon btn-info" title='view folder images'>
									<i class="fa-file-image-o"></i>
								</a>

								
								</td>

						  </tr>
						@endforeach	
						@endif
					</tbody>

				
				</table>
			</div>
		</div>	
	</div>
	@endif

</div>

<script src="{{ asset('/assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('/assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('/assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>

<script src="{{ asset('/assets/js/datatables/js/jquery.dataTables.min.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('/assets/js/datatables/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('/assets/js/datatables/yadcf/jquery.dataTables.yadcf.js') }}"></script>
<script src="{{ asset('/assets/js/datatables/tabletools/dataTables.tableTools.min.js') }}"></script>



@endsection