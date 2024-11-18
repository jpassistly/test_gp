@extends('layouts.master')

@section('title') @lang('translation.inventry_list') @endsection

@section('content')

<div class="row">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Add Farm</h4>
                <form action="{{ url('inventory_form') }}" method="post" name="line_form" id="line_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Dairy Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <input type="text" id="state" class="form-control" name="state">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" id="area1" name="area">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" id="pincode1" name="pincode">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="text" id="lat" name="lat" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="text" id="lon" name="lon" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-md mt-4">Submit</button>
                            </div>
                        </div>
                    </div>

                    <!-- Add a div for the map -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <div id="map" style="height: 400px;"></div>
                            </div>
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
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        // Initialize the map
        var map = L.map('map').setView([lat, lng], 13);

        // Set up the OSM layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Add a marker to the map
        var marker = L.marker([lat, lng]).addTo(map);

        map.on('click', function(e) {
            var clickLat = e.latlng.lat;
            var clickLng = e.latlng.lng;

            // Move the marker to the clicked location
            marker.setLatLng([clickLat, clickLng]);

            $.ajax({
                url: `https://nominatim.openstreetmap.org/reverse?format=json&lat=${clickLat}&lon=${clickLng}`,
                method: 'GET',
                success: function(data) {
                    if (data.address) {
                        var address = data.address;
                        var fullAddress = data.display_name;
                        var lat = data.lat;
                        var lon = data.lon;
                        var city = address.city || address.town || address.village || '';
                        var state = address.state || '';
                        var area = address.suburb || address.neighbourhood || '';
                        var pincode = address.postcode || '';

                        $('#address').val(fullAddress);
                        $('#city').val(city);
                        $('#state').val(state);
                        $('#area').val(area);
                        $('#pincode').val(pincode);
                        $('#lon').val(lon);
                        $('#lat').val(lat);

                        // Display the address on the map marker
                        marker.bindPopup(`<b>Address:</b> ${fullAddress}<br><b>City:</b> ${city}<br><b>State:</b> ${state}<br><b>Area:</b> ${area}<br><b>Pincode:</b> ${pincode}`).openPopup();
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });

        $.ajax({
            url: `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
            method: 'GET',
            success: function(data) {
                if (data.address) {
                    var address = data.address;
                    var fullAddress = data.display_name;
                    var city = address.city || address.town || address.village || '';
                    var state = address.state || '';
                    var area = address.suburb || address.neighbourhood || '';
                    var pincode = address.postcode || '';
                    var lat = data.lat;
                    var lon = data.lon;

                    $('#address').val(fullAddress);
                    $('#city').val(city);
                    $('#state').val(state);
                    $('#area1').val(area);
                    $('#pincode1').val(pincode);
                    $('#lon').val(lon);
                    $('#lat').val(lat);

                    // Optionally display the address on the map div
                    marker.bindPopup(`<b>Address:</b> ${fullAddress}<br><b>City:</b> ${city}<br><b>State:</b> ${state}<br><b>Area:</b> ${area}<br><b>Pincode:</b> ${pincode}`).openPopup();
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
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
        $('#masters').addClass('mm-active');
        $('#master_menu').addClass('mm-show');
        $('#delivery_lines').addClass('mm-active');

        $('#line_form').validate({
            rules: {
                name: { required: true },
                status: { required: true }
            },
            messages: {
                name: "Enter Line",
                status: "Select status"
            }
        });

        // Get the location on page load
        getLocation();
    });
</script>


@endsection
