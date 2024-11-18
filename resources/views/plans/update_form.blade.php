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

                    <h4 class="card-title mb-4">Edit Product</h4>
                    <form action="{{ url('update_plans') }}" method="post" name="product_form" id="product_form" enctype="multipart/form-data">@csrf
                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                <label class="form-label">Name</label>
                                    <!-- <input type="text" class="form-control" id="name" name="name"> -->
                                    <input type="text" id="name" name="name" class="form-control">
                                        <script>$('#name').val('{{ $product_list->name }}');</script>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount</label>
                                    <input type="number" class="form-control" id="discount" name="discount" value="{{ $product_list['discount'] }}"min="0">
                                </div>
                            </div> <script>$('#discount').val('{{ $product_list->discount }}');</script>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Days Count</label>
                                    <input type="number" class="form-control" id="days_count" name="days_count" value="{{ $product_list['days_count'] }}"min="0">
                                </div>
                            </div> <script>$('#days_count').val('{{ $product_list->days_count }}');</script>


                            <div class="col-md-6">

                            <div class="mb-3">
                                <label class="form-label">status</label>
                                <select  name="status" id="status1" class="form-select select2 ">

                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select><script>$('#status1').val('{{ $product_list->status }}');</script>
                            </div>
                        </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <!-- <input type="hidden" name="old_pic" value="{{ $product_list['pic'] }}"> -->
                                    <input type="hidden" name="id" value="{{ $product_list['id'] }}">
                                    <button type="submit" class="btn btn-primary w-md mt-4 float-end">Update</button>
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
