@extends('layouts.master')

@section('title') @lang('translation.Customers') @endsection

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

@component('components.breadcrumb')
@slot('li_1') Wallet History @endslot
@slot('title')Wallet History @endslot
@endcomponent

<!-- Modal -->
<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Wallet List</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body content">
                <!-- Table content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
@php
use Carbon\Carbon;
@endphp
@if(isset($history))
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
                    <div class="col-6">
                        <!--<a href="add_person"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add ++</p></a>-->
                    </div>
                </div>
                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Order Id</th>
                            <th>Transaction Id</th>
                            <th>Note</th>

                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody id="tab_values">
                        @php
                        $i=1;
                        @endphp
                        @foreach ($history as $his)
                        @if($his->delivery_status=='Delivered')
                        @php
                        $del_sts=$his->delivery_at;
                        @endphp

                        @else
                        @php
                        $del_sts="";
                        @endphp

                        @endif
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $his->debit_credit_status }}</td>
                            <td>{{ $his->amount }}</td>
                            <td>{{$his->order_id}}</td>
                            <td>{{$his->transaction_id}}</td>
                            <td>{{ $his->notes }}</td>

                            <td>{{ $his->payment_status }}</td>
                        </tr>
                        @php
                        $i++;
                        @endphp

                        @endforeach

                    </tbody>
                </table>

            </div>

        </div>


        <div class="card">
            <div class="card-body">
                <div class="row">
                <div class="row bg-white">
                <div class="col-12 text-center mt-3">
                    <h4>Gift Amount</h4>
                </div>
                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100 mt-5">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Customer</th>
                            <th>Mobile</th>
                            <th>Amount</th>
                            <th>Date</th>

                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>

                    <tbody>
                        @php $i = 1 @endphp
                        @foreach($giftamount as $clist)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $clist['name'] }}</td>
                            <td>{{ $clist['mobile'] }}</td>
                            <td>{{ $clist['amount'] }}</td>
                            <td>{{ $clist['created_at']->format('d-m-Y') }}</td>

                            <!-- <td><a href="{{ url('update_product/'.$clist['id'].'/' )}}"><i class="fas fa-edit"></i></a></td> -->
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
</div>
@else
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
                    <div class="col-6">
                        
                        <a href="{{url('add_wallet/'.$id)}}"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add Wallet++</p></a>
                         
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    // function add_wallet(id) {
    //     $('.content').html('');
    //     $('#exampleModalLabel').html('Add Gifts');
    //     $('#exampleModal').modal('show');
    //     $('.content').html(
    //         '<form id="wallet_add">' +
    //             '<div class="row">' +
    //                 '<div class="col-6 text-center">' +
    //                     '<label>Amount</label>' +
    //                     '<input type="text" class="form-control" name="amount" id="amount" value="">' +
    //                     '<p class="text-danger" id="alert_error_text"></p>' +
    //                     '<input type="hidden" name="cust_id" id="cust_id" value="' + id + '">' +
    //                 '</div>' +
    //                 '<div class="col-6 text-center">' +
    //                     '<button type="button" class="btn btn-success mt-4" onclick="gift_add()">Add</button>' +
    //                 '</div>' +
    //             '</div>' +
    //         '</form>'
    //     );
    // }

    function gift_add() {
       
        var formData = $("#wallet_add").serialize();
        
        // Regular expression to check if the input is a valid number
        var regex = /^[0-9]+(\.[0-9]{1,2})?$/;
        

            $.ajax({
                url: "{{ env('API_APP_URL') }}/api/add_gift_amount2",
                data: formData,
                type: "post",
                success: function(response) {
                    $('#exampleModal').modal('hide'); // Close the modal on success
                    // window.location.reload();         // Reload page to show updated data
                },
                error: function(response) {
                    $('#alert_error_text').html('An error occurred. Please try again.');
                }
            });
        
    }
</script>

@endif
</div>
@endsection


@section('script')
<!-- Required datatable js -->
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<!-- Datatable init js -->
<script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
