<?php include("inc.header.php"); ?>
<div class="container">
    <div class="maincontent">
        <!--sidebar start-->
        <?php include("inc.sidebar.php"); ?>		
        <!--sidebar end-->
        
             
        <div class="span9 title">
                <h4>Institution Name - Medical Center Listing</h4>
                <br/>
                <div class="treatmentbox">
             	<div class="accordion" id="accordion2">
                      		<div class="accordion-group">
                       			  <div class="accordion-heading">
                        			   <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                             			<h5>Medical Lsitn</h5> 
                           				</a>
                         		</div>
                         		<div id="collapseOne" class="accordion-body collapse in">
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
                        
                                <hr>
        </div> <!-- End of main_content-->
    </div>	
</div>
<?php include("inc.footer.php"); ?>

