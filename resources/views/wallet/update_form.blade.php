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
                    <form action="{{ url('update_walet_plans') }}" method="post" name="product_form" id="product_form" enctype="multipart/form-data">@csrf
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
                                <label class="form-label">Amount</label>
                                    <!-- <input type="text" class="form-control" id="name" name="name"> -->
                                    <input type="text" id="amount" name="amount" class="form-control">
                                        <script>$('#amount').val('{{ $product_list->amount }}');</script>
                                </div>
                            </div>
                        
                        
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Details</label>
                                    <input type="text" class="form-control" id="details" name="details" value="{{ $product_list['details'] }}">
                                </div>
                            </div> <script>$('#cash_back').val('{{ $product_list->details }}');</script>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Banner</label>
                                    <input type="file" class="form-control" id="banner" name="banner" value="">
                                </div>
                                <div class="mb-3">
                               
                               @if (($product_list->banner)!="") 
                                  <img src="{{asset($product_list->banner) }}" alt="Current Image" style="max-width: 200px; max-height: 200px;">
                                  @else 
                                  <p>No image available</p>
                               @endif
                          </div>
                            </div> 
                            <div class="col-md-6">
                            
                            <div class="mb-3">
                                <label class="form-label">status</label>
                                <select  name="status" id="status1" class="form-select select2 ">
                                   
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select><script>$('#status1').val('{{ $product_list->status }}');</script>
                            </div>
                        </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <!-- <input type="hidden" name="old_pic" value="{{ $product_list['pic'] }}"> -->
                                    <input type="hidden" name="id" value="{{ $product_list['id'] }}">
                                    <input type="hidden" name="npic" value="{{ $product_list['banner'] }}">
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
        $('#ecommerce').addClass('mm-active');
		$('#ecommerce_menu').addClass('mm-show');
		$('#product').addClass('mm-active');
	});

    $('#product_form').validate({ // initialize the plugin
		 rules: {
            amount:{required:true },
			name:{required:true },
            banner:{required:true },
           
		 },
		 messages: {
            amount: "Please enter Amount",
            name: "Please enter Wallet Plan Name",
            banner: "Please upload a banner",
           
		}
	 });
</script>

@endsection
