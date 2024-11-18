@extends('layouts.master')

@section('title')
    @lang('translation.Dashboards')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboards
        @endslot
        @slot('title')
            Dashboard
        @endslot
    @endcomponent
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Rating Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body content">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-xl-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Total Customers</p>
                                    <h4 class="mb-0">{{ $total_customers }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-user font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Subscribers -->
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Active Subscribers</p>
                                    <h4 class="mb-0">{{ $active_subscribers }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-user-check font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Delivery Persons -->
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Active Delivery Persons</p>
                                    <h4 class="mb-0">{{ $active_delivery_persons }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="fa-solid fa-person-biking font-size-24"></i>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Number of Pincodes -->
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Number of Pincodes</p>
                                    <h4 class="mb-0">{{ $number_of_pincodes }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-map-pin font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Number of Areas -->
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Number of Areas</p>
                                    <h4 class="mb-0">{{ $number_of_areas }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-map font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Number of Outlets -->
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Number of Outlets</p>
                                    <h4 class="mb-0">{{ $number_of_outlets }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-store-alt font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vendor Count -->
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Vendor Count</p>
                                    <h4 class="mb-0">{{ $vendor_count }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-group font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buyer Count -->
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Buyer Count</p>
                                    <h4 class="mb-0">{{ $buyer_count }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-user-circle font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Number of Categories -->
                {{-- <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Number of Categories</p>
                                    <h4 class="mb-0">{{ $number_of_categories }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-category font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Number of Products -->
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Number of Products</p>
                                    <h4 class="mb-0">{{ $number_of_products }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-package font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Orders</p>
                                    <h4 class="mb-0">1,235</h4>
                                </div>

                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="bx bx-copy-alt font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Revenue</p>
                                    <h4 class="mb-0">35, 723</h4>
                                </div>

                                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-archive-in font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Average Price</p>
                                    <h4 class="mb-0">16.2</h4>
                                </div>

                                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-purchase-tag-alt font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-muted fw-medium">Average Price</p>
                                    <h4 class="mb-0">16.2</h4>
                                </div>

                                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-purchase-tag-alt font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
            <!-- end row -->

            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">Subscription Product</h4>
                        {{-- <div class="ms-auto">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Week</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Month</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="#">Year</a>
                                </li>
                            </ul>
                        </div> --}}
                    </div>

                    <div id="days-subscription-product" class="apex-charts" dir="ltr"></div>
                </div>
                <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

                <!-- dashboard init -->
                <script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>

                @php
                    // print_r($s_p_d_array);
                @endphp
                <script>
                    var options = {
                        chart: {
                            height: 360,
                            type: 'bar',
                            stacked: true,
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '15%',
                                endingShape: 'rounded'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        series: [

                            @foreach ($s_p_d_array['label'] as $key => $label)
                                {
                                    name: '{{ $label->product_name }}',
                                    data: [{{ $s_p_d_array['data'][$label->product_name] }}]
                                },
                            @endforeach
                        ],
                        xaxis: {
                            categories: [
                                @foreach ($s_p_d_array['date'] as $key => $label)
                                    '{{ $label }}',
                                @endforeach
                            ]
                        },
                        colors: ['#556ee6', '#f1b44c', '#34c38f', '#39823e', '#823d39', '#cddc39', '#4caf50', '#009688', '#e555e6'],
                        legend: {
                            position: 'bottom'
                        },
                        fill: {
                            opacity: 1
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#days-subscription-product"), options);
                    chart.render();
                </script>

                <!-- Add this div in your HTML where you want the chart to appear -->
                <div id="days-subscription-product"></div>


            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">Order Product</h4>
                        {{-- <div class="ms-auto">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Week</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Month</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="#">Year</a>
                                </li>
                            </ul>
                        </div> --}}
                    </div>

                    <div id="days-order-product" class="apex-charts" dir="ltr"></div>
                </div>
                @php
                    // print_r($s_p_d_array);
                @endphp
                <script>
                    var options = {
                        chart: {
                            height: 360,
                            type: 'line',
                            stacked: true,
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '15%',
                                endingShape: 'rounded'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        series: [

                            @foreach ($o_p_d_array['label'] as $key => $label)
                                {
                                    name: '{{ $label->product_name }}',
                                    data: [{{ $o_p_d_array['data'][$label->product_name] }}]
                                },
                            @endforeach
                        ],
                        xaxis: {
                            categories: [
                                @foreach ($o_p_d_array['date'] as $key => $label)
                                    '{{ $label }}',
                                @endforeach
                            ]
                        },
                        colors: ['#556ee6', '#f1b44c', '#34c38f', '#39823e', '#823d39', '#cddc39', '#4caf50', '#009688', '#e555e6'],
                        legend: {
                            position: 'bottom'
                        },
                        fill: {
                            opacity: 1
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#days-order-product"), options);
                    chart.render();
                </script>

                <!-- Add this div in your HTML where you want the chart to appear -->
                <div id="days-subscription-product"></div>


            </div>

            <!-- prem -->
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">Customers</h4>
                    </div>
                    <div id="days-cust"></div>

                    <div id="days-cust" class="apex-charts" dir="ltr"></div>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var options = {
                            chart: {
                                height: 360,
                                type: 'line',
                                stacked: true,
                                toolbar: {
                                    show: false
                                },
                                zoom: {
                                    enabled: false
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '15%',
                                    endingShape: 'rounded'
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            series: [{
                                name: 'Customers',
                                data: {!! json_encode($customerCountsArray) !!}
                            }],
                            xaxis: {
                                categories: {!! json_encode(
                                    array_map(function ($date) {
                                        return date('d-m', strtotime($date));
                                    }, $formattedDates),
                                ) !!}
                            },
                            colors: ['#556ee6', '#f1b44c', '#34c38f', '#39823e', '#823d39', '#cddc39', '#4caf50', '#009688',
                                '#e555e6'
                            ],
                            legend: {
                                position: 'bottom'
                            },
                            fill: {
                                opacity: 1
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#days-cust"), options);
                        chart.render();
                    });
                </script>
            </div>
        </div>
    </div>
    <!-- end row -->




    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Latest Reviews</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive  nowrap w-100">
                            <thead class="table-light">
                                <tr>

                                    <th class="align-middle">S No</th>
                                    <th class="align-middle">Name</th>
                                    <th class="align-middle">Product</th>
                                    <th class="align-middle">Pin Code</th>
                                    <th class="align-middle">Area</th>
                                    <th class="align-middle">Delivery person</th>
                                    <th class="align-middle">Rating stars</th>
                                    <th class="align-middle d-none">Rating stars</th>
                                    <th class="align-middle">View Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $j = 1;

                                @endphp
                                @foreach ($delivery_list as $del)
                                    <tr>
                                        <td>{{ $j }}</td>
                                        <td>{{ $del->subscription_customer_id }}</td>
                                        <td>{{ $del->subscription_products_id }}</td>
                                        <td>{{ $del->pincode }}</td>
                                        <td>{{ $del->area }}</td>
                                        <td>{{ $del->deliveryperson_id }}</td>
                                        <td>
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $del->rating)
                                                    <i class="fas fa-star" style="color: gold;"></i> <!-- Filled star -->
                                                @else
                                                    <i class="far fa-star" style="color: lightgray;"></i>
                                                    <!-- Empty star -->
                                                @endif
                                            @endfor
                                        </td>
                                        <td class=' d-none'>{{ $del->rating }}</td>
                                        <td><button style='border:0px; background-color: white;'
                                                onclick='view("{{ $del->pic }}", "{{ $del->comments }}")'>
                                                <i class="fas fa-eye"></i>
                                            </button></td>
                                        @php
                                            $j++;
                                        @endphp
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="rating_report">
                            <p class="btn btn-outline-secondary waves-effect" style="float:right;">View more</p>
                        </a>
                    </div>
                    <!-- end table-responsive -->
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
    <script>
        function view(image, comments) {
            // alert(image + ' ' + comments);

            var images = image;
            $('.content').html(''); // Clear the content
            $('.content').html(
                '<div class="row">' +
                '<div class="col">' +
                '<img src="' + images + '" class="img-fluid">' + // Set the image
                '</div>' +
                '</div>' +
                '<div class="row mt-2">' +
                '<div class="col">' +
                '<p>' + comments + '</p>' + // Set the caption
                '</div>' +
                '</div>'
            );

            $('#exampleModal').modal('show'); // Show the modal
        }
    </script>
    <!-- Transaction Modal -->
    {{-- <div class="modal fade transaction-detailModal" tabindex="-1" role="dialog"
        aria-labelledby="transaction-detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transaction-detailModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Product id: <span class="text-primary">#SK2540</span></p>
                    <p class="mb-4">Billing Name: <span class="text-primary">Neal Matthews</span></p>

                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <div>
                                            <img src="{{ URL::asset('/assets/images/product/img-7.png') }}" alt="" class="avatar-sm">
                                        </div>
                                    </th>
                                    <td>
                                        <div>
                                            <h5 class="text-truncate font-size-14">Wireless Headphone (Black)</h5>
                                            <p class="text-muted mb-0"> 225 x 1</p>
                                        </div>
                                    </td>
                                    <td> 255</td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div>
                                            <img src="{{ URL::asset('/assets/images/product/img-4.png') }}" alt="" class="avatar-sm">
                                        </div>
                                    </th>
                                    <td>
                                        <div>
                                            <h5 class="text-truncate font-size-14">Phone patterned cases</h5>
                                            <p class="text-muted mb-0"> 145 x 1</p>
                                        </div>
                                    </td>
                                    <td> 145</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <h6 class="m-0 text-right">Sub Total:</h6>
                                    </td>
                                    <td>
                                         400
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <h6 class="m-0 text-right">Shipping:</h6>
                                    </td>
                                    <td>
                                        Free
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <h6 class="m-0 text-right">Total:</h6>
                                    </td>
                                    <td>
                                         400
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- end modal -->

    <!-- subscribeModal -->
    {{-- <div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-light rounded-circle text-primary h1">
                                <i class="mdi mdi-email-open"></i>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-xl-10">
                                <h4 class="text-primary">Subscribe !</h4>
                                <p class="text-muted font-size-14 mb-4">Subscribe our newletter and get notification to stay
                                    update.</p>

                                <div class="input-group bg-light rounded">
                                    <input type="email" class="form-control bg-transparent border-0"
                                        placeholder="Enter Email address" aria-label="Recipient's username"
                                        aria-describedby="button-addon2">

                                    <button class="btn btn-primary" type="button" id="button-addon2">
                                        <i class="bx bxs-paper-plane"></i>
                                    </button>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- end modal -->
@endsection
@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
