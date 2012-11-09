 <?php include("inc.header2.php"); ?>
    <div class="container">
        <div class="span5 signupbox">
            <div class="medicalbox-big"><img src="images/institution/medical-clip.png" alt="Medical Clip" longdesc="#"></div>
            <a href="index.html"><h3>HEALTHCARE ABROAD</h3></a>
            <hr>		
            <h4>Create an account.</h4>
			
            <p>Please provide the correct information below, and we will create an exclusive medical account listing for you.</p>
            			
            <div class="row-fluid">
                <form action="#" method="POST" class="form">						
                    <fieldset>
                        <div>
                            <label for="institution name"><b>Name of the Institution</b></label>							
                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                            <!--<ul>
                                <li>Please enter an institution name.</li>
                            </ul>-->
                        </div>
                        
                        <div>
                            <label for="password">Types of Institution</label>
                            <label><input type="radio" name="tab" value="pkfrom" class="div1" />Hospital or Clinic belonging to a Larger Group / Network</label>
                            <label><input type="radio" name="tab" value="pkfrom" class="div2" />Independent Hospital or Clinic</label>
                            <label><input type="radio" name="tab" value="pkfrom" class="div3" />Medical Tourism Facilitator / Agent</label>
                            <!--<ul>
                                <li>Please choose an institution type.</li>
                            </ul>-->
                        </div>									    
                        <hr>
                        
                        <h4>Create USER account.</h4>
                        <div>
                            <label for="username">Email 
                                <span>
                                    <small style="font-size:85% ; color: #ccc;">&nbsp;(your OWN email NOT the email of the institution)</small>
                                </span>
                            </label>
                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                            <!--<ul>
                                <li>Please enter a valid email address.</li>
                            </ul>-->
                        </div>
                        
                        <div>
                            <label for="password">Password</label>
                            <input name="log" type="password" id="log inputIcon" value="" class="username span12" />
                            <!--<ul>
                                <li>Strong password.</li>
                            </ul>-->
                        </div>
                        
                        <div>
                            <label for="re type password">Verify Password</label>
                            <input name="log" type="password" id="log inputIcon" value="" class="username span12" />
                            <!--<ul>
                                <li>Passwords do not match.</li>
                            </ul>-->
                        </div>
                    
                        <br/>
                        <label><input type="checkbox" name="Terms" value="Terms and Conditions" />I read and agree the <a href="#">Terms and Conditions</a>.</label>
                        <input type="submit" name="Submit" value="SIGN-UP" class="span12 btn-large btn-primary" />
                    </fieldset>
                </form>	
            </div>
        </div>
    </div>
<?php include("inc.footer.php"); ?>