<?php

namespace App\Http\Controllers;

use App\Model\FactoryModel\Factory;
use App\Model\FactoryModel\FactoryBottleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Model\BasicModel\DistrictData;
use App\Model\BasicModel\CityData;
use App\Model\BasicModel\ProvinceData;

use App\Model\UserModel\Page;
use App\Model\UserModel\User;
use App\Model\SystemModel\SysLog;

use App\Model\ProductModel\Product;
use App\Model\ProductModel\ProductCategory;
use App\Model\ProductModel\ProductPrice;
use App\Model\FactoryModel\FactoryBoxType;

use Auth;

class ProductCtrl extends Controller
{

    public function show_product_list()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;

        $categories = ProductCategory::where('is_deleted', 0)->where('factory_id', $factory_id)->get();

        $products = Product::where('is_deleted', 0)->where('factory_id', $factory_id)->get();

        $child = 'shangpin';
        $parent = 'jichuxinxi';
        $current_page = 'shangpin';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        return view('gongchang.jichuxinxi.shangpin', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function update_product(Request $request)
    {
        if ($request->ajax()) {

            $name = $request->input('name');
            $simple_name = $request->input('simple_name');
            $category = $request->input('category');
            $intro = $request->input('introduction');
            $bottle_type = $request->input('bottle_type');
            $guarantee_period = $request->input('guarantee_period');
            $guarantee_req = $request->input('guarantee_req');
            $material = $request->input('material');
            $production_period = $request->input('production_period');
            $product_basket_spec = $request->input('product_basket_spec');
            $depot_need = $request->input('depot_need');
            $property = $request->input('property');

            $uecontent = $request->input('uecontent');

            $product_id = $request->input('cpid');
            $current_product = Product::find($product_id);
            if (!$current_product)
                return response()->json(['status' => 'fail', 'message' => '当前的产品不存在.']);

            $current_product->name = $name;
            $current_product->simple_name = $simple_name;

            $category_id = ProductCategory::find($category)->id;

            if (!$category_id)
                return response()->json(['status' => 'fail', 'message' => '该类别不存在. 请检查类别列表.']);

            $current_product->category = $category_id;

            $current_product->introduction = $intro;
            $current_product->bottle_type = $bottle_type;
            $current_product->guarantee_period = $guarantee_period;
            $current_product->guarantee_req = $guarantee_req;
            $current_product->material = $material;
            $current_product->production_period = $production_period;
            $current_product->basket_spec = $product_basket_spec;
            $current_product->property = $property;

            if ($depot_need == 'true')
                $current_product->bottle_back_factory = 1;
            else
                $current_product->bottle_back_factory = 0;

            $current_product->uecontent = $uecontent;

            //upload product image
            $dest_dir = public_path() . '/img/product/logo';

            if (!file_exists($dest_dir))
                $result = File::makeDirectory($dest_dir, 0777, true);

            for ($i = 1; $i <= 4; $i++) {
                if ($request->hasFile('file' . $i)) {
                    $file = $request->file('file' . $i);
                    if ($file->isValid()) {
                        $basename = $file->getClientOriginalName();
                        $ext = $file->getClientOriginalExtension();
                        $filename = basename($basename, '.' . $ext);
                        $new_file_name = 'pid_' . $product_id . '_' . 'img_' . $i . '.' . $ext;
                        $file->move($dest_dir, $new_file_name);

                        switch ($i) {
                            case 1:
                                $current_product->photo_url1 = $new_file_name;
                                break;
                            case 2:
                                $current_product->photo_url2 = $new_file_name;
                                break;
                            case 3:
                                $current_product->photo_url3 = $new_file_name;
                                break;
                            case 4:
                                $current_product->photo_url4 = $new_file_name;
                                break;
                            default:
                                $current_product->photo_url1 = $new_file_name;
                                break;
                        }
                    }
                }
            }

            $current_product->save();
            $product_id = $current_product->id;

            if ($product_id) {
                // 添加系统日志
                $this->addSystemLog(User::USER_BACKEND_FACTORY, '商品管理', SysLog::SYSLOG_OPERATION_EDIT);

                return response()->json(['status' => 'success', 'updated_product_id' => $product_id]);
            }
            else {
                return response()->json(['status' => 'fail', 'message' => '虽然节能产品，错误发生. 请稍后再试.']);
            }
        }
    }

    public function update_product_price(Request $request)
    {
        $product_id = $request->input('product_id');
        $price_tp = $request->input('price_template_data');

        //delete previous template
        ProductPrice::where('product_id', $product_id)->delete();

        foreach ($price_tp as $price_one) {
            $template_name = trim($price_one['template_name']);
            $province = trim($price_one['province']);
            $city = trim($price_one['city']);
            $retail = trim($price_one['retail']);
            $month = trim($price_one['month']);
            $season = trim($price_one['season']);
            $half = trim($price_one['half']);
            $settle = trim($price_one['settle']);

            $districts = trim($price_one['district']);
            $districts_array = explode(',', $districts);


            foreach ($districts_array as $i => $district) {
                $districts_array[$i] = $province . " " . $city . " " . $district;
            }
            $districts = implode(',', $districts_array);

            $product_price = new ProductPrice;
            $product_price->template_name = $template_name;
            $product_price->product_id = $product_id;
            $product_price->sales_area = $districts;
            $product_price->retail_price = $retail;
            $product_price->month_price = $month;
            $product_price->season_price = $season;
            $product_price->half_year_price = $half;
            $product_price->settle_price = $settle;
            $product_price->save();
        }
        return response()->json(['status' => 'success']);
    }

    //update product's one template product price
    public function update_product_price_template_one(Request $request)
    {
        if ($request->ajax()) {
            $product_id = $request->input('product_id');

            $template_id = $request->input('template_id');
            $template_name = $request->input('template_name');
            $province = $request->input('province');
            $city = $request->input('city');
            $district = $request->input('district');

            $retail = $request->input('retail');
            $month = $request->input('month');
            $season = $request->input('season');
            $half = $request->input('half');
            $settle = $request->input('settle');

            $districts_array = $request->input('district');
            $districts_array = explode(',', $districts_array);

            foreach ($districts_array as $i => $district) {
                $districts_array[$i] = $province . " " . $city . " " . $district;
            }
            $districts = implode(',', $districts_array);

            $product_price = ProductPrice::find($template_id);

            if (!$product_price) {
                //This is the new inserted from the xiangqing
                //SO, you should create new product price
                $product_price = new ProductPrice;

            }

            $product_price->product_id = $product_id;
            $product_price->template_name = $template_name;
            $product_price->sales_area = $districts;
            $product_price->retail_price = $retail;
            $product_price->month_price = $month;
            $product_price->season_price = $season;
            $product_price->half_year_price = $half;
            $product_price->settle_price = $settle;
            $product_price->save();

            return response()->json(['status' => 'success']);


        }
    }

    public function check_same_category(Request $request)
    {
        if ($request->ajax()) {
            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $cname = $request->input('category_name_to_add');
            if (ProductCategory::where('name', $cname)->where('factory_id', $factory_id)->get()->count() > 0)
                return response()->json(['status' => 'fail']);
            else
                return response()->json(['status' => 'success']);
        }
    }

    public function add_category(Request $request)
    {
        if ($request->ajax()) {
            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $category_name_to_add = $request->input('category_name_to_add');
            if (ProductCategory::where('name', $category_name_to_add)->where('factory_id', $factory_id)->get()->count() > 0)
                return response()->json(['status' => 'fail', 'message' => '同一分类名称存在']);
            $newpc = new ProductCategory;
            $newpc->name = $category_name_to_add;
            $newpc->factory_id = $factory_id;
            $newpc->save();
            $new_category_id = $newpc->id;

            return response()->json(['status' => 'success', 'added_category_id' => $new_category_id]);
        }
    }

    /**
     * 打开添加奶品页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_insert_product()
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $province = $factory->factory_provinces;

        $categories = ProductCategory::where('is_deleted', 0)->where('factory_id', $factory_id)->get();
        $product_basket_specs = FactoryBoxType::where('is_deleted', 0)->where('factory_id', $factory_id)->get();
        $bottle_types = FactoryBottleType::where('is_deleted', 0)->where('factory_id', $factory_id)->get();

        $child = 'shangpin';
        $parent = 'jichuxinxi';
        $current_page = 'naipinluru';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.jichuxinxi.shangpin.naipinluru', [
            // 页面信息
            'pages'                 => $pages,
            'child'                 => $child,
            'parent'                => $parent,
            'current_page'          => $current_page,

            // 数据
            'province'              => $province,
            'categories'            => $categories,
            'product_basket_specs'  => $product_basket_specs,
            'bottle_types'          => $bottle_types
        ]);
    }

    public function insert_product_price_template(Request $request)
    {
        if ($request->ajax()) {
            $product_id = $request->input('product_id');
            $price_tp = $request->input('price_template_data');

            //price template save
            foreach ($price_tp as $price_one) {
                $template_name = $price_one['template_name'];
                $province = $price_one['province'];
                $city = $price_one['city'];
                $retail = $price_one['retail'];
                $month = $price_one['month'];
                $season = $price_one['season'];
                $half = $price_one['half'];
                $settle = $price_one['settle'];

                $districts = $price_one['district'];
                $districts_array = explode(',', $districts);

                foreach ($districts_array as $key => $district_one) {
                    $districts_array[$key] = $province . " " . $city . " " . $district_one;
                }
                $sales_area = implode(',', $districts_array);

                $product_price = new ProductPrice;
                $product_price->template_name = $template_name;
                $product_price->product_id = $product_id;
                $product_price->sales_area = $sales_area;
                $product_price->retail_price = $retail;
                $product_price->month_price = $month;
                $product_price->season_price = $season;
                $product_price->half_year_price = $half;
                $product_price->settle_price = $settle;

                $product_price->save();
            }
            return response()->json(['status' => 'success', 'price_added_product_id' => $product_id]);
        }
    }

    public function get_series_no($factory_id, $category_id)
    {
        //get current factory 's product count of same category
        $products = Product::where('factory_id', $factory_id)->where('category', $category_id)->get();
        $count = count($products) + 1;
        return $factory_id . '0' . $category_id . '0' . $count;
    }

    public function insert_product(Request $request)
    {

        if ($request->ajax()) {
            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $name = $request->input('name');
            $simple_name = $request->input('simple_name');
            $category = $request->input('category');
            $intro = $request->input('introduction');
            $bottle_type = $request->input('bottle_type');
            $guarantee_period = $request->input('guarantee_period');
            $guarantee_req = $request->input('guarantee_req');
            $material = $request->input('material');
            $production_period = $request->input('production_period');
            $product_basket_spec = $request->input('product_basket_spec');
            $depot_need = $request->input('depot_need');
            $property = $request->input('property');

            $uecontent = $request->input('uecontent');

            $new_product = new Product;

            $new_product->name = $name;
            $new_product->simple_name = $simple_name;

            $categoryo = ProductCategory::where('factory_id', $factory_id)->where('id', $category)->get()->first();
            $category_id = $categoryo->id;

            if (!$category_id)
                return response()->json(['status' => 'fail', 'message' => '该类别不存在. 请检查类别列表.']);

            $new_product->category = $category_id;

            $new_product->introduction = $intro;
            $new_product->bottle_type = $bottle_type;
            $new_product->guarantee_period = $guarantee_period;
            $new_product->guarantee_req = $guarantee_req;
            $new_product->material = $material;
            $new_product->production_period = $production_period;
            $new_product->basket_spec = $product_basket_spec;
            $new_product->series_no = $this->get_series_no($factory_id, $category_id);
            $new_product->property = $property;

            $new_product->factory_id = $factory_id;

            if ($depot_need == 'true')
                $new_product->bottle_back_factory = 1;
            else
                $new_product->bottle_back_factory = 0;

            $new_product->uecontent = $uecontent;

            $new_product->save();
            $product_id = $new_product->id;

            //upload product image
            $dest_dir = public_path() . '/img/product/logo';

            if (!file_exists($dest_dir))
                $result = File::makeDirectory($dest_dir, 0777, true);

            $count = 0;
            if ($request->hasFile('file1'))
                $count++;
            if ($request->hasFile('file2'))
                $count++;
            if ($request->hasFile('file3'))
                $count++;
            if ($request->hasFile('file4'))
                $count++;

            for ($i = 1; $i <= $count; $i++) {
                if ($request->hasFile('file' . $i)) {
                    $file = $request->file('file' . $i);
                    if ($file->isValid()) {
                        $basename = $file->getClientOriginalName();
                        $ext = $file->getClientOriginalExtension();
                        $filename = basename($basename, '.' . $ext);
                        $new_file_name = 'pid_' . $product_id . '_' . 'img_' . $i . '.' . $ext;
                        $file->move($dest_dir, $new_file_name);

                        switch ($i) {
                            case 1:
                                $new_product->photo_url1 = $new_file_name;
                                break;
                            case 2:
                                $new_product->photo_url2 = $new_file_name;
                                break;
                            case 3:
                                $new_product->photo_url3 = $new_file_name;
                                break;
                            case 4:
                                $new_product->photo_url4 = $new_file_name;
                                break;
                            default:
                                $new_product->photo_url1 = $new_file_name;
                                break;
                        }

                    }
                }

            }

            $new_product->save();
            $product_id = $new_product->id;

            if ($product_id) {
                // 添加系统日志
                $this->addSystemLog(User::USER_BACKEND_FACTORY, '商品管理', SysLog::SYSLOG_OPERATION_ADD);

                return response()->json(['status' => 'success', 'saved_product_id' => $product_id]);
            }
            else {
                return response()->json(['status' => 'fail', 'message' => '虽然节能产品，错误发生. 请稍后再试.']);
            }
        }
    }

    /**
     * 打开奶品信息页面
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_detail_product($product_id)
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);
        $product = Product::find($product_id);

        $child = 'shangpin';
        $parent = 'jichuxinxi';
        $current_page = 'shangpinxiangqing';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $provinces = $factory->factory_provinces;

        $templates = ProductPrice::where('product_id', $product_id)->get();
        foreach ($templates as $template) {
            $sales_area = $template->sales_area;

            $addresses = explode(',', $sales_area);

            $all_addr = array();
            foreach ($addresses as $address) {

                $addr_parts = explode(' ', $address);

                if (count($addr_parts) < 3)
                    continue;

                $province = $addr_parts[0];
                $city = $addr_parts[1];
                $district = $addr_parts[2];

                if (!isset($all_addr[$province][$city])) {
                    $all_addr[$province][$city] = array();
                }

                array_push($all_addr[$province][$city], $district);
            }

            foreach ($all_addr as $province => $array1) {
                foreach ($array1 as $city => $array2) {
                    $all_addr[$province][$city] = implode(',', $array2);
                }
            }
            $template['sales_area_array'] = $all_addr;
        }

        $categories = ProductCategory::where('is_deleted', 0)->where('factory_id', $factory_id)->get();
        $bottle_types = FactoryBottleType::where('is_deleted', 0)->where('factory_id', $factory_id)->get();
        $product_basket_specs = FactoryBoxType::where('is_deleted', 0)->where('factory_id', $factory_id)->get();

        $dest_dir = url('/img/product/logo/');

        $dest_dir = str_replace('\\', '/', $dest_dir);

        $dest_dir .= '/';

        if ($product->photo_url1)
            $file1_path = $dest_dir . ($product->photo_url1);
        else
            $file1_path = "";

        if ($product->photo_url2)
            $file2_path = $dest_dir . ($product->photo_url2);
        else
            $file2_path = "";

        if ($product->photo_url3)
            $file3_path = $dest_dir . ($product->photo_url3);
        else
            $file3_path = "";

        if ($product->photo_url4)
            $file4_path = $dest_dir . ($product->photo_url4);
        else
            $file4_path = "";

        return view('gongchang.jichuxinxi.shangpin.shangpinxiangqing', [
            // 页面信息
            'pages'                 => $pages,
            'child'                 => $child,
            'parent'                => $parent,
            'current_page'          => $current_page,

            // 数据
            'product'               => $product,
            'price_template'        => $templates,
            'new_count'             => count($templates),
            'provinces'             => $provinces,
            'categories'            => $categories,
            'bottle_types'          => $bottle_types,
            'product_basket_specs'  => $product_basket_specs,
            'file1'                 => $file1_path,
            'file2'                 => $file2_path,
            'file3'                 => $file3_path,
            'file4'                 => $file4_path,
        ]);

    }

    public function get_all_product_names(Request $request)
    {
        if ($request->ajax()) {
            $names = DB::table('products')->pluck('name');

            if ($names)
                return response()->json(['status' => 'success', 'names' => $names]);
            else
                return response()->json(['status' => 'fail']);
        }
    }

    public function delete_product(Request $request)
    {
        if ($request->ajax()) {

            $product_id = $request->input('product_id');

            $product = Product::find($product_id);
            if ($product) {
                $product->is_deleted = 1;
                $product->save();
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'fail', 'message' => '没有产品一样.']);
            }
        }
    }

    public function disable_product(Request $request)
    {
        if ($request->ajax()) {
            $product_id = $request->input('product_id');
            $action = $request->input('action');

            $product = Product::find($product_id);
            if ($product) {
                if ($action == 'disable') {
                    $product->status = 0;
                } else {
                    $product->status = 1;
                }

                $product->save();
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'fail', 'message' => '没有产品一样.']);
            }
        }
    }

    public function update_category(Request $request)
    {
        if ($request->ajax()) {
            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $cats = $request->input('changed_category');
            $count = count($cats);

            foreach ($cats as $cat) {
                $ccid = $cat['ccid'];
                $origin_val = $cat['origin_value'];
                $new_val = $cat['new_value'];

                $ct = ProductCategory::where('id', $ccid)->where('factory_id', $factory_id)->get()->first();
                if ($ct) {
                    $ct->name = $new_val;
                    $ct->factory_id = $factory_id;
                    $ct->save();
                }

            }
            return response()->json(['status' => 'success', 'count' => $count]);
        }
    }
}
