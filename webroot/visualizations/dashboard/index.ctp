<div class="row">
	<div class="col-md-10">
		<h1>Dashboard</h1>
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


<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">General</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-3" id="stats">
					</div>
					<div class="col-md-9">
						<div class="row">
							<div class="col-md-12" id="sentiment">


								<div id="sentimentresults" class="padded">
									<div class="row">
										<div class="col-md-4 col-xs-4 tcenter">
											<i class="sentiment-icon icon-smile big-icon"></i>
										</div>
										<div class="col-md-4 col-xs-4 tcenter">
											<i class="sentiment-icon icon-meh big-icon"></i>
										</div>
										<div class="col-md-4 col-xs-4 tcenter">
											<i class="sentiment-icon icon-frown big-icon"></i>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4 col-xs-4 sentiment-res" id="positive"></div>
										<div class="col-md-4 col-xs-4 sentiment-res" id="neutral"></div>
										<div class="col-md-4 col-xs-4 sentiment-res" id="negative"></div>
									</div>
								</div>


							</div>
						</div>
						<div class="row">
							<div class="col-md-12" id="sentiment-time">
							</div>
						</div>
						<div class="row">
							<div class="col-md-12" id="sentiment-export">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">Emotions</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12" id="emotions">
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" id="emotions-export">
			</div>
		</div>
	</div>
</div>


<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">Activity</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12" id="activity">
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" id="activity-export">
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">Geography</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-6 col-xs-6">
				<h2>Map</h2>
			</div>
			<div class="col-md-6 col-xs-6">
				<h2>Emotions Map</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 col-xs-6" id="map">
			</div>
			<div class="col-md-6 col-xs-6" id="emotions-map">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-xs-6" ></div>
		<div class="col-md-6 col-xs-6" id="emotions-map-legend"></div>
	</div>
	<div class="row">
		<div class="col-md-6 col-xs-6" id="map-export">
		</div>
		<div class="col-md-6 col-xs-6" id="emotions-map-export">
		</div>
	</div>
</div>




<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">Topics</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12" id="topics">
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" id="topics-export">
			</div>
		</div>
	</div>
</div>


<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">Topic Relations</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12" id="topic-relations">
				<a href="javascript:getTopicRelations(true);" class="btn btn-primary">OPEN</a>
				<a href="javascript:exportTopicRelations();" class="btn btn-primary">Export</a>
			</div>
		</div>
	</div>
</div>


<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">Top Users</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12" id="topusers">
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" id="topusers-export">
			</div>
		</div>
	</div>
</div>



<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">User Relations</h3>
  	</div>
  	<div class="panel-body">
		<div class="row">
			<div class="col-md-12" id="user-relations">
				<a href="javascript:getUserRelations(true);" class="btn btn-primary">OPEN</a>
				<a href="javascript:exportUserRelations();" class="btn btn-primary">Export</a>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=visualization&sensor=false"></script>