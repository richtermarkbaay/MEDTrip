<?php include( "inc.header.php"); ?>
	<div class="container">
		<div class="maincontent">
			<!--sidebar start-->
			<?php include( "inc.sidebar.php"); ?>
				<!--sidebar end-->
				<!--main tab start-->
				<div class="span9 title">
					<h4>Medical Center Listing</h4>
					<div class="addcenterbox">
						<div class="row-fluid">
							<div class="span12">
								<form>
									<div class="treatmentbox">
										<h5>Medical Center 1
											<span class="pull-right dropdown">
												<a class="dropdown-toggle" href="#" data-toggle="dropdown"><button class="btn btn-mini"><i class="icon-edit"></i>&nbsp;Edit</button></a>
												<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
													<!-- Login form here -->
													<form action="[YOUR ACTION]" method="post" accept-charset="UTF-8">
														<input id="user_username" style="margin-bottom: 15px;" type="text" name="user[username]" size="30" />
														<input id="user_password" style="margin-bottom: 15px;" type="password" name="user[password]" size="30" />
														<input id="user_remember_me" style="float: left; margin-right: 10px;" type="checkbox" name="user[remember_me]" value="1" />
														<label class="string optional" for="user_remember_me">Remember me</label>
														<input class="btn btn-primary" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="submit" name="commit" value="Sign In" />
													</form>
												</div>
											</span>
										</h5>
										<div class="boxcontent">
											<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
											<hr>
											<div class="treatmentbox">
												<div class="accordion" id="accordion2">
													<div class="accordion-group">
														<div class="accordion-heading">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
																									   			<h5>Specialization 1</h5> 
																									 				</a>
														</div>
														<div id="collapseOne" class="accordion-body collapse in">
															<div class="accordion-inner">
																<div class="boxcontent">
																	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
																	<h7>Sub-Specialization 1</h7>
																	<label>Treatment 1</label>
																	<label>Treatment 2</label>
																	<label>Treatment 3</label>
																	<hr>
																	<h7>Sub-Specialization 2</h7>
																	<label>Treatment 1</label>
																	<label>Treatment 2</label>
																	<label>Treatment 3</label>
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
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
																											  			<h5>Specialization 2</h5> 
																															</a>
														</div>
														<div id="collapseOne" class="accordion-body collapse in">
															<div class="accordion-inner">
																<div class="boxcontent">
																	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
																	<h7>Treatment</h7>
																	<label>Treatment 1</label>
																	<label>Treatment 2</label>
																	<label>Treatment 3</label>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="treatmentbox">
										<h5>Medical Center 2
											<span class="pull-right">
												<a href="addmedcenter.php"><button class="btn btn-mini"><i class="icon-edit"></i>&nbsp;Edit</button></a>
											</span>
										</h5>
										<div class="boxcontent">
											<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
											<hr>
											<div class="treatmentbox">
												<div class="accordion" id="accordion2">
													<div class="accordion-group">
														<div class="accordion-heading">
															<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
																														 			<h5>Specialization 1</h5> 
																													   				</a>
														</div>
														<div id="collapseOne" class="accordion-body collapse in">
															<div class="accordion-inner">
																<div class="boxcontent">
																	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
																	<h7>Treatment</h7>
																	<label>Treatment 1</label>
																	<label>Treatment 2</label>
																	<label>Treatment 3</label>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<!--/span 12 end-->
						</div>
						<!--/row-fluid end-->
					</div>
				</div>
				<!--main tab start-->
				<div class="clear"></div>
		</div>
	</div>
	<?php include( "inc.footer.php"); ?>