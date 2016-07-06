@extends('../app')

@section('content')

<div class="col-sm-12">
    <div class="panel panel-default">
        <div class="panel-body">

<div class="col-sm-6">
<table class="table table-model-2 table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Step to Import Unique Data From Old Files</th>												
											</tr>
										</thead>
										
										<tbody>
									
											<tr>
												<td>1</td>
												<td>Open ImportExportOldFiles Controller </td>
											<tr>
											<tr>
												<td>2</td>	
												<td>There will be 3 Functions <br /> importR => "Import Reference file" <br /> importP => "Import PDN file" <br /> importW => "Import Writer file" </td>
											</tr>
											<tr>
												<td>3</td>
												<td>You need to update Models as per <br /> Example:- Caroll. ("CepClientCarollRefs", "CepClientCarollPdns", )</td>
											</tr>
											<tr>
												<td>4</td>
												<td>You need to create Folder Inside ref or pdn or writer. <br /> Example:- If you want to import file for Caroll Ref then you have to create a folder inside "/public/uploads/products/454362/ref/" with name old and you all the old file insite that folder.</td>
											</tr>
											
											<tr>
												<td>5</td>
												<td>Then you need to call Url Manually. "Where {id} == product id"<br /> importR => "im-ex-port-s/old/importR/{id}" <br /> importP => "im-ex-port-s/old/importP/{id}" <br /> importW => "im-ex-port-s/old/importR/{id}" </td>
											</tr>
											
										</tbody>
									</table>
</div>

<div class="col-sm-6">
<table class="table table-model-2 table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Step to Export Unique Data To One File</th>												
											</tr>
										</thead>
										
										<tbody>
									
											<tr>
												<td>1</td>
												<td></td>
											<tr>
											<tr>
												<td>2</td>	
												<td> </td>
											</tr>
											<tr>
												<td>3</td>
												<td></td>
											</tr>
											<tr>
												<td>4</td>
												<td></td>
											</tr>
											
											<tr>
												<td>5</td>
												<td></td>
											</tr>
											
										</tbody>
									</table>
</div>

<link rel="stylesheet" href="{{ asset('/assets/js/datatables/dataTables.bootstrap.css') }}">

<script src="{{ asset('/assets/js/datatables/js/jquery.dataTables.min.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('/assets/js/datatables/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('/assets/js/datatables/yadcf/jquery.dataTables.yadcf.js') }}"></script>
<script src="{{ asset('/assets/js/datatables/tabletools/dataTables.tableTools.min.js') }}"></script>

</div>
</div>
</div>

@endsection
