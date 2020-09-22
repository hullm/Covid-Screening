<?php 

// Remove old data
purgeOldData($config['purgeafter']);

// Query the database
$results = getRecentResults(10);

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
    <div class="form-row justify-content-around">
        <div class="lineChart">
            <canvas id="screeningHistory" width="100" height="100"></canvas>
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
                                echo "<i class=\"fas fa-ban\" style=\"color:red\" title=\"Denied\"></i>";
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
                        <td><?php echo fixUserType($row['UserType']);?></td>
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
    <div><i class="fas fa-check" style="color:green" ></i> Allowed</div>
<?php } 
else { ?>
    <div><i class="fas fa-ban" style="color:red" ></i > Entry Denied</div>
<?php } ?>
        </div>
    </div>
</div>
<br />
<br />

<!-- Employees Screened Today Doughnut Chart -->
<script>
var ctx = document.getElementById('employeesScreened').getContext('2d');
var employeesScreened = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Screened', 'Not Screened'],
        render: 'value',
        datasets: [{
            data: [<?php echo getEmployeesScreenedToday(); ?>],
            backgroundColor: [
                'rgba(0, 204, 0, 0.8)', <!-- Green -->, 
                'rgba(255, 15, 15, 0.8)' <!-- Red -->,
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
        },
        plugins: {
            labels: { render: 'value',
                fontColor: '#000',
                position: 'inside'
            }
        },
        onClick: (evt) => {
            var activeElement = employeesScreened.getElementAtEvent(evt)[0];
            var label = employeesScreened.data.labels[activeElement._index];
            var value = employeesScreened.data.datasets[activeElement._datasetIndex].data[activeElement._index];
            var today = new Date();
            var date = today.getFullYear() + '-' + ("0" + (today.getMonth() + 1)).slice(-2) + '-' + ("0" + today.getDate()).slice(-2);
            if (label == "Screened") {
                window.open("index.php?reports&LoadReport&fromDate=" + date +"&toDate=" + date + "&userType=Employee&building=All&passed=All","_self");
            }
            else {
                window.open("index.php?missing&LoadMissing&building=All","_self");
            }
        }
    }
});
</script>

<!-- Total Screened Today Doughnut Chart -->
<script>
var ctx = document.getElementById('totalScreened').getContext('2d');
var totalScreened = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Employees','Students','Visitors'],
        datasets: [{
            data: [<?php echo getScreenedTodayData(); ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)', <!-- Blue -->
                'rgba(178, 102, 255, 0.8)', <!-- Purple -->
                'rgba(255, 206, 86, 0.8)', <!-- Yellow -->
            ],
            borderColor: [
                'rgba(255, 255, 255, 1)',
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
            text: 'Total Screened Today'
        },
        plugins: {
            labels: { render: 'value',
                fontColor: '#000',
                position: 'inside'
            }
        },
        onClick: (evt) => {
            var activeElement = totalScreened.getElementAtEvent(evt)[0];
            var label = totalScreened.data.labels[activeElement._index];
            label = label.slice(0,label.length - 1); // Drop the s off the label
            var value = totalScreened.data.datasets[activeElement._datasetIndex].data[activeElement._index];
            var today = new Date();
            var date = today.getFullYear() + '-' + ("0" + (today.getMonth() + 1)).slice(-2) + '-' + ("0" + today.getDate()).slice(-2);
            window.open("index.php?reports&LoadReport&fromDate=" + date +"&toDate=" + date + "&userType=" + label + "&building=All&passed=All","_self");
        }
    }
});
</script>

<!-- Screening Results Doughnut Chart -->
<script>
var ctx = document.getElementById('screeningResults').getContext('2d');
var screeningResults = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Passed','Failed'],
        datasets: [{
            data: [<?php echo getScreenedResults(); ?>],
            backgroundColor: [
                'rgba(0, 175, 0, 0.8)', <!-- Green -->, 
                'rgba(255, 15, 15, 0.8)' <!-- Red -->,
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
        },
        plugins: {
            labels: { render: 'value',
                fontColor: '#000',
                position: 'inside'
            }
        },
        onClick: (evt) => {
            var activeElement = screeningResults.getElementAtEvent(evt)[0];
            var label = screeningResults.data.labels[activeElement._index];
            var value = screeningResults.data.datasets[activeElement._datasetIndex].data[activeElement._index];
            var today = new Date();
            var date = today.getFullYear() + '-' + ("0" + (today.getMonth() + 1)).slice(-2) + '-' + ("0" + today.getDate()).slice(-2);
            if (label == "Passed") {
                window.open("index.php?reports&LoadReport&fromDate=" + date +"&toDate=" + date + "&userType=All&building=All&passed=True","_self");
            }
            else {
                window.open("index.php?reports&LoadReport&fromDate=" + date +"&toDate=" + date + "&userType=All&building=All&passed=False","_self");
            }
        }
    }
});
</script>

<!-- Line Graph -->
<script>

var ctx = document.getElementById('screeningHistory');

var width = window.innerWidth;
if (width<480) {
    ctx.height = "65";
}
else {
    ctx.height = "25";
};

var screeningHistory = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php echo getScreenedHistoryLabel(14); ?>],
        datasets: [{
            label: ['Passed'],
            fill: false,
            borderColor: 'rgba(0, 204, 0, 0.8)',
            backgroundColor: 'rgba(0, 175, 0, 0.8)', <!-- Green -->

            data: [<?php echo getScreeningHistoryData("Passed",14); ?>],
        },
        {
            label: ['Failed'],
            fill: false,
            borderColor: 'rgba(255, 15, 15, 0.8)',
            backgroundColor: 'rgba(255, 15, 15, 0.8)', <!-- Red -->
            data: [<?php echo getScreeningHistoryData("Failed",14); ?>],
        },
        {
            label: ['Employees'],
            fill: false,
            borderColor: 'rgba(54, 162, 235, 0.8)',
            backgroundColor: 'rgba(54, 162, 235, 0.8)', <!-- Blue -->
            data: [<?php echo getScreeningHistoryData("Employee",14); ?>],
        },
        {
            label: ['Students'],
            fill: false,
            borderColor: 'rgba(178, 102, 255, 0.8)',
            backgroundColor: 'rgba(178, 102, 255, 0.8)', <!-- Purple -->
            data: [<?php echo getScreeningHistoryData("Student",14); ?>],
        },
        {
            label: ['Visitors'],
            fill: false,
            borderColor: 'rgba(255, 206, 86, 0.8)',
            backgroundColor: 'rgba(255, 206, 86, 0.8)', <!-- Yellow -->
            data: [<?php echo getScreeningHistoryData("Visitor",14); ?>],
        }
        ]

    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: 'Screening History'
        },
        plugins: {
            labels: { render: 'value',
                fontColor: '#000',
                position: 'inside'
            }
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        onClick: (evt) => {
            var activeElement = screeningHistory.   getElementAtEvent(evt)[0];
            var value = screeningHistory.data.datasets[activeElement._datasetIndex].label;
            var date = screeningHistory.data.labels[activeElement._index];
            date = new Date(date);
            date = date.getFullYear() + '-' + ("0" + (date.getMonth() + 1)).slice(-2) + '-' + ("0" + date.getDate()).slice(-2);
            if (value == "Passed") {
                window.open("index.php?reports&LoadReport&fromDate=" + date +"&toDate=" + date + "&userType=All&building=All&passed=True","_self");
            }
            else if (value == "Failed") {
                window.open("index.php?reports&LoadReport&fromDate=" + date +"&toDate=" + date + "&userType=All&building=All&passed=False","_self");
            }
            else {
                value = value[0].slice(0,value[0].length - 1); // Drop the s off the label
                window.open("index.php?reports&LoadReport&fromDate=" + date +"&toDate=" + date + "&building=All&userType=" + value,"_self");
            }
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

<script>
// Check for new data
    setInterval(function() {
        $.get("api.php?GetLatestEntry", function(data, status){
            if (<?php echo GetLatestEntry();?> < data){
                window.location.href = "index.php";
            }
        });
    }, 5000);
</script>