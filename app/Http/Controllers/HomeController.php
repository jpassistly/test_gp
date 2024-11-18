<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Customer;
use App\Models\Deliveryperson;
use App\Models\Pincode;
use App\Models\inventerytable;
use App\Models\Area;
use Illuminate\Support\Carbon;
use App\Models\Category;
use App\Models\Order;
use App\Models\vendor;
use App\Models\Product;
use App\Models\ProductName;
use App\Models\Customersubscription;
use Illuminate\Support\Facades\DB;
use App\Models\delivery_line_mapping;
use App\Models\Delivery_lines;
use App\Models\Unit;
use App\Models\Measurement;
use App\Models\Cart;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $total_customers = Customer::where('status', 'Active')->count();
        $active_delivery_persons = Deliveryperson::where('status', 'Active')->count();
        $number_of_pincodes = Pincode::where('status', 'Active')->count();
        $number_of_areas = Area::where('status', 'Y')->count();
        $number_of_outlets = inventerytable::count();
        $active_delivery_persons = Deliveryperson::where('status', 'Active')->count();
        $number_of_categories = Category::where('status', 'Active')->count();
        $vendor_count = vendor::where('status', 'active')->where('type', '1')->count();
        $buyer_count = vendor::where('status', 'active')->where('type', '2')->count();
        $number_of_products = Product::where('status', 'Active')->count();
        $active_subscribers = Customersubscription::where('delivery_status', "Yet to deliver")
            ->distinct('subscription_customer_id')
            ->count();
        // dd($total_customers);

        $dates_15 = [];
        $dates_order = [];
        $today_date = Carbon::today();

        for ($i = 0; $i < 15; $i++) {
            $dates_15[] = $today_date->copy()->subDays($i)->format('Y-m-d');
            $dates_order[] = $today_date->copy()->subDays($i)->format('Y-m-d');
        }
        // dd($dates_15);


        $today_date = date('Y-m-d');
        $dates = $dates_15;


        // Subquery to get the latest date for each subscription product ID
        $latestDatesSubquery = Customersubscription::selectRaw('subscription_products_id, MAX(date) as latest_date')
            ->where('date', '<', $today_date)
            ->groupBy('subscription_products_id');

        $product_ids = Customersubscription::select('customers_subscription.subscription_products_id', 'product_names.name')
            ->join('products', 'customers_subscription.subscription_products_id', '=', 'products.id')
            ->join('product_names', 'products.product_id', '=', 'product_names.id')
            ->joinSub($latestDatesSubquery, 'latest_dates', function ($join) {
                $join->on('customers_subscription.subscription_products_id', '=', 'latest_dates.subscription_products_id')
                    ->on('customers_subscription.date', '=', 'latest_dates.latest_date');
            })
            ->orderBy('latest_dates.latest_date', 'desc')
            ->get();

        foreach ($product_ids as $d) {
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
                ->where('products.id', $d->subscription_products_id)
                ->first();

            $d->pro_name = $pro_details->product_name . '-' . $pro_details->quantity_value;
        }

        // dd($product_ids);

        $product = []; // Initialize the product array
        $pro_names = []; // Initialize the product array
        foreach ($product_ids as $pro) {

            foreach ($dates as $d) {
                $qty_sum = Customersubscription::where('subscription_products_id', $pro->subscription_products_id)
                    ->where('date', $d)
                    ->sum('subscription_quantity');

                // Initialize the product entry if it doesn't exist
                if (!isset($product[$pro->subscription_products_id])) {
                    $product[$pro->subscription_products_id] = [];
                }


                $product[$pro->subscription_products_id][$d] = $qty_sum;
            }
        }


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Create view  subscription_product_dashboard ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Create view  subscription_product_dashboard  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Create view  subscription_product_dashboard  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



        DB::statement('
            CREATE OR REPLACE VIEW subscription_product_dashboard AS
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


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



        $s_p_d_dates_15 = [];
        $today = Carbon::today();

        for ($i = 0; $i <= 15; $i++) {
            $s_p_d_dates_15[] = $today->copy()->subDays($i)->format('Y-m-d');
        }

        $s_p_d_dates_15 = array_reverse($s_p_d_dates_15);
        // dd($s_p_d_dates_15);

        $s_p_d_disnict_prts = DB::table('subscription_product_dashboard')
            ->select('product_name_id', 'product_name')
            ->distinct()
            ->get();

        $s_p_d_array = [];
        $s_p_d_productData = [];

        // Loop through the dates (assuming $dates_15 is an array of the last 15 dates)
        foreach ($s_p_d_dates_15 as $s_p_d_o_date) {
            foreach ($s_p_d_disnict_prts as $distinct_product) {
                $product_id = $distinct_product->product_name_id;

                // Get the total sum for each product on the specific date
                $s_p_d_values = DB::table('subscription_product_dashboard')
                    ->select(DB::raw('SUM(total_sum_qty) as total_qty'))
                    ->where('product_name_id', $product_id)
                    ->where('date', $s_p_d_o_date)
                    ->first();

                // Store the result for each product and date
                $product_name = $distinct_product->product_name;

                // Initialize array key if not set
                if (!isset($s_p_d_productData[$product_name])) {
                    $s_p_d_productData[$product_name] = '';
                }

                // Append total_qty to the product data for that date
                $total_qty = $s_p_d_values->total_qty ?? 0;
                $total_qty = round($total_qty / 1000, 2);
                $s_p_d_productData[$product_name] .= $total_qty . ',';
            }
        }

        // Remove trailing commas from the data
        foreach ($s_p_d_productData as $product => $data) {
            $s_p_d_productData[$product] = rtrim($data, ',');
        }

        // Prepare the final array
        $s_p_d_array['label'] = $s_p_d_disnict_prts;
        $s_p_d_array['date'] = $s_p_d_dates_15;
        $s_p_d_array['data'] = $s_p_d_productData;

        // dd($s_p_d_array);


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        DB::statement('
        CREATE OR REPLACE VIEW order_products_dashboard AS
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



        $o_p_d_dates_15 = [];
        $today = Carbon::today();

        for ($i = 0; $i <= 15; $i++) {
            $o_p_d_dates_15[] = $today->copy()->subDays($i)->format('Y-m-d');
        }

        $o_p_d_dates_15 = array_reverse($o_p_d_dates_15);
        // dd($o_p_d_dates_15);

        $o_p_d_disnict_prts = DB::table('order_products_dashboard')
            ->select('product_name_id', 'product_name')
            ->distinct()
            ->get();

        $o_p_d_array = [];
        $o_p_d_productData = [];

        // Loop through the dates (assuming $dates_15 is an array of the last 15 dates)
        foreach ($o_p_d_dates_15 as $o_p_d_o_date) {
            foreach ($o_p_d_disnict_prts as $distinct_product) {
                $product_id = $distinct_product->product_name_id;

                // Get the total sum for each product on the specific date
                $o_p_d_values = DB::table('order_products_dashboard')
                    ->select(DB::raw('SUM(total_sum_qty) as total_qty'))
                    ->where('product_name_id', $product_id)
                    ->where('date', $o_p_d_o_date)
                    ->first();

                // Store the result for each product and date
                $product_name = $distinct_product->product_name;

                // Initialize array key if not set
                if (!isset($o_p_d_productData[$product_name])) {
                    $o_p_d_productData[$product_name] = '';
                }

                // Append total_qty to the product data for that date
                $total_qty = $o_p_d_values->total_qty ?? 0;
                $total_qty = round($total_qty / 1000, 2);
                $o_p_d_productData[$product_name] .= $total_qty . ',';
            }
        }

        // Remove trailing commas from the data
        foreach ($o_p_d_productData as $product => $data) {
            $o_p_d_productData[$product] = rtrim($data, ',');
        }

        // Prepare the final array
        $o_p_d_array['label'] = $o_p_d_disnict_prts;
        $o_p_d_array['date'] = $o_p_d_dates_15;
        $o_p_d_array['data'] = $o_p_d_productData;

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ customers count ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ customers count ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ customers count ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



        // // Get the date 15 days ago
        $dates_15 = Carbon::now()->subDays(14)->startOfDay();

        // // Retrieve the count of customers created each day between 15 days ago and today
        $customerCounts = Customer::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dates_15, $today_date])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // // Generate a list of all dates in the range with counts
        $dateRange = [];
        for ($date = clone $dates_15; $date->lte($today_date); $date->addDay()) {
            $dateRange[$date->format('Y-m-d')] = 0;
        }

        foreach ($customerCounts as $count) {
            $dateRange[$count->date] = $count->count;
        }

        // // Format dates for the chart
        $formattedDates = array_keys($dateRange);
        $customerCountsArray = array_values($dateRange);




        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Latest Reviews ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Latest Reviews ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Latest Reviews ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~






        // $delper = Deliveryperson::where('status', 'Active')->get();
        // $unitz = Unit::where('status', 'Active')->get();
        $delivery_list = Customersubscription::where('addon_status', null)
            ->where('delivery_status', 'Delivered')
            ->orderBy('rating', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

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
        }
        // dd($additional_dates);
        return view('index', [
            'total_customers' => $total_customers,
            'active_subscribers' => $active_subscribers,
            'active_delivery_persons' => $active_delivery_persons,
            'number_of_pincodes' => $number_of_pincodes,
            'number_of_areas' => $number_of_areas,
            'number_of_categories' => $number_of_categories,
            'number_of_products' => $number_of_products,
            'vendor_count' => $vendor_count,
            'buyer_count' => $buyer_count,
            'product_ids' => $product_ids,
            'product_qty' => $product,
            'number_of_outlets' => $number_of_outlets,
            'dates_order' => $dates_order,
            'dates_15' => $dates_15,
            'customerCountsArray' => $customerCountsArray, // Pass customer counts array to the view
            'formattedDates' => $formattedDates,
            'delivery_list' => $delivery_list,

            's_p_d_array' => $s_p_d_array, // ~~~~~~~~~~~~ s_p_d_productData ~~~~~~~~~~~~~~~~
            'o_p_d_array' => $o_p_d_array, // ~~~~~~~~~~~~ o_p_d_productData ~~~~~~~~~~~~~~~~

        ]);
    }
    public function root()
    {

        $total_customers = Customer::where('status', 'Active')->count();
        $active_delivery_persons = Deliveryperson::where('status', 'Active')->count();
        $number_of_pincodes = Pincode::where('status', 'Active')->count();
        $number_of_areas = Area::where('status', 'Y')->count();
        $number_of_outlets = inventerytable::count();
        $active_delivery_persons = Deliveryperson::where('status', 'Active')->count();
        $number_of_categories = Category::where('status', 'Active')->count();
        $vendor_count = vendor::where('status', 'active')->where('type', '1')->count();
        $buyer_count = vendor::where('status', 'active')->where('type', '2')->count();
        $number_of_products = Product::where('status', 'Active')->count();
        $active_subscribers = Customersubscription::where('delivery_status', "Yet to deliver")
            ->distinct('subscription_customer_id')
            ->count();
        // dd($total_customers);

        $dates_15 = [];
        $dates_order = [];
        $today_date = Carbon::today();

        for ($i = 0; $i < 15; $i++) {
            $dates_15[] = $today_date->copy()->subDays($i)->format('Y-m-d');
            $dates_order[] = $today_date->copy()->subDays($i)->format('Y-m-d');
        }
        // dd($dates_15);


        $today_date = date('Y-m-d');
        $dates = $dates_15;


        // Subquery to get the latest date for each subscription product ID
        $latestDatesSubquery = Customersubscription::selectRaw('subscription_products_id, MAX(date) as latest_date')
            ->where('date', '<', $today_date)
            ->groupBy('subscription_products_id');

        $product_ids = Customersubscription::select('customers_subscription.subscription_products_id', 'product_names.name')
            ->join('products', 'customers_subscription.subscription_products_id', '=', 'products.id')
            ->join('product_names', 'products.product_id', '=', 'product_names.id')
            ->joinSub($latestDatesSubquery, 'latest_dates', function ($join) {
                $join->on('customers_subscription.subscription_products_id', '=', 'latest_dates.subscription_products_id')
                    ->on('customers_subscription.date', '=', 'latest_dates.latest_date');
            })
            ->orderBy('latest_dates.latest_date', 'desc')
            ->get();

        foreach ($product_ids as $d) {
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
                ->where('products.id', $d->subscription_products_id)
                ->first();

            $d->pro_name = $pro_details->product_name . '-' . $pro_details->quantity_value;
        }

        // dd($product_ids);

        $product = []; // Initialize the product array
        $pro_names = []; // Initialize the product array
        foreach ($product_ids as $pro) {

            foreach ($dates as $d) {
                $qty_sum = Customersubscription::where('subscription_products_id', $pro->subscription_products_id)
                    ->where('date', $d)
                    ->sum('subscription_quantity');

                // Initialize the product entry if it doesn't exist
                if (!isset($product[$pro->subscription_products_id])) {
                    $product[$pro->subscription_products_id] = [];
                }


                $product[$pro->subscription_products_id][$d] = $qty_sum;
            }
        }


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Create view  subscription_product_dashboard ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Create view  subscription_product_dashboard  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Create view  subscription_product_dashboard  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



        DB::statement('
            CREATE OR REPLACE VIEW subscription_product_dashboard AS
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


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ get orders_product_dashboard data  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



        $s_p_d_dates_15 = [];
        $today = Carbon::today();

        for ($i = 0; $i <= 15; $i++) {
            $s_p_d_dates_15[] = $today->copy()->subDays($i)->format('Y-m-d');
        }

        $s_p_d_dates_15 = array_reverse($s_p_d_dates_15);
        // dd($s_p_d_dates_15);

        $s_p_d_disnict_prts = DB::table('subscription_product_dashboard')
            ->select('product_name_id', 'product_name')
            ->distinct()
            ->get();

        $s_p_d_array = [];
        $s_p_d_productData = [];

        // Loop through the dates (assuming $dates_15 is an array of the last 15 dates)
        foreach ($s_p_d_dates_15 as $s_p_d_o_date) {
            foreach ($s_p_d_disnict_prts as $distinct_product) {
                $product_id = $distinct_product->product_name_id;

                // Get the total sum for each product on the specific date
                $s_p_d_values = DB::table('subscription_product_dashboard')
                    ->select(DB::raw('SUM(total_sum_qty) as total_qty'))
                    ->where('product_name_id', $product_id)
                    ->where('date', $s_p_d_o_date)
                    ->first();

                // Store the result for each product and date
                $product_name = $distinct_product->product_name;

                // Initialize array key if not set
                if (!isset($s_p_d_productData[$product_name])) {
                    $s_p_d_productData[$product_name] = '';
                }

                // Append total_qty to the product data for that date
                $total_qty = $s_p_d_values->total_qty ?? 0;
                $total_qty = round($total_qty / 1000, 2);
                $s_p_d_productData[$product_name] .= $total_qty . ',';
            }
        }

        // Remove trailing commas from the data
        foreach ($s_p_d_productData as $product => $data) {
            $s_p_d_productData[$product] = rtrim($data, ',');
        }

        // Prepare the final array
        $s_p_d_array['label'] = $s_p_d_disnict_prts;
        $s_p_d_array['date'] = $s_p_d_dates_15;
        $s_p_d_array['data'] = $s_p_d_productData;

        // dd($s_p_d_array);


        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ create view for orders  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        DB::statement('
        CREATE OR REPLACE VIEW order_products_dashboard AS
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



        $o_p_d_dates_15 = [];
        $today = Carbon::today();

        for ($i = 0; $i <= 15; $i++) {
            $o_p_d_dates_15[] = $today->copy()->subDays($i)->format('Y-m-d');
        }

        $o_p_d_dates_15 = array_reverse($o_p_d_dates_15);
        // dd($o_p_d_dates_15);

        $o_p_d_disnict_prts = DB::table('order_products_dashboard')
            ->select('product_name_id', 'product_name')
            ->distinct()
            ->get();

        $o_p_d_array = [];
        $o_p_d_productData = [];

        // Loop through the dates (assuming $dates_15 is an array of the last 15 dates)
        foreach ($o_p_d_dates_15 as $o_p_d_o_date) {
            foreach ($o_p_d_disnict_prts as $distinct_product) {
                $product_id = $distinct_product->product_name_id;

                // Get the total sum for each product on the specific date
                $o_p_d_values = DB::table('order_products_dashboard')
                    ->select(DB::raw('SUM(total_sum_qty) as total_qty'))
                    ->where('product_name_id', $product_id)
                    ->where('date', $o_p_d_o_date)
                    ->first();

                // Store the result for each product and date
                $product_name = $distinct_product->product_name;

                // Initialize array key if not set
                if (!isset($o_p_d_productData[$product_name])) {
                    $o_p_d_productData[$product_name] = '';
                }

                // Append total_qty to the product data for that date
                $total_qty = $o_p_d_values->total_qty ?? 0;
                $total_qty = round($total_qty / 1000, 2);
                $o_p_d_productData[$product_name] .= $total_qty . ',';
            }
        }

        // Remove trailing commas from the data
        foreach ($o_p_d_productData as $product => $data) {
            $o_p_d_productData[$product] = rtrim($data, ',');
        }

        // Prepare the final array
        $o_p_d_array['label'] = $o_p_d_disnict_prts;
        $o_p_d_array['date'] = $o_p_d_dates_15;
        $o_p_d_array['data'] = $o_p_d_productData;

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ customers count ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ customers count ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ customers count ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



        // // Get the date 15 days ago
        $dates_15 = Carbon::now()->subDays(14)->startOfDay();

        // // Retrieve the count of customers created each day between 15 days ago and today
        $customerCounts = Customer::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dates_15, $today_date])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // // Generate a list of all dates in the range with counts
        $dateRange = [];
        for ($date = clone $dates_15; $date->lte($today_date); $date->addDay()) {
            $dateRange[$date->format('Y-m-d')] = 0;
        }

        foreach ($customerCounts as $count) {
            $dateRange[$count->date] = $count->count;
        }

        // // Format dates for the chart
        $formattedDates = array_keys($dateRange);
        $customerCountsArray = array_values($dateRange);




        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Latest Reviews ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Latest Reviews ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Latest Reviews ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~






        // $delper = Deliveryperson::where('status', 'Active')->get();
        // $unitz = Unit::where('status', 'Active')->get();
        $delivery_list = Customersubscription::where('addon_status', null)
            ->where('delivery_status', 'Delivered')
            ->orderBy('rating', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

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
        }
        // dd($additional_dates);
        return view('index', [
            'total_customers' => $total_customers,
            'active_subscribers' => $active_subscribers,
            'active_delivery_persons' => $active_delivery_persons,
            'number_of_pincodes' => $number_of_pincodes,
            'number_of_areas' => $number_of_areas,
            'number_of_categories' => $number_of_categories,
            'number_of_products' => $number_of_products,
            'vendor_count' => $vendor_count,
            'buyer_count' => $buyer_count,
            'product_ids' => $product_ids,
            'product_qty' => $product,
            'number_of_outlets' => $number_of_outlets,
            'dates_order' => $dates_order,
            'dates_15' => $dates_15,
            'customerCountsArray' => $customerCountsArray, // Pass customer counts array to the view
            'formattedDates' => $formattedDates,
            'delivery_list' => $delivery_list,

            's_p_d_array' => $s_p_d_array, // ~~~~~~~~~~~~ s_p_d_productData ~~~~~~~~~~~~~~~~
            'o_p_d_array' => $o_p_d_array, // ~~~~~~~~~~~~ o_p_d_productData ~~~~~~~~~~~~~~~~

        ]);
    }



    /*Language Translation*/
    public function myprofile()
    {

        $user_details = User::where('id', auth()->user()->id)->first();
        return view('user.myprofile', compact('user_details'));
    }
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = '/images/' . $avatarName;
        }

        $user->update();
        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => "User Details Updated successfully!"
            ], 200); // Status code here
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json([
                'isSuccess' => true,
                'Message' => "Something went wrong!"
            ], 200); // Status code here
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your Current password does not matches with the password you provided. Please try again."
            ], 200); // Status code
        } else {
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!"
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!"
                ], 200); // Status code here
            }
        }
    }
}
