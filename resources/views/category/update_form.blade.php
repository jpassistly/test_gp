@extends('layouts.master')

@section('title') @lang('translation.Update_Category') @endsection

@section('content')
    <div class="row">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Update Category</h4>



                    <form action="{{ url('update_category', $category_details->id) }}" method="post" name="category_form" id="category_form" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <input type="text" class="form-control" id="name" name="name" maxlength="20" value="{{ old('name', $category_details->name) }}">
                                    {{-- Display validation error for "name" --}}
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Picture</label>
                                    <input type="file" class="form-control" id="pic" name="pic">
                                    {{-- Display validation error for "pic" --}}
                                    @error('pic')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    {{-- Show the existing image --}}

                                    @if ($category_details->pic)
                                        <img src="{{ env('ASSET_LINK_URL') . $category_details['pic'] }}" alt="Category Image" height="100" width="100" class="mt-2">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="formrow-inputState" name="status" class="form-select">
                                        <option value="Active" {{ old('status', $category_details->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $category_details->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    {{-- Display validation error for "status" --}}
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary w-md mt-4">Update</button>
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
    $(document).ready(function() {
        // Add active classes for menu highlighting
        $('#masters').addClass('mm-active');
        $('#master_menu').addClass('mm-show');
        $('#category').addClass('mm-active');

    // Add a click event listener to buttons with the text "Update"
    $('button:contains("Update")').on('click', function(e) {
    e.preventDefault(); // Prevent the form from submitting immediately

    // SweetAlert confirmation dialog
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this category?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, update it!",
        cancelButtonText: "No, cancel",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Find the parent form and submit it
            $(this).closest('form').submit();
        }
    });
});
});

</script>
@endsection

