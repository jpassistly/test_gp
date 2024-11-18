@extends('layouts.master')

@section('title') @lang('translation.Products') @endsection

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

                    <h4 class="card-title mb-4">Add Plans</h4>
                    <form action="{{ url('add_plans_value') }}" method="post" name="product_form" id="product_form" enctype="multipart/form-data">@csrf
                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                <label class="form-label">Name</label>
                                    <input type="text" id="name" name="name" class="form-control">

                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount</label>
                                    <input type="number" class="form-control" id="discount" name="discount" value=""  min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Days Count</label>
                                    <input type="number" class="form-control" id="days_count" name="days_count" value="" min="0">
                                </div>
                            </div>


                            <div class="col-md-6">

                            <div class="mb-3">
                                <label class="form-label">status</label>
                                <select  name="status" id="status1" class="form-select select2 ">

                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                            <div class="col-md-12">
                                <div class="mb-3">

                                    <button type="submit" class="btn btn-primary w-md mt-4 float-end">Submit</button>
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
        $('#subscription').addClass('mm-active');
		$('#subscription_menu').addClass('mm-show');
		$('#splans').addClass('mm-active');
	});

    $('#product_form').validate({ // initialize the plugin
		 rules: {
            days_count:{required:true },
			name:{required:true },
            discount:{required:true },

            status1:{required:true }
		 },
		 messages: {
            discount: "Enter Discount",
            name: "Enter Name",
            days_count: "Enter Days Count",

            status1: "Select status"
		}
	 });
</script>

@endsection
