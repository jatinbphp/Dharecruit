<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Cart;
use App\Models\ContactUs;
use App\Models\ProductImages;
use App\Models\Products;
use App\Models\Orders;
use App\Models\OrderItems;
use App\Models\PickupPoints;
use App\Models\DeliveryCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /*public function __construct(){
        $this->middleware('auth');
    }*/

    public function index()
    {
        $data['products'] = Products::with('ProductImages')->where('status', 'active')->get();
        $data['pickup_points'] = PickupPoints::where('status','active')->get();
        $data['delivery_charges'] = DeliveryCharges::where('status','active')->get();

        $data['cart_products'] = [];
        $data['user'] = [];
        if(Auth::check()){
            $user = Auth::user();
            $data['cart_products'] = Cart::with('Product','Product.ProductImages')->where('user_id',$user->id)->get();

            $data['user'] = $user;

            $data['cart_total'] = Cart::where('user_id', $user->id)->sum('sub_total');

        } else {

            if(!empty(session()->get('cart'))){
               $data['cart_products'] = session()->get('cart');
            }


            $cart_total = 0;
            if(!empty($data['cart_products'])){
                $cart_total = array_sum(array_column($data['cart_products'],'sub_total'));
            }

            $data['cart_total'] = number_format($cart_total,2, '.', '');
        }
        $data['settings'] = Setting::findOrFail(1);
        return view('home',$data);
    }

    public function contact_us()
    {
        return view('contact_us');
    }

    public function storeContactInfo(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $input = $request->all();
        ContactUs::create($input);
        $mail_status = $this->sendContactusMail($input);
        \Session::flash('success', 'Thank you for getting in touch! We will get back in touch with you soon!Have a great day!');
        return redirect()->back();
    }

    public function getProduct(Request $request){
        $product = Products::where('id',$request['id'])->first();
        $data = [];
        if(!empty($product)){
            $image = ProductImages::where('product_id',$product['id'])->first();
            if(!empty($image)){
                $product['image'] = url('storage/'.$image['image']);
            }
            $data['id'] = $product['id'];
            $data['name'] = $product['name'];
            $data['price'] = '<i class="fa-solid fa-peso-sign"></i>'.number_format($product['price'],2, '.', '');
            $data['image'] = $product['image'];
        }
        return $data;
    }

    public function addToCart(Request $request){
        $product = Products::with('ProductImages')->where('id',$request['pId'])->first();
        $input['product_id'] = $product['id'];
        $input['price'] = $product['price'];
        $input['quantity'] = $request['qty'];
        $input['sub_total'] = $product['price'] * $request['qty'];
        $input['product'] = $product;

        if(Auth::check()){
            $user = Auth::user()->id;

            $existCart = Cart::where('user_id',$user)->where('product_id',$request['pId'])->first();
            if(!empty($existCart)){
                $existCart->update($input);
            }else{
                $input['user_id'] = $user;
                Cart::create($input);
            }
            return 1;
        }else{

            //Session::forget('cart');
            $cart = session()->get('cart', []);

            // Add new item to the cart
            $exist = 0;
            if(!empty($cart)){
                foreach ($cart as $key => $value) {
                    if($cart[$key]['product_id']==$input['product_id']){
                        $cart[$key] = $value;
                        $cart[$key]['quantity'] = $cart[$key]['quantity']+$input['quantity'];
                        $cart[$key]['sub_total'] = $cart[$key]['quantity']*$input['price'];
                        $exist = 1;
                    }
                }

                if($exist==0){
                    $cart[] = $input;
                }
            }else{
                $cart[] = $input;
            }

            // Store updated cart data in session
            session(['cart' => $cart]);
            return 1;
        }
    }

    public function updateToCart(Request $request){
        $product = Products::where('id',$request['pId'])->first();
        $input['product_id'] = $product['id'];
        $input['price'] = $product['price'];
        $input['quantity'] = $request['qty'];
        $input['sub_total'] = $product['price'] * $request['qty'];

        if(Auth::check()){
            $user = Auth::user()->id;

            if($request['qty']==0){
                Cart::where('user_id',$user)->where('product_id',$request['pId'])->delete();
            } else {

                $existCart = Cart::where('user_id',$user)->where('product_id',$request['pId'])->first();
                if(!empty($existCart)){
                    $existCart->update($input);
                }
            }
            return 1;
        }else{

            //Session::forget('cart');
            $cart = session()->get('cart', []);

            // Add new item to the cart
            if(!empty($cart)){
                foreach ($cart as $key => $value) {
                    if($cart[$key]['product_id']==$input['product_id']){

                        if($input['quantity']==0){
                            unset($cart[$key]);
                        } else {
                            $cart[$key] = $value;
                            $cart[$key]['quantity'] = $input['quantity'];
                            $cart[$key]['sub_total'] = $cart[$key]['quantity']*$input['price'];
                        }
                    }
                }
            }

            // Store updated cart data in session
            session(['cart' => $cart]);
            return 1;
        }
    }

    public function getCartProducts(Request $request){
        if(Auth::check()){
            $user = Auth::user()->id;
            $data['cart_products'] = Cart::with('Product','Product.ProductImages')->where('user_id',$user)->get();

            $data['cart_total'] = number_format(Cart::where('user_id', $user)->sum('sub_total'),2, '.', '');
            return view('cart',$data);
        }else{

            $data['cart_products'] = session()->get('cart');

            $cart_total = 0;
            if(!empty($data['cart_products'])){
                $cart_total = array_sum(array_column($data['cart_products'],'sub_total'));
            }

            $data['cart_total'] = number_format($cart_total,2, '.', '');
            return view('cart',$data);
        }
    }

    public function getCartTotal()
    {
        if(Auth::check()){
            $user = Auth::user()->id;
            $data['cart_products'] = Cart::with('Product','Product.ProductImages')->where('user_id',$user)->get();

            return number_format(Cart::where('user_id', $user)->sum('sub_total'),2, '.', '');

        }else{

            $data['cart_products'] = session()->get('cart');

            $cart_total = 0;
            if(!empty($data['cart_products'])){
                $cart_total = array_sum(array_column($data['cart_products'],'sub_total'));
            }

            return number_format($cart_total,2, '.', '');
        }
    }

    public function addOrder(Request $request){

        $data['status'] = 0;
        if(Auth::check()) {
            $user = Auth::user()->id;

            $cart_products = Cart::with('Product', 'Product.ProductImages')->where('user_id', $user)->get()->all();

            if (!empty($cart_products)) {

                if ($request['payment_type'] == 'cod' || $request['payment_type'] == 'bank' || $request['payment_type'] == 'gcash') {

                    $input = $request->all();

                    $userDetails = User::with('Orders')->where('id', $user)->first();
                    $totalOrder = count($userDetails['Orders']) > 0 ? count($userDetails['Orders']) + 1 : 1;
                    $input['unique_id'] = strtoupper(substr($userDetails['name'], 0, 3)) . '0' . $totalOrder;

                    if(!empty($request['pickup_type'])){
                        $pickup_points = PickupPoints::where('status','active')->where('id',$request['pickup_type'])->first();
                        $input['pickup_address'] = $pickup_points['address'];
                    }

                    if(!empty($request['delivery_address_id'])){
                        $delivery_charges = DeliveryCharges::where('status','active')->where('id',$request['delivery_address_id'])->first();
                        $input['delivery_address'] = $delivery_charges['city'];
                        $input['delivery_fee'] = $delivery_charges['charge'];
                    }

                    $input['customer_id'] = $user;
                    $input['order_total'] = number_format(Cart::where('user_id', $user)->sum('sub_total'), 2, '.', '');
                    $input['status'] = $request['payment_type'] == 'cod' ? 'pending' : 'bank_manually';
                    $order = Orders::create($input);

                    $orderTotal = 0;
                    $orderItems = [];
                    foreach ($cart_products as $key => $value) {

                        $product = Products::where('id', $value['product_id'])->where('status', 'active')->first();

                        $orderItems['order_id'] = $order['id'];
                        $orderItems['product_id'] = $value['product_id'];
                        $orderItems['sku'] = $product['sku'];
                        $orderItems['name'] = $product['name'];
                        $orderItems['quantity'] = $value['quantity'];
                        $orderItems['price'] = $value['price'];
                        $orderItems['total'] = ($value['price'] * $value['quantity']);

                        $orderTotal = ($orderTotal + ($value['price'] * $value['quantity']));

                        OrderItems::create($orderItems);
                    }

                    /*$order_total['order_total'] = $orderTotal;
                    $orderData = Orders::updateOrCreate(['id' => $order['id']], $order_total);*/

                    $mail_status = $this->sendCustomerMail($order, 'success', []);
                    Cart::where('user_id', $user)->delete();

                    $data['status'] = 2;
                    $data['order_id'] = $order['id'];

                } else {
                    $data['status'] = 3;
                }
            } else {
                $data['status'] = 1;
            }
        } else {

            $cart_products = session()->get('cart');

            if (!empty($cart_products)) {

                if ($request['payment_type'] == 'cod' || $request['payment_type'] == 'bank' || $request['payment_type'] == 'gcash') {

                    $input = $request->all();

                    $totalOrder = Orders::where('customer_id', 0)->count();
                    $uniqueId = $totalOrder+1;
                    $input['unique_id'] = 'GUEST0'.$uniqueId;

                    if(!empty($request['pickup_type'])){
                        $pickup_points = PickupPoints::where('status','active')->where('id',$request['pickup_type'])->first();
                        $input['pickup_address'] = $pickup_points['address'];
                    }

                    if(!empty($request['delivery_address_id'])){
                        $delivery_charges = DeliveryCharges::where('status','active')->where('id',$request['delivery_address_id'])->first();
                        $input['delivery_address'] = $delivery_charges['city'];
                        $input['delivery_fee'] = $delivery_charges['charge'];
                    }

                    $input['customer_id'] = 0;

                    $cart_total = 0;
                    if(!empty($cart_products)){
                        $cart_total = array_sum(array_column($cart_products,'sub_total'));
                    }

                    $input['order_total'] = number_format($cart_total, 2, '.', '');
                    $input['status'] = 'pending';
                    $order = Orders::create($input);

                    $orderTotal = 0;
                    $orderItems = [];
                    foreach ($cart_products as $key => $value) {

                        $product = Products::where('id', $value['product_id'])->where('status', 'active')->first();

                        $orderItems['order_id'] = $order['id'];
                        $orderItems['product_id'] = $value['product_id'];
                        $orderItems['sku'] = $product['sku'];
                        $orderItems['name'] = $product['name'];
                        $orderItems['quantity'] = $value['quantity'];
                        $orderItems['price'] = $value['price'];
                        $orderItems['total'] = ($value['price'] * $value['quantity']);

                        $orderTotal = ($orderTotal + ($value['price'] * $value['quantity']));

                        OrderItems::create($orderItems);
                    }

                    $mail_status = $this->sendCustomerMail($order, 'success', []);
                    Session::forget('cart');

                    $data['status'] = 2;
                    $data['order_id'] = $order['id'];

                } else {
                    $data['status'] = 3;
                }
            } else {
                $data['status'] = 1;
            }
        }
        return $data;
    }

    public function orderPlaced($id){
        $data['order'] = Orders::where('id',$id)->first();

        if(empty($data['order'])){
            return redirect()->route('home');
        } else {
            return view('orderPlaced',$data);
        }
    }

    public function getConfirmOrderSection(Request $request){

        if(!empty($request['delivery_address_id'])){
            $data['delivery_charges'] = DeliveryCharges::where('status','active')->where('id',$request['delivery_address_id'])->first();
        }

        if(!empty($request['pickup_type'])){
            $data['pickup_point'] = PickupPoints::where('status','active')->where('id',$request['pickup_type'])->first();
        }        

        if(Auth::check()){
            $user = Auth::user()->id;
            $data['cart_products'] = Cart::with('Product','Product.ProductImages')->where('user_id',$user)->get()->all();

            $data['cart_total'] = number_format(Cart::where('user_id', $user)->sum('sub_total'),2, '.', '');
            return view('confrimOrder',$data);
        }else{
            $data['cart_products'] = session()->get('cart');

            $cart_total = 0;
            if(!empty($data['cart_products'])){
                $cart_total = array_sum(array_column($data['cart_products'],'sub_total'));
            }

            $data['cart_total'] = number_format($cart_total,2, '.', '');
            return view('confrimOrder',$data);
        }
    }

    public function privacyPolicy(){
        return view('privacyPolicy');
    }

    public function deletionInstruction(){
        return view('deletionInstruction');
    }

    private function sendContactusMail($input){
        $data['data'] = $input;
        \Mail::send('mail_template/contact_us_mail_template',$data, function($message) use($input){
            $message->from('cakshop@ysabelles.ph');
            $message->to($input['email']);
            $message->subject($input['email']);
        });
        return 1;
    }
}
