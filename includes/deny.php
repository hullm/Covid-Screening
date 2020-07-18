<div id="overlay" onclick="off()">
    <div id="text"><i class="fas fa-times" style="color:red" ></i></br>Entry Denied</div>
</div>

<script>
function off() {
    document.getElementById("overlay").style.display = "none";
    location.replace("index.php?logout");
}
</script>