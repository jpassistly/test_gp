@extends('layouts.master')

@section('title')
@lang('translation.Pincodes')
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="delivery_line" action="{{ route('route-mapping.store') }}" method="POST">@csrf
                            <div class="mb-3">
                                <label class="form-label">Delivery Line <span class='text-danger'>*</span></label>
                                <select id="category_id" name="category_id" class="form-select select2" onchange="submitForm()">
                                    <option value="">Select the option</option>
                                    @foreach ($delivery_line as $clist)
                                    <option value="{{ $clist->id }}"
                                        {{ request()->get('category_id') == $clist->id ? 'selected' : '' }}>
                                        {{ $clist->name }}
                                    </option>
                                    @endforeach
                                </select>

                                @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </form>
                    </div>

                    <script>
                        function submitForm() {
                            document.getElementById('delivery_line').submit();
                        }
                    </script>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <div id="map" style="height:800px"></div>
                            
                            <!-- Download Map Button -->
                            <button onclick="downloadMapAsPDF()" class="btn btn-primary mt-3">Download Map as PDF</button>

                            <h4 class="pt-4">Location Details</h4>
                            <p class="pt-2">Location count: <span id='location_total_count'></span></p>
                            <p class="text-success">Active location: <span id='location_total_active'></span></p>
                            <p class="text-danger">Inactive location: <span id='location_total_inactive'></span></p>
                        </div>
                    </div>
                </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<!-- Leaflet Routing Machine CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<!-- Leaflet Routing Machine JS -->
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<!-- html2canvas and jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

<style>
    .fa-marker {
        width: 24px;
        height: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Hide alternative routes */
    .leaflet-routing-alt {
        display: none !important;
    }
</style>

<script>
    let isDownloadMode = false; // Flag to control route display during download

    const customers = @json($customer);

    document.getElementById('location_total_count').textContent = customers.length;
    document.getElementById('location_total_active').textContent = '{{$active}}';
    document.getElementById('location_total_inactive').textContent = '{{$inactive}}';

    function createMarkerIcon(color) {
        return L.divIcon({
            html: `<i class="fa fa-map-marker" aria-hidden="true" style="color: ${color}; font-size: 24px;"></i>`,
            className: 'fa-marker'
        });
    }

    function initMap(lat, lng) {
        const map = L.map('map').setView([lat, lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([lat, lng], {
            icon: L.divIcon({
                html: '<i class="fa fa-map-marker" aria-hidden="true" style="color: blue; font-size: 24px;"></i>',
                className: 'fa-marker'
            })
        }).addTo(map).bindPopup(`<b>Your Location</b>`).openPopup();

        const deliveryLocations = [];

        customers.forEach(customer => {
            if (customer.latlon) {
                const [custLat, custLng] = customer.latlon.split(',').map(coord => parseFloat(coord));
                deliveryLocations.push([custLat, custLng]);

                const color = customer.color_code ? customer.color_code : 'black';
                const markerIcon = createMarkerIcon(color);

                L.marker([custLat, custLng], {
                    icon: markerIcon
                }).addTo(map).bindPopup(`
                    <b>Name:</b> ${customer.name}<br>
                    <b>Address:</b> ${customer.address}<br>
                    <b>Status:</b> ${customer.status}<br>
                    <b>Delivery Line ID:</b> ${customer.deliverylines_id}<br>
                    <b>Color:</b> ${color}
                `);
            }
        });

        // Only add the route if not in download mode
        if (!isDownloadMode && deliveryLocations.length > 0) {
            L.Routing.control({
                waypoints: [
                    L.latLng(lat, lng),
                    ...deliveryLocations.map(loc => L.latLng(loc[0], loc[1]))
                ],
                routeWhileDragging: true,
                lineOptions: {
                    styles: [{
                        color: 'black',
                        opacity: 0.6,
                        weight: 4
                    }]
                },
                createMarker: () => null
            }).addTo(map);
        }
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = parseFloat(position.coords.latitude.toFixed(6));
                    const lng = parseFloat(position.coords.longitude.toFixed(6));
                    initMap(lat, lng);
                },
                showError
            );
        } else {
            alert("Geolocation is not supported by this browser.");
            initMap(11.0490436, 77.0307578);
        }
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
            default:
                alert("An unknown error occurred.");
                break;
        }
        initMap(11.0490436, 77.0307578);
    }

    function downloadMapAsPDF() {
        isDownloadMode = true; // Set flag to exclude routes
        html2canvas(document.querySelector("#map"), {
            scale: 2, // Increase scale for higher resolution
            useCORS: true // Ensure cross-origin images are handled
        }).then(canvas => {
            const imgData = canvas.toDataURL("image/png");
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF("landscape", "mm", "a4"); // Adjust to 'a3' for a larger format
            const imgWidth = 290; // Adjust width for better fit on A4 paper
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            pdf.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);
            pdf.save("map.pdf");
            isDownloadMode = false; // Reset the flag
            getLocation(); // Reinitialize the map with routes after download
        });
    }

    $(document).ready(function() {
        $('#masters').addClass('mm-active');
        $('#master_menu').addClass('mm-show');
        $('#pincode').addClass('mm-active');
        getLocation();
    });
</script>
@endsection
