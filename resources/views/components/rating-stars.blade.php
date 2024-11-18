@props(['rating'])

@php
    $ratingPercentage = ($rating / 5) * 100;
@endphp

<div class="stars-outer">
    <div class="stars-inner" style="width: {{ $ratingPercentage }}%;"></div>
</div>
