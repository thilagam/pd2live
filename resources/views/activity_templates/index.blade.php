@extends('../app')

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">

@if($permit->crud_activity_templates_create)
<a href="{{ url('/activity-templates/create') }}" class="btn btn-primary btn-sm" >Create</a>
@endif

<script type="text/javascript">
jQuery(document).ready(function($)
{
	$("#example-2").dataTable({
         });
	
         // Replace checkboxes when they appear
});
</script>


<table class="table table-bordered table-striped" id="example-2">
		<thead>
		<tr class="bg-info">
		<th>#</th>
		<th>Name</th>
		<th>Description</th>
		<th>Languages</th>
		<th>Type</th>
		<th>Status</th>
		<th>Actions</th>
		<th></th>
		<th></th>
                </tr>
		</thead>
			<tbody>
			          @foreach ($act_templates as $act)
                                       <tr>
					<td> {{ $act->actmp_id }} </td>
					<td> {{ $act->actmp_name }} </td>
					<td> {{ $act->actmp_description }} </td>
					<td> {{ $act->lang_name }} </td>
					<td> {{ $act->acttype_name }} </td>
					<td>
						@if($act->actmpplus_status == 1)  Active @else In-Active @endif
                    </td>
					<td>
					@if($permit->crud_activity_templates_read)
					     <a href="{{url('activity-templates',$act->actmpplus_id)}}" class="btn btn-primary btn-sm btn-icon icon-left">Read</a>
					@endif     	
					</td><td>
					@if($permit->crud_activity_templates_edit)
					<a href="{{route('activity-templates.edit',$act->actmpplus_id)}}" class="btn btn-secondary btn-sm btn-icon icon-left">Update</a>
					@endif
</td><td>
			@if($permit->crud_activity_templates_delete)
             {!! Form::open(['method' => 'DELETE', 'route'=>['activity-templates.destroy', $act->actmpplus_id]]) !!}
             {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm btn-icon icon-left', 'onclick' => 'return confirm("'.$dictionary['delete_confirm_alert'].'");']) !!}
             {!! Form::close() !!}
            @endif 
				        </td>
					</tr>
				   @endforeach
							
			</tbody>
</table>

<link rel="stylesheet" href="{{ asset('/assets/js/datatables/dataTables.bootstrap.css') }}">

<script src="{{ asset('/assets/js/datatables/js/jquery.dataTables.min.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('/assets/js/datatables/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('/assets/js/datatables/yadcf/jquery.dataTables.yadcf.js') }}"></script>
<script src="{{ asset('/assets/js/datatables/tabletools/dataTables.tableTools.min.js') }}"></script>

</div>
</div>
</div>
</div>

@endsection

