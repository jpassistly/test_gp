@extends('layouts.master')

@section('title')
    @lang('translation.user_profile')
@endsection

@section('content')
<style>
    #map {
    height: 400px;
    width: 100%; /* Ensure it's wide enough */
}

</style>
<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Uploaded image</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body content m-0">

                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="container">
<div class="row m-5">
    <!-- <div class="col-xl-10"> -->
        <div class="card m-5">
            <div class="card-body">
                <div class="mb-3 p-5">
                    <label class="form-label">Location</label>
                    <div id="map" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    <!-- </div> -->
</div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    
<script>
    // Convert lat/lon string to separate values and trim any spaces
    var latlon = @json($customer->latlon).split(',').map(function(coord) {
        return parseFloat(coord.trim());
    });

    var lat = latlon[0];
    var lon = latlon[1];
    var name="{{$customer->name}}";
// alert(lat+"---"+lon);
    // Initialize the map
    var map = L.map('map').setView([lat, lon], 13); // 13 is the zoom level

    // Add the OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Add a marker to the map
    L.marker([lat, lon]).addTo(map)
        .bindPopup(name)
        .openPopup();
</script>

