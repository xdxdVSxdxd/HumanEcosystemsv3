<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li><a href="visualize">Visualizations</a></li>
		  <li class="active">Single Topic Relations</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h1>Single Topic Relations</h1>
	</div>
</div>
<div class="well">
	<div class="row">
		<div class="col-md-12">
			<div class="padded">
				<div class="form-group">
				    <label for="topic">Enter Topic</label>
				    <input type="text" class="form-control" id="topic" placeholder="topic">
				    <button id="submit-topic" class="btn btn-default">Visualize</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="well">
	<div class="row">
		<div class="col-md-12">
			<h3>Statistics</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="padded" id="results2stats">
			</div>
			<div class="padded" id="results2graph">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div id="exporter-stats" class="padded"><a href="javascript:exportTopicStats();" class="btn btn-default"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> EXPORT STATISTICS</a></div>
		</div>
		<div class="col-md-6">
			<h4>Legend</h4>
			<table class="legendtable">
				<tr>
					<td ></td>
					<td class="td1"></td>
					<td class="td2"></td>
					<td class="td3"></td>
					<td class="td4"></td>
				</tr>
				<tr>
					<td><strong>Comfort</strong></td>
					<td>> 0</td>
					<td>> 0</td>
					<td>< 0</td>
					<td>< 0</td>
				</tr>
				<tr>
					<td><strong>Energy</strong></td>
					<td>> 0</td>
					<td>< 0</td>
					<td>< 0</td>
					<td>> 0</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="results" class="padded"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="exporter" class="padded"><a href="javascript:exportTopicRelations();" class="btn btn-default"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> EXPORT DATA</a></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 small">
		<em>To transform JSON data in other formats (for example CSV, to use in other software), consider using the services which are available online, such as <a href="https://konklone.io/json/" target="_blank">this one</a>, or <a href="http://www.convertcsv.com/json-to-csv.htm" target="_blank">this one</a>, or <a href="https://json-csv.com/" target="_blank">this one</a>.</em>
	</div>
</div>