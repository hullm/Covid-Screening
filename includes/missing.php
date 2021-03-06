<?php
$buildings = explode(',',$config['sites']);
?>
<div class="container" id="reportsForm">
    <div class="row justify-content-center">
        <h3>
            Missing Employee Report
            <?php if (isset($results)) { echo ": ". $results->num_rows; } ?>
        </h3>
    </div>
    <form class="needs-validation" method="POST" action="index.php?missing" novalidate>
        <div class="form-row justify-content-around">
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
                        <th>Last Screening</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row=$results->fetch_assoc()){?>
                        <tr>
                            <td><i class="fas fa-ghost"></i></td>
                            <td><?php echo $row['FirstName'];?></td>
                            <td><?php echo $row['LastName'];?></td>
                            <td><?php echo $row['Email'];?></td>
                            <td><?php echo $row['PhoneNumber'];?></td>
                            <td><?php echo $row['Building'];?></td>
                            <td><?php if ($row['LastCheckIn']==""){echo "N/A";}else{echo date('m/d/Y',strtotime($row['LastCheckIn']));}?></td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
<?php }?>
<br />
<br />

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
        table.columns([3,4,5,6]).visible(false);
    }

});
</script>