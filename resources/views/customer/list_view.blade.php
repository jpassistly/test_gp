@extends('layouts.master')

@section('title')
    @lang('translation.Customers')
@endsection

@section('content')
    @php
    @endphp
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />

    <style>
        .logo-lg2{
            margin-top: 10px !important;
        }
        .dataTables_length>label{
            display: flex;
            align-items: center;

        }
        .table-responsive {
            overflow-x: auto;
        }

        .dataTables_wrapper .dt-buttons {
            float: left;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            font-size: 12px !important;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            font-size: 12px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .dataTables_scrollBody {
            max-height: 50vh !important;
        }
    </style>
 <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">image</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body contentz m-0">

                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" tabindex="-1" id="exampleModal" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body content" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <h6>Customer Dashboard</h6>
        </div>

        <div class="col-6 mb-3">

            <button class="btn btn-success float-end mx-1" id="cust_info"
                onclick="client_detail('{{ $id }}')">Customer Info</button>
                @if ($wallet != '')
            <a href="{{ url('add_product_cust/' . $id) }}"><button class="btn btn-danger float-end mx-1" id="cust_info">Add
                    Product</button></a>
                    @endif
                    @if ($wallet != '')
                    <button class="btn btn-warning float-end mx-1" id="cust_info" onclick="rupee(' {{ $wallet->current_amount }}','{{$id}}')">Add Cash</button>



                    @endif

        </div>
        <div class="row">
            <div class="col-sm-3" onclick="subscription('{{ $id }}')">
                <div class="card">

                    <a href="{{ url('order_subscription_dash/' . $id) }}" target="_blank">
                        <div class="card-body">
                            <h5 class="card-title">Subscription</h5>
                            <p class="card-text">
                                @if (count($subscriber_user) != 0)
                                    Yes
                                @else
                                    No
                                @endif
                            </p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Days Left</h5>
                        <p class="card-text">{{ $subscriber_user_count }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3" onclick="wallet_balance('{{ $id }}')">
                <a href="{{ url('order_walet_dash/' . $id) }}" target="_blank">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Wallet Balance</h5>
                            <p class="card-text">
                                @if ($wallet != '')
                                    {{ $wallet->current_amount }}
                                @else
                                    0
                                @endif
                                RS
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-3" onclick="order_count('{{ $id }}')">
                <a href="{{ url('order_count/' . $id) }}" target="_blank">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Ordered Count</h5>
                            <p class="card-text">{{ $orderCount }}</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <!-- <div class="row bg-white" id="table_val">
            </div>
            <div class="row bg-white" id="table_val2">
            </div>
            <div class="row bg-white" id="table_val3">
            </div> -->



    </div>



    <div class="row bg-white">
        <div class="col-md-12 text-center mt-3">
            <h4>Subscription Product</h4>
        </div>

        @if (session('success') === false)
            <div class="alert alert-danger">
                {{ session('success_message') }}
            </div>
        @endif
        <div class="col-md-12 mt-1">
            <form id="delivery_list" method="post" action="{{url('cust_list_view3')}}">
                @csrf
                <div class="row mb-3">
                    <div class="col-4">
                        <label for=""> From Date</label>
                        <input type="date" name="from_date" class="form-control" id="from_date"
                            value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-4">
                        <label for=""> To Date</label>
                        <input type="date" name="to_date" class="form-control" id="to_date"
                            value="{{ date('Y-m-t') }}">
                    </div>
                    <input type="hidden" name="cust_id" id="cust_id" value="{{ $id }}">
                    <div class="col-4 mt-4 float-end">
                        <label for="" class="mt-1"> </label>
                        <button type="submit" class="btn btn-outline-secondary waves-effect"
                            style="float:right;margin-top:5px" >Search</button>
                    </div>
                </div>
            </form>


            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100 mt-5">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Date</th>
                        <th>Aditional Product</th>
                        <th>Delivery Status</th>
                        <th>Delivery Person</th>
                        <th>Ratings</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tab_values">
                    <!-- Rows will be dynamically inserted here -->
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($delivery_list as $del_list)
                        @php
                            if ($del_list->addon_status == null) {
                                $add_on = 'NO';
                                $add = '';
                            } else {
                                $add_on = 'YES';
                                $add =
                                    '<button class="btn btn-warning" onclick="add_on(' . $del_list->id .')"><i class="fa fa-check-square" aria-hidden="true"></i></button>';


                            }

                            $rating_stars = '';
                            if ($del_list->rating != null) {
                                for ($j = 0; $j < $del_list->rating; $j++) {
                                    $rating_stars .=
                                        '<i class="fa fa-star" aria-hidden="true" style="color: gold;"></i>';
                                }
                                for ($j = $del_list->rating; $j < 5; $j++) {
                                    $rating_stars .=
                                        '<i class="fa fa-star-o" aria-hidden="true" style="color: gold;"></i>';
                                }
                                $rating_star =
                                    '<button class="btn btn-secondary" onclick="rating_star(' .
                                    $del_list->id .
                                    ')"><i class="fa fa-star" aria-hidden="true"></i></button>';
                            } else {
                                $rating_stars = '';
                                $rating_star = '';
                            }
                        @endphp
                        <tr>
                            <td>{{ $i }} </td>
                            <td>{{ \Carbon\Carbon::parse($del_list->date)->format('d-m-Y') }}</td>
                            <td> {{ $add_on }}</td>
                            <td>{{ $del_list->delivery_status }}</td>
                            <td>{{ $del_list->deliveryperson_id }}</td>
                            <td>{!! $rating_stars !!}</td>
                            <td>
                                <button class="btn btn-success" onclick="pro_sts('{{ $del_list->id }}')"><i
                                        class="fa fa-eye"></i></button>
                                {!! $add !!}
                                {!! $rating_star !!}
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
            <div id="pagination" class="mt-3">
                <!-- Pagination buttons will be dynamically inserted here -->
            </div>
        </div>
    </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
    box-sizing: border-box;
    display: inline-block;
    min-width: 1.5em;
    padding: 0em !important;
    margin-left: 2px;
    text-align: center;
    text-decoration: none !important;
    cursor: pointer;
    color: #333 !important;
    border: 1px solid transparent;
    border-radius: 2px;
}
</style>
<script>
    function rupee(amount,id){
        $('.content').html('');
    $('#exampleModalLabel').html('Add Cash');
    $('#exampleModal').modal('show');
    $('.content').html(
        '<form>' +
            '<div class="row">' +
                    '<div class="col-5 text-center">' +
                        '<label>Amount</label>' +
                        '<input type="text" class="form-control" name="amount" id="amount">' +
                        '<input type="hidden" name="cust_id" id="cust_id" value="' + id + '">' + '<input type="hidden" name="current_amount" id="current_amount" value="' + amount + '">'+
                    '</div>' +
                    '<div class="col-5 text-center">' +
                        '<label>Remarks</label>' +
                        '<input type="text" class="form-control" name="remarks" id="remarks">' +
                    '</div>' +
                    '<div class="col-2 text-center">' +
                        '<button type="button" class="btn btn-success mt-4" onclick="gift_add()">Add</button>' +
                    '</div>' +
            '</div>' +
            `<div id="success_message" class="alert alert-success mt-3 d-none"></div>`+
        '</form>'
    );
    }
    $(document).ready(function() {
        $('#customer_main_menu').addClass('mm-active');
        $('#customer_menu').addClass('mm-show');
        $('#customer').addClass('mm-active');
    });
    function search(page = 1) {
        var cust_id = $('#cust_id').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/delivery_list_cust_dash",
            type: "post",
            data: { cust_id: cust_id, from_date: from_date, to_date: to_date },
            success: function(response) {
                if (response.success) {
                    $('#tab_values').html(response.table);
                }
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }
    function client_detail(id) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/client_detail",
            type: "post",
            data: {
                id: id
            },
            success: function(response) {
                $('#exampleModal').modal('show');
                $('.content').html(response.tabel);
                $('#exampleModalLabel').html('Client Details');
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }

    function wallet_balance(id) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/wallet_balance",
            type: "post",
            data: {
                id: id
            },
            success: function(response) {
                $("#table_val3").html('');
                $("#table_val3").html(response.tabel);
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }

    function subscription(id) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/subscription",
            type: "post",
            data: {
                id: id
            },
            success: function(response) {
                $("#table_val").html('');
                $("#table_val").html(response.tabel);
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }

    function order_count(id) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/order_count",
            type: "post",
            data: {
                id: id
            },
            success: function(response) {
                $("#table_val2").html('');
                $("#table_val2").html(response.tabel);
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }

    function pro_sts(id) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/pro_sts",
            type: "post",
            data: {
                id: id
            },
            success: function(response) {
                $('.content').html('');
                $('#exampleModal').modal('show');
                $('.content').html(response.table);
                $('#exampleModalLabel').html('Product Details');
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }

    function add_on(id) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/add_on",
            type: "post",
            data: {
                id: id
            },
            success: function(response) {
                $('.content').html('');
                $('#exampleModal').modal('show');
                $('.content').html(response.tabel);
                $('#exampleModalLabel').html('Order Details');
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }

    function rating_star(id) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/rating_star",
            type: "post",
            data: {
                id: id
            },
            success: function(response) {
                $('.content').html('');
                $('#exampleModal').modal('show');
                $('.content').html(response.tabel);
                $('#exampleModalLabel').html('Rating Details');
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }

    function sub_plans(id) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/sub_plans",
            type: "post",
            data: {
                id: id
            },
            success: function(response) {
                $('.content').html('');
                $('#exampleModal').modal('show');
                $('.content').html(response.tabel);
                $('#exampleModalLabel').html('Rating Details');
            },
            error: function(response) {
                console.error("Error: ", response);
            }
        });
    }

</script>
<script>
function gift_add() {
    var giftamount = $('#amount').val();
    var remarks = $('#remarks').val();
    var cust_id = $('#cust_id').val();
    var current_amount = $('#current_amount').val();
    var regex = /^[0-9]+(\.[0-9]{1,2})?$/;
    var uid = "{{ auth()->user()->id }}";

    if (regex.test(giftamount)) {
        $.ajax({
            url: "{{ env('API_APP_URL') }}/api/add_gift_amount2",
            data: { amount: giftamount, remarks: remarks, cust_id: cust_id, uid: uid, current_amount: current_amount },
            type: "post",
            success: function(response) {
                if (response.success) {
                    // Display the success message
                    $('#success_message').text(response.message).removeClass('d-none');
                    
                    // Clear input fields
                    $('#amount').val('');
                    $('#remarks').val('');
                    
                    
                    // Optionally, hide the success message after a delay
                    setTimeout(function() {
                        $('#success_message').addClass('d-none');
                    }, 3000);
                    // Close the modal
                    $('#giftModal').modal('hide');
                    $('#exampleModal').modal('hide');
                    location.reload();
                } else {
                    $('#alert_error_text').text(response.message);
                }
            },
            error: function(response) {
                $('#alert_error_text').text('An error occurred. Please try again.');
            }
        });
    } else {
        $('#alert_error_text').text('Please enter a valid number for the gift amount.');
        $('#amount').focus();
    }
}

</script>

<script>
        function viewimage(imagesrc) {
            $('#exampleModal').modal('hide');
            $('.contentZ').html('');
            $('#exampleModalToggleLabel').html('');
            $('#exampleModalToggleLabel').html('Home Image');
    var contant = `
        <div class='d-flex justify-content-center'>
            <img src="` + imagesrc + `" alt="uploaded Image" class="img-fluid" />
        </div>
    `;

    $('.contentZ').html(contant);
    $('#exampleModalToggle').modal('show');
}
function profileimage(imagesrc) {
            $('#exampleModal').modal('hide');
            $('.contentZ').html('');
            $('#exampleModalToggleLabel').html('');
            $('#exampleModalToggleLabel').html('Profile Image');
    var contant = `
        <div class='d-flex justify-content-center'>
            <img src="` + imagesrc + `" alt="uploaded Image" class="img-fluid" />
        </div>
    `;

    $('.contentZ').html(contant);
    $('#exampleModalToggle').modal('show');
}
    </script>


<script>
    $(document).ready(function() {
        subscription('{{ $id }}');
        order_count('{{ $id }}');
        wallet_balance('{{ $id }}');
    })
</script>
