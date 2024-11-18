@extends('layouts.master')

@section('title')
    @lang('translation.user_profile')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">{{ isset($user->mobile) ? 'Edit Customer' : 'Add Customer' }}</h4>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <div id="map" style="height: 400px;"></div>
                    </div>
                    <!-- Single Form for Add and Update -->
                    <form action="{{ route('customer_create') }}" method="post" id="user_form"
                        enctype="multipart/form-data">

                        @csrf
                        <!-- Form Fields -->

                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="hidden" id="id" name="id" class="form-control"
                                        value="{{ old('id', $user->id ?? '') }}">
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ old('name', $user->name ?? '') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select ">
                                        <option value="M"
                                            {{ old('gender', $user->gender ?? '') == 'M' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="F"
                                            {{ old('gender', $user->gender ?? '') == 'F' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                    @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Mobile -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" id="mobile" name="mobile" class="form-control"
                                        value="{{ old('mobile', $user->mobile ?? '') }}">
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <!-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="hidden" id="address" name="address" class="form-control"
                                        value="{{ old('address', $user->address ?? '') }}">
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Door No</label>
                                    <input type="text" id="door_no" name="door_no" class="form-control"
                                        value="{{ old('door_no', $user->door_no ?? '') }}">
                                    <input type="hidden" id="address" name="address" class="form-control"
                                        value="{{ old('address', $user->address ?? '') }}">
                                    @error('door_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Flat No</label>
                                    <input type="text" id="flat_no" name="flat_no" class="form-control"
                                        value="{{ old('flat_no', $user->flat_no ?? '') }}">
                                    @error('flat_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Floor No</label>
                                    <input type="text" id="floor_no" name="floor_no" class="form-control"
                                        value="{{ old('floor_no', $user->floor_no ?? '') }}">
                                    @error('floor_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Street</label>
                                    <input type="text" id="street" name="street" class="form-control"
                                        value="{{ old('street', $user->street ?? '') }}">
                                    @error('street')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Land Mark</label>
                                    <input type="text" id="land_mark" name="land_mark" class="form-control"
                                        value="{{ old('land_mark', $user->land_mark ?? '') }}">
                                    @error('land_mark')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Area -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Area</label>
                                    <select id="area_id" name="area_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach ($area as $areas)
                                            <option {{ $areas['id'] == ($user['area_id'] ?? '') ? 'selected' : '' }}
                                                value="{{ $areas->id }}">{{ $areas->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('area_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <select id="city" name="city" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach ($city as $citys)
                                            <option {{ $citys['id'] == ($user['city'] ?? '') ? 'selected' : '' }}
                                                value="{{ $citys->id }}">{{ $citys->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Delivery Line</label>
                                    <select id="deliverylines_id" name="deliverylines_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach ($delivery as $d)
                                            <option {{ $d['id'] == ($user['temp_deliverylines_id'] ?? '') ? 'selected' : '' }}
                                                value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('deliverylines_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Pincode -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pincode</label>
                                    <select id="pincode_id" name="pincode_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach ($pincode as $qlist)
                                            <option {{ $qlist['id'] == ($user['pincode_id'] ?? '') ? 'selected' : '' }}
                                                value="{{ $qlist['id'] }}">{{ $qlist['pincode'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('pincode_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Pincode -->
                            



                            <!-- Lat/Lon -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Lat/Lon</label>
                                    <input type="text" id="latlon" name="latlon" class="form-control"
                                        value="{{ old('latlon', $user->latlon ?? '') }}">
                                    @error('latlon')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Subscription Status -->
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Subscription Status</label>
                                    <select name="sub_status" class="form-select">
                                        <option value="Subscribed"
                                            {{ old('sub_status', $user->sub_status ?? '') == 'Subscribed' ? 'selected' : '' }}>
                                            Subscribed</option>
                                        <option value="Unsubscribed"
                                            {{ old('sub_status', $user->sub_status ?? '') == 'Unsubscribed' ? 'selected' : '' }}>
                                            Unsubscribed</option>
                                    </select>
                                    @error('sub_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}

                            <!-- Profile Picture -->
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" id="profile_pic" name="profile_pic">

                                    <!-- Show current profile picture if updating -->
                                    @if (isset($user) && $user->profile_pic)
                                        <img src="{{ asset($user->profile_pic) }}" alt="Profile Pic" width="100" height="100">
                                    @endif

                                    @error('profile_pic')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}

                            <!-- Home Image -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Home Image</label>
                                    <input type="file" class="form-control" id="home_img" name="home_img">

                                    <!-- Show current home image if updating -->
                                    @if (isset($user) && isset($user->home_img))
                                        <img src="{{ asset($user->home_img) }}" alt="Home Image" width="100"
                                            height="100">
                                    @endif

                                    @error('home_img')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                            <!-- Location Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Location Status</label>
                                    <select name="loc_status" class="form-select ">
                                        <option value="Active"
                                            {{ old('loc_status', $user->loc_status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Approve</option>
                                        <option value="Inactive"
                                            {{ old('loc_status', $user->loc_status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Reject</option>
                                    </select>
                                    @error('loc_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Active"
                                            {{ old('status', $user->status ?? '') == 'Active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="Inactive"
                                            {{ old('status', $user->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary w-md">
                                    {{ isset($user->mobile) ? 'Update' : 'Submit' }}
                                </button>
                            </div>

                        </div>

                    </form>

                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
<!-- <script>
     document.getElementById('user_form').addEventListener('submit', function (event) {
        // Get values from input fields
        var doorNo = document.getElementById('door_no').value;
        var floorNo = document.getElementById('floor_no').value;
        var street = document.getElementById('street').value;
        var landMark = document.getElementById('land_mark').value;

        // Combine values and set to the address field, separating with commas
        var address = [doorNo, floorNo, street, landMark].filter(Boolean).join(', ');

        // Set the hidden address field
        document.getElementById('address').value = address;
    });
</script> -->
@section('script')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
    @php
        if (isset($user->latlon)) {
            $latlon = explode(',', $user->latlon);
            $lat = $latlon[0];
            $lon = $latlon[1];

            echo 'var lat = ' . number_format($lat, 6) . ';';
            echo 'var lng = ' . number_format($lon, 6) . ';';
        } elseif (old('latlon')) {
            $latlon = explode(',', old('latlon'));
            $lat = $latlon[0];
            $lon = $latlon[1];

            echo 'var lat = ' . number_format($lat, 6) . ';';
            echo 'var lng = ' . number_format($lon, 6) . ';';
        } else {
            echo 'var lat = parseFloat(position.coords.latitude.toFixed(6));';
            echo 'var lng = parseFloat(position.coords.longitude.toFixed(6));';
        }
    @endphp

    // Initialize the map
    var map = L.map('map').setView([lat, lng], 13);

    // Set up the OSM layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    // Add a marker to the map
    var marker = L.marker([lat, lng]).addTo(map);

    map.on('click', function(e) {
        var clickLat = e.latlng.lat.toFixed(6); // Limit to 6 digits
        var clickLng = e.latlng.lng.toFixed(6); // Limit to 6 digits

        // Move the marker to the clicked location
        marker.setLatLng([clickLat, clickLng]);

        $.ajax({
            url: `https://nominatim.openstreetmap.org/reverse?format=json&lat=${clickLat}&lon=${clickLng}`,
            method: 'GET',
            success: function(data) {
                if (data.address) {
                    var address = data.address;
                    var fullAddress = data.display_name;
                    var lat = parseFloat(data.lat).toFixed(6);
                    var lon = parseFloat(data.lon).toFixed(6);
                    var latlong = lat + ',' + lon;

                    // Set values for specific fields
                    var doorNo = address.house_number || '';
                    var floorNo = address.level || '';
                    var street = address.road || '';
                    var landMark = address.landmark || '';

                    $('#address').val(fullAddress);
                    $('#latlon').val(latlong);
                    $('#door_no').val(doorNo);
                    // $('#floor_no').val(floorNo);
                    $('#street').val(street);
                    $('#land_mark').val(landMark);

                    // Display the address on the map marker
                    marker.bindPopup(
                        `<b>Address:</b> ${fullAddress}<br><b>Door No:</b> ${doorNo}<br><b>Floor No:</b> ${floorNo}<br><b>Street:</b> ${street}<br><b>Landmark:</b> ${landMark}`
                    ).openPopup();
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });
}

        function showError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
            }
        }

        $(document).ready(function() {
            getLocation();
        });

        $(document).ready(function() {
            $('#customer_main_menu').addClass('mm-active');
            $('#customer_menu').addClass('mm-show');
            $('#customer').addClass('mm-active');
        });
    </script>
@endsection
