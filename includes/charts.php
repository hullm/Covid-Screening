<?php 
// Query the database
$results = getRecentResults(5);
?>

<div class="container">
    <div class="form-row justify-content-around">
        <div class="pieChart">
            <canvas id="employeesScreened" width="100" height="100"></canvas>
        </div>
        <div class="pieChart">
            <canvas id="totalScreened" width="100" height="100"></canvas>
        </div>
        <div class="pieChart">
            <canvas id="screeningResults" width="100" height="100"></canvas>
        </div>
    </div>
</div>

<div class = "container">
    <div class = "row justify-content-md-center">
        <table id="report" class="table table-striped table-dark hover">
            <thead>
                <tr><th colspan="9" style="text-align: center;">Most Recent Survey Results</th></tr>
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

<div class="container">
    <div class="form-row justify-content-around">
        <div class="adminResults">
<?php 
if (getUserResults($currentUser)) { ?>
    <div><i class="fas fa-check" style="color:green" ></i>Allowed</div>
<?php } 
else { ?>
    <div><i class="fas fa-times" style="color:red" ></i>Entry Denied</div>
<?php } ?>
        </div>
    </div>
</div>
<br />
<br />

<script>
var ctx = document.getElementById('employeesScreened').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Screened', 'Not Screened'],
        datasets: [{
            data: [<?php echo getEmployeesScreenedToday(); ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 15, 15, 0.8)'
            ],
            borderColor: [
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: 'Employees Screened Today'
        }
    }
});
</script>

<script>
var ctx = document.getElementById('totalScreened').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [<?php echo getScreenedTodayLabels(); ?>],
        datasets: [{
            data: [<?php echo getScreenedTodayData(); ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 15, 15, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(153, 102, 255, 0.8)',
            ],
            borderColor: [
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: 'Total Screened Today'
        }
    }
});
</script>

<script>
var ctx = document.getElementById('screeningResults').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Passed','Failed'],
        datasets: [{
            data: [<?php echo getScreenedResults(); ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 15, 15, 0.8)',
            ],
            borderColor: [
                'rgba(255, 255, 255, 1)',
                'rgba(255, 255, 255, 1)',
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: 'Screening Results'
        }
    }
});
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