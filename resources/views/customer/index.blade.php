@extends('layouts.master')

@section('title') @lang('translation.Customers') @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')


    <!-- Modal -->
    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Order List</h1>
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4> Customer list</>
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success :</strong> {{ Session::get('success_message') }}
                                </div>
                            @endif
                        </div>
                        <div class="col-6">
                            <a href="{{route('customer_create')}}"><p class="btn btn-outline-secondary waves-effect" style="float:right;">Add</p></a>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Pincode</th>
                                <th>Area</th>
                                <th>Mobile Type</th>
                                {{-- <th>Delivery Line</th> --}}
                                <th>Address Status</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1 @endphp
                            @foreach($user_list as $ulist)
                            @php
                                $astatus = $ulist['loc_status'];
                                $disp_status = $astatus == 'Active' ? "Verified" : "Not Verified";
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $ulist['name'] }}</td>
                                <td>{{ $ulist['mobile'] }}</td>
                                <td>{{ $ulist['pincode_name'] }}</td>
                                <td>{{ $ulist['area_name'] }}</td>
                                <td>{{ $ulist['type'] }}</td>
                                {{-- <td>{{ $ulist['line_name'] }}</td> --}}
                                <td>{{ $disp_status }}</td>
                                <td>{{ $ulist['status'] }}</td>
                                <td>
                                    <a href="{{ url('cust_list_view2/'.$ulist['id']) }}"><i class="fas fa-eye"></i></a>
                                    &nbsp;
                                    <a href="{{ url('update_customer/'.$ulist['id']) }}"><i class="fas fa-edit"></i></a>

                                    <!-- <a href="{{ url('cust_list_view/'.$ulist['id']) }}"><i class="fas fa-eye"></i></a>| -->

                                </td>
                            </tr>
                            @php $i++ @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
<script>
    function view_client(id) {
    $.ajax({
        url: "{{ env('APP_URL') }}/api/cust_list_view",
        type: 'POST',
        data: {
            '_token': '{{ csrf_token() }}',
            'id': id,
        },
        success: function(response) {
            if (response.success === true) {

                let tableHtml = '<div class="row">Customer Balance:'+response.wallet_balance.current_amount+'Rupees</div><table class="table table-bordered">';
                tableHtml += '<thead><tr><th>Date</th><th>Product</th><th>Quantity</th></tr></thead><tbody>';

                response.table.forEach(function(row) {
                    tableHtml += `<tr><td>${row.date}</td><td>${row.product}</td><td>${row.quantity}</td></tr>`;
                });

                tableHtml += '</tbody></table>';

                $('.content').html(tableHtml);
                $('#exampleModalToggle').modal('show');
            } else {
                alert('No data found');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('An error occurred while fetching data. Please try again.');
        }
    });
}
</script>

    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim. per.js/1.14.7/umd/popper.min.js"></script> -->
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script> -->
    <!-- <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script> -->


@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
