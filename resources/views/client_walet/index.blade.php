@extends('layouts.master')

@section('title') @lang('translation.Category') @endsection

@section('css')
    <!-- DataTables... -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Wallet @endslot
        @slot('title') Wallet List @endslot
    @endcomponent
<!-- Button trigger modal -->
@php
use Carbon\Carbon;
@endphp

<!-- Modal -->
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
                        <div class="col-6"> @if (Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success :</strong> {{ Session::get('success_message') }}
                            </div>
                        @endif</div>
                        <!-- <div class="col-6">
                            <a href="add_category"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p></a>
                        </div> -->
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Price</th>
                                <th>Last Gift</th>
                                <th>Action</th>
                            </tr>
                        </thead>


                        <tbody>
                            @php $i = 1 @endphp
                            @foreach($wallet as $clist)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $clist['customers_id'] }}</td>
                                <td>{{ $clist['customers_mobile'] }}</td>
                                <td>{{ $clist['current_amount'] }}</td>
                                <td>
    @if ($clist['last_gift_at'])
        {{ \Carbon\Carbon::parse($clist['last_gift_at'])->format('d-m-Y') }}
    @else
        N/A
    @endif
</td>

                                <td><button class="btn btn-warning mx-1"onclick="rupee('{{$clist->id}}','{{$clist['current_amount']}}')" ><i class="fa fa-gift"></i></button><button class="btn btn-success" onclick="balance('{{$clist->id}}')"><i class="fa fa-eye" aria-hidden="true"></i></button></td>
                            </tr>
                            @php $i++ @endphp
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col   -->
    </div> <!-- end row -->
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
                '<div class="col-6 text-center">' +
                    '<label>Amount</label>' +
                    '<input type="text" class="form-control" name="amount" id="amount">' +
                    '<p class="text-danger" id="alert_error_text"></p>' +
                    '<input type="hidden" name="cust_id" id="cust_id" value="' + id + '">' + '<input type="hidden" name="current_amount" id="current_amount" value="' + amount + '">'+
                '</div>' +
                '<div class="col-6 text-center">' +
                    '<button type="button" class="btn btn-success mt-4" onclick="gift_add()">Add</button>' +
                '</div>' +
            '</div>' +
        '</form>'
    );
}
    // function add_product(id){
    //     alert(id);

    // }
    function balance(id){
        // alert(id);
        $.ajax({
            url:"{{ env('API_APP_URL') }}/api/client_walet",
            type:"post",
            data:{id:id},
            success:function(response){
                $('#exampleModalLabel').html('');
                $('#exampleModalLabel').html('Wallet History');
                $('.gift_amount_text').hide();
                   $('#exampleModal').modal('show');

                   $('.content').html(response.data);
                },
                error: function(response) {

                }
        })
    }
    function gift_add() {
            var giftamount = $('#amount').val();
            var cust_id=$('#cust_id').val();
            var current_amount=$('#current_amount').val();
            // Regular expression to check if the input is a valid number
            var regex = /^[0-9]+(\.[0-9]{1,2})?$/;
            var uid="{{auth()->user()->id}}";
            if (regex.test(giftamount)) {
                // alert("Gift amount: " + giftamount);
                $.ajax({
                    url:"{{ env('API_APP_URL') }}/api/add_gift_amount",
                    data:{amount:giftamount,cust_id:cust_id,uid:uid,current_amount:current_amount},
                    type:"post",
                    success:function(response){
                //    $('#exampleModal').modal('show');
                //    $('.content').html(response.data);
                window.location.reload();

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
@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
