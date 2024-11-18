@extends('layouts.master')

@section('title') @lang('translation.Products') @endsection

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

                    <h4 class="card-title mb-4">Add Product</h4>
                    <form action="{{ url('store_sproduct') }}" method="post" name="product_form" id="product_form" enctype="multipart/form-data">@csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Quantity</label>
                                    <select id="quantity_id" name="quantity_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach($quantity_list as $qlist)
                                            <option value="{{ $qlist['id'] }}">{{ $qlist['name'] }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Measurement</label>
                                    <select id="measurement_id" name="measurement_id" class="form-select select2">
                                        <option value="">Select</option>
                                        @foreach($measurement_list as $mlist)
                                            <option value="{{ $mlist['id'] }}">{{ $mlist['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="text" class="form-control" id="price" name="price">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Picture</label>
                                    <input type="file" class="form-control " id="pic" name="pic">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="formrow-inputState" name="status" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
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
		$('#subscription').addClass('mm-active');
		$('#subscription_menu').addClass('mm-show');
		$('#sproduct').addClass('mm-active');
	});

    $('#product_form').validate({ // initialize the plugin
		 rules: {
			name:{required:true },
            price:{required:true },
            quantity_id:{required:true },
            pic:{required:true },
            status:{required:true }
		 },
		 messages: {
            name: "Enter product",
            price: "Enter price",
            quantity_id: "Select quantity",
            pic: "Select picture",
            status: "Select status"
		}
	 });
</script>

@endsection
