<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="../">Home</a></li>
		  <li><a href="visualize">Visualizations</a></li>
		  <li class="active">Co-Relations</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h1>Co-Relations</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<h3>Choose the order</h3>
	</div>
	<div class="col-md-3">
		<select id="order">
		  <option value="name">by Word</option>
		  <option value="count">by Number of links</option>
		  <option value="group">by Emotion: Energy/Comfort</option>
		</select>
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
		<em>To transform JSON data in other formats (for example CSV, to use in other software), consider using the services which are available online, such as <a href="https://konklone.io/json/" target="_blank">this one</a>, or <a href="http://www.convertcsv.com/json-to-csv.htm" target="_blank">this one</a>, or <a href="https://json-csv.com/" target="_blank">this one</a>.</em><br /><br />
		<em>You might also want to try out tools such as <a href="http://openrefine.org/" target="_blank">OpenRefine</a> to import the data and prepare it for usage in other software packages such as R and Gephi.</em>
	</div>
</div>