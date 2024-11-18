@extends('layouts.master')

@section('title') @lang('translation.Customers') @endsection

@section('content')

{{--     @component('components.breadcrumb')
        @slot('li_1') Vendors @endslot
        @slot('title') Add Vendor @endslot
    @endcomponent
 --}}
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Update Customer</h4>
                    <form action="{{ url('update_store_customer') }}" method="post" name="user_form" id="user_form">@csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $user_details['name'] }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="">Select</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                    </select><script>$('#gender').val('{{ $user_details['gender'] }}');</script>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $user_details['mobile'] }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pincode</label>
                                    <select id="pincode_id" name="pincode_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach($pincode_list as $qlist)
                                            <option value="{{ $qlist['id'] }}">{{ $qlist['pincode'] }}</option>
                                        @endforeach
                                    </select><script>$('#pincode_id').val('{{ $user_details['pincode_id'] }}');</script>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" value="{{ $user_details['address'] }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Lat long</label>
                                    <input type="text" class="form-control" id="latlon" name="latlon" value="{{ $user_details['latlon'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Delivery Line</label>
                                    <select id="deliverylines_id" name="deliverylines_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach($line_list as $qlist)
                                            <option value="{{ $qlist['id'] }}">{{ $qlist['name'] }}</option>
                                        @endforeach
                                    </select><script>$('#deliverylines_id').val('{{ $user_details['deliverylines_id'] }}');</script>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address Status</label>
                                    <select id="address_status" name="address_status" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Y">Verified</option>
                                        <option value="N">Not Verified</option>
                                    </select><script>$('#address_status').val('{{ $user_details['address_status'] }}');</script>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                            <div class="mb-3">
                                    <label class="form-label">Area</label>
                                    <select id="area_id" name="area_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($area as $areas)
                                        <option value="{{$areas->id}}">{{$areas->name}}</option>
                                        @endforeach
                                    </select><script>$('#area_id').val('{{ $user_details['area_id'] }}');</script>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="sstatus" name="status" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select><script>$('#sstatus').val('{{ $user_details['status'] }}');</script>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{ $user_details['id'] }}">
                                    <button type="submit" class="btn btn-primary w-md mt-4">Submit</button>
                                </div>
                            </div>
                            <div class="col-md-6">

                            </div>
                        </div>
                    </form>
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

<script>
   	$(document).ready(function () {
		$('#ecommerce').addClass('mm-active');
		$('#ecommerce_menu').addClass('mm-show');
		$('#customer').addClass('mm-active');
	});

    $('#user_form').validate({ // initialize the plugin
		 rules: {
            name:{required:true },
            mobile:{required:true },
            status:{required:true }
		 },
		 messages: {
            name: "Enter name",
            mobile: "Enter mobile",
            status: "Select status"
		}
	 });
</script>

@endsection
