<?php
include 'includes/config.php';
include 'includes/functions.php';
include 'includes/submit.php';
include 'includes/header.php';
include 'includes/footer.php';
$buildings = explode(',',$config['sites']);
purgeOldData($config['purgeafter']);
?>
<div class="container">
    <form id="reportsForm" class="needs-validation" method="POST" action="reports.php" novalidate>
        <div class="form-row justify-content-around">
            <div class="col-md-2 mb-5">
                <label for="fromdate">From Date</label>
                <input type="date" class="form-control" name="from_date" id="from_date" placeholder="mm/dd/yyyy" value="<?php echo $fromDateValue;?>" required>
                <div class="valid-feedback">
                    Looks good!
                </div>
                <div class="invalid-feedback">
                    Please select a date!
                </div>
            </div>
            <div class="col-md-2 mb-5">
                <label for="todate">To Date</label>
                <input type="date" class="form-control" name="to_date" id="to_date" placeholder="mm/dd/yyyy" value="<?php echo $toDateValue;?>" required>
                <div class="valid-feedback">
                    Looks good!
                </div>
                <div class="invalid-feedback">
                    Please select a date!
                </div>
            </div>
            <div class="col-md-2 mb-5">
                <label for="usertype">User Type</label>
                <div class="input-group">
                    <select class="custom-select form-control" name="user_type" id="user_type" required>
                        <option value="All" <?php echo $userTypeAllSelected;?>>All</option>
                        <option value="Employee" <?php echo $employeeSelected;?>>Employee</option>
                        <option value="Student" <?php echo $studentSelected;?>>Student</option>
                        <option value="Visitor" <?php echo $visitorSelected;?>>Visitor</option>
                    </select>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                    <div class="invalid-feedback">
                        Please choose a user type.
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-5">
                <label for="usertype">Building</label>
                <div class="input-group">
                    <select class="custom-select form-control" name="building" id="building" required>
                        <option value="All" <?php echo $buildingAllSelected; ?>>All</option>
                        <?php 
                        foreach($buildings as $location ) {
                            if ($location ==  $buildingValue){ 
                                echo "<option value=\"". $location. "\" selected>". $location. "</option>";
                            }
                            else {
                                echo "<option value=\"". $location. "\">". $location. "</option>";
                            }
                        }?>
                    </select>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                    <div class="invalid-feedback">
                        Please choose a building.
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-5">
                <label for="usertype">Results</label>
                <div class="input-group">
                    <select class="custom-select form-control" name="passed" id="passed" required>
                        <option value="All" <?php echo $resultsAllSelected;?>>All</option>
                        <option value="True" <?php echo $resultsPassedSelected;?>>Passed</option>
                        <option value="False" <?php echo $resultsFailedSelected;?>>Failed</option>
                    </select>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                    <div class="invalid-feedback">
                        Please choose a building.
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row justify-content-md-center">
            <div class="col-md-2 mb-2">
                <button class="btn btn-success mx-auto d-block" type="submit" name="submit">Submit form</button>
            </div>
            <div class="col-md-2 mb-2"> 
                <button class="btn btn-danger mx-auto d-block" type="reset" name="reset">Reset Form</button>
            </div>
        </div>
    </form>
</div>
<?php if(isset($_POST["submit"]) AND $results->num_rows>0){?>
    <div class = "container">
        <div class = "row justify-content-md-center">
            <table id="report" class="table table-striped table-dark hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Status</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Building</th>
                        <th>Type</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row=$results->fetch_assoc()){?>
                        <tr>
                            <td><?php 
                                if ($row['HasPassed']) {
                                    echo "<i class=\"fas fa-check\" style=\"color:green\" title=\"Allowed\"></i>";
                                }
                                else {
                                    echo "<i class=\"fas fa-times\" style=\"color:red\" title=\"Denied\"></i>";
                                };?></td>
                            <td><?php 
                                if ($row['HasPassed']) {
                                    echo "Passed";
                                }
                                else {
                                    echo "Failed";
                                };?></td>
                            <td><?php echo $row['FirstName'];?></td>
                            <td><?php echo $row['LastName'];?></td>
                            <td><?php echo $row['Email'];?></td>
                            <td><?php echo $row['PhoneNumber'];?></td>
                            <td><?php echo $row['Building'];?></td>
                            <td><?php echo $row['UserType'];?></td>
                            <td><?php echo date_format(date_create($row['DateSubmitted']),"m/d/Y"). " ". date_format(date_create($row['TimeSubmitted']),"g:ia");?></td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
    <br />
    <br />
<?php }?>

<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

<script>
$(document).ready(function() {
    var table = $('#report').DataTable( {
        paging: false,
        "info": false,
        "autoWidth": false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'colvis',
                text: 'Show/Hide Columns'
            }
            ,
            {
                extend: 'csvHtml5',
                text: 'Download CSV',
                title: '<?php echo $config['title'];?>'
            }
        ]
    });
    var width = window.innerWidth;

    if (width<480) {
        table.columns([1,4,5,6,7,8]).visible(false);
    }
    else {
        table.columns([1]).visible(false);
    }
});
</script>