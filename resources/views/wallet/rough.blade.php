<?php
require_once('header1.php');
ini_set("display_errors", 1);

// Safely get the redirect URL if it exists
$ur = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : null;
require "google/vendor/autoload.php";

// Set up Google client credentials
$clientId = "38970175349-5ugc0ttas6s7f01dsgma2gnp5j3dfc1s.apps.googleusercontent.com";
$clientSecret = "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";
// $redirectURI = $ur ? $ur : "https://teesandtoes.com/website/demo/gourl.php";
$redirectURI = "https://teesandtoes.com/website/demo/gourl.php";

// Initialize Google client
$client = new Google_Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectURI($redirectURI);
$client->addScope("email");
$client->addScope("profile");

// Optionally print request data for debugging (use carefully)
// print_r($_REQUEST);

// Avoid using `extract($_REQUEST)` for security reasons
?>

<!-- Body Container -->
<div id="page-content">
    <!--Page Header-->
    <div class="page-header text-center">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-content-between align-items-center">
                    <div class="page-title">
                        <h1>Login</h1>
                    </div>
                    <!--Breadcrumbs-->
                    <div class="breadcrumbs"><a href="index.php" title="Back to the home page">Home</a><span class="title"><i class="icon anm anm-angle-right-l"></i>My Account</span><span class="main-title fw-bold"><i class="icon anm anm-angle-right-l"></i>Create an Account</span></div>
                    <!--End Breadcrumbs-->
                </div>
            </div>
        </div>
    </div>
    <!--End Page Header-->

    <!--Main Content-->
    <div class="container">
        <div class="login-register pt-2">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                    <div class="inner h-100">
                        <form method="post" action="#" id="customer-form" class="customer-form">
                            <h2 class="text-center fs-4 mb-4">Login here if you are a Existing customer</h2>
                            <div class="form-row">
                                <!--<div class="form-group col-12">-->
                                <!--    <label for="CustomerUsername" class="d-none">Username <span class="required">*</span></label>-->
                                <!--    <input type="text" name="customer[Username]" placeholder="Username" id="CustomerUsername" value="" required />-->
                                <!--</div>-->
                                <div class="form-group col-12">
                                    <label for="CustomerEmail" class="d-none">Email <span class="required">*</span></label>
                                    <input type="email" name="customer[email]" placeholder="Email" id="CustomerEmail" value="" autocomplete="new-email" required />
                                    <input type="hidden" name="type" value="9">
                                </div>
                                <div class="form-group col-12">
                                    <label for="CustomerPassword" class="d-none">Password <span class="required">*</span></label>
                                    <input type="password" name="customer[password]" placeholder="Password" id="CustomerPassword" value="" autocomplete="new-password" required />

                                </div>
                                <!--<div class="form-group col-12">-->
                                <!--    <label for="CustomerConfirmPassword" class="d-none">Confirm Password <span class="required">*</span></label>-->
                                <!--    <input id="CustomerConfirmPassword" type="Password" name="customer[ConfirmPassword]" placeholder="Confirm Password" required />                         	-->
                                <!--</div>-->
                                <!--<div class="form-group col-12">-->
                                <!--    <div class="login-remember-forgot d-flex justify-content-between align-items-center">-->
                                <!--        <div class="agree-check customCheckbox">-->
                                <!--            <input id="agree" name="agree" type="checkbox" value="agree" required />-->
                                <!--            <label for="agree"> I agree to terms & Policy.</label>-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="form-group col-12 mb-0">
                                    <input type="submit" class="btn btn-primary btn-lg w-100" value="Login" />
                                </div>
                            </div>

                            <div class="login-signup-text mt-4 mb-2 fs-6 text-center text-muted">If You Are New Person?
                                <a href="register.php" class="btn-link">Register Now</a>
                            </div>
                        </form>
                        <div class="login-signup-text mt-4 mb-2 fs-6 text-center text-muted">
                            <a href="./forget.php" class="btn-link"><ins>forget password</ins>!</a>
                        </div>
                        <div class="form-group col-12 mb-0">
                            <a href="<?php echo $client->createAuthUrl() ?>" class="btn btn-primary btn-lg w-100" />Sign With Google</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Main Content-->
</div>
<!-- End Body Container -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('customer-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Create a FormData object to collect the form data
        var formData = new FormData(this);

        // Send the form data using fetch API
        fetch('add_to_wishlist.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Parse the response as JSON
            .then(data => {
                // Check the response status
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: data.message,
                    }).then(() => {
                        // Optionally redirect after success
                        window.location.href = 'create_user.php?id=' + data.id;
                        // setsession()
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Unexpected Error',
                    text: 'Something went wrong, please try again later.',
                });
            });
    });
</script>

<?php
require_once('footer1.php');
?>