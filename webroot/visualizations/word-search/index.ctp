<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li><a href="visualize">Visualizations</a></li>
		  <li class="active">Word Search</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<h1>Word Search</h1>
	</div>
	<div class="col-md-8">
		<h3>Search for a word</h3><br />(or beginning of a word...)
	</div>
</div>
<div class="row">
	<div class="col-md-4">
	</div>
	<div class="col-md-4">
		<input type="email" class="form-control" id="search" placeholder="type your search...">
	</div>
</div>
<div class="row">
	<div class="col-md-4">
	</div>
	<div class="col-md-4">
		<a class="btn btn-primary" href="javascript:doSearch();">Search</a>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="results" class="padded"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="exporter" class="padded"><a href="javascript:exportdata();" class="btn btn-default"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> EXPORT DATA</a></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 small">
		<em>To transform JSON data in other formats (for example CSV, to use in other software), consider using the services which are available online, such as <a href="https://konklone.io/json/" target="_blank">this one</a>, or <a href="http://www.convertcsv.com/json-to-csv.htm" target="_blank">this one</a>, or <a href="https://json-csv.com/" target="_blank">this one</a>.</em>
	</div>
</div>