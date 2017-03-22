<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li><a href="visualize">Visualizations</a></li>
		  <li class="active">Geographic Distribution</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h1>Geographic Distribution on Points</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="results" class="padded"><div id='mapholder'></div></div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div id="exporter" class="padded"><a href="javascript:exportalldata();" class="btn btn-default"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> EXPORT DATA</a></div>
	</div>
	<div class="col-md-3">
		<h4>Venues</h4>
		<table class='legendtable'>
			<tr>
				<th>Type</th>
				<th>Color</th>
			</tr>
			<tr>
				<td class='voice'>Arene, piazze, parchi<td>
				<td class='color c1'><td>
			</tr>
			<tr>
				<td class='voice'>Teatri, sale concerto<td>
				<td class='color c2'><td>
			</tr>
			<tr>
				<td class='voice'>venues<td>
				<td class='color c3'><td>
			</tr>
		</table>
	</div>
	<div class="col-md-3">
		<h4>Areas</h4>
		<table class='legendtable'>
			<tr>
				<th>Type</th>
				<th>Color</th>
			</tr>
			<tr>
				<td class='voice'>Distretti<td>
				<td class='color c4'><td>
			</tr>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12 small">
		<em>To transform JSON data in other formats (for example CSV, to use in other software), consider using the services which are available online, such as <a href="https://konklone.io/json/" target="_blank">this one</a>, or <a href="http://www.convertcsv.com/json-to-csv.htm" target="_blank">this one</a>, or <a href="https://json-csv.com/" target="_blank">this one</a>.</em>
	</div>
</div>
<script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=visualization&sensor=false"></script>