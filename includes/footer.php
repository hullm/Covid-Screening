<?php 
include 'includes/privacy.php';
include 'includes/easteregg.php';
?>
<div class="footer">
    <button id="privacy_policy"
        class="btn btn-sm btn-link"
        data-toggle="modal" 
        data-target="#privacyPolicy">
        Privacy Policy
    </button>
    &copy;2020 Lake George CSD

    <button id="easter_egg"
        class="btn btn-sm btn-link"
        data-toggle="modal"
        data-target="#easterEgg">
    </button>
</div>
</body>
</html>
<!-- Handles typewriter effect for subtitle -->
<script>
var i = 0;
var txt = '<?php echo str_replace("'","\'",$config['logontext']); ?>';
var speed = 50;

function typeWriter() {
    if (i < txt.length) {
        document.getElementById("subtitle").innerHTML += txt.charAt(i);
        i++;
        setTimeout(typeWriter, speed);
    }
}
</script>