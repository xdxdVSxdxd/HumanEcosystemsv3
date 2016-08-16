<h1 id="insta"></h1>
<p>Copu the token above and paste it in the previous window of your browser, in the "Instagram App Token".</p>
<script>
	$( document ).ready(function() {
		if(window.location.hash) {
      		var hash = window.location.hash.substring(1); 
      		$("#insta").text(hash);
      	} else {
      		var hash = "No token returned. Check your Instagram parameters."
      		$("#insta").text(hash);
      	}
	});
</script>