@extends('layouts.master')

@section('title')
    @lang('translation.Delivery_lines')
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Delivery Product List
        @endslot
        @slot('title')
            Delivery Product List
        @endslot
    @endcomponent
    @php
        use Carbon\Carbon;
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success :</strong> {{ Session::get('success_message') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <form action="deliver_list_person" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-4">
                                <label for="">Date</label>
                                <input type="date" name="from_date" class="form-control" id="from_date"
                                    value="{{ request()->from_date ?? date('Y-m-d') }}">
                            </div>
                            <div class="col-4">
                                <label for=""> Delivery Person</label>
                                <select id="formrow-inputState" name="delivery_person" class="form-select select2">
                                    <option value="">Select</option>
                                    @foreach ($delper as $delpe)
                                        <option {{ request()->delivery_person == $delpe->id ? 'selected' : '' }}
                                            value="{{ $delpe->id }}">{{ $delpe->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2 mt-4 ">
                                <button type="submit" class="btn btn-outline-secondary waves-effect"
                                    style="float:left;margin-top:3px">Search</button>
                            </div>
                        </div>
                    </form>
                    <table id="example--" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                {{-- <th>Date</th> --}}
                                <th>Product</th>
                                {{-- <th>Measurement</th> --}}
                                @foreach ($unitz as $units)
                                    <th>{{ $units->name }}</th>
                                @endforeach
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="tab_values">
                            @php
                                $i = 1;
                                $culumn_total = [];

                                $culumn_tt = 0;
                            @endphp
                            @foreach ($product_name_all as $product_name)
                                @php
                                    $all_total = 0;
                                @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    {{-- <td>{{ Carbon::parse($dlist->date)->format('d-m-Y') }}</td> --}}
                                    <td>{{ $product_name->name }}</td>
                                    @foreach ($unitz as $units)
                                        @php
                                            $total_ml_all = 0;

                                            // Summing the total quantities for $s_p_d_values
                                            $s_p_d_count = $s_p_d_values
                                                ->where('product_name_id', $product_name->id)
                                                ->where('quantity_id', $units->id)
                                                ->count();
                                                // ->sum('total_sum_qty');

                                            // Summing the total quantities for $o_p_d_values
                                            $o_p_d_count = $o_p_d_values
                                                ->where('product_name_id', $product_name->id)
                                                ->where('quantity_id', $units->id)
                                                ->count();
                                                // ->sum('total_sum_qty');

                                            // Row total
                                            $total_ml_all =$s_p_d_count + $o_p_d_count;
                                            // $total_ml_all = ($s_p_d_count + $o_p_d_count) / 1000;
                                            $total_ml_all = round($total_ml_all, 2);
                                            $all_total += $total_ml_all;

                                            // Initialize column total if not set
                                            if (!isset($culumn_total[$units->id])) {
                                                $culumn_total[$units->id] = 0;
                                            }

                                            // Column total
                                            $culumn_total[$units->id] += $total_ml_all;

                                        @endphp
                                        <th>{{ $total_ml_all }}</th>
                                    @endforeach
                                    @php
                                        $culumn_tt = $culumn_tt + $all_total;
                                    @endphp
                                    <th>{{ $all_total }}</th>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                @foreach ($unitz as $units)
                                    <th>{{ $culumn_total[$units->id] ?? 0 }}</th>
                                @endforeach
                                <th>{{$culumn_tt}}</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery -->
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <!-- Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <!-- Optional: Export Buttons for Excel, PDF, and Print -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    // {
                    //     extend: 'copy',
                    //     title: 'Delivery Product List'
                    // },
                    {
                        extend: 'excel',
                        title: 'Delivery Product List'
                    },
                    {
                        extend: 'pdf',
                        title: 'Delivery Product List'
                    },
                    {
                        extend: 'print',
                        title: 'Delivery Product List',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(win) {
                            // $(win.document.body)
                            //     .css('font-size', '10pt')
                            //     .prepend('<img src="https://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />');

                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                "footer": true // Display footer in DataTable
            });
        });
    </script>
@endsection
