<div id="overlay" onclick="off()">
    <div id="text"><i class="fas fa-ban" style="color:red" ></i></br><?php echo $config['denytext'];?></div>
</div>

<script>
function off() {
    document.getElementById("overlay").style.display = "none";
    
    // When an admin finishes the survey we don't want to log them out
    if ("<?php echo $_SESSION['userType']?>" != "Admin") {
        location.replace("index.php?logout");
    }
}
</script>