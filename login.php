<?php
include 'includes/config.php';
include 'includes/functions.php';
include 'includes/submit.php';
include 'includes/header.php';
?>
    <!-- Login Buttons -->
    <div class="container">
        <div class="row justify-content-center" style="padding-top:4%;">
            <div class="col-sm-5 text-center">
                <button id="employee-login-button"
                class="btn btn-lg btn-light button-rounded"
                style="border-color: grey; border-size: 3px;"
                data-toggle="modal" data-target="#employee-login-modal">Employee Log In</button>
            </div>
            <div class="col-sm-2 text-center">
                &nbsp;
            </div>
            <div class="col-sm-5 text-center">
                <button id="visitor-login-button" 
                type="button" 
                class="btn btn-lg btn-light button-rounded" 
                style="border-color: grey; border-size: 3px;"
                data-toggle="modal" data-target="#visitor-sign-in-modal">Visitor Sign In</button>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center" style="padding-top:4%;">
            <div class="col-16 text-center">
                
            </div>
        </div>
    </div>

    <!-- Employee Login Form Modal -->
    <div class="container">
        <div id="employee-login-modal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-round">
                    <div class="modal-header">
                        <h2 class="modal-title text-center" id="employee-modal-title">Employee Login</h2>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Employee Login Form -->
                        <form class="animate"
                        action="login.php"
                        method="post">
                            <div class="form-group login">
                                <label for="username">  <i class="fas fa-user"></i> Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter username">
                            </div>
                            <div class="form-group login">
                                <label for="exampleInputPassword1">  <i class="fas fa-lock"></i> Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="employee_submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Sign In Modal  -->
    <div class="container">
        <div id="visitor-sign-in-modal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-round">
                    <div class="modal-header">
                        <h2 class="modal-title text-center" id="visitor-modal-title">Visitor Sign In</h2>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Visitor Sign In Form  -->
                        <form class="animate"
                        action="login.php"
                        method="post">
                            <div class="form-group login">
                                <label for="firstname"> First Name </label>
                                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name..." oninvalid=";" required>
                            </div>
                            <div class="form-group login">
                                <label for="lastname"> Last Name </label>
                                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name..." oninvalid=";" required>
                            </div>
                            <div class="form-group login">
                                <label for="email"> Email </label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email..." oninvalid=";" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="visitor_submit" class="btn btn-success">Submit</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php';?>


<script>
// Get the modal
var employeeLogin = document.getElementById('employee-login-modal');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == employeeLogin) {
        employeeLogin.style.display = "none";
    }
}

var visitorSignIn = document.getElementById('visitor-sign-in-modal');

window.onclick = function(event){
    if (event.target == visitorSignIn) {
        visitorSignIn.style.display = "none";
    }
}
</script>