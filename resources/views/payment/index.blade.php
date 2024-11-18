@extends('layouts.master')

@section('title') @lang('translation.client_payment_history') @endsection

@section('css')
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Payment list @endslot
        @slot('title') Payment List @endslot
    @endcomponent

@php
use Carbon\Carbon;
@endphp
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body content  m-0 pt-3">

      </div>
      <div class="modal-footer">
       <h5 class="float-start gift_amount_text">This gift is been issued for the customer is keeping the balance of <span class="text-danger" id="gift_amount_text"></span></h5> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success :</strong> {{ Session::get('success_message') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <form action="payment_list" method="post">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for=""> From Date</label>
                            <input type="date" name="from_date" class="form-control" id="from_date" value="{{ request()->from_date ?? date('Y-m-d') }}">
                        </div>
                        <div class="col-4">
                            <label for=""> To Date</label>
                            <input type="date" name="to_date" class="form-control" id="to_date" value="{{date('Y-m-d')}}">
                        </div>
                        <div class="col-4">
                            <label for="formrow-inputState">Wallet Plan</label>
                            <select id="formrow-inputState" name="plan" class="form-select {{ $errors->has('plan') ? 'is-invalid' : '' }}">
                            <option value="" >Select</option>
                            @foreach ($wallet_plans as $wb)
                                    <option value="{{ $wb->amount }}" {{request()->plan  == $wb->amount ? 'selected' : '' }}>
                                        {{ $wb->name }} ({{ $wb->amount }})
                                    </option>
                                @endforeach
                            </select>

                            <!-- Clear validation error message -->
                            @if ($errors->has('plan'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('plan') }}
                                </div>
                            @endif
                        </div>



                        
                    </div>
                    <div class="row mb-3">
    <div class="col-md-12 float-end">
        <button type="submit" class="btn btn-outline-secondary waves-effect float-end" style="margin-top: 5px;">Search</button>
    </div>
</div>

                    </form>

                    <table style='font-size:10px' id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Customer </th>
                                <th>Mobile</th>
                                <th>Price</th>
                                <th>Transcation</th>
                                <th>Order Id</th>
                                <th>Status</th>
                                <th>Gifted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                                $totalQuantity = 0;
                                $i = 1;
                                /*dd($delivery_list);*/
                            @endphp
                            @foreach($delivery_list as $dlist)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <!-- <td>{{ $dlist->created_at ? Carbon::parse($dlist->created_at)->format('j-F-Y') : 'N/A' }}</td> -->
                                    <td>{{ $dlist->created_at ? Carbon::parse($dlist->created_at)->format('d-m-Y') : 'N/A' }}</td>
                                    <td>{{ $dlist->customers_id }}</td>
                                    <td>{{ $dlist->customers_mobile }}</td>
                                    <td>{{ $dlist->amount }}</td>
                                    <td>{{ $dlist->transaction_id }}</td>
                                    <td>{{ $dlist->order_id }}</td>
                                    <td>{{ $dlist->payment_status }}</td>
                                    <td>
                                        @if($dlist->last_gift_at)
                                            <!-- Display hyperlink if 'Yes' -->
                                            <a href="javascript:void(0);" onclick="gift('{{$dlist->cid}}')"><u>Yes</u></a>
                                        @else
                                            <!-- Display 'No' if no gift data exists -->
                                            No
                                        @endif
                                    </td>                                    
                                    <td><button class="btn btn-warning mx-1"onclick="rupee('{{$dlist->cid}}','{{$dlist->amount}}')" ><i class="fa fa-gift"></i></button><button class="btn btn-success" onclick="balance('{{$dlist->cid}}')"><i class="fa fa-eye" aria-hidden="true"></i></button></td>
                                </tr>
                                @php
                                    $totalQuantity += $dlist->amount;
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Display Total Quantity -->
                    <div class="mt-3">
                        <strong>Total Amount:</strong> {{ $totalQuantity}} Rs

                    </div>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
<script>
    $(document).ready(function() {
        $('#wallet').addClass('mm-active');
        $('#wallet_menu').addClass('mm-show');
        $('#client_payment_history').addClass('mm-active');
    });
   
</script>
@endsection

@section('script')
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 
                ]
            });
        });


    </script>
    <script>
         $(document).ready(function() {
        $('.buttons-print').hide();
        // $('#wallet_menu').addClass('mm-show');
        // $('#client_payment_history').addClass('mm-active');
    });
    </script>

<script>
    function rupee(id,amount){
                    $('.content').html('');
                    $('#exampleModalLabel').html('Add Gifts');
                   $('#exampleModal').modal('show');
                   $('.content').html('<div class="col-md-12 text-center" style=""><button class="btn btn-warning mx-1" onclick="add_cash('+id+','+amount+')">Gift Cash</button><a href="add_gift_products/'+id+'"class="btn btn-secondary")">Gift Product</a></div>');
                   $('.gift_amount_text').show();
                   $('#gift_amount_text').html('');
                   $('#gift_amount_text').html(amount);
    }
    function add_cash(id,amount) {

    $('.content').html('');
    $('#exampleModalLabel').html('Add Gifts');
    $('#exampleModal').modal('show');
    $('.content').html(
        '<form>' +
            '<div class="row">' +
                '<div class="col-5 text-center">' +
                    '<label>Amount</label>' +
                    '<input type="text" class="form-control" name="amount" id="amount">' +
                    '<p class="text-danger" id="alert_error_text"></p>' +
                    '<input type="hidden" name="cust_id" id="cust_id" value="' + id + '">' + '<input type="hidden" name="current_amount" id="current_amount" value="' + amount + '">'+
                '</div>' +
                '<div class="col-5 text-center">' +
                    '<label>Remarks</label>' +
                    '<input type="text" class="form-control" name="remarks" id="remarks">' +
                    '<p class="text-danger" id="alert_error_text"></p>' +
                '</div>' +
                '<div class="col-2 text-center">' +
                    '<button type="button" class="btn btn-success mt-4" onclick="gift_add()">Add</button>' +
                '</div>' +
            '</div>' +
            `<div id="success_message" class="alert alert-success mt-3 d-none"></div>`+
        '</form>'
    );
}
    // function add_product(id){
    //     alert(id);

    // }
    function balance(id) {
    $.ajax({
        url: "{{ env('API_APP_URL') }}/api/client_walet",
        type: "post",
        data: { id: id },
        success: function(response) {
            $('#exampleModalLabel').html('Wallet History');
            $('.gift_amount_text').hide();
            $('.content').html(''); 
            $('.content').html(response.data);
            $('#exampleModal').modal('show');
        },
        error: function(response) {
        }
    });
}

function gift(id) {
    $.ajax({
        url: "{{ env('API_APP_URL') }}/api/gift_payment",
        type: "post",
        data: { id: id },
        success: function(response) {
            $('#exampleModalLabel').html('Gift Product History');
            $('.gift_amount_text').hide();
            $('.content').html(''); 
            $('.content').html(response.data);
            $('#exampleModal').modal('show');
        },
        error: function(response) {
        }
    });
}

    function gift_add() {
            var giftamount = $('#amount').val();
            var remarks = $('#remarks').val();
            var cust_id=$('#cust_id').val();
            var current_amount=$('#current_amount').val();
            // Regular expression to check if the input is a valid number
            var regex = /^[0-9]+(\.[0-9]{1,2})?$/;
            var uid="{{auth()->user()->id}}";
            if (regex.test(giftamount)) {
                // alert("Gift amount: " + giftamount);
                $.ajax({
                    url:"{{ env('API_APP_URL') }}/api/add_gift_amount",
                    data:{amount:giftamount,remarks:remarks,cust_id:cust_id,uid:uid,current_amount:current_amount},
                    type:"post",
                    success:function(response){
                //    $('#exampleModal').modal('show');
                //    $('.content').html(response.data);
                // window.location.reload();
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

                },
                error: function(response) {

                }
                });
            } else {
                // alert("Please enter a valid number for the gift amount.");
                $('#alert_error_text').html('Please enter a valid number for the gift amount.');
                $('#amount').focus();  // Optional: sets focus back to the input field
            }
        }
</script>
@endsection
