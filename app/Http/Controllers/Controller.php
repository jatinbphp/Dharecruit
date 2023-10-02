<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Orders;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function image($photo, $path){
        $root = storage_path('app/public/uploads/'.$path);
        $name = Str::random(20).".".$photo->getClientOriginalExtension();
        if (!file_exists($root)) {
            mkdir($root, 0777, true);
        }
        $photo->move($root,$name);
        return 'uploads/'.$path."/".$name;
    }

    public function sendCustomerMail($order, $mailType, $user=null){
        $data['order'] = $order;
        $data['orderItems'] = OrderItems::where('order_id', $order->id)->get();
        $data['mailType'] = $mailType;
        $data['user'] = $user;
        $subject = ($mailType == "success") ? "Order Placed Successfully" : "Order Status Updated";

        /*if(!empty($order->customer_email)){
            //Mail Send
            \Mail::send('mail_template/order_mail_template',$data, function($message) use ($order, $subject) {
                $message->from('cakshop@ysabelles.ph');
                $message->to($order->customer_email);
                $message->subject($subject);
            });
        }*/

        return 1;
    }
}
