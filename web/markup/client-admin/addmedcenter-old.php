<?php include( "inc.header.php"); ?>
	<div class="container">
		<div class="maincontent">
			<!--sidebar start-->
			<?php include( "inc.sidebar.php"); ?>
				<!--sidebar end-->
				<!--main tab start-->
				<div class="span9 title">
					<h4>Add Medical Center</h4>
					<div class="addcenterbox">
						<div class="row-fluid">
							<div class="span12">
								<form>
									<h3>Medical Center Information</h3>
									<div class="span2" style="text-align: ;">
										<img alt="..." src="images/institution/institution-logo.gif" />
										<a href="#"><span class="label photo-label" style="position: absolute;"><i class="icon-picture"></i></span></a>
									</div>
									<div class="span8">
										<div>
											<label for="username">Medical Center Name</label>
											<input name="log" type="text" id="log inputIcon" value="" class="username span8" />
											<!--<ul>
																				<li>Please enter a medical center name.</li>
																			</ul>-->
										</div>
										<div>
											<label for="password">Medical Center Description</label>
											<textarea class="field span12" id="textarea" rows="6" placeholder=" Add Description about your Medical Center"></textarea>
											<!--<ul>
																				<li>Please enter a short description.</li>
																			</ul>-->
										</div>
										<div>
											<label for="Clinic Hours">Clinic Hours</label>
											<input name="log" type="text" id="log inputIcon" value="" class="username span8" />
											<!--<ul>
																					<li>Please enter a medical center name.</li>
																				</ul>-->
										</div>
										<span class="btn-medium">
											<i class="icon-picture"></i>Add Media File Here</span>
										<div class="clear">
											<br/>
										</div>
										<div class="pull-left">
											<input type="submit" name="Submit" value="SAVE AND ADD DETAILS" class="btn btn-large btn-primary" />
										</div>
										<div class="pull-right">
											<input type="submit" name="Submit" value="SAVE and ADD ANOTHER MEDICAL CENTER" class="btn btn-large btn-success" />
										</div>
									</div>
							</div>
						</div>
					</div>
					</form>
				</div>
				<!--/row-fluid end-->
		</div>
	</div>
	<!--main tab start-->
	<div class="clear"></div>
	</div>
	</div>
	<?php include( "inc.footer.php"); ?>