<style>
    @keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0; }
    100% { opacity: 1; }
}

.dot {
    position: absolute;
    top: 5px; /* Adjust based on your icon size */
    right: 5px; /* Adjust based on your icon size */
    height: 10px; /* Size of the dot */
    width: 10px; /* Size of the dot */
    background-color: red; /* Dot color */
    border-radius: 50%;
    border: 2px solid white; /* Optional: Add a border to make it look cleaner */
    animation: blink 1s infinite; /* Blinking animation */
}



</style>


<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="index" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ URL::asset ('/assets/images/logo.svg') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img class="logo-lg2" src="{{ URL::asset ('/assets/images/logo-dark.png') }}" alt="" height="17">
                    </span>
                </a>

                <a href="index" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ URL::asset ('/assets/images/logo_green.png') }}" alt="" height="25">
                    </span>
                    <span class="logo-lg">
                        <img class="logo-lg2" src="{{ URL::asset ('/assets/logo.png') }}" alt="" height="45">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
            </div>

            <!-- <div class="dropdown d-none d-lg-inline-block ms-1">
            <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                <i class="bx bx-fullscreen"></i>
            </button>
        </div> -->
    <div class="dropdown d-none d-lg-inline-block ms-1">
        <button type="button" onclick="notification()" class="btn header-item noti-icon waves-effect">
            <i class="fa fa-bell" style="font-size:24px; position: relative;"></i>
            <span id="notificationDot" class="dot d-none"></span>
        </button>
    </div>




            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ isset(Auth::user()->avatar) ? asset(Auth::user()->avatar) : asset('/assets/images/users/avatar-1.jpg') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ucfirst(Auth::user()->name)}}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ url('contacts-profile')}}"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i> <span
                            key="t-profile">@lang('translation.Profile')</span></a>
                    <a class="dropdown-item d-block" href="#" data-bs-toggle="modal"
                        data-bs-target=".change-password"><i class="bx bx-wrench font-size-16 align-middle me-1"></i>
                        <span key="t-settings">@lang('translation.Change_password')</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout">@lang('translation.Logout')</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>

                
            </div>

            {{-- <div class="dropdown d-inline-block">
            <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                <i class="bx bx-cog bx-spin"></i>
            </button>
        </div> --}}

        </div>
    </div>
</header>

<div class="modal fade" id="deliveryModal" tabindex="-1" aria-labelledby="deliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deliveryModalLabel">Today's Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Notes</th>
                            <th>Time</th>
                            
                        </tr>
                    </thead>
                    <tbody id="deliveryTableBody">
                        <!-- Table rows will be inserted here by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--  Change-Password example -->
<div class="modal fade change-password" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="change-password">
                    @csrf
                    <input type="hidden" value="{{ Auth::user()->id }}" id="data_id">

                    <div class="mb-3">
                        <label for="current_password">Current Password</label>
                        <div class="input-group">
                            <input id="current-password" type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                name="current_password" autocomplete="current_password"
                                placeholder="Enter Current Password" value="{{ old('current_password') }}">
                            <button type="button" class="btn btn-outline-secondary toggle-password"
                                data-target="current-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="text-danger" id="current_passwordError" data-ajax-feedback="current_password"></div>
                    </div>

                    <div class="mb-3">
                        <label for="newpassword">New Password</label>
                        <div class="input-group">
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password"
                                autocomplete="new_password" placeholder="Enter New Password">
                            <button type="button" class="btn btn-outline-secondary toggle-password"
                                data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="text-danger" id="passwordError" data-ajax-feedback="password"></div>
                    </div>

                    <div class="mb-3">
                        <label for="userpassword">Confirm Password</label>
                        <div class="input-group">
                            <input id="password-confirm" type="password" class="form-control"
                                name="password_confirmation" autocomplete="new_password"
                                placeholder="Enter New Confirm password">
                            <button type="button" class="btn btn-outline-secondary toggle-password"
                                data-target="password-confirm">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="text-danger" id="password_confirmError" data-ajax-feedback="password-confirm"></div>
                    </div>

                    <div class="mt-3 d-grid">
                        <button class="btn btn-primary waves-effect waves-light UpdatePassword"
                            data-id="{{ Auth::user()->id }}" type="submit">
                            Update Password
                        </button>
                    </div>
                </form>


                <script>
                $(document).ready(function() {
                    // Toggle password visibility
                    $('.toggle-password').on('click', function() {
                        let inputField = $('#' + $(this).data(
                        'target')); // Get the targeted input field
                        let inputType = inputField.attr('type');
                        let icon = $(this).find('i'); // Get the icon inside the button

                        if (inputType === 'password') {
                            inputField.attr('type', 'text'); // Change to text to show password
                            icon.removeClass('fa-eye').addClass(
                            'fa-eye-slash'); // Change icon to eye-slash
                        } else {
                            inputField.attr('type', 'password'); // Change to password to hide it
                            icon.removeClass('fa-eye-slash').addClass(
                            'fa-eye'); // Change icon back to eye
                        }
                    });
                });
                </script>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>

function checkNotifications() {
    $.ajax({
        url: "{{ env('API_APP_URL') }}/api/check-notifications", // Replace with your actual endpoint
        type: 'POST',
        success: function(response) {
            if (response.success) {
                const deliveryList = response.data;

                // Check if there are any records for today
                if (deliveryList.length > 0) {
                    // Show the notification dot if there are records
                    $('#notificationDot').removeClass('d-none');
                    console.log("New records found:", deliveryList);
                } else {
                    // Hide the notification dot if no records
                    $('#notificationDot').addClass('d-none');
                }
            } else {
                console.log("No new records found.");
            }
        },
        error: function(error) {
            console.error("Error fetching notifications:", error);
        }
    });
}

// Optionally, set up an interval to check notifications periodically
setInterval(checkNotifications, 6000); // Check every minute
// Optionally, call the notification function at intervals to keep checking for new notifications
//setInterval(notification, 3000); // Check every minute
function formatDate(dateString) {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
    const date = new Date(dateString);
    return date.toLocaleString('en-GB', options).replace(',', '');
}

function notification() {
    $.ajax({
        url: "{{ env('API_APP_URL') }}/api/check-notifications2",
        type: 'POST',
        success: function(response) {
            if (response.success) {
                const deliveryList = response.data;
                console.log(deliveryList);

                // Show the notification dot if there are records
                if (deliveryList.length > 0) {
                    $('#notificationDot').removeClass('d-none');
                    
                    // Create table rows from the deliveryList data
                    let tableRows = '';
                    deliveryList.forEach(item => {
                        tableRows += `
                            <tr>
                                <td>${item.name}</td>
                                <td>${item.mobile}</td>
                                <td>${item.debit_credit_status}</td>
                                <td>${item.amount}</td>
                                <td>${item.remarks}</td>
                                <td>${item.notes}</td>
                               <td>${formatDate(item.created_at)}</td>
                            </tr>
                        `;
                    });

                    // Insert the rows into the table body in the modal
                    $('#deliveryTableBody').html(tableRows);

                    // Ensure modal is displayed after updating table content
                    setTimeout(() => {
                        $('#deliveryModal').modal('show');
                    }, 100);
                } else {
                    $('#notificationDot').addClass('d-none'); // Hide the dot if no records
                }
            } else {
                console.log("No new records found.");
            }
        },
        error: function(error) {
            console.error("Error fetching notifications:", error);
        }
    });
}



</script>