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