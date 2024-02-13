<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Model\FlashDealProduct;
use App\Model\Product;
use App\Model\Review;
use App\Model\Tag;
use App\Model\Translation;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct(
        private BusinessSetting $business_setting,
        private Category $category,
        private Product $product,
        private Review $review,
        private Tag $tag,
        private Translation $translation
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function variant_combination(Request $request): \Illuminate\Http\JsonResponse
    {
        $options = [];
        $price = $request->price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        $combinations = $result;
        return response()->json([
            'view' => view('admin-views.product.partials._variant-combinations', compact('combinations', 'price', 'product_name'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_categories(Request $request): \Illuminate\Http\JsonResponse
    {
        $cat = $this->category->where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'options' => $res,
        ]);
    }

    /**
     * @return Factory|View|Application
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $categories = $this->category->where(['position' => 0])->get();
        return view('admin-views.product.index', compact('categories'));
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function list(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->product->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $query_param = ['search' => $request['search']];
        }else{
            $query = $this->product->latest();
        }
        $products = $query->with('order_details.order')->paginate(Helpers::getPagination())->appends($query_param);

        foreach ($products as $product) {
            $total_sold = 0;
            foreach ($product->order_details as $detail) {
                if ($detail->order->order_status == 'delivered'){
                    $total_sold += $detail->quantity;
                }
            }
             $product->total_sold = $total_sold;
        }

        return view('admin-views.product.list', compact('products','search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $key = explode(' ', $request['search']);
        $products = $this->product->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.product.partials._table', compact('products'))->render(),
        ]);
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function view($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $product = $this->product->where(['id' => $id])->first();
        $reviews = $this->review->where(['product_id' => $id])->latest()->paginate(20);
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products',
            'category_id' => 'required',
            'images' => 'required',
            'total_stock' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => translate('Product name is required!'),
            'name.unique' => translate('Product name must be unique!'),
            'category_id.required' => translate('category  is required!'),
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', 'Discount can not be more or equal to the price!');
        }

        $img_names = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image_data = Helpers::upload('product/', 'png', $img);
                $img_names[] = $image_data;
            }
            $image_data = json_encode($img_names);
        } else {
            $image_data = json_encode([]);
        }

        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if(isset($tags)){
            foreach ($tags as $key => $value) {
                $tag = $this->tag->firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                $tag_ids[] = $tag->id;
            }
        }

        $p = $this->product;
        $p->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $p->category_ids = json_encode($category);
        $p->description = $request->description[array_search('en', $request->lang)];

        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choice_options[] = $item;
            }
        }

        $p->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);

        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);

                if ($request['discount_type'] == 'amount' && $item['price'] <= $request['discount'] ){
                    $validator->getMessageBag()->add('discount_mismatch', 'Discount can not be more or equal to the price. Please change variant '. $item['type'] .' price or change discount amount!');
                }

                $variations[] = $item;
                $stock_count += $item['stock'];
            }
        } else {
            $stock_count = (integer)$request['total_stock'];
        }

        if ((integer)$request['total_stock'] != $stock_count) {
            $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $p->variations = json_encode($variations);
        $p->price = $request->price;
        $p->unit = $request->unit;
        $p->image = $image_data;
        $p->capacity = $request->capacity;
        $p->maximum_order_quantity = $request->maximum_order_quantity;
        // $p->set_menu = $request->item_type;

        $p->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $p->tax_type = $request->tax_type;

        $p->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $p->discount_type = $request->discount_type;
        $p->total_stock = $request->total_stock;

        $p->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $p->status = $request->status? $request->status:0;

        $p->save();

        $p->tags()->sync($tag_ids);

        $data = [];
        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                $data[] = array(
                    'translationable_type' => 'App\Model\Product',
                    'translationable_id' => $p->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
            if($request->description[$index] && $key != 'en')
            {
                $data[] = array(
                    'translationable_type' => 'App\Model\Product',
                    'translationable_id' => $p->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                );
            }
        }


        $this->translation->insert($data);

        return response()->json([], 200);
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $product = $this->product->withoutGlobalScopes()->with('translations')->find($id);
        $product_category = json_decode($product->category_ids);
        $categories = $this->category->where(['parent_id' => 0])->get();
        return view('admin-views.product.edit', compact('product', 'product_category', 'categories'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->status = $request->status;
        $product->save();
        Toastr::success(translate('Product status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function feature(Request $request): \Illuminate\Http\RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->is_featured = $request->is_featured;
        $product->save();
        Toastr::success(translate('product feature status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function daily_needs(Request $request): \Illuminate\Http\JsonResponse
    {
        $product = $this->product->find($request->id);
        $product->daily_needs = $request->status;
        $product->save();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name,'.$request->id,
            'category_id' => 'required',
            'total_stock' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Product name is required!',
            'category_id.required' => 'category  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', 'Discount can not be more or equal to the price!');
        }

        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if(isset($tags)){
            foreach ($tags as $key => $value) {
                $tag = $this->tag->firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                $tag_ids[] = $tag->id;
            }
        }

        $p = $this->product->find($id);

        $images = json_decode($p->image);
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image_data = Helpers::upload('product/', 'png', $img);
                $images[] = $image_data;
            }

        }

        if (!count($images)) {
            $validator->getMessageBag()->add('images', 'Image can not be empty!');
        }

        $p->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $p->category_ids = json_encode($category);
        $p->description = $request->description[array_search('en', $request->lang)];

        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choice_options[] = $item;
            }
        }
        $p->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);

                if ($request['discount_type'] == 'amount' && $item['price'] <= $request['discount'] ){
                    $validator->getMessageBag()->add('discount_mismatch', 'Discount can not be more or equal to the price. Please change variant '. $item['type'] .' price or change discount amount!');
                }

                $variations[] = $item;
                $stock_count += $item['stock'];
            }
        } else {
            $stock_count = (integer)$request['total_stock'];
        }

        if ((integer)$request['total_stock'] != $stock_count) {
            $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $p->variations = json_encode($variations);
        $p->price = $request->price;
        $p->capacity = $request->capacity;
        $p->unit = $request->unit;
        $p->maximum_order_quantity = $request->maximum_order_quantity;

        // $p->image = json_encode(array_merge(json_decode($p['image'], true), json_decode($image_data, true)));
        // $p->set_menu = $request->item_type;
        $p->image = json_encode($images);
        // $p->available_time_starts = $request->available_time_starts;
        // $p->available_time_ends = $request->available_time_ends;

        $p->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $p->tax_type = $request->tax_type;

        $p->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $p->discount_type = $request->discount_type;
        $p->total_stock = $request->total_stock;

        $p->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $p->status = $request->status? $request->status:0;

        $p->save();

        $p->tags()->sync($tag_ids);

        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                $this->translation->updateOrInsert(
                    ['translationable_type'  => 'App\Model\Product',
                        'translationable_id'    => $p->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
            if($request->description[$index] && $key != 'en')
            {
                $this->translation->updateOrInsert(
                    ['translationable_type'  => 'App\Model\Product',
                        'translationable_id'    => $p->id,
                        'locale'                => $key,
                        'key'                   => 'description'],
                    ['value'                 => $request->description[$index]]
                );
            }
        }

        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $product = $this->product->find($request->id);
        foreach (json_decode($product['image'], true) as $img) {
            if (Storage::disk('public')->exists('product/' . $img)) {
                Storage::disk('public')->delete('product/' . $img);
            }
        }

        $flash_deal_products = FlashDealProduct::where('product_id', $product->id)->get();
        foreach ($flash_deal_products as $flash_deal_product) {
            $flash_deal_product->delete();
        }
        $product->delete();
        Toastr::success(translate('Product removed!'));
        return back();
    }

    /**
     * @param $id
     * @param $name
     * @return RedirectResponse
     */
    public function remove_image($id, $name): \Illuminate\Http\RedirectResponse
    {
        if (Storage::disk('public')->exists('product/' . $name)) {
            Storage::disk('public')->delete('product/' . $name);
        }

        $product = $this->product->find($id);
        $img_arr = [];
        // if (count(json_decode($product['images'])) < 2) {
        //     Toastr::warning('You cannot delete all images!');
        //     return back();
        // }

        foreach (json_decode($product['image'], true) as $img) {
            if (strcmp($img, $name) != 0) {
                $img_arr[] = $img;
            }
        }

        $this->product->where(['id' => $id])->update([
            'image' => json_encode($img_arr),
        ]);
        Toastr::success(translate('Image removed successfully!'));
        return back();
    }

    /**
     * @return Factory|View|Application
     */
    public function bulk_import_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin-views.product.bulk-import');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulk_import_data(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('You have uploaded a wrong format file, please upload the right file.'));
            return back();
        }
        $col_key = ['name','description','price','tax','category_id','sub_category_id','discount','discount_type','tax_type','unit','total_stock','capacity','daily_needs'];
        foreach ($collections as $key => $collection) {

            foreach ($collection as $key => $value) {
                if ($key!="" && !in_array($key, $col_key)) {
                    Toastr::error('Please upload the correct format file.');
                    return back();
                }
            }
        }

        $data = [];
        foreach ($collections as $collection) {

            $data[] = [
                'name' => $collection['name'],
                'description' => $collection['description'],
                'image' => json_encode(['def.png']),
                'price' => $collection['price'],
                'variations' => json_encode([]),
                'tax' => $collection['tax'],
                'status' => 1,
                'attributes' => json_encode([]),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 0], ['id' => (string)$collection['sub_category_id'], 'position' => 1]]),
                'choice_options' => json_encode([]),
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'tax_type' => $collection['tax_type'],
                'unit' => $collection['unit'],
                'total_stock' => $collection['total_stock'],
                'capacity' => $collection['capacity'],
                'daily_needs' => $collection['daily_needs'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('products')->insert($data);
        Toastr::success(count($data) . (translate(' - Products imported successfully!')));
        return back();
    }

    /**
     * @return Factory|View|Application
     */
    public function bulk_export_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin-views.product.bulk-export-index');
    }

    /**
     * @param Request $request
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function bulk_export_data(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $start_date = $request->type == 'date_wise' ? $request['start_date'] : null;
        $end_date = $request->type == 'date_wise' ? $request['end_date'] : null;

        //dd($start_date, $end_date);

        $products = $this->product->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
            return $query->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date);
            })
            ->get();

        $storage = [];
        foreach($products as $item){
            $category_id = 0;
            $sub_category_id = 0;

            foreach(json_decode($item->category_ids, true) as $category)
            {
                if($category['position']==1)
                {
                    $category_id = $category['id'];
                }
                else if($category['position']==2)
                {
                    $sub_category_id = $category['id'];
                }
            }

            if (!isset($item['description'])) {
                $item['description'] = 'No description available';
            }

            if (!isset($item['capacity'])) {
                $item['capacity'] = 0;
            }

            $storage[] = [
                'name' => $item['name'],
                'description' => $item['description'],
                'price' => $item['price'],
                'tax' => $item['tax'],
                'category_id'=>$category_id,
                'sub_category_id'=>$sub_category_id,
                'discount'=>$item['discount'],
                'discount_type'=>$item['discount_type'],
                'tax_type'=>$item['tax_type'],
                'unit'=>$item['unit'],
                'total_stock'=>$item['total_stock'],
                'capacity'=>$item['capacity'],
                'daily_needs'=>$item['daily_needs'],
            ];

        }
        return (new FastExcel($storage))->download('products.xlsx');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function limited_stock(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $stock_limit = $this->business_setting->where('key','minimum_stock_limit')->first()->value;
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->product->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->where('total_stock', '<', $stock_limit)->latest();
            $query_param = ['search' => $request['search']];
        }else{
            $query = $this->product->where('total_stock', '<', $stock_limit)->latest();
        }

        $products = $query->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.product.limited-stock', compact('products', 'search', 'stock_limit'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_variations(Request $request): \Illuminate\Http\JsonResponse
    {
        $product = $this->product->find($request['id']);
        return response()->json([
            'view' => view('admin-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_quantity(Request $request): \Illuminate\Http\RedirectResponse
    {
        $variations = [];
        $stock_count = $request['total_stock'];
        $product_price = $request['product_price'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = (abs($request['price_' . str_replace('.', '_', $str)]));
                $item['stock'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                $variations[] = $item;
            }
        }

        $product = $this->product->find($request['product_id']);

        if ($stock_count >= 0) {
            $product->total_stock = $stock_count;
            $product->variations = json_encode($variations);
            $product->save();
            Toastr::success(translate('product_quantity_updated_successfully!'));
        } else {
            Toastr::warning(translate('product_quantity_can_not_be_less_than_0_!'));
        }
        return back();
    }

}
