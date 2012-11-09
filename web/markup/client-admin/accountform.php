<?php include("inc.header2.php"); ?>


<div class="container">
	<div class="accountbox">
	
	<div class="row-fluid">
	<div class="medicalbox-big"><img src="images/institution/medical-clip.png" alt="Medical Clip" longdesc="#"></div>
		<form action="#" method="POST" class="form">
		<div class="accountbox2"><a href="index.html"><h3>HEALTHCARE ABROAD</h3></a>
                    <h4>Institution Profile Form</h4>
                    <p>In order to appear on our comprehensive MEDICAL DIRECTORY LISTING, please tell us more about your Institution (questions in grey are optional).</p>

                    <hr>
                    <h2>INSTITUTION NAME HERE</h2>
                    <h4>Institution type here</h4>
		</div>
			<fieldset>
		  			<div class="span6">
		  				
		  				<h5>This how your patients can connect to you.</h5>
                                                <div>
                                                    <label for="institution website">Institution Website</label>
                                                    <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                                    <!--<ul>
                                                            <li>Please enter a valid website url</li>
                                                    </ul>-->
		  				</div>
                                                
                                                <div>
                                                    <label for="institution email">Institution Email Address</label>
                                                    <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                                    <!--<ul>
                                                            <li>Please enter a valid email address.</li>
                                                    </ul>-->
		  				</div>
                                                
                                                <div>
                                                    <label for="institution phone">Institution Phone Number</label>
                                                        <select name="country" class="span2">
                                                            <option value="">Select a Country Code</option>
                                                            <option value="Afganistan">+2</option>
                                                            <option value="Albania">+63</option>
                                                            <option value="Algeria">+1670</option>
                                                        </select>

                                                        <select name="country" class="span2">
                                                            <option value="">Select an Area Code</option>
                                                            <option value="Afganistan">32</option>
                                                            <option value="Albania">2</option>
                                                            <option value="Algeria">11</option>
                                                        </select>
                                                    
                                                        <input type="text" size="4" maxlength="4" name="local2" class="span7">
                                                        <!--<ul>
                                                            <li>Please enter a valid phone number.</li>
                                                        </ul>-->
                                                    <script type="text/javascript">
                                                        var p;

                                                        function next(i) {
                                                                return function() {
                                                                        //strip non-digits
                                                                        p[i].value=p[i].value.replace(/[^0-9]/g, "");

                                                                        //go forward one box when full, except when on the end box
                                                                        if(p[i].value.length==p[i].size && i<p.length) p[i+1].focus();
                                                                }
                                                        }

                                                        function back(i) {
                                                                return function(e) {
                                                                        //go backward one when empty, except when on the first box
                                                                        if(e.keyCode==8 && p[i].value.length==0 && i>0) p[i-1].focus();
                                                                }
                                                        }

                                                        window.onload=function() {
                                                                p=document.getElementById("phones").getElementsByTagName("input");

                                                                for(var i=0; i<p.length; i++) {
                                                                        p[i].onkeyup=next(i);
                                                                        p[i].onkeydown=back(i);
                                                                }
                                                        }
                                                    </script>
                                                </div> <!--End of institution phone div-->				
						
                                            <div>
                                                <label for="institution fax">Institution Fax Number</label>
                                                    <select name="country" class="span2">
                                                        <option value="">Select a Country Code</option>
                                                        <option value="Afganistan">+2</option>
                                                        <option value="Albania">+63</option>
                                                        <option value="Algeria">+1670</option>
                                                    </select>

                                                    <select name="country" class="span2">
                                                        <option value="">Select an Area Code</option>
                                                        <option value="Afganistan">32</option>
                                                        <option value="Albania">2</option>
                                                        <option value="Algeria">11</option>
                                                    </select>
                                                
                                                    <input type="text" size="4" maxlength="4" name="local2" class="span7">
                                                    <!--<ul>
                                                            <li>Please enter a valid fax number.</li>
                                                    </ul>-->        
                                            <script type="text/javascript">
                                                var p;

                                                function next(i) {
                                                        return function() {
                                                                //strip non-digits
                                                                p[i].value=p[i].value.replace(/[^0-9]/g, "");

                                                                //go forward one box when full, except when on the end box
                                                                if(p[i].value.length==p[i].size && i<p.length) p[i+1].focus();
                                                        }
                                                }

                                                function back(i) {
                                                        return function(e) {
                                                                //go backward one when empty, except when on the first box
                                                                if(e.keyCode==8 && p[i].value.length==0 && i>0) p[i-1].focus();
                                                        }
                                                }

                                                window.onload=function() {
                                                        p=document.getElementById("phones").getElementsByTagName("input");

                                                        for(var i=0; i<p.length; i++) {
                                                                p[i].onkeyup=next(i);
                                                                p[i].onkeydown=back(i);
                                                        }
                                                }
                                            </script>
                                        </div>  <!--End of institution fax div-->
					
                                        <div>
                                            <label for="facebook page">Facebook Page</label>
                                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                            <!--<ul>
                                                <li>Please enter a Facebook page.</li>
                                            </ul>-->
					</div>
                                        
                                        <div>
                                            <label for="twitter account">Twitter Account</label>
                                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                            <!--<ul>
                                                <li>Please enter a Twitter account.</li>
                                            </ul>-->
		  			</div>
                                    </div>
		  						  				
                                    <div class="span6">
                                        <h5>This how your patients can locate you.</h5>
                                        <div class="map"><img src="images/institution/map.png" alt="Medical Clip" longdesc="#"></div>
                                        
                                        <div>      
                                            <label for="unit number">Unit Number and Building</label>	
                                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                            <!--<ul>
                                                <li>Please enter a valid unit number and building.</li>
                                            </ul>-->
                                        </div>
                                        
                                        <div>
                                            <label for="street">Street</label>
                                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                            <!--<ul>
                                                <li>Please enter a valid street number.</li>
                                            </ul>-->
		  			</div>
                                        
                                        <div>
                                            <label for="city">City</label>
                                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                            <!--<ul>
                                                <li>Please enter a valid city.</li>
                                            </ul>-->
		  			</div>
                                        
                                        <div>
                                            <label for="state province">State / Province</label>
                                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                            <!--<ul>
                                                <li>Please enter a valid state/province.</li>
                                            </ul>-->
		  			</div>
                                        
                                        <div>
                                            <label for="zip code">Zip / Mail Code </label>
                                            <input name="log" type="text" id="log inputIcon" value="" class="username span12" />
                                            <!--<ul>
                                                <li>Please enter a valid zip/mail code.</li>
                                            </ul>-->
                                        </div>
                                        
                                        <div>
                                            <label for="country">Country</label>
                                            <select name="country" class="span12">
                                            <option value="">Select a Country</option>
                                            <option value="Afganistan">Afghanistan</option>
                                            <option value="Albania">Albania</option>
                                            <option value="Algeria">Algeria</option>
                                            <option value="American Samoa">American Samoa</option>
                                            <option value="Andorra">Andorra</option>
                                            <option value="Angola">Angola</option>
                                            <option value="Anguilla">Anguilla</option>
                                            <option value="Antigua &amp; Barbuda">Antigua &amp; Barbuda</option>
                                            <option value="Argentina">Argentina</option>
                                            <option value="Armenia">Armenia</option>
                                            <option value="Aruba">Aruba</option>
                                            <option value="Australia">Australia</option>
                                            <option value="Austria">Austria</option>
                                            <option value="Azerbaijan">Azerbaijan</option>
                                            <option value="Bahamas">Bahamas</option>
                                            <option value="Bahrain">Bahrain</option>
                                            <option value="Bangladesh">Bangladesh</option>
                                            <option value="Barbados">Barbados</option>
                                            <option value="Belarus">Belarus</option>
                                            <option value="Belgium">Belgium</option>
                                            <option value="Belize">Belize</option>
                                            <option value="Benin">Benin</option>
                                            <option value="Bermuda">Bermuda</option>
                                            <option value="Bhutan">Bhutan</option>
                                            <option value="Bolivia">Bolivia</option>
                                            <option value="Bonaire">Bonaire</option>
                                            <option value="Bosnia &amp; Herzegovina">Bosnia &amp; Herzegovina</option>
                                            <option value="Botswana">Botswana</option>
                                            <option value="Brazil">Brazil</option>
                                            <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                            <option value="Brunei">Brunei</option>
                                            <option value="Bulgaria">Bulgaria</option>
                                            <option value="Burkina Faso">Burkina Faso</option>
                                            <option value="Burundi">Burundi</option>
                                            <option value="Cambodia">Cambodia</option>
                                            <option value="Cameroon">Cameroon</option>
                                            <option value="Canada">Canada</option>
                                            <option value="Canary Islands">Canary Islands</option>
                                            <option value="Cape Verde">Cape Verde</option>
                                            <option value="Cayman Islands">Cayman Islands</option>
                                            <option value="Central African Republic">Central African Republic</option>
                                            <option value="Chad">Chad</option>
                                            <option value="Channel Islands">Channel Islands</option>
                                            <option value="Chile">Chile</option>
                                            <option value="China">China</option>
                                            <option value="Christmas Island">Christmas Island</option>
                                            <option value="Cocos Island">Cocos Island</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Comoros">Comoros</option>
                                            <option value="Congo">Congo</option>
                                            <option value="Cook Islands">Cook Islands</option>
                                            <option value="Costa Rica">Costa Rica</option>
                                            <option value="Cote DIvoire">Cote D'Ivoire</option>
                                            <option value="Croatia">Croatia</option>
                                            <option value="Cuba">Cuba</option>
                                            <option value="Curaco">Curacao</option>
                                            <option value="Cyprus">Cyprus</option>
                                            <option value="Czech Republic">Czech Republic</option>
                                            <option value="Denmark">Denmark</option>
                                            <option value="Djibouti">Djibouti</option>
                                            <option value="Dominica">Dominica</option>
                                            <option value="Dominican Republic">Dominican Republic</option>
                                            <option value="East Timor">East Timor</option>
                                            <option value="Ecuador">Ecuador</option>
                                            <option value="Egypt">Egypt</option>
                                            <option value="El Salvador">El Salvador</option>
                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                            <option value="Eritrea">Eritrea</option>
                                            <option value="Estonia">Estonia</option>
                                            <option value="Ethiopia">Ethiopia</option>
                                            <option value="Falkland Islands">Falkland Islands</option>
                                            <option value="Faroe Islands">Faroe Islands</option>
                                            <option value="Fiji">Fiji</option>
                                            <option value="Finland">Finland</option>
                                            <option value="France">France</option>
                                            <option value="French Guiana">French Guiana</option>
                                            <option value="French Polynesia">French Polynesia</option>
                                            <option value="French Southern Ter">French Southern Ter</option>
                                            <option value="Gabon">Gabon</option>
                                            <option value="Gambia">Gambia</option>
                                            <option value="Georgia">Georgia</option>
                                            <option value="Germany">Germany</option>
                                            <option value="Ghana">Ghana</option>
                                            <option value="Gibraltar">Gibraltar</option>
                                            <option value="Great Britain">Great Britain</option>
                                            <option value="Greece">Greece</option>
                                            <option value="Greenland">Greenland</option>
                                            <option value="Grenada">Grenada</option>
                                            <option value="Guadeloupe">Guadeloupe</option>
                                            <option value="Guam">Guam</option>
                                            <option value="Guatemala">Guatemala</option>
                                            <option value="Guinea">Guinea</option>
                                            <option value="Guyana">Guyana</option>
                                            <option value="Haiti">Haiti</option>
                                            <option value="Hawaii">Hawaii</option>
                                            <option value="Honduras">Honduras</option>
                                            <option value="Hong Kong">Hong Kong</option>
                                            <option value="Hungary">Hungary</option>
                                            <option value="Iceland">Iceland</option>
                                            <option value="India">India</option>
                                            <option value="Indonesia">Indonesia</option>
                                            <option value="Iran">Iran</option>
                                            <option value="Iraq">Iraq</option>
                                            <option value="Ireland">Ireland</option>
                                            <option value="Isle of Man">Isle of Man</option>
                                            <option value="Israel">Israel</option>
                                            <option value="Italy">Italy</option>
                                            <option value="Jamaica">Jamaica</option>
                                            <option value="Japan">Japan</option>
                                            <option value="Jordan">Jordan</option>
                                            <option value="Kazakhstan">Kazakhstan</option>
                                            <option value="Kenya">Kenya</option>
                                            <option value="Kiribati">Kiribati</option>
                                            <option value="Korea North">Korea North</option>
                                            <option value="Korea Sout">Korea South</option>
                                            <option value="Kuwait">Kuwait</option>
                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                            <option value="Laos">Laos</option>
                                            <option value="Latvia">Latvia</option>
                                            <option value="Lebanon">Lebanon</option>
                                            <option value="Lesotho">Lesotho</option>
                                            <option value="Liberia">Liberia</option>
                                            <option value="Libya">Libya</option>
                                            <option value="Liechtenstein">Liechtenstein</option>
                                            <option value="Lithuania">Lithuania</option>
                                            <option value="Luxembourg">Luxembourg</option>
                                            <option value="Macau">Macau</option>
                                            <option value="Macedonia">Macedonia</option>
                                            <option value="Madagascar">Madagascar</option>
                                            <option value="Malaysia">Malaysia</option>
                                            <option value="Malawi">Malawi</option>
                                            <option value="Maldives">Maldives</option>
                                            <option value="Mali">Mali</option>
                                            <option value="Malta">Malta</option>
                                            <option value="Marshall Islands">Marshall Islands</option>
                                            <option value="Martinique">Martinique</option>
                                            <option value="Mauritania">Mauritania</option>
                                            <option value="Mauritius">Mauritius</option>
                                            <option value="Mayotte">Mayotte</option>
                                            <option value="Mexico">Mexico</option>
                                            <option value="Midway Islands">Midway Islands</option>
                                            <option value="Moldova">Moldova</option>
                                            <option value="Monaco">Monaco</option>
                                            <option value="Mongolia">Mongolia</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Morocco">Morocco</option>
                                            <option value="Mozambique">Mozambique</option>
                                            <option value="Myanmar">Myanmar</option>
                                            <option value="Nambia">Nambia</option>
                                            <option value="Nauru">Nauru</option>
                                            <option value="Nepal">Nepal</option>
                                            <option value="Netherland Antilles">Netherland Antilles</option>
                                            <option value="Netherlands">Netherlands (Holland, Europe)</option>
                                            <option value="Nevis">Nevis</option>
                                            <option value="New Caledonia">New Caledonia</option>
                                            <option value="New Zealand">New Zealand</option>
                                            <option value="Nicaragua">Nicaragua</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Nigeria">Nigeria</option>
                                            <option value="Niue">Niue</option>
                                            <option value="Norfolk Island">Norfolk Island</option>
                                            <option value="Norway">Norway</option>
                                            <option value="Oman">Oman</option>
                                            <option value="Pakistan">Pakistan</option>
                                            <option value="Palau Island">Palau Island</option>
                                            <option value="Palestine">Palestine</option>
                                            <option value="Panama">Panama</option>
                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                            <option value="Paraguay">Paraguay</option>
                                            <option value="Peru">Peru</option>
                                            <option value="Phillipines">Philippines</option>
                                            <option value="Pitcairn Island">Pitcairn Island</option>
                                            <option value="Poland">Poland</option>
                                            <option value="Portugal">Portugal</option>
                                            <option value="Puerto Rico">Puerto Rico</option>
                                            <option value="Qatar">Qatar</option>
                                            <option value="Republic of Montenegro">Republic of Montenegro</option>
                                            <option value="Republic of Serbia">Republic of Serbia</option>
                                            <option value="Reunion">Reunion</option>
                                            <option value="Romania">Romania</option>
                                            <option value="Russia">Russia</option>
                                            <option value="Rwanda">Rwanda</option>
                                            <option value="St Barthelemy">St Barthelemy</option>
                                            <option value="St Eustatius">St Eustatius</option>
                                            <option value="St Helena">St Helena</option>
                                            <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                            <option value="St Lucia">St Lucia</option>
                                            <option value="St Maarten">St Maarten</option>
                                            <option value="St Pierre &amp; Miquelon">St Pierre &amp; Miquelon</option>
                                            <option value="St Vincent &amp; Grenadines">St Vincent &amp; Grenadines</option>
                                            <option value="Saipan">Saipan</option>
                                            <option value="Samoa">Samoa</option>
                                            <option value="Samoa American">Samoa American</option>
                                            <option value="San Marino">San Marino</option>
                                            <option value="Sao Tome & Principe">Sao Tome &amp; Principe</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                            <option value="Senegal">Senegal</option>
                                            <option value="Seychelles">Seychelles</option>
                                            <option value="Sierra Leone">Sierra Leone</option>
                                            <option value="Singapore">Singapore</option>
                                            <option value="Slovakia">Slovakia</option>
                                            <option value="Slovenia">Slovenia</option>
                                            <option value="Solomon Islands">Solomon Islands</option>
                                            <option value="Somalia">Somalia</option>
                                            <option value="South Africa">South Africa</option>
                                            <option value="Spain">Spain</option>
                                            <option value="Sri Lanka">Sri Lanka</option>
                                            <option value="Sudan">Sudan</option>
                                            <option value="Suriname">Suriname</option>
                                            <option value="Swaziland">Swaziland</option>
                                            <option value="Sweden">Sweden</option>
                                            <option value="Switzerland">Switzerland</option>
                                            <option value="Syria">Syria</option>
                                            <option value="Tahiti">Tahiti</option>
                                            <option value="Taiwan">Taiwan</option>
                                            <option value="Tajikistan">Tajikistan</option>
                                            <option value="Tanzania">Tanzania</option>
                                            <option value="Thailand">Thailand</option>
                                            <option value="Togo">Togo</option>
                                            <option value="Tokelau">Tokelau</option>
                                            <option value="Tonga">Tonga</option>
                                            <option value="Trinidad &amp; Tobago">Trinidad &amp; Tobago</option>
                                            <option value="Tunisia">Tunisia</option>
                                            <option value="Turkey">Turkey</option>
                                            <option value="Turkmenistan">Turkmenistan</option>
                                            <option value="Turks &amp; Caicos Is">Turks &amp; Caicos Is</option>
                                            <option value="Tuvalu">Tuvalu</option>
                                            <option value="Uganda">Uganda</option>
                                            <option value="Ukraine">Ukraine</option>
                                            <option value="United Arab Erimates">United Arab Emirates</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="United States of America">United States of America</option>
                                            <option value="Uraguay">Uruguay</option>
                                            <option value="Uzbekistan">Uzbekistan</option>
                                            <option value="Vanuatu">Vanuatu</option>
                                            <option value="Vatican City State">Vatican City State</option>
                                            <option value="Venezuela">Venezuela</option>
                                            <option value="Vietnam">Vietnam</option>
                                            <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                            <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                            <option value="Wake Island">Wake Island</option>
                                            <option value="Wallis &amp; Futana Is">Wallis &amp; Futana Is</option>
                                            <option value="Yemen">Yemen</option>
                                            <option value="Zaire">Zaire</option>
                                            <option value="Zambia">Zambia</option>
                                            <option value="Zimbabwe">Zimbabwe</option>
                                            </select>
                                            <!--<ul>
                                                <li>Please enter country.</li>
                                            </ul>-->
                                        </div>                                    
                                    </div>                       
		  		</fieldset>
                    
                            <div class="pull-right"><input type="submit" name="Submit" value="SUBMIT" class="btn-large btn-primary"/></div>
		 </form> <!--End of form-->		  
	</div>
    </div>  
</div>
<?php include("inc.footer.php"); ?>