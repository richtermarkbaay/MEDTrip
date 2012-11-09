<?php include("inc.header.php"); ?>
<div class="container">
	<div class="maincontent">
		
		<!--sidebar start-->
		<?php include("inc.sidebar.php"); ?>		
		<!--sidebar end-->
		
			<!--main tab start-->
			<div class="span9">
			<h2>Manage Institution Profile</h2>
			 <div class="addcenterbox"><div class="row-fluid">
			    <div class="span12">
			            <div class="tabbable">
							<!-- /.menu - tab start -->
							<ul class="nav nav-tabs">
								<li class="active"><a data-toggle="tab" href="#tabs1-pane1"> 1 - Medical Center</a></li>
								<li class=""><a data-toggle="tab" href="#tabs1-pane2"> 2 - Specialization</a></li>
								<li class=""><a data-toggle="tab" href="#tabs1-pane3"> 3 - Select Treatments</a></li>
								<li class=""><a data-toggle="tab" href="#tabs1-pane4"> 4 - Assigned Doctors</a></li>
								<li class=""><a data-toggle="tab" href="#tabs1-pane5"> 5 - Preview</a></li>
							</ul><!-- /.menu - tab end -->
							<!-- /.tab-content start -->
							<div class="tab-content">
								<div id="tabs1-pane1" class="tab-pane active">
												
													<form>
															<h3>Medical Center Information</h3>		
																					
										<label for="username">Medical Center Name</label>
										<div >
											<div class="input-prepend"><input name="log" type="text" id="log inputIcon" value="" class="username span8" /></div>
										</div>
																						
												
																						
														<label for="password">Medical Center Short Description</label>
															<div >
																<div class="input-prepend"><textarea class="field span12" id="textarea" rows="6" placeholder=" Add a Short Descriptions about the Medical Center"></textarea>
																</div>
															</div>
																						
																						
															<div><input type="submit" name="Submit" value="next" class="btn btn-large btn-primary" /></div>
																						
													</form>
												</div>
								<div id="tabs1-pane2" class="tab-pane">
									
											<form>
											
											<h3>Specialization and its Description</h3>
											<p>please add all specialization under the center name</p>
											
												<label for="Center Name">Specialization</label>
												<div >
													<div class="input-prepend">
														<select size="1" id="field_16" name="field_16" class="text_input">
															<option value="Abdominal Medicine">Abdominal Medicine</option>
															<option value="Acute Medicine">Acute Medicine</option>
															<option value="Adolescent  Medicine">Adolescent  Medicine</option>
															<option value="Alcohol  &amp;  Drug  Dependency/Rehab">Alcohol  &amp;  Drug  Dependency/Rehab</option>
															<option value="Allergy">Allergy</option>
															<option value="Anaesthesiology">Anaesthesiology</option>
							
														</select>
													</div>
												</div>
												<label>Specialization Details</label>
												<div >
													<div class="input-prepend"><textarea class="field span12" id="textarea" rows="6" placeholder=" Add a Short Descriptions about the Specialization"></textarea>
													</div>
												</div>
												
												
												
												<input type="submit" name="Submit" value="Add Specialization" class="btn btn-success" />
												
												<div class="clear">&nbsp;</div>											
												<hr>
																							
	
												
												
													
															<h4>specialization 1</h4>
															<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
															labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
															nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
															esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in 
															culpa qui officia deserunt mollit anim id est laborum.</p>
													
												
												
												<div class="span7">
												
													<span class="span4"><input type="submit" name="Submit" value="Edit Specialization" class="btn btn-info" /></span>
													<span class="span6"><input type="submit" name="Submit" value="Add Another Specialization" class="btn btn-success" /></span>
												
												</div>
												
												<div class="clear">&nbsp;</div>											
												<hr>
												<input type="submit" name="Submit" value="next" class="btn btn-large btn-primary" />
												
											</form>
								</div>
								<div id="tabs1-pane3" class="tab-pane">
									
									<form>
									<h3>Select Treatments</h3>
									<div>
										
										
										<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
										<script type="text/javascript">
										function toggleDiv(divId) {
										   $("#"+divId).toggle();
										}
										</script>
										
										
										
										<div class="treatmentbox">
										<div class="box" id="box-1">
										  	<h4>Specialization 1
										  		<a href="javascript:toggleDiv('menu1');">
										  		<span><img src="images/arrow2.png"></span>
										  		</a>
										  	</h4>         
										</div>
										
											<div id="menu1">
												<div class="box-container-toggle">
										    		<div class="boxcontent">
										    			<h5>Sub-specialization</h5>
										  
										    	     	<label><input type="checkbox" />Treatment 1</label>
										    			<label><input type="checkbox" />Treatment 2</label>
										    			<label><input type="checkbox" />Treatment 3</label>   
										    		 </div>
										   			<div class="clear"></div>
										    	</div>
											</div>
											
										</div>
										</div>
													
									
									<hr>
									
									<div class="treatmentbox">
									<div class="box" id="box-1">
									  	<h4>Specialization 2
									  		<a href="javascript:toggleDiv('menu3');">
									  		<span><img src="images/arrow2.png"></span>
									  		</a>
									  	</h4>         
									</div>
									
										<div id="menu3">
											<div class="box-container-toggle">
									    		<div class="boxcontent">
									    			<h5>Sub-specialization</h5>
									  
									    	     	<label><input type="checkbox" />Treatment 1</label>
									    	     	<label><input type="checkbox" />Treatment 2</label>
									    	     	<label><input type="checkbox" />Treatment 3</label>  
									    		 </div>
									   			<div class="clear"></div>
									    	</div>
										</div>
										
									</div>
									
									
									
									<div>
										
										
										<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
										<script type="text/javascript">
										function toggleDiv(divId) {
										   $("#"+divId).toggle();
										}
										</script>
										
										
										<hr>
										<div class="treatmentbox">
										<div class="box" id="box-1">
										  	<h4>Specialization 3
										  		<a href="javascript:toggleDiv('menu2');">
										  		<span><img src="images/arrow2.png"></span>
										  		</a>
										  	</h4>         
										</div>
											<div id="menu2">
												<div class="box-container-toggle">
										    		<div class="boxcontent">
										    	<h5>Sub-specialization</h5>
										  
										    	 
										    	<label><input type="checkbox" />Hospital or Clinic belonging to a Larger Group / Network</label>
										    	<label><input type="checkbox" />Hospital or Clinic belonging to a Larger Group / Network</label>
										    	<label><input type="checkbox" />Hospital or Clinic belonging to a Larger Group / Network</label>   
										    	
										    	
										
										    </div>
										   			<div class="clear"></div>
										    	</div>
											</div>
											<div class="boxcontent2"><h5>Treatments:</h5><p>Sub-specialization - Treatment 1</p></div>
										</div>	
										</div>
									
									
									<div>
										
										
										<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
										<script type="text/javascript">
										function toggleDiv(divId) {
										   $("#"+divId).toggle();
										}
										</script>
										
										
										
									
										</div>
																				
																				
										<hr>
										<input type="submit" name="Submit" value="next" class="btn btn-large btn-primary" />
									</form>
									
								</div>
								<div id="tabs1-pane4" class="tab-pane">
					
						
											<form>
											
											<h3>Medical Specialist Assigned in this Medical Center</h3>
											
											<label for="username"></label>
											<div >
												<div class="input-prepend"><input name="log" type="text" id="log inputIcon" value="" class="username span8" /></div>
											</div>
													
											
											   <h4>List of All Doctors</h4>
											    <ul class="nav nav-tabs nav-stacked">
											    	<li>Doctor 1</li>
											    	<li>Doctor 2</li>
											    </ul>
											    
											    
											<div><input type="submit" name="Submit" value="next" class="btn btn-large btn-primary" /></div>
											</form>																			
										</div>
								<div id="tabs1-pane5" class="tab-pane">
								
									
														<form>
															<h4>Preview</h4>
															<p>same design with Front End Listing</p>
														    
															<div class="span9">
															
																<span class="span4"><input type="submit" name="Submit" value="Edit Medical Center Details" class="btn btn-info" /></span>
																<span class="span5"><input type="submit" name="Submit" value="Add Another Medical Center" class="btn btn-success" /></span>
															
															</div>
															<div class="clear">&nbsp;</div>											
															<hr>
															
																<div><input type="submit" name="Submit" value="Done" class="btn btn-large btn-primary"/></div>
														    
																<!--<div><input type="submit" name="Submit" value="edit" class="btn btn-large btn-primary" /></div>-->
														</form>																			
								</div>
							</div><!-- /.tab-content end -->
						</div>
			    </div><!--/span 12 end-->
			 </div><!--/row-fluid end--></div>
		</div>
			<!--main tab start-->
		
			<div class="clear"></div>
		
	
	</div>	
</div>



<?php include("inc.footer.php"); ?>
