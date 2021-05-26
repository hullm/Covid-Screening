<?php 

// Get the sites from the config file
$sites = explode(',',$config['sites']); 

// Get the user's phone number and the last site the used from the database
$phoneNumber = getPhoneNumber($_SESSION["userName"]);
$lastBuilding = getLastBuilding($_SESSION["userName"]);

?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <form id="screeningForm" method="POST" action="index.php?screeningform">
                <!-- Phone & Building Tab -->
                <div class="row">
                    <div class="tab"><h4>Please enter your phone number</h4>
                        <p><input name="phone_number" placeholder="(123) 456-7890" value="<?php echo $phoneNumber;?>" oninput="this.className = ''" onblur="formatPhone(this);"></p>
                        <label for building> <h4>Please select a building</h4></label>
                            <select name = "building" class = "form-control" required>
                                <option value="" disable selected>Select a building...</option>
                                <?php foreach($sites as $site) {
                                        if ($site == $lastBuilding) {
                                            echo "<option value=\"". $site. "\" selected>". $site. "</option>";
                                        } 
                                        else {
                                            echo "<option value=\"". $site. "\">". $site. "</option>";
                                        }
                                    }?>
                            </select>
                    </div>
                </div>
                <!-- Have You Been Vaccinated Tab -->
                <div class ="tab"><h4>Have you been vaccinated?</h4>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="vaccinated_radios" id="vaccinated_yes" value="yes" required/>Yes
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="vaccinated_radios" id="vaccinated_no" value="no"/>No
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="vaccinated_radios" id="vaccinated_unknown" value="no"/>I'd prefer not to say
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Temperature Tab -->
                <div class="tab"><h4>Is your temperature greater than or equal to 100 today?</h4>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="temperature_radios" id="temperature_yes" value="yes"/>Yes
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="temperature_radios" id="temperature_no" value="no"/>No
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Symptoms Tab -->
                <div class="tab"><h4>Do you currently have, or have you had in the last 10 days, one or more of these <b>new</b> or <b>worsening</b> symptoms? <?php echo getSymptoms(); ?></h4>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="symptoms_radios" id="symptoms_yes" value="yes"/>Yes
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="symptoms_radios" id="symptoms_no" value="no"/>No
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Test Positive Tab -->
                <div class="tab"><h4>In the past 10 days have you been tested for COVID-19 resulting in a positive result or are you still waiting for a result?</h4>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="tested_radios" id="tested_yes" value="yes">Yes
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="tested_radios" id="tested_no" value="no">No
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Proximate Contact Tab -->
                <div class="tab"><h4>In the past 10 days, have you been designated a contact of a person who tested positive for COVID-19 by a local health department?</h4>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="contact_radios" id="contact_yes" value="yes">Yes
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="contact_radios" id="contact_no" value="no">No
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <p style=color:red><em>If you have been fully vaccinated and/or are previously diagnosed with 
                        laboratory confirmed COVID-19 and since recovered, please answer no.</em></p>
                    </div>
                </div>
                <!-- Travel Tab -->
                <!--
                <div class="tab"><h4>In the past 14 days, have you traveled internationally to a 
                <a href="https://www.cdc.gov/coronavirus/2019-ncov/travelers/map-and-travel-notices.html#travel-1" target="_blank">
                    CDC level 2 or 3 COVID-19 related travel health country?
                </a></h4>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="travel_radios" id="travel_yes" value="yes">Yes
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="radio">
                            <label>
                                <input type="radio" name="travel_radios" id="travel_no" value="no">No
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <p style=color:red><em>For domestic travel, if you have been fully vaccinated and/or are previously diagnosed with 
                        laboratory confirmed COVID-19 and since recovered, please answer no.</em></p>
                    </div> 
                </div> -->
                    <!-- Next & Previous Buttons -->
                <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" class="btn btn-secondary" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                        <button type="button" class="btn btn-success" id="nextBtn" onclick="nextPrev(1)">Next</button>
                    </div>
                </div>
                <!-- Circles which indicates the steps of the form: -->
                <div style="text-align:center;margin-top:40px;">
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
    // This function will display the specified tab of the form...
    var x = document.getElementsByClassName("tab");
    x[n].style.display = "block";
    //... and fix the Previous/Next buttons:
    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    if (n == (x.length - 1)) {
        document.getElementById("nextBtn").innerHTML = "Submit";
    } else {
        document.getElementById("nextBtn").innerHTML = "Next";
    }
    //... and run a function that will display the correct step indicator:
    fixStepIndicator(n)
}

function nextPrev(n) {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("tab");
    // Exit the function if any field in the current tab is invalid:
    if (n == 1 && !validateForm()) return false;
    // Hide the current tab:
    x[currentTab].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentTab = currentTab + n;
    // if you have reached the end of the form...
    if (currentTab >= x.length) {
        // ... the form gets submitted:
        document.getElementById("nextBtn").type="submit";
        document.getElementById("nextBtn").name="submit";
        document.getElementById("screeningForm").submit();
        return false;
    }
    // Otherwise, display the correct tab:
    showTab(currentTab);
}

function validateForm() {
    var w, x, y, i, valid = true;
    var vaccinated = document.getElementsByName("vaccinated_radios");
    x = document.getElementsByClassName("tab");
    y = x[currentTab].getElementsByTagName("input");
    if (currentTab == 0){
        w = x[currentTab].getElementsByTagName("select");
    }
    else {
        w="Created by Matt Hull and Dane Davis.";
    }

    if ((currentTab == 0 && y[0].value.length != 16) || (y[0].value=="") || w[0].value==""){
        valid = false;
        if(currentTab == 0 && y[0].value.length !=16 && w[0].value==""){
            alert("Please enter a valid phone number and select a building.");
        }else if(currentTab == 0 && y[0].value.length != 16){
            alert("Please enter a valid phone number");
        }else if(w[0].value==""){
            alert("Please select a building");
        }
    }
    else{
        valid = true;
    }

    //Determine if vaccinated was selected
    for (var i = 0; i<vaccinated.length; i++){
        vaccinated[i].addEventListener('change', function(){
            if(this.value=='yes'){
                currentTab = x.length-1;
                console.log(document.getElementsByName('vaccinated_radios')[0].value);
                console.log(document.getElementsByName('temperature_radios')[0].value);
            }
            if(this.value=="no"){
                currentTab = 1;
            }
        });
    }

    // Loop that checks every radio input in the current tab
    for (i=1; i < y.length; i++){
        valid=true;
        if(($("input[type='radio']:checked").length==currentTab-1)){
            // Should be adding invalid class to field.... doesn't work
            // y[i].className += " invalid";
            alert("Please select an option.");
            // set valid status to false
            valid = false;
        }
    }
    if (valid){
        document.getElementsByClassName("step")[currentTab].className += " finish";
    }
    return valid;
}

function fixStepIndicator(n) {
    // This function removes the "active" class of all steps...
    var i, x = document.getElementsByClassName("step");
    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
    }
    //... and adds the "active" class on the current step:
    x[n].className += " active";
}

function formatPhone(obj) {
    // This function will make sure the phone number is 10 digits and then it will format it properly
    var numbers = obj.value.replace(/\D/g, ''),
        char = {0:'(',3:') ',6:' - '};
    obj.value = '';
    for (var i = 0; i < numbers.length; i++) {
        obj.value += (char[i]||'') + numbers[i];
    }
}


</script>