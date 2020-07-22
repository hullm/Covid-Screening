<?php
include 'includes/config.php';
include 'includes/functions.php';
include 'includes/submit.php';
include 'includes/header.php';
include 'includes/footer.php';
$buildings = explode(',',$config['sites']);

//Get the missing data
$results = getMissingReports();
?>

<div class = "container">
    <div class = "row justify-content-md-center">
        <h4>Today's Missing Check Ins</h>
    </div>
    <div class = "row justify-content-md-center">
        <table id="missing" class="table table-striped table-dark hover">
            <thead>
                <tr>
                    <th></th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row=$results->fetch_assoc()){?>
                    <tr>
                        <td><i class="fas fa-ghost"></i></td>
                        <td><?php echo $row['FirstName'];?></td>
                        <td><?php echo $row['LastName'];?></td>
                        <td><?php echo $row['Email'];?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
</div>
<br />
<br />

<script>
$(document).ready(function() {
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
});
</script>