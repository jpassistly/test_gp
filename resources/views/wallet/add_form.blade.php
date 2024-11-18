@extends('layouts.master')

@section('title') @lang('translation.wallet_plans') @endsection

@section('content')

    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">{{ isset($plan) ? 'Edit Plan' : 'Add Plan' }}</h4>

                    <!-- Single Form for Add and Update -->
                    <form action="{{ isset($plan) ? url('save_wallet_plan/'.$plan->id) : url('save_wallet_plan') }}"
                          method="post"
                          id="product_form"
                          enctype="multipart/form-data">

                        @csrf
                        <!-- No need for @method('PUT') because we'll handle both add/update in the same method -->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                           value="{{ old('name', $plan->name ?? '') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="text" id="amount" name="amount" class="form-control"
                                           value="{{ old('amount', $plan->amount ?? '') }}">
                                    @error('amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Details</label>
                                    <input type="text" class="form-control" id="details" name="details"
                                           value="{{ old('details', $plan->details ?? '') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Banner</label>
                                    <input type="file" class="form-control" id="banner" name="banner">

                                    <!-- Show current banner if updating -->
                                    @if(isset($plan) && $plan->banner)
                                        <img src="{{ asset($plan->banner) }}" alt="Current Banner" width="100" height="100">
                                    @endif

                                    @error('banner')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="status1" class="form-select select2">
                                        <option value="Active" {{ (old('status', $plan->status ?? '') == 'Active') ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ (old('status', $plan->status ?? '') == 'Inactive') ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary w-md">
                                    {{ isset($plan) ? 'Update' : 'Submit' }}
                                </button>
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
        $('#wallet').addClass('mm-active');
		$('#wallet_menu').addClass('mm-show');
		$('#wallet_plans').addClass('mm-active');
	});
</script>

@endsection
