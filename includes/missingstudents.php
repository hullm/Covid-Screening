<?php
$buildings = explode(',',$config['sites']);
?>
<div class="container" id="reportsForm">
    <div class="row justify-content-center">
        <h3>
            Missing Student Report
            <?php if (isset($results)) { echo ": ". $results->num_rows; } ?>
        </h3>
    </div>
    <form class="needs-validation" method="POST" action="index.php?missingstudents" novalidate>
        <div class="form-row justify-content-around">
            <div class="col-md-3 mb-5">
                <label for="fromdate">From Date</label>
                <input type="date" class="form-control" name="from_date" id="from_date" placeholder="mm/dd/yyyy" value="<?php echo $fromDateValue;?>" required>
                <div class="valid-feedback">
                    Looks good!
                </div>
                <div class="invalid-feedback">
                    Please select a date!
                </div>
            </div>
            <div class="col-md-3 mb-5">
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
        </div>
        <div class="form-row justify-content-md-center">
            <div class="col-md-2 mb-6">
                <button class="btn btn-success mx-auto d-block" type="submit" name="submit">Submit form</button>
            </div>
            <div class="col-md-2 mb-6"> 
                <button class="btn btn-danger mx-auto d-block" type="reset" name="reset">Reset Form</button>
            </div>
        </div>
    </form>
</div>
<?php if((isset($_POST["submit"]) || isset($_GET["LoadMissing"])) AND $results->num_rows>0){?>
    <div class = "container">
        <div class = "row justify-content-md-center">
            <table id="missing" class="table table-striped table-dark hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Building</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row=$results->fetch_assoc()){?>
                        <tr>
                            <td>
                                <button id="<?php echo $row['FirstName']. $row['UserName'];?>-btn"
                                    class="btn btn-sm btn-link"
                                    style="color:white;"
                                    data-toggle="modal"
                                    data-target="#<?php echo $row['FirstName']. $row['UserName'];?>">
                                    <i class="far fa-id-card"></i>
                                </button>
                            </td>
                            <td><?php echo $row['FirstName'];?></td>
                            <td><?php echo $row['LastName'];?></td>
                            <td><?php echo $row['Email'];?></td>
                            <td><?php echo $row['PhoneNumber'];?></td>
                            <td><?php echo $row['Building'];?></td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
<?php }?>
<br />
<br />

<?php 
if((isset($_POST["submit"]) || isset($_GET["LoadMissing"])) AND $results->num_rows>0){
    $results->data_seek(0); 
    while ($row=$results->fetch_assoc()){ 
        
        $userName = $row['UserName'];

        // Get the contact info from the database
        $contactInfo = getContactInfo($userName);
        $studentID = $row['StudentID'];

        // Get the student image if available
        if (file_exists($config['studentPhotos']. $studentID. ".". strtoupper($config['photoFormat']))) {
            $studentImage = "<img src=\"/images/students/". $studentID. ".". strtoupper($config['photoFormat']). "\" title=\"". $studentID. "\" />";
        }
        elseif (file_exists($config['studentPhotos']. $studentID. ".". strtolower($config['photoFormat']))) {
            $studentImage = "<img src=\"/images/students/". $studentID. ".". strtolower($config['photoFormat']). "\" title=\"". $studentID. "\" />";
        }
        else {
            $studentImage =  "<img src=\"/images/student.png\" title=\"". $studentID. "\" />";
        }
?>
        <div class="container">
            <div id="<?php echo $row['FirstName']. $row['UserName'];?>" class="modal fade" style="display:none" tabindex="-1">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content modal-round">
                        <div class="modal-header">
                            <h3>Student Information</h3>
                            <button class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body center">
                            <div class="container">
                                <div class="form-row justify-content-around">
                                    <div class="col-md-3 mb-5">
                                        <?php echo $studentImage; ?>
                                    </div>
                                    <div class="col-md-7 mb-5">
                                        <h3><?php echo $row['FirstName']. " ". $row['LastName']; ?></h1>
                                        <p>
                                            <i><?php echo $row['Grade'];?></i><br />
                                            <b>Building</b>: <?php echo $row['Building'];?><br />
                                            <b>Username</b>: <?php echo $row['UserName'];?><br />
                                            <b>Password</b>: <?php echo $row['PWord'];?><br />
                                        </p>
                                        <?php while ($row=$contactInfo->fetch_assoc()){ ?>
                                                <b><?php echo $row['Relationship']. "</b>: ". $row['FirstName']. " ". $row['LastName'] ;?>
                                                <li>Home Phone: <?php echo formatPhoneNumber($row['HomePhone']);?></li>
                                                <li>Cell Phone: <?php echo formatPhoneNumber($row['CellPhone']);?></li>
                                                <li>Email Address: <?php echo $row['Email'];?></li>
                                                <br />
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
?>

<script>
$(document).ready(function() {
    var width = window.innerWidth;
    var table = $('#missing').DataTable( {
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
    
    if (width<480) {
        table.columns([3,4,5]).visible(false);
    }

});
</script>