@extends('layouts.master')

@section('title') @lang('translation.Delivery_lines') @endsection

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

                    <h4 class="card-title mb-4">Add Delivery Line</h4>
                    <form action="{{ url('store_line') }}" method="post" name="line_form" id="line_form">@csrf
                    <form>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Delivery Line</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="formrow-inputState" name="status" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
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
		$('#delivery_lines').addClass('mm-active');
	});

    $('#line_form').validate({ // initialize the plugin
		 rules: {
			name:{required:true },
            status:{required:true }
		 },
		 messages: {
            name: "Enter Line",
            status: "Select status"
		}
	 });
</script>

@endsection
