<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li><a href="visualize">Visualizations</a></li>
		  <li class="active">Top Users</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-md-10">
		<h1>Top Users</h1>
	</div>
	<div class="col-md-2">
		<select id="modeselect" name="modeselect">
				<option value=''>select duration</option>
				<option value=''>---------------</option>
				<option value='day'>Day</option>
				<option value='week'>Week</option>
				<option value='month'>Month</option>
				<option value='year'>Year</option>
				<option value='all'>ALL</option>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h4>by number of followers</h4>
		<div id="results-followers" class="padded"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h4>by number of friends</h4>
		<div id="results-friends" class="padded"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h4>by number of posts</h4>
		<div id="results-nposts" class="padded"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h4>by reach</h4>
		<div id="results-reach" class="padded"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h4>by engagement</h4>
		<div id="results-engagement" class="padded"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h4>by engagement / post ratio</h4>
		<div id="results-epratio" class="padded"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="exporter" class="padded"><a href="javascript:exportdata();" class="btn btn-default"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> EXPORT DATA</a></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 small">
		<em>To transform JSON data in other formats (for example CSV, to use in other software), consider using the services which are available online, such as <a href="https://konklone.io/json/" target="_blank">this one</a>, or <a href="http://www.convertcsv.com/json-to-csv.htm" target="_blank">this one</a>.</em>
	</div>
</div>