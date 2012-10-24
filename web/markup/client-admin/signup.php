<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Health Care Abroad - Login Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="HTML5 Admin Health Care Abroad Template">
<meta name="author" content="healthcare">

 <!-- stylesheet -->
 <link rel="stylesheet" href="css/bootstrap.min.css">
 <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
 <link rel="stylesheet" href="css/hca-style.css">


 <!-- Le fav and touch icons -->
  <link rel="shortcut icon" href="images/ico/favicon.ico">


<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

</head>

<body>


<div class="container">
	<div class="span5 signupbox">
		
			<div class="medicalbox-big"><img src="images/medical-clip.png" alt="Medical Clip" longdesc="#"></div>
			<a href="index.html"><h1>HEALTHCARE ABROAD</h1></a>
			<h3>Create an account.</h3>
			<p>Please provide the correct information below, and we will create an exclusive medical account listing for you.</p>
			<div class="row-fluid">
			  							
			  									<form action="#" method="POST" class="form">
			  							<fieldset>
			  									
			  									<label for="institution name"><b>Name of the Institution</b></label>
			  									<div >
			  									<div class="input-prepend"><input name="log" type="text" id="log inputIcon" value="" class="username span12" /></div>
			  									</div>
			  									</fieldset>
			  									</form>
			  							
			  					<h5>Create USER account.</h5>		
			  								<form action="#" method="POST" class="form">
			  		<fieldset>
			  				
			  						  				
			  			<label for="username">email <span><small style="font-size:85% ; color: #ccccc;">&nbsp;(your own email not the email of the institution)</small>
			  			</span></label>
			  					  			
			  				<div class="input-prepend"><input name="log" type="text" id="log inputIcon" value="" class="username span12" /></div>
			  			
			  			
			  			<label for="password">password</label>
			  						  				<div class="input-prepend"><input name="log" type="password" id="log inputIcon" value="" class="username span12" /></div>
			  			<ul class="alert-success">
			  				<li>Strong password</li>
			  			</ul>
			  			
			  			
			  			
			  
			  		<label for="re type password">re-type password</label>
			  		
			  		<div class="input-prepend"><input name="log" type="password" id="log inputIcon" value="" class="username span12" /></div>
			  		
			  		
			  
			  		
			  		
			  	
			  		
			  					
			  					<label for="password">Types of Institution</label>
			  				<div id="tabs">
			  				    <div id="nav">
			  				      <label><input type="radio" name="tab" value="pkfrom" class="div1" />Hospital or Clinic belonging to a Larger Group / Network</label>
			  				    
			  				      		      
			  				    
			  				    
			  				      <label><input type="radio" name="tab" value="pkfrom" class="div2" />Independent hospital or clinic</label>
			  				   
			  				  
			  				    
			  				      <label><input type="radio" name="tab" value="pkfrom" class="div3" />Medical Tourism Facilitator / Agent</label>
			  				
			  				    </div>
			  				
			  				   
			  				
			  				    
			  				    
			  				      
			  				    
			  				    
			  				  </div>
			  				
			  				  <script type="text/javascript" charset="utf-8">
			  				    (function(){
			  				      var tabs =document.getElementById('tabs');
			  				      var nav = tabs.getElementsByTagName('input');
			  				
			  				      /*
			  				      * Hide all tabs
			  				      */
			  				      function hideTabs(){
			  				        var tab = tabs.getElementsByTagName('div');
			  				        for(var i=0;i<=nav.length;i++){
			  				          if(tab[i].className == 'tab'){
			  				            tab[i].className = tab[i].className + ' hide';
			  				          }
			  				        }
			  				      }
			  				
			  				      /*
			  				      * Show the clicked tab
			  				      */
			  				      function showTab(tab){
			  				        document.getElementById(tab).className = 'tab'
			  				      }
			  				
			  				      hideTabs(); /* hide tabs on load */
			  				
			  				      /*
			  				      * Add click events
			  				      */
			  				      for(var i=0;i<nav.length;i++){
			  				        nav[i].onclick = function(){
			  				          hideTabs();
			  				          showTab(this.className);
			  				        }
			  				      }
			  				    })();
			  				  </script>
			  					
			  		
			  		</fieldset>
			  		
			  		<br/>
			  		
			  		  <label><input type="checkbox" name="Terms" value="Terms and Conditions" />I read and agree the <a href="#">Terms and Conditions</a>.</label>
			  		   
			  		
			  		<input type="submit" name="Submit" value="SIGN-UP" class="span12 btn-large btn-primary" />
			  		</form>		  
		</div>
		
		
	</div>
	
	
	
	</div>
<?php include("inc.footer.php"); ?>