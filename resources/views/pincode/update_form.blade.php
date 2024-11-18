@extends('layouts.master')

@section('title') @lang('translation.Pincodes') @endsection

@section('content')

{{--     @component('components.breadcrumb')
        @slot('li_1') Vendors @endslot
        @slot('title') Edir Vendor @endslot
    @endcomponent
 --}}
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Edit Pincode</h4>
                    <form action="{{ url('update_store_pincode') }}" method="post" name="pincode_form" id="pincode_form">@csrf
                    <form>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" readonly placeholder="Click on the Map" name="pincode" value="{{ $pincode_details['pincode'] }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="formrow-inputState" name="status" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select><script>$('#formrow-inputState').val('{{ $pincode_details['status'] }}');</script>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{ $pincode_details['id'] }}">
                                    <button type="submit" class="btn btn-primary w-md mt-4">Submit</button>
                                </div>
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
		$('#masters').addClass('mm-active');
		$('#master_menu').addClass('mm-show');
		$('pincode').addClass('mm-active');
	});

    $('#pincode_form').validate({ // initialize the plugin
		 rules: {
			 pincode:{required:true },
             status:{required:true }
		 },
		 messages: {
            pincode: "Enter vendor category",
            status: "Select status"
		}
	 });
</script>

@endsection
