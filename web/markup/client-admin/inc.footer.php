<div class="container">
<br/>
<hr>
<footer>
	<p>&copy; <a href="#">www.HealthCareAbroad.com</a>. All rights reserved.</p>
</footer>
</div>
</div>

<!--/.fluid-container--> 
<!-- javascript Templates
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 

<!-- Le javascript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<!-- Google API --> 
<script type="text/javascript" src="http://www.google.com/jsapi"></script> 


<!-- jQuery --> 
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> 



<!-- Bootstrap --> 
<script src="js/bootstrap.min.js"></script> 
<script type="text/javascript">
	$('.add').click(function(e){
		$('.specialization').addClass('active');
	});
	$('.description').blur(function(e){
		var val = $(this).val();
		//alert(val)
		$('.specialization').removeClass('active');
		$('.add').text(val);
	})
</script>







</body>
</html>
