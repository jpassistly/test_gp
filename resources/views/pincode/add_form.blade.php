@extends('layouts.master')

@section('title')
    @lang('translation.Pincodes')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <div id="map" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                    <h4 class="card-title mb-4">{{ isset($pincode) ? 'Edit Pincode' : 'Add Pincode' }}</h4>
                    <form action="{{ url('store_pincode') }}" method="post" name="pincode_form" id="pincode_form">
                        @csrf

                        @isset($pincode)
                            <input type="hidden" id="id" name="id" value="{{ $pincode->id }}">
                        @endisset

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Pincode</label>
                                    <input type="text"
                                        class="form-control pincode @error('pincode') is-invalid @enderror" id="pincode"
                                        name="pincode" value="{{ old('pincode', $pincode->pincode ?? '') }}">
                                    @error('pincode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" readonly
                                        class="form-control location @error('location') is-invalid @enderror" id="location"
                                        name="location" value="{{ old('location', $pincode->location ?? '') }}"
                                        placeholder="Click on the Map">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Radius in km</label>
                                    <input type="number" class="form-control @error('radius') is-invalid @enderror"
                                        id="radius" name="radius" value="{{ old('radius', $pincode->radius ?? '') }}">
                                    @error('radius')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        id="address" name="address" value="{{ old('address', $pincode->address ?? '') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="formrow-inputState" name="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                        <option value="Active"
                                            {{ old('status', $pincode->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $pincode->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <button type="submit"
                                        class="btn btn-primary w-md mt-4">{{ isset($pincode) ? 'Update' : 'Submit' }}</button>
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
    <script>
        $('#pincode_form').validate({ // initialize the plugin
            rules: {
                pincode: {
                    required: "enter pincode.",
                    number: "enter only 6 digits numbers."
                },
                location: {
                    required: true
                },
                radius: {
                    required: true
                },
                address: {
                    required: true
                }
            },
            messages: {
                pincode: "Enter Pincode",
                location: "Need Location",
                radius: "Need Radius",
                address: "Need Address"
            }
        });
    </script>
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
                if (isset($farm['lat'])) {
                    echo 'var lat = ' . number_format($farm['lat'], 6) . ';';
                    echo 'var lng = ' . number_format($farm['lon'], 6) . ';';
                } elseif (old('location')) {
                    $latlongvalue = old('location');
                    $ltln = explode(',', $latlongvalue);
                    echo 'var lat = ' . number_format($ltln[0], 6) . ';';
                    echo 'var lng = ' . number_format($ltln[1], 6) . ';';
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

            // Function to update the circle radius
            function updateCircle(lat, lng) {
                var radiusKm = parseFloat(document.getElementById('radius').value) || 0;
                var radiusMeters = radiusKm * 1000; // Convert km to meters

                // Remove existing circle if it exists
                if (window.mapCircle) {
                    map.removeLayer(window.mapCircle);
                }

                // Add a new circle
                window.mapCircle = L.circle([lat, lng], {
                    color: 'blue',
                    fillColor: '#blue',
                    fillOpacity: 0.2,
                    radius: radiusMeters
                }).addTo(map);
            }

            // Initialize the circle
            updateCircle(lat, lng);

            map.on('click', function(e) {
                var clickLat = e.latlng.lat.toFixed(6);  // Limit to 6 digits
                var clickLng = e.latlng.lng.toFixed(6);  // Limit to 6 digits

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
                            var city = address.city || address.town || address.village || '';
                            var state = address.state || '';
                            var area = address.suburb || address.neighbourhood || '';
                            var pincode = address.postcode || '';
                            var latlong = lat + ',' + lon;

                            $('#address').val(fullAddress);
                            $('#city').val(city);
                            $('#area_name').val(area);
                            $('#location').val(latlong);
                            $('#state').val(state);
                            $('#lon').val(lon);
                            $('#lat').val(lat);

                            // Display the address on the map marker
                            marker.bindPopup(
                                `<b>Address:</b> ${fullAddress}<br><b>City:</b> ${city}<br><b>State:</b> ${state}<br><b>Area:</b> ${area}<br><b>Pincode:</b> ${pincode}`
                            ).openPopup();

                            updateCircle(clickLat, clickLng);
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
            $('#masters').addClass('mm-active');
            $('#master_menu').addClass('mm-show');
            $('#pincode').addClass('mm-active');

            getLocation();
        });
    </script>

@endsection
