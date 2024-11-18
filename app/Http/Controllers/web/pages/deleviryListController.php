<?php

namespace App\Http\Controllers\web\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customersubscription;
use App\Models\Customer;
use App\Models\Subscriptionproduct;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Measurement;
use App\Models\ProductName;
use App\Models\Unit;
use App\Models\Deliveryperson;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Pincode;
use App\Models\Area;
use App\Models\Delivery_lines;
use App\Models\delivery_line_mapping;
use App\Models\subscription_log;
use App\Models\Customerwallethistory;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\DeliveriesExport;    

class deleviryListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $delper = Deliveryperson::where('status', 'Active')->get();
    //     $unitz = Unit::where('status', 'Active')->get();
    //     // Order by date in ascending order
    //     if ($request->isMethod('post')) {
    //         $from_date = $request->from_date;
    //         $to_date = $request->to_date;
    //         $del_per = $request->delivery_person;

    //         // Get the delivery list within the specified date range
    //         $query = Customersubscription::whereBetween('date', [$from_date, $to_date])
    //             ->where('addon_status', null);

    //         if ($del_per != "") {
    //             $query->where('deliveryperson_id', $del_per);
    //         }

    //         $delivery_list = $query->orderBy('date', 'asc')->get();
    //     } else {
    //         $delivery_list = Customersubscription::where('addon_status', null)
    //             ->orderBy('date', 'asc')->get();
    //     }


    //     foreach ($delivery_list as &$del_list) {
    //         $cus = Customer::find($del_list->subscription_customer_id);
    //         $pro = Product::find($del_list->subscription_products_id); // Use the correct model to find the product
    //         $cart = Cart::find($del_list->order_id);
    //         $del_very = Deliveryperson::find($del_list->deliveryperson_id);
    //         $mes = Measurement::find($pro->measurement_id); // Fetch measurement details
    //         $unit = Unit::find($pro->quantity_id); // Fetch unit details
    //         $pin = Pincode::find($del_list->pincode);
    //         $area = Area::find($del_list->area);
    //         $del_line = delivery_line_mapping::find($del_list->delivery_line_id);
    //         // dd($del_line);
    //         // $delivery_lines=Delivery_lines::where('delivery_line_id',$del_line->delivery_line_id)->first();
    //         //    dd($delivery_lines);

    //         // dd($dl);
    //         if ($cus) {
    //             $del_list->subscription_customer_id = $cus->name;
    //             $del_list->subscription_customer_mobile = $cus->mobile;
    //         }
    //         if ($pro) {
    //             $del_list->subscription_products_id = $pro->name;
    //         }
    //         if ($del_very) {
    //             $del_list->deliveryperson_id = $del_very->name;
    //         }
    //         if ($mes) {
    //             $del_list->measurement_name = $mes->name; // Add measurement name
    //         }
    //         if ($unit) {
    //             $del_list->unit_name = $unit->name; // Add unit name..
    //         }
    //         if ($pin) {
    //             $del_list->pincode = $pin->pincode;
    //         }
    //         if ($area) {
    //             $del_list->area = $area->name;
    //         }
    //         if ($del_line) {
    //             $dl = Delivery_lines::where('id', $del_line->delivery_line_id)->first();
    //             $del_list->delivery_line_id = $dl->name;
    //         }
    //     }
    //     // dd($delivery_list);
    //     // Return or use $delivery_list as needed
    //     return view('deleviry-list.index', compact('delivery_list', 'delper'));
    // }
    public function index(Request $request)
{
    $delper = Deliveryperson::where('status', 'Active')->get();
    $unitz = Unit::where('status', 'Active')->get();

    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $del_per = $request->delivery_person;
    $rows_per_page = $request->input('rows_per_page', 10); // Default to 10 if not set

    // Base query
    $query = Customersubscription::select([
        'customers_subscription.*',
        'customers.name as customer_name',
        'customers.mobile as customer_mobile',
        'products.name as product_name',
        'deliverypersons.name as deliveryperson_name',
        'measurements.name as measurement_name',
        'units.name as unit_name',
        'pincodes.pincode as pincode_value',
        'areas.name as area_name',
        'delivery_lines.name as delivery_line_name'
    ])
    ->leftJoin('customers', 'customers_subscription.subscription_customer_id', '=', 'customers.id')
    ->leftJoin('products', 'customers_subscription.subscription_products_id', '=', 'products.id')
    ->leftJoin('deliverypersons', 'customers_subscription.deliveryperson_id', '=', 'deliverypersons.id')
    ->leftJoin('measurements', 'products.measurement_id', '=', 'measurements.id')
    ->leftJoin('units', 'products.quantity_id', '=', 'units.id')
    ->leftJoin('pincodes', 'customers_subscription.pincode', '=', 'pincodes.id')
    ->leftJoin('areas', 'customers_subscription.area', '=', 'areas.id')
    ->leftJoin('delivery_line_mappings', 'customers_subscription.delivery_line_id', '=', 'delivery_line_mappings.id')
    ->leftJoin('delivery_lines', 'delivery_line_mappings.delivery_line_id', '=', 'delivery_lines.id')
    ->where('customers_subscription.addon_status', null)
    ->orderBy('customers_subscription.date', 'asc');

    // Apply filters
    if ($request->isMethod('post')) {
        if ($from_date && $to_date) {
            $query->whereBetween('customers_subscription.date', [$from_date, $to_date]);
        }
        if ($del_per) {
            $query->where('customers_subscription.deliveryperson_id', $del_per);
        }
    }

    // Paginate results using the dynamically selected rows per page
    $delivery_list = $query->paginate($rows_per_page); // Paginate with selected rows per page

    // Map joined data to fields for display
    $delivery_list->getCollection()->transform(function ($item) {
        $item->subscription_customer_id = $item->customer_name;
        $item->subscription_customer_mobile = $item->customer_mobile;
        $item->subscription_products_id = $item->product_name;
        $item->deliveryperson_id = $item->deliveryperson_name;
        $item->measurement_name = $item->measurement_name;
        $item->unit_name = $item->unit_name;
        $item->pincode = $item->pincode_value;
        $item->area = $item->area_name;
        $item->delivery_line_id = $item->delivery_line_name;
        return $item;
    });

    return view('deleviry-list.index', compact('delivery_list', 'delper'));
    
}

public function exportDeliveries(Request $request)
{
        // dd($request->all());
        // Get filters from request
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        
        $delivery_person = $request->delivery_person;
    
        // Query to fetch delivery records based on filters
        $query = Customersubscription::select([
            'customers_subscription.*',
            'customers.name as customer_name',
            'customers.mobile as customer_mobile',
            'products.name as product_name',
            'deliverypersons.name as deliveryperson_name',
            'measurements.name as measurement_name',
            'units.name as unit_name',
            'pincodes.pincode as pincode_value',
            'areas.name as area_name',
            'delivery_lines.name as delivery_line_name'
        ])
        ->leftJoin('customers', 'customers_subscription.subscription_customer_id', '=', 'customers.id')
        ->leftJoin('products', 'customers_subscription.subscription_products_id', '=', 'products.id')
        ->leftJoin('deliverypersons', 'customers_subscription.deliveryperson_id', '=', 'deliverypersons.id')
        ->leftJoin('measurements', 'products.measurement_id', '=', 'measurements.id')
        ->leftJoin('units', 'products.quantity_id', '=', 'units.id')
        ->leftJoin('pincodes', 'customers_subscription.pincode', '=', 'pincodes.id')
        ->leftJoin('areas', 'customers_subscription.area', '=', 'areas.id')
        ->leftJoin('delivery_line_mappings', 'customers_subscription.delivery_line_id', '=', 'delivery_line_mappings.id')
        ->leftJoin('delivery_lines', 'delivery_line_mappings.delivery_line_id', '=', 'delivery_lines.id')
        ->where('customers_subscription.addon_status', null); // Exclude addon status
        
    // Apply the filters with table prefixes
    if ($from_date) {
        $query->where('customers_subscription.date', '>=', $from_date);
    }
    
    if ($to_date) {
        $query->where('customers_subscription.date', '<=', $to_date);
    }
    
    if ($delivery_person) {
        $query->where('customers_subscription.deliveryperson_id', $delivery_person);
    }
    
    $deliveryList = $query->get();


    // Export the filtered list using Laravel Excel
    return Excel::download(new DeliveriesExport($deliveryList), 'filtered_deliveries.xlsx');
}


    
    /**
     * Show the form for creating a new resource..
     */
    public function create(Request $request)
    {
        $from_date = $request->from_date;
        if (!isset($request->from_date)) {
            $from_date = date('Y-m-d');
        }

        $product_name_all = ProductName::get();


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


        DB::statement('
                          CREATE OR REPLACE VIEW subscription_product_report AS
                          SELECT
                              customers_subscription.subscription_customer_id,
                              customers_subscription.subscription_products_id,
                              customers_subscription.subscription_total_qty,
                              customers_subscription.date,
                              products.name as product_name,
                              products.quantity_id,
                              products.measurement_id,
                              products.price,
                              products.product_id as product_name_id,
                              products.subscription,
                              units.name AS unit_name,
                              units.name AS unit_quantity,
                              measurements.name AS measurement_name,
                              (units.name * customers_subscription.subscription_total_qty) AS total_sum_qty
                          FROM
                              customers_subscription
                          JOIN
                              products ON products.id = customers_subscription.subscription_products_id
                          JOIN
                              units ON units.id = products.quantity_id
                          JOIN
                              measurements ON measurements.id = products.measurement_id
                          WHERE
                              customers_subscription.delivery_status != "cancelled"
                              AND customers_subscription.subscription_total_qty IS NOT NULL;
                          ');



        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        DB::statement('
                         CREATE OR REPLACE VIEW order_products_report AS
                         SELECT
                             orders.customer_id as subscription_customer_id,
                             orders.products_id as subscription_products_id,
                             orders.quantity as subscription_total_qty,
                             orders.delivery_date as date,
                             products.name as product_name,
                             products.quantity_id,
                             products.measurement_id,
                             products.price,
                             products.product_id as product_name_id,
                             products.subscription,
                             units.name AS unit_name,
                             units.name AS unit_quantity,
                             measurements.name AS measurement_name,
                             (units.name * orders.quantity) AS total_sum_qty
                         FROM
                             orders
                         JOIN
                             products ON products.id = orders.products_id
                         JOIN
                             units ON units.id = products.quantity_id
                         JOIN
                             measurements ON measurements.id = products.measurement_id
                         WHERE
                             orders.delivery_status != "cancelled"
                             AND orders.price != "0"
                             AND orders.quantity IS NOT NULL;
                        ');

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Orders ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Orders ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Orders ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



        $s_p_d_values = DB::table('subscription_product_report')
            // ->select(DB::raw('SUM(total_sum_qty) as total_qty'))
            // ->where('product_name_id', $product_id)
            ->where('date', $from_date)
            ->get();

        $o_p_d_values = DB::table('order_products_report')
            // ->select(DB::raw('SUM(total_sum_qty) as total_qty'))
            // ->where('product_name_id', $product_id)
            ->where('date', $from_date)
            ->get();

        // dd($s_p_d_values);

        $pro_details = Product::select(
            'products.*',
            'products.price as product_price',
            'products.product_id as product_name_id',
            'products.name as product_name',
            'products.pic as product_image',
            'measurements.name as measurement_name',
            'units.name as quantity_value'
        )
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->join('units', 'products.quantity_id', '=', 'units.id')
            // ->where('products.id', $d->subscription_products_id)
            ->get();











        // $del_per = $request->delivery_person;
        $delper = Deliveryperson::where('status', 'Active')->get();
        $unitz = Unit::where('status', 'Active')->get();

        // $query = DB::table('customers_subscription')
        //     ->select('subscription_products_id', 'date', DB::raw('SUM(subscription_quantity) as total_quantity'))
        //     ->where('date', $from_date)
        //     ->where('delivery_status', '!=', 'cancelled');

        // if (!empty($del_per)) {
        //     $query->where('deliveryperson_id', $del_per);
        // }

        // $query = $query->groupBy('subscription_products_id', 'date')->get();

        // $unit_totals = [];
        // foreach ($unitz as $unit) {
        //     $unit_totals[$unit->id] = 0;
        // }

        // foreach ($query as &$qry) {
        //     $qry->unit = "name";
        //     $pro_details = Product::select(
        //         'products.*',
        //         'products.price as product_price',
        //         'products.product_id as product_name_id',
        //         'products.name as product_name',
        //         'products.pic as product_image',
        //         'measurements.name as measurement_name',
        //         'units.name as quantity_value'
        //     )
        //         ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
        //         ->join('units', 'products.quantity_id', '=', 'units.id')
        //         ->where('products.id', $qry->subscription_products_id)
        //         ->first();

        //     if ($pro_details) {
        //         $qry->unit = $pro_details->quantity_value;
        //         $qry->measurement = $pro_details->measurement_name;
        //         $qry->unit_id = $pro_details->quantity_id;
        //         $qry->name = $pro_details->name;

        //         // Update unit totals
        //         if (isset($unit_totals[$qry->unit_id])) {
        //             $unit_totals[$qry->unit_id] += $qry->total_quantity;
        //         }
        //     }
        // }

        return view('delivery-person.delivery_person', compact('pro_details', 's_p_d_values', 'o_p_d_values', 'product_name_all', 'unitz', 'delper'));
        // return view('delivery-person.delivery_person', compact('pro_details','s_p_d_values','o_p_d_values','product_name_all', 'delper', 'unitz', 'query', 'unit_totals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            // Get the delivery list within the specified date range
            // $delivery_list = Order::whereBetween('delivery_date', [$from_date, $to_date])
            //     ->orderBy('date', 'asc')
            //     ->get();
            $delivery_list = DB::table('orders')
                ->select('orders.*', 'Cart.*', 'products.name', 'products.quantity_id', 'products.measurement_id') // Select columns from all tables
                ->join('Cart', 'Cart.order_id', '=', 'orders.order_id') // Join with Cart table
                ->join('products', 'Cart.product_id', '=', 'products.id') // Join with products table
                ->whereBetween('delivery_date', [$from_date, $to_date])
                ->orderBy('orders.delivery_date', 'asc') // Order by delivery_date ascending
                ->get();
        } else {
            $delivery_list = DB::table('orders')
                ->select('orders.*', 'Cart.*', 'products.name', 'products.quantity_id', 'products.measurement_id') // Select columns from all tables
                ->join('Cart', 'Cart.order_id', '=', 'orders.order_id') // Join with Cart table
                ->join('products', 'Cart.product_id', '=', 'products.id') // Join with products table
                ->orderBy('orders.delivery_date', 'asc') // Order by delivery_date ascending
                ->get();
        }
        foreach ($delivery_list as &$del_list) {
            $cus = Customer::find($del_list->customer_id);
            $pro = Subscriptionproduct::find($del_list->product_id); // Use the correct model to find the product
            $mes = Measurement::find($del_list->measurement_id);
            if ($cus) {
                $del_list->customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($pro) {
                $del_list->product_id = $pro->name;
            }
            if ($mes) {
                $del_list->measurement_id = $mes->name;
            }
        }

        //    dd($delivery_list);
        $delivery_person = Deliveryperson::where('status', 'Active')->get();

        return view('order-list.index', ['delivery_list' => $delivery_list, 'delivery_person' => $delivery_person]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $delper = Deliveryperson::where('status', 'Active')->get();
        $unitz = Unit::where('status', 'Active')->get();
        // Order by date in ascending order
        if ($request->isMethod('post')) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $del_per = $request->delivery_person;
            $rat = $request->ratings;

            // Get the delivery list within the specified date range
            $query = Customersubscription::whereBetween('date', [$from_date, $to_date])
                ->where('addon_status', null)->where('delivery_status', 'Delivered');

            if ($del_per != "") {
                $query->where('deliveryperson_id', $del_per);
            }
            if ($rat != "") {
                $query->where('rating', $rat);
            }

            $delivery_list = $query->orderBy('rating', 'DESC')->get();
        } else {
            $delivery_list = Customersubscription::where('addon_status', null)->where('delivery_status', 'Delivered')
                ->orderBy('rating', 'DESC')->get();
        }


        foreach ($delivery_list as &$del_list) {
            $cus = Customer::find($del_list->subscription_customer_id);
            $pro = Product::find($del_list->subscription_products_id); // Use the correct model to find the product
            $cart = Cart::find($del_list->order_id);
            $del_very = Deliveryperson::find($del_list->deliveryperson_id);
            $mes = Measurement::find($pro->measurement_id); // Fetch measurement details
            $unit = Unit::find($pro->quantity_id); // Fetch unit details
            $pin = Pincode::find($del_list->pincode);
            $area = Area::find($del_list->area);
            $del_line = delivery_line_mapping::find($del_list->delivery_line_id);
            // dd($del_line);
            // $delivery_lines=Delivery_lines::where('delivery_line_id',$del_line->delivery_line_id)->first();
            //    dd($delivery_lines);

            // dd($dl);
            if ($cus) {
                $del_list->subscription_customer_id = $cus->name;
                $del_list->mobile = $cus->mobile;
            }
            if ($pro) {
                $del_list->subscription_products_id = $pro->name;
            }
            if ($del_very) {
                $del_list->deliveryperson_id = $del_very->name;
            }
            if ($mes) {
                $del_list->measurement_name = $mes->name; // Add measurement name
            }
            if ($unit) {
                $del_list->unit_name = $unit->name; // Add unit name..
            }
            if ($pin) {
                $del_list->pincode = $pin->pincode;
            }
            if ($area) {
                $del_list->area = $area->name;
            }
            if ($del_line) {
                $dl = Delivery_lines::where('id', $del_line->delivery_line_id)->first();
                $del_list->delivery_line_id = $dl->name;
            }
        }
        // dd($delivery_list);
        // Return or use $delivery_list as needed
        return view('rating.index', compact('delivery_list', 'delper'));

        // return view('rating.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function delivery_list_cust_dash(Request $request)
    {
        $delper = Deliveryperson::where('status', 'Active')->get();
        $unitz = Unit::where('status', 'Active')->get();

        if ($request->isMethod('post')) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $cust_id = $request->cust_id;

            $query = Customersubscription::whereBetween('date', ["$from_date", "$to_date"])
                ->where('subscription_customer_id', $cust_id);

            $delivery_list = $query->orderBy('date', 'asc')->get();
            //  dd($delivery_list);
        }

        $sel = "";
        $i = 1;
        foreach ($delivery_list as &$del_list) {
            $cus = Customer::find($del_list->subscription_customer_id);
            $pro = Product::find($del_list->subscription_products_id);
            $cart = Cart::find($del_list->order_id);
            $del_very = Deliveryperson::find($del_list->deliveryperson_id);
            $mes = Measurement::find($pro->measurement_id);
            $unit = Unit::find($pro->quantity_id);
            $pin = Pincode::find($del_list->pincode);
            $area = Area::find($del_list->area);
            $del_line = delivery_line_mapping::find($del_list->delivery_line_id);

            if ($cus) {
                $del_list->subscription_customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($pro) {
                $del_list->subscription_products_id = $pro->name;
            }
            if ($del_very) {
                $del_list->deliveryperson_id = $del_very->name;
            }
            if ($mes) {
                $del_list->measurement_name = $mes->name;
            }
            if ($unit) {
                $del_list->unit_name = $unit->name;
            }
            if ($pin) {
                $del_list->pincode = $pin->pincode;
            }
            if ($area) {
                $del_list->area = $area->name;
            }
            if ($del_line) {
                $dl = Delivery_lines::where('id', $del_line->delivery_line_id)->first();
                $del_list->delivery_line_id = $dl->name;
            }

            if ($del_list->addon_status == null) {
                $add_on = "NO";
                $add = "";
            } else {
                $add_on = "YES";
                $add = '<button class="btn btn-warning " onclick="add_on(' . $del_list->id . ')"><i class="fa fa-plus-square" aria-hidden="true"></i></button>';
            }

            $rating_stars = "";
            if ($del_list->rating != null) {
                for ($j = 0; $j < $del_list->rating; $j++) {
                    $rating_stars .= '<i class="fa fa-star" aria-hidden="true" style="color: gold;"></i>';
                }
                for ($j = $del_list->rating; $j < 5; $j++) {
                    $rating_stars .= '<i class="fa fa-star-o" aria-hidden="true" style="color: gold;"></i>';
                }
                $rating_star = '<button class="btn btn-secondary" onclick="rating_star(' . $del_list->id . ')"><i class="fa fa-star" aria-hidden="true"></i></button>';
            } else {
                $rating_stars = "";
                $rating_star = "";
            }

            $sel .= '<tr>
                        <td>' . $i . '</td>
                        <td>' . $del_list->date . '</td>
                        <td>' . $add_on . '</td>
                        <td>' . $del_list->delivery_status . '</td>
                        <td>' . $del_list->deliveryperson_id . '</td>
                        <td>' . $rating_stars . '</td>
                        <td><button class="btn btn-success mx-2" onclick="pro_sts(' . $del_list->id . ')"><i class="fa fa-eye"></i></button>' . $add . $rating_star . '</td>
                       </tr>';
            $i++;
        }

        return response()->json(['success' => true, 'message' => 'check out successfully', 'table' => $sel]);
    }
    public function full_delivery_list(Request $request)
    {
        $delper = Deliveryperson::where('status', 'Active')->get();
        $unitz = Unit::where('status', 'Active')->get();
    
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $del_per = $request->delivery_person;
        $rows_per_page = $request->input('rows_per_page', 10); // Default to 10 if not set
    
        // Base query
        $query = Customersubscription::select([
            'customers_subscription.*',
            'customers.name as customer_name',
            'customers.mobile as customer_mobile',
            'products.name as product_name',
            'deliverypersons.name as deliveryperson_name',
            'measurements.name as measurement_name',
            'units.name as unit_name',
            'pincodes.pincode as pincode_value',
            'areas.name as area_name',
            'delivery_lines.name as delivery_line_name'
        ])
        ->leftJoin('customers', 'customers_subscription.subscription_customer_id', '=', 'customers.id')
        ->leftJoin('products', 'customers_subscription.subscription_products_id', '=', 'products.id')
        ->leftJoin('deliverypersons', 'customers_subscription.deliveryperson_id', '=', 'deliverypersons.id')
        ->leftJoin('measurements', 'products.measurement_id', '=', 'measurements.id')
        ->leftJoin('units', 'products.quantity_id', '=', 'units.id')
        ->leftJoin('pincodes', 'customers_subscription.pincode', '=', 'pincodes.id')
        ->leftJoin('areas', 'customers_subscription.area', '=', 'areas.id')
        ->leftJoin('delivery_line_mappings', 'customers_subscription.delivery_line_id', '=', 'delivery_line_mappings.id')
        ->leftJoin('delivery_lines', 'delivery_line_mappings.delivery_line_id', '=', 'delivery_lines.id')
        ->where('customers_subscription.addon_status', null)
        ->orderBy('customers_subscription.date', 'asc');
    
        // Apply filters
        if ($request->isMethod('post')) {
            if ($from_date && $to_date) {
                $query->whereBetween('customers_subscription.date', [$from_date, $to_date]);
            }
            if ($del_per) {
                $query->where('customers_subscription.deliveryperson_id', $del_per);
            }
        }
    
        // Paginate results using the dynamically selected rows per page
        $delivery_list = $query->paginate($rows_per_page); // Paginate with selected rows per page
    
        // Map joined data to fields for display
         $delivery_list->getCollection()->transform(function ($item) {
            $item->subscription_customer_id = $item->customer_name;
            $item->subscription_customer_mobile = $item->customer_mobile;
            $item->subscription_products_id = $item->product_name;
            $item->deliveryperson_id = $item->deliveryperson_name;
            $item->measurement_name = $item->measurement_name;
            $item->unit_name = $item->unit_name;
            $item->pincode = $item->pincode_value;
            $item->area = $item->area_name;
            $item->delivery_line_id = $item->delivery_line_name;
            return $item;
        });
    
        // return view('deleviry-list.index', compact('delivery_list', 'delper'));
        
    }
    public function subscription_log(Request $request)
    {
       $delivery_list=subscription_log::get(); 
       return view('deleviry-list.list', compact('delivery_list'));
     }
    
     public function check_notifications(Request $request)
     {
         // Define the start and end of today
         $startOfDay = Carbon::today()->startOfDay();  // Start of today's date
         $endOfDay = Carbon::today()->endOfDay();      // End of today's date
     
         // Retrieve records created between the start and end of today
         $delivery_list = Customerwallethistory::whereBetween('created_at', [$startOfDay, $endOfDay])->get();
     
        // Define the start and end of today
    $startOfDay = Carbon::today()->startOfDay();  // Start of today's date (e.g., '2024-11-12 00:00:00')
    $endOfDay = Carbon::today()->endOfDay();      // End of today's date (e.g., '2024-11-12 23:59:59')

    // Retrieve records created between the start and end of today
    $delivery_list = Customerwallethistory::whereBetween('created_at', [$startOfDay, $endOfDay])->get();

    // Return the data as a JSON response
    return response()->json([
        'success' => true,
        'data' => $delivery_list,
        'message' => 'Records fetched successfully.',
    ]);
     }
     public function check_notifications2(Request $request)
{
    // Define the start and end of today
    $startOfDay = Carbon::today()->startOfDay();
    $endOfDay = Carbon::today()->endOfDay();

    // Retrieve records created between the start and end of today, selecting specific fields
    $delivery_list = Customerwallethistory::whereBetween('customer_wallet_history.created_at', [$startOfDay, $endOfDay])
    ->leftJoin('customers', 'customer_wallet_history.customer_id', '=', 'customers.id')
    ->select(
        'customers.name',       // Name from Customer table
        'customers.mobile',     // Mobile from Customer table
        'customer_wallet_history.debit_credit_status',
        'customer_wallet_history.amount',
        'customer_wallet_history.remarks',
        'customer_wallet_history.created_at',
        'customer_wallet_history.notes'
    )
    ->get();

    // Return the data as a JSON response
    return response()->json([
        'success' => true,
        'data' => $delivery_list,
        'message' => 'Records fetched successfully.',
    ]);
}
    
}


