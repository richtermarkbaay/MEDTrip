<?php include("inc.header.php"); ?>
<div class="container">
    <div class="maincontent">
        <!--sidebar start-->
        <?php include("inc.sidebar.php"); ?>		
        <!--sidebar end-->
        
             
        <div class="span9 title">
                
            
                	
                	
                
                                
                	   
                             
                                
                	    
                	   	
                	   	<div class="accordion" id="accordion2">
                	   	  		<div class="accordion-group">
                	   	   			  <div class="accordion-heading">
                	   	    			   <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#Stats">
                	   	         			<h4>Statistics</h4> 
                	   	       				</a>
                	   	     		</div>
                	   	     		<div id="Stats" class="accordion-body collapse in">
                	   	       <div class="accordion-inner">
                	   	       		
                	   	       		
                	   	       		                           
                	   	   			<div class="boxcontent">
                	   	                <div class="span3">
                                        	
                                        <table>
                                        	<tr>
                                            	<td class="pull-right"><h1>127</h1></td>
                                                	<td>Users</td>
                                            </tr>
                                            <tr>
                                            	<td class="pull-right"><h1>98</h1></td>
                                                	<td>Posts</td>
                                            </tr>
                                            <tr>
                                            	<td class="pull-right"><h1>13</h1></td>
                                                	<td>Pages</td>
                                            </tr>
                                            <tr>
                                            	<td class="pull-right"><h1>23</h1></td>
                                                	<td>Comments</td>
                                            </tr>
                                            <tr>
                                            	<td class="pull-right"><h1>723</h1></td>
                                                	<td>Messages</td>
                                            </tr>
                                      
                                                                                        <tr>
                                            	<td class="pull-right"><h1>2,123</h1></td>
                                                	<td>Page Views</td>
                                            </tr>
                                        </table>
                                            
                                          
                                     
                                            </div>
                                                
                                        	
                                        </div>
                                        <div class="pull-left">
                                        
                                       <!-- stat start |just for sample actual will change for width and height| -->
                                         <canvas id="cvs" width="550" height="250">[No canvas support]</canvas>
    
    <script>
        window.onload = function ()
        {
            var gutterLeft = 150;
            var gutterRight = 25;
            var gutterTop   = 25;

            var line1 = new RGraph.Line('cvs', [1,3,5,2,5,6,8,4,4,5,3,6]);
            line1.Set('chart.ymax', 10);
            line1.Set('chart.hmargin', 5);
            line1.Set('chart.gutter.right', gutterRight);
            line1.Set('chart.gutter.left', gutterLeft);
            line1.Set('chart.gutter.top', gutterTop);
            line1.Set('chart.labels', ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']);
            line1.Set('chart.tooltips', ['rJan','rFeb','rMar','rApr','rMay','rJun','rJul','rAug','rSep','rOct','rNov','rDec']);
            line1.Set('chart.colors', ['red', 'green', 'blue']);
            line1.Set('chart.key', ['Unique', 'Pages', 'Hits']);
            line1.Set('chart.key.position', 'gutter');
            line1.Set('chart.key.position.gutter.boxed', false);
            line1.Set('chart.key.position.x', 275);
            line1.Set('chart.noaxes', true);
            line1.Set('chart.ylabels', false);
            line1.Draw();
    
            var line2 = new RGraph.Line('cvs', [54,53,56,58,57,53,49,52,53,56,61,58]);
            line2.Set('chart.ymax', 100);
            line2.Set('chart.background.grid', false);
            line2.Set('chart.colors', ['green']);
            line2.Set('chart.hmargin', 5);
            line2.Set('chart.noaxes', true);
            line2.Set('chart.gutter.right', gutterRight);
            line2.Set('chart.gutter.left', gutterLeft);
            line2.Set('chart.gutter.top', gutterTop);
            line2.Set('chart.tooltips', ['gJan','gFeb','gMar','gApr','gMay','gJun','gJul','gAug','gSep','gOct','gNov','gDec']);
            line2.Set('chart.ylabels', false);
            line2.Draw();
    
            var line3 = new RGraph.Line('cvs', [31,35,32,36,34,32,33,35,28,17,18,18]);
            line3.Set('chart.ymax', 50);
            line3.Set('chart.background.grid', false);
            line3.Set('chart.ylabels', false);
            line3.Set('chart.noaxes', true);
            line3.Set('chart.colors', ['blue']);
            line3.Set('chart.hmargin', 5);
            line3.Set('chart.gutter.right', gutterRight);
            line3.Set('chart.gutter.left', gutterLeft);
            line3.Set('chart.gutter.top', gutterTop);
            line3.Set('chart.tooltips', ['bJan','bFeb','bMar','bApr','bMay','bJun','bJul','bAug','bSep','bOct','bNov','bDec']);
    
    
    
            /**
            * This draws the extra axes. It's run whenever the line3 object is drawn
            */
            myFunc = function ()
            {
            
     
                RGraph.DrawAxes(line3, {
                                        'axis.x': 150,
                                        'axis.y': 25,
                                        'axis.color': 'gray',
                                        'axis.text.color': 'blue',
                                        'axis.max': 50
                                       });
            };
            RGraph.AddCustomEventListener(line3, 'ondraw', myFunc);
    
    
    
            line3.Draw();
        }
    </script>
                                      <!-- stat graph end -->

                                        </div>  
                	   	               
                	   	            </div>
                	   	            
                	   	            
                	   	            
                	   	            
                	   	      
                	   	     </div>
                	   	   		</div>
                	   	   	</div>			
            
            
            <div class="accordion" id="accordion2">
              		<div class="accordion-group">
               			  <div class="accordion-heading">
                			   
                     			<h4>Recently Added Medical Center<span class="pull-right"><a href="addmedcenter.php"><button class="btn btn-mini"><i class="icon-edit"></i>Add Another Medical Center</button></a></span></h4> 
                   			
                 		</div>
                 		
                 		
                 		
                 		
                
            
                   		
                   		
                   		                           
               			<div class="boxcontent">
                           <div class="treatmentbox">   
                                                          	<div id="accordion2" class="accordion">
                                                                		<div class="accordion-group">
                                                                 
                                                                        
                                                                   		<div class="accordion-body collapse in" id="collapseOne">
                                                                     <div class="accordion-inner">
                                                                     		
                                                                     		
                                                                     		<div class="boxcontent">
                                                                         
                                                                           <h5>Medical Center Name 1</h5>
                                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
                                                                                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
                                                                                            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
                                                                                            esse cillum dolore eu fugiat nulla pariatur.</p>
                                                                            
                                                             		
                                                                     		
                                                                     		</div>
                                                                     
                                                                 			<div class="boxcontent" style="padding-top:0;">
                                                                            <div class="listing">
                                                                            
                                                                                                                                            
                                                                            <div class="detailbox" >
                                                                       
               
  
                
                <div class="clearfix"></div>
               
                                                                        <!--specialization deails start -->        <div class="specializationdetails"> <h7>Specialization 1 <span> <!--<button class="btn btn-small btn-mini">edit Specialization entry</button>--></span></h7>
                                                                  
                                                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
                                                                                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
                                                                                            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
                                                                                            esse cillum dolore eu fugiat nulla pariatur.</p>
               
                          <div class="treatmentlisting">
                            <h8>Treatment</h8>
                            <!--<button class="btn btn-small btn-mini">edit Treatment entry</button>-->
                            <ul>
                             <li>Sub-specialization - Treatment 1, treatment2</li>  
                              <li>Sub-specialization - Treatment 1, treatment2</li> 
                             <li>Sub-specialization - Treatment 1, treatment2</li>                                </ul>      </div>
                             </div>
                             <!--specialization deails end -->
                             
                             <hr>
                             
                                                                 <!--specialization deails start -->        <div class="specializationdetails"> <h7>Specialization 2 <span> <!--<button class="btn btn-small btn-mini">edit Specialization entry</button>--></span></h7>
                                                                  
                                                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
                                                                                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
                                                                                            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
                                                                                            esse cillum dolore eu fugiat nulla pariatur.</p>
               
                          <div class="treatmentlisting">
                            <h8>Treatment</h8>
                            <!--<button class="btn btn-small btn-mini">edit Treatment entry</button>-->
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
                        
                        
                     
                        
                   </div>
                 </div>
               		</div>
               	</div>
            
             <div class="accordion" id="accordion2">
                         		<div class="accordion-group">
                          		
                           		
                                			<h4>Recently Added Medical Specialist<span class="pull-right"><button class="btn btn-mini"><i class="icon-edit"></i>Add Another Medical Specialist</button></span> </h4> 
                              				
                            	
                            		<div id="MedicalListing" class="accordion-body collapse in">
                              <div class="accordion-inner">
                              		
                              		
                              		                           
                          			<div class="treatmentbox"><div class="boxcontent">
                          			    
                          			  
                          			 
                          				<div class="specialist">
                                      <div class="span4">
                                      	<div class="span1"><a href="#"><img class="member-box-avatar" src="images/institution/institution-logo.gif"></a></div>
                                      	<div class="span3"><h5>Dr. First Name Last Name</h5><p>Specialization</p><button class="btn btn-small btn-mini">View Specialist's Profile</button></div>
                                      </div>
									<div class="span4">
	<div class="span1"><a href="#"><img class="member-box-avatar" src="images/institution/institution-logo.gif"></a></div>
	<div class="span3"><h5>Dr. First Name Last Name</h5><p>Specialization</p><button class="btn btn-small btn-mini">View Specialist's Profile</button></div>
</div>
									<div class="span4">
	<div class="span1"><a href="#"><img class="member-box-avatar" src="images/institution/institution-logo.gif"></a></div>
	<div class="span3"><h5>Dr. First Name Last Name</h5><p>Specialization</p><button class="btn btn-small btn-mini">View Specialist's Profile</button></div>
</div>
									<div class="span4">
	<div class="span1"><a href="#"><img class="member-box-avatar" src="images/institution/institution-logo.gif"></a></div>
	<div class="span3"><h5>Dr. First Name Last Name</h5><p>Specialization</p><button class="btn btn-small btn-mini">View Specialist's Profile</button></div>
</div>
                              </div>
                          </div>
                       </div>
                                   
                                   
                                   
                              </div>
                            </div>
                          		</div>
                          	</div>
                
                
        </div> <!-- End of main_content-->
    </div>	
</div>
<?php include("inc.footer.php"); ?>
