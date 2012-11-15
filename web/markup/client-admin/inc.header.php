<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8">
						<title>Admin Health Care Abroad - Login Page</title>
						<meta name="viewport" content="width=device-width, initial-scale=1.0">
						<meta name="description" content="HTML5 Admin Health Care Abroad Template">
						<meta name="author" content="healthcare">
						<!-- stylesheet -->
						<!--<link rel="stylesheet" href="css/bootstrap.css">
						
						  	
						
						  	<link rel="stylesheet" href="css/bootstrap-responsive.css">-->
						<link rel="stylesheet" href="css/bootstrap.min.css">
						<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
						<link rel="stylesheet" href="css/institution/hca-style.css">
						<!-- Le fav and touch icons -->
						<link rel="shortcut icon" href="images/favicon.ico">
						<script src="js/jquery-1.8.2.js"></script>
						<script src="js/jquery-ui-1.9.0.custom.min.js"></script>
						<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
						<!--[if lt IE 9]>
							<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
						<![endif]-->
						<!-- stat graphs -->
						<script src="js/institution/RGraph.common.core.js"></script>
						<script src="js/institution/RGraph.common.dynamic.js"></script>
						<script src="js/institution/RGraph.common.tooltips.js"></script>
						<script src="js/institution/RGraph.common.effects.js"></script>
						<script src="js/institution/RGraph.common.key.js"></script>
						<script src="js/institution/RGraph.line.js"></script>
						<!--star rating-->
						<style type="text/css">
							/* star rating code - use lists because its more semantic */
										/* No javascript required */
										/* all the stars are contained in one matrix to solve rollover problems with delay */
										/* the background position is just shifted to reveal the correct image. */
										/* the images are 16px by 16px and the background position will be shifted in negative 16px increments */
										/*  key:  B=Blank : O=Orange : G = Green * /
										/*..... The Matrix ....... */
										/* colours ....Background position */
										/* B B B B B - (0 0)*/
										/* G B B B B - (0 -16px)*/
										/* G G B B B - (0 -32px)*/
										/* G G G B B - (0 -48px)*/
										/* G G G G B - (0 -64px)*/
										/* G G G G G - (0 -80px)*/
										/* O B B B B - (0 -96px)*/
										/* O O B B B - (0 -112px)*/
										/* O O O B B - (0 -128px)*/
										/* O O O O B - (0 -144px)*/
										/* O O O O O - (0 -160px)*/
										
										
										/* the default rating is placed as a background image in the ul */
										/* use the background position according to the table above to display the required images*/
										.rating{
											width:80px;
											height:16px;
											margin:0 0 20px 0;
											padding:0;
											list-style:none;
											clear:both;
											position:relative;
											background: url(images/institution/star-matrix.gif) no-repeat 0 0;
										}
										/* add these classes to the ul to effect the change to the correct number of stars */
										.nostar {background-position:0 0}
										.onestar {background-position:0 -16px}
										.twostar {background-position:0 -32px}
										.threestar {background-position:0 -48px}
										.fourstar {background-position:0 -64px}
										.fivestar {background-position:0 -80px}
										ul.rating li {
											cursor: pointer;
										 /*ie5 mac doesn't like it if the list is floated*/
											float:left;
											/* end hide*/
											text-indent:-999em;
										}
										ul.rating li a {
											position:absolute;
											left:0;
											top:0;
											width:16px;
											height:16px;
											text-decoration:none;
											z-index: 200;
										}
										ul.rating li.one a {left:0}
										ul.rating li.two a {left:16px;}
										ul.rating li.three a {left:32px;}
										ul.rating li.four a {left:48px;}
										ul.rating li.five a {left:64px;}
										ul.rating li a:hover {
											z-index:2;
											width:80px;
											height:16px;
											overflow:hidden;
											left:0;	
											background: url(images/star-matrix.gif) no-repeat 0 0
										}
										ul.rating li.one a:hover {background-position:0 -96px;}
										ul.rating li.two a:hover {background-position:0 -112px;}
										ul.rating li.three a:hover {background-position:0 -128px}
										ul.rating li.four a:hover {background-position:0 -144px}
										ul.rating li.five a:hover {background-position:0 -160px}
										/* end rating code */
									 
										
									
										
												* {
													border: none;
													padding: 0;
													margin: 0;
													outline: none;
												}
										
												}
											
												span.add {
													display: block;
													font-family: Arial;
													margin-bottom: 12px;
													cursor: pointer;
												}
												span.add:hover {
													color: #aaa;
												}
												.active span.add {
													display: none;
												}
												textarea.description {
													display: none;
													border: 1px solid #aaa;
													padding: 2px;
													width: 200px;
													height: 70px;
												}
												.active textarea.description {
													display: block;
												}
						</style>
						<script type="text/javascript">
							$(document).ready(function(){
											$('.add').click(function(e){
												$('.specialization').addClass('active');
											});
											$('.description').blur(function(e){
												var val = $(this).val();
												//alert(val)
												$('.specialization').removeClass('active');
												$('.add').text(val);
											});
											
							
											$( "#specializationAccordion" ).accordion();
											});
						</script>
						
						
					   <script>
					   // increase the default animation speed to exaggerate the effect
					   $.fx.speeds._default = 1000;
					   $(function() {
					       $( "#dialog" ).dialog({
					           autoOpen: false,
					           show: "blind",
					           hide: "blind"
					       });
					
					       $( "#opener" ).click(function() {
					           $( "#dialog" ).dialog( "open" );
					           return false;
					       });
					   });
					   </script>
						
					</head>
					<body>Top navigation bar
						<div class="navbar navbar-fixed-top">
							<div class="navbar-inner">
								<div class="container">
									<a href="main.php" class="brand"><img src="images/institution/HCA-logo-small.png" alt="Health Care Abroad Logo" longdesc="#" title=""> HEALTHCARE ABROAD</a>
									<div class="pull-right">
										<span class="userbox pull-left">you are logged in as
											<i class="icon-user">
												<a href="#"></i>marcjacob@gmail</a>
												<!-- <a href="accountsettings.html"><img src="images/institution/Setting-icon.png"></a>
											   		<a href="logout.html"><img src="images/institution/logout-icon.png"></a>-->
										</span>
										<nav>
											<ul class="menu pull-right">
												<li>
													<a href="#"><i class="icon-list"></i><span style="margin-left:5px ;">MENU</span></a>
													<ul>
														<li>
															<a href="#"><i class="icon-home"></i> Main Dashboard</a>
														</li>
														<li>
															<a href="medcenterlisting.php"><i class="icon-star"></i> Medical Center</a>
														</li>
														<li>
															<a href="specialistlisting.php"><i class="icon-user"></i> Doctors Listing</a>
														</li>
														<li>
															<a href="#"><i class="icon-user"></i> Staff Listing</a>
														</li>
														<li>
															<a href="#"><i class="icon-certificate"></i> Affiliations / Certifications</a>
														</li>
														<li>
															<a href="gallery.php"><i class="icon-picture"></i> Media Gallery</a>
														</li>
														<li>
															<a href="help.php"><i class="icon-info-sign"></i> Help</a>
														</li>
														<li>
															<a href="#"><i class="icon-off"></i> Logout</a>
														</li>
													</ul>
												</li>
											</ul>
										</nav>
									</div>
								</div>
							</div>
						</div>
						<div class="row-fluid">
							<div class="institutionbox">
								<div class="container">
									<div class="institutionbox-inner">
										<div class="navbar-inner">
											<div class="span6">
												<div class="span3">
													<div class="photo-group">
														<img alt="..." src="images/institution/institution-logo.gif" />
														<span class="label photo-label" style="margin-left:100px; position: absolute; margin-top: -25px;">
															<a href="#" data-placement="right" rel="tooltip" href="#" data-original-title="Manage Institution Profile Photo"><i class="icon-picture"></i></a>
														</span>
													</div>
												</div>
												<div class="span9">
													<h3>Institution Name Here</h3>
													<a href="#"><h5>Manage Your Accreditations, Affiliations and Awards</h5></a>
												</div>
											</div>
											<div class="span6">
												<a href="#"><h5>Manage Your Institution Profile</h5></a>
												<!--<p><a href="#">[Manage your Institutional Profile here]</a></p>
						
											
						
												<p><a href="#">[add your locations here]</a></p>
						
											
						
												<p><a href="#">[add your awards, certification and affiliation]</a></p>
						
											
						
												<h4><a href="adding-center.html">ADD YOUR MEDICAL CENTER here!</a></h4>-->
												<p>+6332-2555555 (contact number)</p>
												<p>Osmeña Boulevard Cebu City, Philippines 6000 (address)</p>
												<p>www.site.com (address)</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<section id="feature">
								<div class="container">
									<div class="row">
										<div class="span12">
											<div id="myContent">
												<!-- <nav>
						
								
						
													<ul>
						
						
						
														<li><a href="#"><h1>1</h1><h5>Manage your Institution Profile</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
						
						
						
														<li><a href="#"><h1>2</h1><h5>Add your Medical Center</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
						
						
						
														<li><a href="#"><h1>3</h1><h5>Add your world Class Specialist (Doctors)</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
						
						
						
														<li><a href="#"><h1>4</h1><h5>Uploads Photos, Videos etc. (Media Files)</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
						
						
						
														<li><a href="#"><h1>5</h1><h5>Add your Affiliations, Awards & Certifications</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
						
						
						
								
						
													</ul>
						
							</nav>-->
												<div class="box-container-toggle stepbox">
													<span class="span11">
														<h5>Thank you for registering on Health Care Abroad.com. If you are still new to things, we’ve provided a few walkthroughs to get you started.</h5>
													</span>
													<div class="clearfix"></div>
													<!-- <div class="stepsbox">
																
																		<ul class="stepsbox">
																					  
																				
																									<li class="active"><a href="#"><h1>1</h1><h5>Manage your Institution Profile</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
																				
																									<li><a href="#"><h1>2</h1><h5>Add your Medical Center</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
																				
																									<li><a href="#"><h1>3</h1><h5>Add your world Class Specialist (Doctors)</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
																				
																									<li><a href="#"><h1>4</h1><h5>Uploads Photos, Videos etc. (Media Files)</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
																				
																									<li><a href="#"><h1>5</h1><h5>Add your Affiliations, Awards & Certifications</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a></li>
																								   
																						  		
																			
																								</ul> 
																								<div class="clearfix"></div>				
																												
																		</div>-->
													<ul class="nav nav-tabs nav-stacked">
														<li class="active">
															<a href="#"><h1>1</h1><h5>Manage your Institution Profile</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a>
														</li>
														<li>
															<a href="#"><h1>2</h1><h5>Add your Medical Center</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a>
														</li>
														<li>
															<a href="#"><h1>3</h1><h5>Add your world Class Specialist (Doctors)</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a>
														</li>
														<li>
															<a href="#"><h1>4</h1><h5>Uploads Photos, Videos etc. (Media Files)</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a>
														</li>
														<li>
															<a href="#"><h1>5</h1><h5>Add your Affiliations, Awards & Certifications</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean bibendum, est at congue gravida...</p></a>
														</li>
													</ul>
												</div>
												<div class="clear"></div>
											</div>
										</div>
									</div>
								</div>
							</section>
							<!--end feature-->
							
							<div class="container">
							
					
							
								<!-- Bread Crumb Navigation -->
						
								 
								    <ul class="breadcrumb">
								      <li>
								        <a href="#">Home</a> <span class="divider">/</span>
								      </li>
								      <li>
								        <a href="#">Library</a> <span class="divider">/</span>
								      </li>
								      <li class="active">Data</li>
								    </ul>
							
							
							</div>
						</div>