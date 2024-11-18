@extends('layouts.master')

@section('title')
    @lang('translation.Customers')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JavaScript -->


    @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            {{-- Subscription History --}}
        @endslot
        @slot('title')
            {{-- Subscription History --}}
        @endslot
    @endcomponent
    

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" tabindex="-1" id="subscriptionModal" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class='pt-2'>Select product</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body content2 m-0 " style="max-height: 70vh; overflow-y: auto;">
                    <form action="" id="form_subscription">
                        <div class="row py-3">
                            <div class="col-md-6 py-2">
                                <label for="">Plan</label>
                                <select id="subscription_id"  name="subscription_id" class="form-select select2" onchange="subscription_ids()">
                                    <option value="">Select plan</option>
                                    @foreach ($subscription_plans as $d)
                                        <option {{ request()->subscription_plans == $d->id ? 'selected' : '' }}
                                            value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 py-2">
                            <label for="">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control p-2" value="" onchange="datechanges()">
                                <input type="hidden" name="customer_id" id="customer_id" class="form-control p-2" value="{{$id}}">
                                <input type="hidden" name="total_price" id="total_price" class="form-control p-2" value="">
                                <input type="hidden" name="discount" id="discount" class="form-control p-2" value="">
                                <!-- <input type="hidden" name="final_price" id="final_price" class="form-control p-2" value=""> -->
                            </div>
                            <div class="col-md-6 py-2">
                            <label for="">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="" readonly>
                            </div>
                            <div class="col-md-6 py-2">
                            <label for="">Final Price</label>
                                <input type="number" name="final_price" id="final_price" class="form-control" value="" readonly>
                            </div>
                            <div class="col-md-12">
                                <h4>Select product</h4>
                                <table id="datatabler" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>

                                        <tr>
                                            <th>S.No</th>
                                            <th>Product</th>
                                            <th>Measurement</th>
                                            <th>Quantity</th>
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php

                                            $i = 1;

                                        @endphp
                                        @foreach ($product as $d)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $d->product_name }}</td>
                                                <td>{{ $d->quantity_name . '-' . $d->measurement_name }}</td>
                                                <td>
                                                    <input name='product_id_{{ $d->id }}' data-product-id='{{ $d->id }}' class='form-control product_qty'
                                                        type="number" min="0">
                                                </td>

                                            </tr>
                                            @php $i++ @endphp
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row p-3">
                    <div class="col-md-4">
                    <button type="button" class="btn btn-success"  onclick="calculate()">Calculate</button>
                    </div>
                    <div class="col-md-4" id="savebtn">
                        
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
                {{-- <div class="modal-footer">

                </div> --}}
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" tabindex="-1" id="exampleModal" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Subscription History</h1>
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
    @php
        use Carbon\Carbon;
       
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row py-2">
                        <div class="col-6">
                            <h4>Subscription History</h4>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success :</strong> {{ Session::get('success_message') }}
                                </div>
                            @endif
                        </div>
                        <div class="col-6">
                            <button onclick="addsubscription()" class="btn btn-outline-secondary waves-effect px-4"
                                style="float:right;">Add</button>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100 mt-5">
                        <thead>

                            <tr>
                                <th>S.No</th>
                                <th>Plan</th>
                                <th>Offer% </th>
                                <th>Subscription payment date </th>
                                <th>Start date</th>
                                <th>End Date</th>

                                <th>Action</th>
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>

                        <tbody>
                            @php

                                $i = 1;

                            @endphp
                            @foreach ($wallet as $his)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $his->plan_id }}</td>
                                    <td>{{ $his->discount }}</td>
                                    <td>{{ Carbon::parse($his->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ Carbon::parse($his->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ Carbon::parse($his->end_date)->format('d-m-Y') }}</td>

                                    <td><button class="btn btn-success"onclick="sub_plans({{ $his->id }})"><i
                                                class="fa fa-eye"></i></button></td>
                                </tr>
                                @php $i++ @endphp
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    </div>
    <script>
        function addsubscription() {
            // $('.content').html('');
            $('#subscriptionModal').modal('show');
            $('.select2').addClass(' w-100 ');

        }

        $(document).ready(function() {
            $('#customer_main_menu').addClass('mm-active');
            $('#customer_menu').addClass('mm-show');
            $('#customer').addClass('mm-active');
        });

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

                },
                error: function(response) {
                    console.error("Error: ", response);
                }
            });
        }
        function datechanges(){
           // alert();
           var sub_id=$("#subscription_id").val();
           var fromdate=$("#start_date").val();
           if(sub_id =="" || fromdate==""){
            $("#start_date").val('');
            alert("First Select The Plan Value")
           
           }else{
            $.ajax({
                url: "{{ env('API_APP_URL') }}/api/get_to_date",
                type: "post",
                data: {
                    sub_id: sub_id,
                    fromdate:fromdate
                },
                success: function(response) {
                    // alert(response.discount);
                   $("#end_date").val(response.to_date); 
                   $("#discount").val(response.discount); 
                },
                error: function(response) {
                    console.error("Error: ", response);
                }
            });

           }
        }
        
    </script>
    <script>
        function subscription_ids(){
            $("#start_date").val("");
            
        }
        function form_subscription(){
            
        }
    </script>
    <script>
function setMinDate() {
    let dateInput = document.getElementById('start_date');
    let now = new Date();
    // now.setHours(23, 0, 0, 0); 
    // Check if the current time is 11:00 PM or later
    if (now.getHours() >= 23) {
        // Set the min date to the day after tomorrow
        now.setDate(now.getDate() + 2);
    } else {
        // Otherwise, set the min date to today
        now.setDate(now.getDate() + 1);
    }
    
    // Format the date as YYYY-MM-DD
    let day = ('0' + now.getDate()).slice(-2);
    let month = ('0' + (now.getMonth() + 1)).slice(-2);
    let year = now.getFullYear();
    let minDate = `${year}-${month}-${day}`;
    
    // Set the min attribute to the calculated date
    dateInput.min = minDate;
}

// Call the function on page load
window.onload = setMinDate;



function calculate2() {
    // Serialize all form data
    let formData = $("#form_subscription").serializeArray();
    let productIds = [];
    let quantities = [];

    // Filter and prepare the product_id and quantity lists
    formData.forEach(function(item) {
        if (item.name.startsWith("product_id_") && item.value) {
            let productId = item.name.split("product_id_")[1];
            productIds.push(productId);
            quantities.push(item.value);
        }
    });

    // Helper function to format dates to 'dd-mm-yyyy'
    function formatDate(dateStr) {
        let date = new Date(dateStr);
        let day = String(date.getDate()).padStart(2, '0');
        let month = String(date.getMonth() + 1).padStart(2, '0');
        let year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }

    // Prepare additional form data fields
    let data = {
        subscription_id: $("select[name='subscription_id']").val(),
        start_date: formatDate($("input[name='start_date']").val()),
        end_date: formatDate($("input[name='end_date']").val()),
        customer_id: $("input[name='customer_id']").val(),
        total_price: $("input[name='total_price']").val(),
        discount: $("input[name='discount']").val(),
        final_price: $("input[name='final_price']").val(),
        product_id: productIds.join(','),
        total_quantity: quantities.join(','),
        product_qty: quantities.join(','),
        session_ids: 1
    };

    // Send the data via AJAX
    $.ajax({
        url: "{{ env('API_APP_URL') }}/api/save_subscription_web",
        type: 'POST',
        headers: {
            'Authorization': 'Bearer your-auth-token' // replace 'your-auth-token' with the actual token
        },
        data: data,
        success: function(response) {
            if (data.customer_id > 0) {
                window.location.href = "{{ env('APP_URL') }}/order_subscription_dash/" + data.customer_id;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid customer ID.',
                });
            }
        },
        error: function(error) {
            if (error.responseJSON && error.responseJSON.message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.responseJSON.message,
                });
            } else {
                console.log("Error:", error);
            }
        }
    });
}






function calculate() {
    // Get all values from form with ID 'form_subscription'
    let formData = $("#form_subscription").serialize();

    $.ajax({
        url: "{{ env('API_APP_URL') }}/api/calculate",
        type: 'POST',
        data: formData,
        success: function(response) {
            // Handle success response
            console.log("Response:", response);
            if (response.wallet == 1) {
                let wallet_balance = response.wallet_balance;
                let total_cost = response.total_cost;
                let discount = response.total_cost;
                let final_price=response.final_price;
                if (wallet_balance >= total_cost) {
                    $("#savebtn").html(''); // Clear previous save button
                    $("#total_price").val(total_cost);
                    $("#final_price").val(final_price);

                    $("#savebtn").append('<button type="button" class="btn btn-primary" onclick="calculate2()">Save</button>');
                } else {
                    // Not enough balance in wallet
                    Swal.fire({
                        title: "Insufficient Wallet Balance",
                        text: "Your wallet balance is insufficient for this transaction.",
                        icon: "warning",
                        confirmButtonText: "Add Funds"
                    });
                }
            } else {
                // Wallet does not exist
                Swal.fire({
                    title: "Wallet Not Found",
                    text: "Please create a wallet to proceed with this transaction.",
                    icon: "error",
                    confirmButtonText: "Create Wallet"
                });
            }
        },
        error: function(error) {
            // Handle error response
            console.log("Error:", error);
        }
    });
}

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection


@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
