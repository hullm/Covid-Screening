<div id="overlay" onclick="off()">
    <div id="text"><i class="fas fa-check" style="color:green" ></i></br>Allowed</div>
</div>

<script>
function off() {
    document.getElementById("overlay").style.display = "none";
    if ("<?php echo $_SESSION['userType']?>" != "Admin") {
        location.replace("index.php?logout");
    }
}
</script>