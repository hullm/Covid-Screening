<?php 
include 'includes/privacy.php';
include 'includes/easteregg.php'; //Shhhh it's a secret...
?>
<div class="footer">
    <button onclick="reloadCSS()"
        class="btn btn-sm btn-link">
    </button>
    <button id="privacy_policy"
        class="btn btn-sm btn-link"
        data-toggle="modal" 
        data-target="#privacyPolicy">
        Privacy Policy
    </button>
    &copy;2020-2021 Lake George CSD
    <button id="easter_egg"
        class="btn btn-sm btn-link"
        data-toggle="modal"
        data-target="#easterEgg">
    </button>
</div>
</body>
</html>

<!-- Handles typewriter effect for on the login page -->
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

function reloadCSS() {
    var h, a, f;
    a = document.getElementsByTagName('link');
    for (h = 0; h < a.length; h++) {
        f = a[h];
        if (f.rel.toLowerCase().match(/stylesheet/) && f.href) {
            var g = f.href.replace(/(&|\?)rnd=\d+/, '');
            f.href = g + (g.match(/\?/) ? '&' : '?');
            f.href += 'rnd=' + (new Date().valueOf());
        }
    }
}

</script>
