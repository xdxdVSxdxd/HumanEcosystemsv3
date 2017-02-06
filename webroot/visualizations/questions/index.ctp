<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-10">

				<div class="btn-group" role="group" aria-label="questions">
				  <button type="button" class="btn btn-default" id="q1">Generi Musicali</button>
				  <button type="button" class="btn btn-default" id="q2">Desiderio di musica live</button>
				  <button type="button" class="btn btn-default" id="q3">Grandi vs. Piccole venue</button>
				  <button type="button" class="btn btn-default" id="q4">Clubbing</button>
				  <!--button type="button" class="btn btn-default" id="q5">Destinazioni</button-->
				</div>

			</div>
			<div class="col-md-2">
				Select period:<br />
				<select id="modality">
					<option value="day">Day</option>
					<option value="week">Week</option>
					<option value="month">Month</option>
					<option value="all">All</option>
				</select>
			</div>
		</div>
	</div>
</div>



<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">Risultati</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12" id="results">
			</div>
		</div>
	</div>
	<div class="panel-body" id="explanation">
	</div>
</div>


<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">Export</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12" id="export-div">
			</div>
		</div>
	</div>
</div>


<script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=visualization&sensor=false"></script>