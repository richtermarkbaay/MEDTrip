<?php include("inc.header2.php"); ?>

<div class="container">
    <div class="span4 signupbox">
    <div class="medicalbox-big"><img src="images/institution/medical-clip.png" alt="Medical Clip" longdesc="#"></div>
    <a href="index.html"><h3>HEALTHCARE ABROAD</h3></a>
    <h4>Existing Users</h4>		
        <div class="row-fluid">
            <form action="#" method="POST" class="form">
                <fieldset>
                    <div>
                        <label for="username">Email</label>
                        <div class="input-prepend"><span class="add-on"><i class="icon-envelope"></i></span><input name="log" type="text" id="log inputIcon" value="" class="username span11" /></div>
                        <!--<ul>
                                <li>Please enter a valid email address.</li>
                        </ul>-->
                    </div>
                    
                    <div>
                        <label for="password">Password</label>
                        <div class="input-prepend"><span class="add-on"><i class="icon-lock"></i></span><input name="pwd" type="password" id="pwd inputIcon" class="password span11" /></div>
                        <!--<ul>
                                <li>Invalid password.</li>
                        </ul>-->
                    </div>
                    
                    <input type="hidden" name="redirect_to" value="#" /><input name="a" type="hidden" value="login" />

                    <div class="pull-right">
                        <input name="rememberme" type="checkbox" id="rememberme" value="forever" />&nbsp;<small>Remember me</small>
                        <input type="submit" name="Submit" value="Login" class="btn btn-primary" />
                    </div>
                    <div class="clear">&nbsp;</div>
                    <div class="clear">&nbsp;</div>        
                    <div class="clear"></div>
                    
                    <div>Forgot password?&nbsp;<a href="#">Click here to reset</a></div>
                    <div>New User?&nbsp;<a href="register.html">Click here to register</a></div>	
                    <div class="clear"></div>
                </fieldset>
            </form>	  
        </div>
    </div>
</div>
<?php include("inc.footer.php"); ?>