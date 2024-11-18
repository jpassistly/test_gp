@extends('layouts.master')

@section('title')
    @lang('translation.Delivery_lines')
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet" />
    <style>
        .pagination {
            justify-content: flex-end;
        }
    </style>
    <style>
        /* Small Excel export button */
        /* .dt-button.buttons-excel {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 3px;
            text-transform: uppercase;
            transition: background-color 0.3s ease;
        } */
        a.dt-button {
            position: relative;
            display: inline-block;
            box-sizing: border-box;
            margin-right: 0.333em;
            padding: 0.5em 1em;
            border: 1px solid #999;
            border-radius: 2px;
            cursor: pointer;
            font-size: 12px;
            color: black;
            white-space: nowrap;
            overflow: hidden;
            background-color: #e9e9e9;
            user-select: none;
            background-image: linear-gradient(to bottom, #fff 0%, #e9e9e9 100%);
        }
        a, button {
            outline: none !important;
        }
        a {
            text-decoration: none !important;
        }

    
        /* Hover effect for the Excel button */
        .dt-button.buttons-excel:hover {
            background-color: #45a049;
            cursor: pointer;
        }
        .btn-success {
            padding: 4px 8px;
            font-size: 12px;
        }
    </style>
            
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Delivery List
        @endslot
        @slot('title')
            Delivery List
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filters Form -->
                    <form action="delivery_list" method="post">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                <label for="from_date">From Date</label>
                                <input type="date" name="from_date" class="form-control" id="from_date" value="{{ request()->from_date ?? date('Y-m-d') }}">
                                <input type="hidden" name="export_from_date" class="form-control" id="export_from_date" value="{{ request()->from_date }}">
                            </div>
                            
                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                <label for="to_date">To Date</label>
                                <input type="date" name="to_date" class="form-control" id="to_date" value="{{ request()->to_date ?? date('Y-m-d') }}">
                                <input type="hidden" name="export_to_date" class="form-control" id="export_to_date" value="{{ request()->to_date }}">
                            </div>
                            
                            <div class="col-12 col-md-4 mb-3 mb-md-0">
                                <label for="delivery_person">Delivery Person</label>
                                <select id="formrow-inputState" name="delivery_person" class="form-select select2">
                                    <option value="">Select</option>
                                    @foreach ($delper as $delpe)
                                        <option {{ request()->delivery_person == $delpe->id ? 'selected' : '' }} value="{{ $delpe->id }}">{{ $delpe->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-12 col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
                            </div>
                        </div>
                    </form>                    

                    <!-- Rows Per Page Dropdown -->
                    <form action="deliveries" method="get" class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <!-- Rows Per Page Dropdown -->
                            <label for="rows_per_page" class="mb-0 me-2">Show</label>
                            <select name="rows_per_page" aria-controls="datatable" class="mx-2 custom-select custom-select-sm form-control form-control-sm form-select w-auto" style="padding-top: 0.25rem; padding-bottom: 0.25rem; line-height: 1.5;" onchange="this.form.submit()">
                                <option value="10" {{ request()->rows_per_page == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request()->rows_per_page == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request()->rows_per_page == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request()->rows_per_page == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <label class="mb-0 ms-2">entries</label>
                        </div>
                    
                        <!-- Export Button -->
                        <div class="dt-buttons">
                            <a class="dt-button buttons-excel buttons-html5" tabindex="0" aria-controls="example" href="#" onclick="exportToExcel()">
                                <span>Excel</span>
                            </a>
                        </div>
                        
                    </form>                   
                    
                    
                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="deliveryTable" class="table table-bordered dt-responsive nowrap w-100 dataTable no-footer dtr-inline" role="grid" aria-describedby="datatable_info">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Mobile</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Measurement</th>
                                    <th>Delivery Person</th>
                                    <th>Status</th>
                                    <th>Pincode</th>
                                    <th>Area</th>
                                    <th>Delivery line</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = $delivery_list->firstItem(); @endphp
                                @foreach ($delivery_list as $dlist)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ \Carbon\Carbon::parse($dlist->date)->format('d-m-Y') }}</td>
                                        <td>{{ $dlist->subscription_customer_id }}</td>
                                        <td>{{ $dlist->subscription_customer_mobile }}</td>
                                        <td>{{ $dlist->subscription_products_id }}</td>
                                        <td>{{ $dlist->subscription_total_qty }}</td>
                                        <td>{{ $dlist->unit_name . ' ' . $dlist->measurement_name }}</td>
                                        <td>{{ $dlist->deliveryperson_id }}</td>
                                        <td>{{ $dlist->delivery_status }}</td>
                                        <td>{{ $dlist->pincode }}</td>
                                        <td>{{ $dlist->area }}</td>
                                        <td>{{ $dlist->delivery_line_id }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination & Entry Count -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $delivery_list->firstItem() }} to {{ $delivery_list->lastItem() }} of {{ $delivery_list->total() }} entries
                        </div>
                        <div>
                            {{ $delivery_list->appends(request()->input())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

    
    <script>
        function exportToExcel() {
            var from_date = document.getElementById("export_from_date").value;
            var to_date = document.getElementById("export_to_date").value;
            var delivery_person = document.getElementById("formrow-inputState").value;
            var url = "export-deliveries?from_date=" + from_date + "&to_date=" + to_date + "&delivery_person=" + delivery_person;
            window.location.href = url;
        }

    </script>
    <script>
        $(document).ready(function() {
            $('#deliveryTable').DataTable({
                paging: false,
                ordering: true,
                searching: true,
                info: false
            });
        });
    </script>    
@endsection
