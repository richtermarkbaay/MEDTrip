<?php include("inc.header.php"); ?>
<div class="container">
	<div class="maincontent">
		
		<!--sidebar start-->
			<?php include("inc.sidebar.php"); ?>		
		<!--sidebar end-->
		
			<!--main tab start-->
			<div class="span9 title">
			<h4>Add Medical Center</h4>
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
                                                                        <div>
                                                                            <label for="username">Medical Center Name</label>
                                                                            <input name="log" type="text" id="log inputIcon" value="" class="username span8" />
                                                                            <!--<ul>
                                                                                <li>Please enter a medical center name.</li>
                                                                            </ul>-->
                                                                        </div>
                                                                    
                                                                        <div>
                                                                            <label for="password">Medical Center Short Description</label>
                                                                            <textarea class="field span12" id="textarea" rows="6" placeholder=" Add a Short Descriptions about the Medical Center"></textarea>
                                                                            <!--<ul>
                                                                                <li>Please enter a short description.</li>
                                                                            </ul>-->
                                                                        </div>

                                                                        <div><input type="submit" name="Submit" value="Next" class="btn btn-large btn-primary" /></div>
                                                                </form>
                                                            </div>
								<div id="tabs1-pane2" class="tab-pane">						
											<form>
											
											<h3>Specialization and its Description</h3>
											<p>please add all specialization under the center name</p>
											<div>
                                                                                            <label for="Center Name">Specialization</label>
                                                                                            <select size="1" id="field_16" name="field_16" class="text_input">
                                                                                                    <option value="Abdominal Medicine">Abdominal Medicine</option>
                                                                                                    <option value="Acute Medicine">Acute Medicine</option>
                                                                                                    <option value="Adolescent  Medicine">Adolescent  Medicine</option>
                                                                                                    <option value="Alcohol  &amp;  Drug  Dependency/Rehab">Alcohol  &amp;  Drug  Dependency/Rehab</option>
                                                                                                    <option value="Allergy">Allergy</option>
                                                                                                    <option value="Anaesthesiology">Anesthesiology</option>
                                                                                            </select>
                                                                                            <!--<ul>
                                                                                                <li>Please select a specialization.</li>
                                                                                            </ul>-->
                                                                                        </div>
                                                                                        
                                                                                        <div>
                                                                                            <label>Specialization Details</label>
                                                                                            <textarea class="field span12" id="textarea" rows="6" placeholder=" Add a Short Descriptions about the Specialization"></textarea>
                                                                                            <!--<ul>
                                                                                                <li>Please enter specialization details.</li>
                                                                                            </ul>-->
                                                                                        </div>
                                                                                        
                                                                                        <input type="submit" name="Submit" value="Add Specialization" class="btn btn-success" />
												
                                                                                        <div class="clear">&nbsp;</div>											
                                                                                        <hr>
                                                                                            <h5>specialization 1</h5>
                                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
                                                                                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
                                                                                            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
                                                                                            esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in 
                                                                                            culpa qui officia deserunt mollit anim id est laborum.</p>
                                                                                            
                                                                                            <div class="pull-left span7">
                                                                                                <span class="span5"><a href="#Edit" role="button" class="btn btn-primary" data-toggle="modal">Edit Specialization</a></span>
                                                                                                <span class="span6"> <a href="#myModal" role="button" class="btn btn-success" data-toggle="modal">Add Another Specialization</a></span>
                                                                                            </div>
                                                                                            
                                                                                            
                                                                                           
                                                                                             
                                                                                            <!-- Modal -->
                                                                                            <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                                            	<div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                                            <h3 id="myModalLabel">Add Another Specialization</h3>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                            <div>
                                                                                            <select size="1" id="field_16" name="field_16" class="text_input">
                                                                                                    <option value="Abdominal Medicine">Abdominal Medicine</option>
                                                                                                    <option value="Acute Medicine">Acute Medicine</option>
                                                                                                    <option value="Adolescent  Medicine">Adolescent  Medicine</option>
                                                                                                    <option value="Alcohol  &amp;  Drug  Dependency/Rehab">Alcohol  &amp;  Drug  Dependency/Rehab</option>
                                                                                                    <option value="Allergy">Allergy</option>
                                                                                                    <option value="Anaesthesiology">Anesthesiology</option>
                                                                                            </select>
                                                                                                <label>Specialization Details</label>
                                                                                                <textarea class="field span12" id="textarea" rows="6" placeholder=" Add a Short Descriptions about the Specialization"></textarea>
                                                                                                <!--<ul>
                                                                                                    <li>Please enter specialization details.</li>
                                                                                                </ul>-->
                                                                                            </div>
                                                                                            
                                                                                            
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                            
                                                                                            <button class="btn btn-success">Add Specialization</button>
                                                                                            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                                            
                                                                                            </div>
                                                                                            </div>
                                                                                            
                                                                                            
                                                                                            
                                                                                            <!-- Modal -->
                                                                                            <div id="Edit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                                            	<div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                                            <h3 id="myModalLabel">Edit Specialization</h3>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                            <div>
                                                                                                <label>Specialization Details</label>
                                                                                                <textarea class="field span12" id="textarea" rows="6" placeholder=" Add a Short Descriptions about the Specialization">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris  nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in 
                                                                                                culpa qui officia deserunt mollit anim id est laborum.</textarea>
                                                                                                <!--<ul>
                                                                                                    <li>Please enter specialization details.</li>
                                                                                                </ul>-->
                                                                                            </div>
                                                                                            
                                                                                            
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                            
                                                                                            <button class="btn btn-success">Save Changes</button>
                                                                                            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                                            
                                                                                            </div>
                                                                                            </div>
												
                                                                                            <div class="clear">&nbsp;</div>											
                                                                                            <hr>
                                                                                            
												<input type="submit" name="Submit" value="Next" class="btn btn-large btn-primary" />
											</form>
								</div>
                                                            
								<div id="tabs1-pane3" class="tab-pane">
                                                                    <form>
									<h3>Select Treatments</h3>
									
                                                           
                                                          <div class="treatmentbox">
                                                          	<div class="accordion" id="accordion2">
                                                                		<div class="accordion-group">
                                                                 			  <div class="accordion-heading">
                                                                  			   <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                                                                       			<h5>Specialization</h5> 
                                                                     				</a>
                                                                   		</div>
                                                                   		<div id="collapseOne" class="accordion-body collapse in">
                                                                     <div class="accordion-inner">
                                                                     		
                                                                     		
                                                                     		<div class="boxcontent">
                                                                     			<h5>Treatments (will only show once there has already been checked treatment)</h5>
                                                                     				<ul>
                                                                     					<li>List of All check Treatment here.</li>
                                                                     					<li>Sub-specialization - Treatment</li>
                                                                     				</ul>
                                                                     		</div>
                                                                     
                                                                 			<div class="boxcontent">
                                                                               <h5>Sub-Specialization</h5>
                                                                  
                                                                               <label><input type="checkbox" />Treatment 1</label>
                                                                               <label><input type="checkbox" />Treatment 2</label>
                                                                               <label><input type="checkbox" />Treatment 3</label>   
                                                                              <hr> 
                                                                             <h5>Sub-Specialization</h5>
                                                                               
                                                                             <label><input type="checkbox" />Treatment 1</label>
                                                                             <label><input type="checkbox" />Treatment 2</label>
                                                                              <label><input type="checkbox" />Treatment 3</label>  
                                                                              <hr>              
                                                                             <h5>Sub-Specialization</h5>
                                                                                            
                                                                             <label><input type="checkbox" />Treatment 1</label>
                                                                             <label><input type="checkbox" />Treatment 2</label>
                                                                             <label><input type="checkbox" />Treatment 3</label>  
                                                                              <hr>                           
                                                                             <h5>Sub-Specialization</h5>
                                                                                                         
                                                                             <label><input type="checkbox" />Treatment 1</label>
                                                                             <label><input type="checkbox" />Treatment 2</label>
                                                                             <label><input type="checkbox" />Treatment 3</label>  
                                                                             
                                                                          </div>
                                                                          
                                                                          
                                                                          
                                                                          
                                                                     </div>
                                                                   </div>
                                                                 		</div>
                                                                 	</div>				
                                                          	</div>
                                                          
                                                                  <hr>
                                                                  
                                                                  
                                                                  <div class="treatmentbox">
                                                                      	<div class="accordion" id="accordion2">
                                                                        		<div class="accordion-group">
                                                                         			  <div class="accordion-heading">
                                                                          			   <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                                                                               			<h5>Specialization 2</h5> 
                                                                             				</a>
                                                                           		</div>
                                                                           		<div id="collapseTwo" class="accordion-body collapse">
                                                                             <div class="accordion-inner">
                                                                         			<div class="boxcontent">
                                                                                       <h5>Sub-specialization</h5>
                                                                          
                                                                                       <label><input type="checkbox" />Treatment 1</label>
                                                                                       <label><input type="checkbox" />Treatment 2</label>
                                                                                       <label><input type="checkbox" />Treatment 3</label>   
                                                                                  </div>
                                                                             </div>
                                                                           </div>
                                                                         		</div>
                                                                         	</div>				
                                                                  	</div> 
                                                           <br/>
                                                                        
										<input type="submit" name="Submit" value="Next" class="btn btn-large btn-primary" />
									</form>
									
								</div>
                                                            
                                                                <div id="tabs1-pane4" class="tab-pane">
                                                                    <form>
                                                                        <h3>Medical Specialist Assigned in this Medical Center</h3>
                                                                        <div>
                                                                            <label for="username"></label>
                                                                            <select>
                                                                              <option value="volvo">Search Medical Specialist</option>
                                                                              <option value="saab">Saab</option>
                                                                              <option value="opel">Opel</option>
                                                                              <option value="audi">Audi</option>
                                                                            </select> 
                                                                            <!--<ul>
                                                                                <li>Please enter a medical specialist.</li>
                                                                            </ul>-->
                                                                        </div>
                                                                        
                                                                         <div class="clearfix"></div><div style="margin-top: 10px; margin-bottom: 10px;"><input type="submit" name="Submit" value="Add Doctors" class="btn btn-large btn-primary" /></div>
                                                                         

                                                                        <h5>List of All Doctors</h5>
                                                                     <div class="treatmentbox"><div class="boxcontent">
                                                                                               				<div class="specialist">
                                                                                                           <div class="span6">
                                                                                                           	<div class="span3"><a href="#"><img class="member-box-avatar" src="images/institution/institution-logo.gif"></a></div>
                                                                                                           	<div class="span9"><h5>Dr. First Name Last Name</h5><p>Specialization</p><button class="btn btn-small btn-mini">View Specialist's Profile</button></div>
                                                                                                           </div>
                                                                     									<div class="span6">
                                                                     	<div class="span3"><a href="#"><img class="member-box-avatar" src="images/institution/institution-logo.gif"></a></div>
                                                                     	<div class="span9"><h5>Dr. First Name Last Name</h5><p>Specialization</p><button class="btn btn-small btn-mini">View Specialist's Profile</button></div>
                                                                     </div>
                                                                     									<div class="span6" style="margin-top: 10px;">
                                                                     	<div class="span3"><a href="#"><img class="member-box-avatar" src="images/institution/institution-logo.gif"></a></div>
                                                                     	<div class="span9"><h5>Dr. First Name Last Name</h5><p>Specialization</p><button class="btn btn-small btn-mini">View Specialist's Profile</button></div>
                                                                     </div>
                                                                     									<div class="span6" style="margin-top: 10px;">
                                                                     	<div class="span3"><a href="#"><img class="member-box-avatar" src="images/institution/institution-logo.gif"></a></div>
                                                                     	<div class="span9"><h5>Dr. First Name Last Name</h5><p>Specialization</p><button class="btn btn-small btn-mini">View Specialist's Profile</button></div>
                                                                     </div>
                                                                                                   </div>
                                                                                               </div>
                                                                                            </div>

                                                                        <div class="clearfix"></div><div style="margin-top: 10px; margin-left: 10px;"><input type="submit" name="Submit" value="Next" class="btn btn-large btn-primary" /></div>
                                                                    </form>																			
                                                                </div>
                                                            
								<div id="tabs1-pane5" class="tab-pane">	
                                                                    <form>
                                                                        <h3>Preview</h3>
                                                                        
																	
																		                   		
																		                   		
																		                   		                           
																		               		
																		                                                          	<div id="accordion2" class="accordion">
																		                                                                		<div class="accordion-group">
																		                                                                 			  <div class="accordion-heading">
																		                                                                  			   <a href="#collapseOne" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle">
																		                                                                       			<h5>Medical Center Name 1 <span> <button class="btn btn-small btn-mini">edit medical center entry</button></span></h5>         
																		                                                                     				</a>
																		                                                                   		</div>
																		                                                                        
																		                                                                   		<div class="accordion-body collapse in" id="collapseOne">
																		                                                                     <div class="accordion-inner">
																		                                                                     		
																		                                                                     		
																		                                                                     		<div class="boxcontent">
																		                                                                            
																		                                                                            
																		                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
																		                                                                                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
																		                                                                                            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
																		                                                                                            esse cillum dolore eu fugiat nulla pariatur.</p>
																		                                                                            
																		     <!--modal button to view medical specialist -->  <a data-toggle="modal" class="btn btn-primary" role="button" href="#MedicalSpecialist">View All Medical Center Specialist</a>
																		     
																		        
																		        
																		             <!-- Modal -->
																		                                                                                            <div id="MedicalSpecialist" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
																		                                                                                            	<div class="modal-header">
																		                                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
																		                                                                                            <h3 id="myModalLabel">Medical Center Specialist</h3>
																		                                                                                            </div>
																		                                                                                            <div class="modal-body">
																		                                                                                            <div>
																		                                                                                          <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered bootstrap-datatable" id="datatable">
																		                          <thead>
																		                              <tr>
																		                                  <th>Last Name</th>
																		                                  <th>First Name</th>
																		                                  <th>Specialization</th>
																		 
																		                                  <th>Actions</th>
																		                              </tr>
																		                          </thead>   
																		                          <tbody>
																		                            <tr>
																		                                <td>Fred Flinstone</td>
																		                                <td class="center">2011/01/01</td>
																		                                <td class="center">Member</td>
																		
																		                               <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Spiderman</td>
																		                                <td class="center">2011/02/01</td>
																		                                <td class="center">Staff</td>
																		                    
																		                               <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Batman</td>
																		                                <td class="center">2011/02/01</td>
																		                                <td class="center">Admin</td>
																		                       
																		                              <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Robin</td>
																		                                <td class="center">2011/03/01</td>
																		                                <td class="center">Member</td>
																		                   
																		                                <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Catwomen</td>
																		                                <td class="center">2010/01/21</td>
																		                                <td class="center">Staff</td>
																		                      
																		                               <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Garfield</td>
																		                                <td class="center">2011/08/23</td>
																		                                <td class="center">Staff</td>
																		                        
																		                               <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Bananaman</td>
																		                                <td class="center">2011/06/01</td>
																		                                <td class="center">Admin</td>
																		                         
																		                               <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Paul</td>
																		                                <td class="center">2011/03/01</td>
																		                                <td class="center">Member</td>
																		                          
																		                                <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Wilma Flinstone</td>
																		                                <td class="center">2011/01/01</td>
																		                                <td class="center">Member</td>
																		                      <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Hulk</td>
																		                                <td class="center">2011/02/01</td>
																		                                <td class="center">Staff</td>
																		                     
																		                                <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>Bob the Builder</td>
																		                                <td class="center">2011/02/01</td>
																		                                <td class="center">Admin</td>
																		                        <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                            <tr>
																		                                <td>MacAndCheese</td>
																		                                <td class="center">2011/03/01</td>
																		                                <td class="center">Member</td>
																		                   
																		                               <td class="center">
																		                                    <a class="btn btn-small btn-success" href="#">
																		                                        <i class="icon-zoom-in icon-white"></i>  
																		                                        View                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-info" href="#">
																		                                        <i class="icon-edit icon-white"></i>  
																		                                        Edit                                            
																		                                    </a>
																		                                    <a class="btn btn-small btn-danger" href="#">
																		                                        <i class="icon-trash icon-white"></i> 
																		                                        Delete
																		                                    </a>
																		                                </td>
																		                            </tr>
																		                        
																		                      
																		                       
																		                        
																		                          </tbody>
																		                      </table>     
																		                                                                                                <!--<ul>
																		                                                                                                    <li>Please enter specialization details.</li>
																		                                                                                                </ul>-->
																		                                                                                            </div>
																		                                                                                            
																		                                                                                            
																		                                                                                            </div>
																		                                                                                            <div class="modal-footer">
																		                                                                                            
																		                                                                                            <button class="btn btn-success">Add Medical Specialist</button>
																		                                                                                            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
																		                                                                                            
																		                                                                                            </div>
																		                                                                                            </div>
																		        
																		             <!-- end modal for medical specialist -->                                                              		
																		                                                                     		
																		                                                                     		</div>
																		                                                                     
																		                                                                 			<div class="boxcontent" style="padding-top:0;">
																		                                                                            <div class="listing">
																		                                                                            
																		                                                                                                                                            
																		                                                                            <div class="detailbox" >
																		                 <h3 class="pull-left">List of All Specializations</h3>                                                         
																		               
																		                   <div class="pull-right">
																		              
																		                </div>
																		                
																		                <div class="clearfix"></div>
																		               
																		                               <br/>                                                <!--specialization deails start -->        <div class="specializationdetails"> <h7>Specialization 1 <span> <button class="btn btn-small btn-mini">edit Specialization entry</button></span></h7>
																		                                                                  
																		                                                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
																		                                                                                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
																		                                                                                            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
																		                                                                                            esse cillum dolore eu fugiat nulla pariatur.</p>
																		               
																		                          <div class="treatmentlisting">
																		                            <h8>Treatment</h8>
																		                            <button class="btn btn-small btn-mini">edit Treatment entry</button>
																		                            <ul>
																		                             <li>Sub-specialization - Treatment 1, treatment2</li>  
																		                              <li>Sub-specialization - Treatment 1, treatment2</li> 
																		                             <li>Sub-specialization - Treatment 1, treatment2</li>                                </ul>      </div>
																		                             </div>
																		                             <!--specialization deails end -->
																		                             
																		                             <hr>
																		                             
																		                                                                 <!--specialization deails start -->        <div class="specializationdetails"> <h7>Specialization 2 <span> <button class="btn btn-small btn-mini">edit Specialization entry</button></span></h7>
																		                                                                  
																		                                                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
																		                                                                                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
																		                                                                                            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
																		                                                                                            esse cillum dolore eu fugiat nulla pariatur.</p>
																		               
																		                          <div class="treatmentlisting">
																		                            <h8>Treatment</h8>
																		                            <button class="btn btn-small btn-mini">edit Treatment entry</button>
																		                            <ul>
																		                             <li>Sub-specialization - Treatment 1, treatment2</li>  
																		                              <li>Sub-specialization - Treatment 1, treatment2</li> 
																		                             <li>Sub-specialization - Treatment 1, treatment2</li>                                </ul>      </div>
																		                             </div>
																		                             <!--specialization deails end -->
																		                             
																		                             
																		                             
																		                             
																		                             </div></div>
																		                                                                                            
																		                                                                             
																		                                                                                                                        
																		                                                                          </div>
																		                                                                          
																		                                                                          
																		                                                                          
																		                                                                          
																		                                                                     </div>
																		                                                                     
																		                                                                     
																		                                                                   </div>
																		                                                                   
																		                                                                   
																		                                                                 		</div>
																		                                                                 		
																		                                                                 		
																		                                                                 	</div>	
																		                                                                 	
																		                                                                 	
																		                                                                 	<div class="pull-left" style="margin-top: 10px;"><input type="submit" name="Submit" value="Edit Medical Center" class="btn btn-large btn-primary" />&nbsp;&nbsp;<input type="submit" name="Submit" value="Add Another Medical Center" class="btn btn-large btn-success" /></div>
																		                                                                 	<div class="pull-right" style="margin-top: 10px;"><input type="submit" name="Submit" value="SUBMIT" class="btn btn-large btn-primary" /></div>
																		                                                                 				
																		                                                          	</div> 
																		                                                          	
																		                                                          	
																		                                                          	  
																		                           
																		                        </div>
																		                        
																		                        
																		                        <!-- /.tab-content end -->
						</div>
			    </div><!--/span 12 end-->
			 </div><!--/row-fluid end--></div>
		</div>
			<!--main tab start-->
		
			<div class="clear"></div>
		
	
	</div>	
</div>

<?php include("inc.footer.php"); ?>
