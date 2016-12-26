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


<div class="row">
	<div class="col-md-12">
		<h2>General</h2>
	</div>
</div>
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
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-12">
		<h2>Emotions</h2>
	</div>
</div>
<div class="row">
	<div class="col-md-12" id="emotions">
	</div>
</div>


<div class="row">
	<div class="col-md-12">
		<h2>Activity</h2>
	</div>
</div>
<div class="row">
	<div class="col-md-12" id="activity">
	</div>
</div>


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


<div class="row">
	<div class="col-md-6 col-xs-6" ></div>
	<div class="col-md-6 col-xs-6" id="emotions-map-legend"></div>
</div>


<div class="row">
	<div class="col-md-12">
		<h2>Topics</h2>
	</div>
</div>
<div class="row">
	<div class="col-md-12" id="topics">
	</div>
</div>


<div class="row">
	<div class="col-md-12">
		<h2>Topic Relations</h2>
	</div>
</div>
<div class="row">
	<div class="col-md-12" id="topic-relations">
		<a href="javascript:getTopicRelations(true);" class="btn btn-primary">OPEN</a>
	</div>
</div>



<div class="row">
	<div class="col-md-12">
		<h2>Top Users</h2>
	</div>
</div>
<div class="row">
	<div class="col-md-12" id="topusers">
	</div>
</div>



<div class="row">
	<div class="col-md-12">
		<h2>User Relations</h2>
	</div>
</div>
<div class="row">
	<div class="col-md-12" id="user-relations">
		<a href="javascript:getUserRelations(true);" class="btn btn-primary">OPEN</a>
	</div>
</div>
<script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=visualization&sensor=false"></script>