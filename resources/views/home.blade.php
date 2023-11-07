@extends('layouts.app')
@section('content')
@if(isset($products) && !empty($products))
<section class="popular-items section-padding40" id="ourexclusivecakes">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle text-center mb-60">
                    <h2>Our Exclusive Cakes</h2>
                </div>
            </div>
        </div>
        <div class="popular-active">
            <div class="row justify-content-center" id="errorMsg" style="display:none;">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="section-tittle text-center" id="errorMsgAlert">
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach ($products as $list)
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                        <div class="single-items text-center mb-30">
                            <div class="items-top">
                                @if(isset($list['ProductImages'][0]))
                                    <img src="{{url('storage/'.$list['ProductImages'][0]->image)}}" alt="">
                                @endif
                            </div>
                            <div class="items-bottom">
                                <h4><a href="#">{{$list['name']}} </a></h4>
                                <p>{{$list['description']}}</p>
                                <a href="javascript:void(0)" class="btn order-btn addToCart" data-id="{{$list['id']}}">
                                    ₱ {{$list['price']}} | Order Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<div class="modal fade addToCartModal" id="addToCartModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="productBox">
                    <div class="proImgBox">
                        <img src="" id="proImg">
                    </div>
                    <div class="proCnt">
                        <input type="hidden" name="productId" id="proId">
                        <h3 id="proName"></h3>
                        <h4 id="proPrice"></h4>
                    </div>
                    <div class="qtyBox">
                        <button type="button" class="btn" id="btnMinus"><i class="fa fa-minus"></i></button>
                        <input type="number" name="qty" id="proQty" min="1" step="1" value="1">
                        <button type="button" class="btn" id="btnPlus"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addToCart">Add To Cart</button>
            </div>
        </div>
    </div>
</div>
@endif

<section class="popular-items section-padding40" id="cartMainListDiv" @if(count($cart_products)==0) style="display:none" @endif>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle">
                    <button type="button" class="btn btn-primary w-100" id="previewCart">Preview Cart</button>
                </div>
            </div>
        </div>
    </div>

    <div id="cartListDiv" class="mt-40" style="display: none;">

        @if(isset($cart_products) && count($cart_products)>0)


            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                        <div class="section-tittle mb-60">
                            <h2 style="float: left;">Your Order</h2> <h3 style="color:#000;float: right;">₱ {{number_format($cart_total, 2, '.', '')}}</h3>
                        </div>
                    </div>
                </div>
                <div class="orderProcess">
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                            <div class="item-list">
                                @foreach ($cart_products as $list)
                                    @php
                                        $proImage = '';
                                        if(isset($list['product']['ProductImages'][0])){
                                            $proImage = url('storage/'.$list['product']['ProductImages'][0]['image']);
                                        }
                                    @endphp
                                    <div class="single-items">
                                        <div class="items-left">
                                            <img src="{{$proImage}}" alt="" class="item-img">
                                            <div class="items-cnt">
                                                <h4><a href="#">{{$list['product']['name']}} </a></h4>
                                                <p>₱ {{$list['product']['price']}} X {{$list['quantity']}} = {{number_format($list['sub_total'], 2, '.', '')}}</p>
                                            </div>
                                        </div>
                                        <div class="items-right">
                                            <div class="qtyBox">
                                                <button type="button" class="btn" onclick="updateCart({{$list['product']['id']}},0)" id="btnMinusCart{{$list['product']['id']}}"><i class="fa fa-minus"></i></button>
                                                <input type="number" name="qty" id="proQtyCart{{$list['product']['id']}}" min="1" step="1" value="{{$list['quantity']}}">
                                                <button type="button" class="btn" onclick="updateCart({{$list['product']['id']}},1)" id="btnPlusCart{{$list['product']['id']}}"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" onclick="btnPreviewCart()" style="width: 100%;border-radius: 0;">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<section class="popular-items section-padding40" id="calendarDiv" style="display: none;">
    <div class="container">

        <div class="row justify-content-center" id="errorMsgDate" style="display:none;">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle text-center" id="errorMsgDateAlert">
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>When do you want your order?</h2>
                    <p>Showing next available dates <a style="color:#000;float: right;" href="javascript:void(0)" data-toggle="modal" data-target="#datepickerModal">More dates</a></p>
                </div>
            </div>
        </div>
        <div class="orderProcess" id="timeDiv">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="rescalendar" id="my_calendar_calSize"></div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="form-group">
                        <label>Times shown in Asia/Manila time</label>
                        <select class="order_time" name="order_time" id="order_time" onchange="selectTime(this.value)">
                            <option value="">Pick a time</option>
                            <option value="09:00">09:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="13:00">01:00 PM</option>
                            <option value="14:00">02:00 PM</option>
                            <option value="15:00">03:00 PM</option>
                            <option value="16:00">04:00 PM</option>
                            <option value="17:00">05:00 PM</option>
                            <option value="18:00">06:00 PM</option>
                        </select>
                        <p id="pickATimeError" class="error"></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="datepickerModal" tabindex="-1" role="dialog" aria-labelledby="datepickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div id="datepickerContainer"></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal end -->

<section class="popular-items section-padding40" id="whatsYourName" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>What's your name?</h2>
                </div>
            </div>
        </div>
        <div class="orderProcess">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="form-group">
                        <input type="text" name="whatsYourNameField" id="whatsYourNameField" placeholder="Enter Here" class="form-control" @if(!empty($user->name)) value="{{$user->name}}" @endif>
                        <p id="whatsYourNameFieldError" class="error"></p>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnwhatsYourName">Next</button>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="popular-items section-padding40" id="whatsYourEmail" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>What's your email?</h2>
                    <p>Your order confirmation will be sent here</p>
                </div>
            </div>
        </div>
        <div class="orderProcess">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="form-group">
                        <input type="email" name="whatsYourEmailField" id="whatsYourEmailField" placeholder="coco@gmail.com" class="form-control" @if(!empty($user->email)) value="{{$user->email}}" @endif>
                        <p id="whatsYourEmailFieldError" class="error"></p>
                    </div>
                    <div class="form-group d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" name="">
                        <label class="m-0">I'd like to receive shop offers and updates through email</label>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnwhatsYourEmail">Next</button>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="popular-items section-padding40" id="whatsYourPhone" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>What's your phone number?</h2>
                    <p>No spam. For order purposes only</p>
                </div>
            </div>
        </div>
        <div class="orderProcess">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="form-group">
                        <input type="number" name="whatsYourPhoneField" id="whatsYourPhoneField" placeholder="081234 56789" class="form-control" @if(!empty($user->phone)) value="{{$user->phone}}" @endif>
                        <p id="whatsYourPhoneFieldError" class="error"></p>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnwhatsYourPhone">Next</button>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="popular-items section-padding40" id="whatsYourNotes"style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>Do you want to write a short note? Type below.</h2>
                </div>
            </div>
        </div>
        <div class="orderProcess">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="form-group">
                        <input type="text" name="whatsYourNotesField" id="whatsYourNotesField" placeholder="Enter Here" class="form-control">
                    </div>
                    <button type="button" class="btn btn-primary" id="btnwhatsYourNotes">Skip</button>
                </div>
            </div>
        </div>
    </div>
</section>

@if((!empty($delivery_charges) && ($settings['delivery_method']==1)) || (!empty($pickup_points) && ($settings['pickup_method']==1)))
<section class="popular-items section-padding40" id="deliveryDiv" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>How do you want to take your order?</h2>
                </div>
            </div>
        </div>

        <div class="orderProcess">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="radio-group-list" id="delivery_type_radio">
                        @if(!empty($delivery_charges) && ($settings['delivery_method']==1))
                            <div class="radio-group-item" >
                                <input type="radio" id="take_delivery" name="delivery_type" value="take_delivery">
                                <label for="take_delivery">Delivery</label>
                            </div>
                        @endif
                        @if(!empty($pickup_points) && ($settings['pickup_method']==1))
                        <div class="radio-group-item">
                                <input type="radio" id="take_pickup" name="delivery_type" value="take_pickup">
                                <label for="take_pickup">Pickup</label>
                            </div>
                        @endif
                    </div>

                    <p id="whatsYourDeliveryFieldError" class="error"></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<section class="popular-items section-padding40" id="deliveryAddressDiv" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>Where do you want to delivery?</h2>
                </div>
            </div>
        </div>

        <div class="orderProcess">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="radio-group-list" id="delivery_address_radio">

                        @if(!empty($delivery_charges))
                            @foreach ($delivery_charges as $listc)
                                <div class="radio-group-item" >
                                    <input type="radio" id="city_{{$listc['id']}}" name="city" value="{{$listc['id']}}" data-value="{{$listc['city']}}">
                                    <label for="city_{{$listc['id']}}">
                                        {{$listc['city']}}</br>
                                        <b>Charge :</b> ₱ {{number_format($listc['charge'], 2, '.', '')}}
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <p id="whatsYourPickupFieldError" class="error"></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="popular-items section-padding40" id="pickupDiv" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>Where do you want to pickuo?</h2>
                </div>
            </div>
        </div>

        <div class="orderProcess">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="radio-group-list" id="pickup_type_radio">

                        @if(!empty($pickup_points))
                            @foreach ($pickup_points as $lista)
                                <div class="radio-group-item" >
                                    <input type="radio" id="address_{{$lista['id']}}" name="address" value="{{$lista['id']}}" data-value="{{$lista['address']}}">
                                    <label for="address_{{$lista['id']}}">{{$lista['address']}}</label>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <p id="whatsYourPickupFieldError" class="error"></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="popular-items section-padding40" id="paymentDiv" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle mb-60">
                    <h2>And finally, how would you like to pay?</h2>
                </div>
            </div>
        </div>

        <div class="orderProcess">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                    <div class="radio-group-list" id="payment_type_radio">
                        @if($settings['p_cod']==1)
                            <div class="radio-group-item" >
                                <input type="radio" id="cod" name="payment_type" value="cod">
                                <label for="cod">Cash On Delivery</label>
                            </div>
                        @endif

                        @if($settings['p_gcash']==1)
                            <div class="radio-group-item">
                                <input type="radio" id="gcash" name="payment_type" value="gcash">
                                <label for="gcash">GCash</label>
                            </div>
                        @endif

                        @if($settings['p_bank']==1)
                            <div class="radio-group-item">
                                <input type="radio" id="bank" name="payment_type" value="bank">
                                <label for="bank">Bank To Bank</label>
                            </div>
                        @endif
                    </div>

                    <p id="whatsYourPaymentFieldError" class="error"></p>
                </div>

                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10" id="paymentNoteDiv" style="display: none;">
                    <div class="form-group">
                        <span class="form-control" style="height: auto; display: none;" id="payment_notes" >{!!$settings["gcash_mobile"]!!}</span>
                        <span class="form-control" style="height: auto; display: none;" id="payment_notes1">{!!$settings["gcash_screenshot_mobile"]!!}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="popular-items section-padding40" id="confirmOrderDiv" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="confirmOrder-sec">
                    <div class="section-tittle">
                        <h2>Your order</h2>
                        <a href="javascript:void(0)" class="edit-cart" onclick="editCartFunction()"><i class="fa-solid fa-edit"></i></a>
                    </div>
                    <div id="itemList">
                        @if(isset($cart_products) && !empty($cart_products))
                            <div class="item-list">
                                @foreach ($cart_products as $list)
                                    @php
                                        $proImage = '';
                                        if(isset($list['product']['ProductImages'][0])){
                                            $proImage = url('storage/'.$list['product']['ProductImages'][0]['image']);
                                        }
                                    @endphp
                                    <div class="single-items">
                                        <div class="items-left">
                                            <img src="{{$proImage}}" alt="" class="item-img">
                                            <div class="items-cnt">
                                                <h4><a href="#">{{$list['product']['name']}}</a></h4>
                                                <p>₱ {{number_format($list['price'], 2, '.', '')}} X {{$list['quantity']}}</p>
                                            </div>
                                        </div>
                                        <div class="items-right">
                                            <p class="price">₱ {{number_format($list['sub_total'], 2, '.', '')}}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pickup-txt" id="pickuptxtid">
                                <h6 id="pickuptxtnameType">Pickup - <span>Free</span></h6>
                                <p><span id="pickuptxtname">Quezon City</span> • <span id="selectedTime"></span></p>
                            </div>
                            <div class="total-txt">
                                <h5>Sub Total</h5>
                                <h3>₱ {{number_format($cart_total, 2, '.', '')}}</h3>
                            </div>
                            <div class="total-txt">
                                <h5>Grand Total</h5>
                                <h3>₱ {{number_format($cart_total, 2, '.', '')}}</h3>
                            </div>
                        @endif
                    </div>
                    <div class="instructions-cnt">
                        <h5>Instructions</h5>
                        <p>To avoid delays, schedule your courier in advance via Lalamove.And send us your tracking:</p>
                        <ul>
                            <li><i class="fas fa-clock"></i>Pickup Time Hours: 9am to 6pm only</li>
                            <li><i class="fas fa-exclamation-triangle"></i>Click BAG for cake safety (under ADD-ONS)</li>
                            <li><i class="fas fa-map-pin"></i>Lalamove: 7 Dominador, Quezon City.</li>
                            <li><i class="fas fa-mobile-alt"></i>09060649461</li>
                        </ul>
                        <p>Pls tell your rider to knock on the Green Gate and give your name and code</p>
                    </div>
                    <div class="btn-input">
                        <button type="button" class="btn btn-primary" id="confirmOrder">Confirm Order</button>
                        <div class="form-group d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" name="">
                            <label class="m-0">Save my info for future orders</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="popular-items section-padding40" id="orderPlacedDiv" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10">
                <div class="section-tittle">
                    Order Placed
                </div>
            </div>
        </div>
    </div>
</section>

{!! Form::open(['url' => route('payment.process'), 'id' => 'ordersFormData', 'class' => 'form-horizontal','files'=>true]) !!}
<input type="hidden" name="hidden_order_date" id="hidden_order_date">
<input type="hidden" name="hidden_order_time" id="hidden_order_time">
<input type="hidden" name="hidden_customer_name" id="hidden_customer_name" @if(!empty($user->name)) value="{{$user->name}}" @endif>
<input type="hidden" name="hidden_customer_email" id="hidden_customer_email" @if(!empty($user->email)) value="{{$user->email}}" @endif>
<input type="hidden" name="hidden_customer_phone" id="hidden_customer_phone" @if(!empty($user->phone)) value="{{$user->phone}}" @endif>
<input type="hidden" name="hidden_short_notes" id="hidden_short_notes">
<input type="hidden" name="hidden_payment_type" id="hidden_payment_type">
<input type="hidden" name="hidden_delivery_type" id="hidden_delivery_type">
<input type="hidden" name="hidden_pickup_type" id="hidden_pickup_type">
<input type="hidden" name="hidden_delivery_address_id" id="hidden_delivery_address_id">
{!! Form::close() !!}

@endsection
@section('jQuery')
    <script>
        $(function(){
            $('#my_calendar_calSize').rescalendar({
                id: 'my_calendar_calSize',
                dateFormat: "yy-mm-dd",
                jumpSize: -1,
                calSize: 5,
                dataKeyField: 'name',
                dataKeyValues: ['']
            });
        });

        $('.addToCart').on('click', function(){

            $("#proQty").val(1);
            var pId = $(this).attr('data-id');
            $.ajax({
                url: "{{route('getProduct')}}",
                type: "post",
                data: {'id': pId, '_token' : $('meta[name=_token]').attr('content') },
                success: function(data){
                    $('#proId').val(data.id);
                    $('#proImg').attr('src',data.image);
                    $('#proName').html(data.name);
                    $('#proPrice').html(data.price);
                    $('#proImg').attr('src',data.image);
                    $('#addToCartModal').modal('show');
                }
            });
        });

        $('#btnMinus').on('click', function(){
            var qty = $('#proQty').val();
            if(qty > 1){
                var newQty = parseInt(qty) - 1;
                $('#proQty').val(newQty);
            }
        });

        $('#btnPlus').on('click', function(){
            var qty = $('#proQty').val();
            var newQty = parseInt(qty) + 1;
            $('#proQty').val(newQty);
        });

        $('#addToCart').on('click', function(){
            var pId = $('#proId').val();
            var pQty = $('#proQty').val();
            $.ajax({
                url: "{{route('addToCart')}}",
                type: "post",
                data: {'pId': pId, 'qty': pQty, '_token' : $('meta[name=_token]').attr('content') },
                success: function(data){
                    /*if(data == 0){
                        window.location.href = "{{url('/login')}}";
                    }else{*/
                        $('#addToCartModal').modal('hide');

                        $.ajax({
                            url: "{{route('getCartProducts')}}",
                            type: "post",
                            data: {'_token' : $('meta[name=_token]').attr('content') },
                            success: function(data){
                                //$(data).insertBefore('#calendarDiv');
                                $("#cartListDiv").html(data);
                                $("#cartMainListDiv").css("display", "");

                                swal($('#proName').text(), "Added!", "success");

                                $("html, body").animate({
                                    //scrollTop: $("#calendarDiv").offset().top-200
                                    scrollTop: $("#cartMainListDiv").offset().top-100
                                }, 1000);

                                if($('#cartListDiv').css('display') == 'none'){
                                    $('#previewCart').trigger("click");
                                }

                                $("#errorMsg").css("display", "none");

                                $("#errorMsgAlert").html('');

                                $.ajax({
                                    url: "{{route('getConfirmOrderSection')}}",
                                    type: "post",
                                    data: {'_token' : $('meta[name=_token]').attr('content') },
                                    success: function(data){
                                        $("#itemList").html(data);

                                        var timingVar = $("#hidden_order_date").val()+' '+$("#hidden_order_time").val();

                                        $("#selectedTimeAjax").text(dateFormat(timingVar, "ddd, mmm dS, yyyy, h:MM TT"));
                                    }
                                });

                                selectionCheck(0);
                            }
                        });
                    //}
                }
            });
        });

        // update cart
        function updateCart(productId, type) {
            var proQtyCart = $("#proQtyCart"+productId).val();

            if(type==0){
                $("#proQtyCart"+productId).val(parseInt(proQtyCart)-1);
                var qty = (parseInt(proQtyCart)-1);
            } else if(type==1){
                $("#proQtyCart"+productId).val(parseInt(proQtyCart)+1);
                var qty = (parseInt(proQtyCart)+1);
            }

            $.ajax({
                url: "{{route('updateToCart')}}",
                type: "post",
                data: {'pId': productId, 'qty': qty, '_token' : $('meta[name=_token]').attr('content') },
                success: function(data){
                    if(data == 0){
                        window.location.href = "{{url('/login')}}";
                    }else{

                        $.ajax({
                            url: "{{route('getCartProducts')}}",
                            type: "post",
                            data: {'_token' : $('meta[name=_token]').attr('content') },
                            success: function(data){

                                if(data == 0){

                                    window.location.href = "{{url('/')}}";

                                    /*$("#errorMsg").css("display", "");

                                    $("#errorMsgAlert").html('<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button>Sorry, you do not have any product in the cart. Please add the product to the cart.</div>');


                                    $("html, body").animate({
                                        scrollTop: $("#ourexclusivecakes").offset().top
                                    }, 1000);*/


                                }else{

                                    $.ajax({
                                        url: "{{route('getCartTotal')}}",
                                        type: "post",
                                        data: {'pId': productId, 'qty': qty, '_token' : $('meta[name=_token]').attr('content') },
                                        success: function(dataresponce){
                                            if(dataresponce == 0){
                                                $("#total_amount").text('₱ 0.00');
                                                $("#cartMainListDiv").css("display", "none");
                                            }else{
                                                $("#total_amount").text('₱ '+dataresponce);
                                                $("#cartMainListDiv").css("display", "");
                                            }

                                            $.ajax({
                                                url: "{{route('getConfirmOrderSection')}}",
                                                type: "post",
                                                data: {'_token' : $('meta[name=_token]').attr('content') },
                                                success: function(data){
                                                    $("#itemList").html(data);

                                                    var timingVar = $("#hidden_order_date").val()+' '+$("#hidden_order_time").val();

                                                    $("#selectedTimeAjax").text(dateFormat(timingVar, "ddd, mmm dS, yyyy, h:MM TT"));
                                                }
                                            });
                                        }
                                    });

                                }

                                $("#cartListDiv").html(data);
                            }
                        });
                    }
                }
            });
        }

        // toggle cart
        $(function() {
            $('#previewCart').on('click', function() {
                $('#cartListDiv').slideToggle();
            });
        });

        //datepicker js code
        function initializeDatepicker() {
            $('#datepickerContainer').datepicker({
                format: 'yyyy-mm-dd', // Customize the date format as needed
                autoclose: true,
                startDate: '+1d',
                minDate : 1,
                onSelect: function(dateText, inst) {
                  // Close the datepicker after date selection
                  $(this).datepicker("hide");
                }
            });
        }

        // more dates
        $('#datepickerContainer').on('changeDate', function() {

            $('#my_calendar_calSize').rescalendar({
                id: 'my_calendar_calSize',
                dateFormat: "yy-mm-dd",
                jumpSize: 0,
                calSize: 5,
                dataKeyField: 'name',
                dataKeyValues: ['']
            });

            var customDate = $('#datepickerContainer').datepicker('getFormattedDate');
            $(".refDate").val(customDate);
            $('#datepickerModal').modal('toggle');
            $('.move_to_tomorrow').trigger("click");
            $('.move_to_yesterday').trigger("click");
            $("#hidden_order_date").val(customDate);

            $('.middleDay').addClass('active');
            $("#pickATimeError").text("");

            var element = document.querySelector('.nice-select');
            $('.list').children("li[data-value='']").remove();

            $('.nice-select .current').text('09:00 AM');
            $('.list li:first-child').addClass('selected');
            $('.order_time option[value="09:00"]').attr('selected','selected');
            element.classList.add("open");
            //element.niceSelect('update');
            selectionCheck(0);

            $("html, body").animate({
                scrollTop: $("#timeDiv").offset().top-5
            }, 1000);
        });

        $('#datepickerModal').on('shown.bs.modal', function () {
            initializeDatepicker();
        });

        $('#datepickerModal').on('hidden.bs.modal', function () {
            $('#datepickerContainer').datepicker('destroy');
        });

        $(document).on('click', '.nice-select', function(e){

            var isSelected = 0;
            $('.day_cell').each(function(i, obj) {
                if($(this).hasClass('active')){
                    isSelected = 1;
                    return false;
                }
            });

            if(isSelected == 0){
                var element = document.querySelector('.nice-select');
                element.classList.remove("open");
                $("#pickATimeError").text("Select a date to see which time slots are available");
            }else{
                $("#pickATimeError").text("");
            }
        });

        // date select
        $(document).on("click",".rescalendar_table", function(e){
            $("#hidden_order_date").val($(".refDate").val());
            $("#pickATimeError").text("");

            var element = document.querySelector('.nice-select');
            $('.list').children("li[data-value='']").remove();

            if($(".order_time option:selected").val()==''){
                $('.nice-select .current').text('09:00 AM');
                $('.list li:first-child').addClass('selected');
                $('.order_time option[value="09:00"]').attr('selected','selected');
                element.classList.add("open");

                $("html, body").animate({
                    scrollTop: $("#timeDiv").offset().top-5
                }, 1000);
                element.niceSelect('update');
            } else {
                selectionCheck(0);
            }
        });

        // time select
        function selectTime(order_time){

            if(order_time!=''){

                $("#hidden_order_time").val(order_time);
                selectionCheck(0);

            }
        }

        function selectionCheck(type) {

            $.ajax({
                url: "{{route('getCartProducts')}}",
                type: "post",
                data: {'_token' : $('meta[name=_token]').attr('content') },
                success: function(data){

                    if(data == 0){

                        $("#errorMsg").css("display", "");

                        $("#errorMsgAlert").html('<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button>Sorry, you do not have any product in the cart. Please add the product to the cart.</div>');


                        $("html, body").animate({
                            scrollTop: $("#ourexclusivecakes").offset().top
                        }, 1000);

                    }else{

                        var timingVar = $("#hidden_order_date").val()+' '+$("#hidden_order_time").val();

                        $("#selectedTime").text(dateFormat(timingVar, "ddd, mmm dS, yyyy, h:MM TT"));

                        $("#selectedTimeAjax").text(dateFormat(timingVar, "ddd, mmm dS, yyyy, h:MM TT"));

                        @if(empty($user))
                        //if(type==1){

                            if($("#hidden_order_date").val()==''){

                                $("#errorMsgDate").css("display", "");

                                //$("#errorMsgDateAlert").html('<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button>Please select the date & time,</div>');

                                $("html, body").animate({
                                    scrollTop: $("#calendarDiv").offset().top
                                }, 1000);

                            } else if($("#hidden_order_time").val()==''){

                                $("#errorMsgDate").css("display", "");

                                //$("#errorMsgDateAlert").html('<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button>Please select the date & time,</div>');

                                $("html, body").animate({
                                    scrollTop: $("#timeDiv").offset().top-5
                                }, 1000);

                            } else if($("#whatsYourNameField").val()==''){

                                $("#errorMsgDate").css("display", "none");

                                $("#errorMsgDateAlert").html('');

                                $("#btnwhatsYourName").trigger("click");
                                $("#whatsYourName").css("display", "");

                                $("html, body").animate({
                                    scrollTop: $("#whatsYourName").offset().top-100
                                }, 1000);

                            } /*else if($("#whatsYourEmailField").val()==''){

                                $("#btnwhatsYourEmail").trigger("click");
                                $("#whatsYourEmail").css("display", "");

                                $("html, body").animate({
                                    scrollTop: $("#whatsYourEmail").offset().top-100
                                }, 1000);

                            }*/ else if($("#whatsYourPhoneField").val()==''){

                                $("#btnwhatsYourPhone").trigger("click");
                                $("#whatsYourPhone").css("display", "");

                                $("html, body").animate({
                                    scrollTop: $("#whatsYourPhone").offset().top-100
                                }, 1000);

                            } else if($('input[name="payment_type"]').is(':not(:radio)')){

                                $("#paymentDiv").css("display", "");

                                $("#whatsYourPaymentFieldError").text('Please select payment method.');

                                $("html, body").animate({
                                    scrollTop: $("#paymentDiv").offset().top-100
                                }, 1000);
                            }

                            $("#whatsYourPaymentFieldError").text('');

                        //} else {
                        @else

                            if(($("#hidden_order_date").val()!='') && ($("#hidden_order_time").val()!='')){

                                $("#whatsYourName").css("display", "");
                                $("#whatsYourEmail").css("display", "");
                                $("#whatsYourPhone").css("display", "");
                                $("#whatsYourNotes").css("display", "");

                                @if((!empty($delivery_charges) && ($settings['delivery_method']==1)) || (!empty($pickup_points) && ($settings['pickup_method']==1)))
                                    $("#deliveryDiv").css("display", "");
                                @else
                                    $("#paymentDiv").css("display", "");
                                @endif                                

                                $("html, body").animate({
                                    scrollTop: $("#whatsYourName").offset().top-100
                                }, 1000);

                            } else {

                                $("html, body").animate({
                                    scrollTop: $("#calendarDiv").offset().top
                                }, 1000);
                            }
                        //}
                        @endif
                    }
                }
            });
        }

        $('#btnwhatsYourName').on('click', function(){
            var whatsYourNameField = $('#whatsYourNameField').val();

            if(whatsYourNameField!=''){

                $("#hidden_customer_name").val(whatsYourNameField);
                $("#whatsYourEmail").css("display", "");

                $("html, body").animate({
                    scrollTop: $("#whatsYourEmail").offset().top-100
                }, 1000);

                $("#whatsYourNameFieldError").text('');

            } else {
                $("#whatsYourNameFieldError").text('Please fill in a value.');
            }
        });

        $('#btnwhatsYourEmail').on('click', function(){
            var whatsYourEmailField = $('#whatsYourEmailField').val();

            if(whatsYourEmailField==''){
                $("#hidden_customer_email").val('');
            } else {
                $("#hidden_customer_email").val(whatsYourEmailField);
            }

            $("#whatsYourPhone").css("display", "");

            $("html, body").animate({
                scrollTop: $("#whatsYourPhone").offset().top-100
            }, 1000);

            /*if(whatsYourEmailField!=''){

                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if(!regex.test(whatsYourEmailField)){
                    $("#whatsYourEmailFieldError").text('Please fill in a valid value.');
                } else{

                    $("#hidden_customer_email").val(whatsYourEmailField);
                    $("#whatsYourPhone").css("display", "");

                    $("html, body").animate({
                        scrollTop: $("#whatsYourPhone").offset().top-100
                    }, 1000);

                    $("#whatsYourEmailFieldError").text('');
                }
            } else {
                $("#whatsYourEmailFieldError").text('Please fill in a valid value.');
            }*/
        });

        $('#btnwhatsYourPhone').on('click', function(){
            var whatsYourPhoneField = $('#whatsYourPhoneField').val();

            if(whatsYourPhoneField!=''){

                if(whatsYourPhoneField.length==11){
                    $("#hidden_customer_phone").val(whatsYourPhoneField);
                    $("#whatsYourNotes").css("display", "");

                    $("html, body").animate({
                        scrollTop: $("#whatsYourNotes").offset().top-100
                    }, 1000);

                    $("#whatsYourPhoneFieldError").text('');
                } else {
                    $("#whatsYourPhoneFieldError").text('Please enter valid number.');
                }
            } else {
                $("#whatsYourPhoneFieldError").text('Please fill in a value.');
            }
        });

        $('#whatsYourNameField').keyup(function() {
            var whatsYourNameField = $('#whatsYourNameField').val();

            if(whatsYourNameField==''){
                $("#hidden_customer_name").val('');
            } else {
                $("#hidden_customer_name").val(whatsYourNameField);
            }
        });

        $('#whatsYourEmailField').keyup(function() {
            var whatsYourEmailField = $('#whatsYourEmailField').val();

            if(whatsYourEmailField==''){
                $("#hidden_customer_email").val('');
            } else {
                $("#hidden_customer_email").val(whatsYourEmailField);
            }
        });

        $('#whatsYourPhoneField').keyup(function() {
            var whatsYourPhoneField = $('#whatsYourPhoneField').val();

            if(whatsYourPhoneField==''){
                $("#hidden_customer_phone").val('');
            } else {
                $("#hidden_customer_phone").val(whatsYourPhoneField);
            }
        });

        $('#whatsYourNotesField').keyup(function() {
            if ($('#whatsYourNotesField').val() != '') {
                $("#btnwhatsYourNotes").text('Next');
                $("#hidden_short_notes").val($('#whatsYourNotesField').val());
            } else {
                $("#btnwhatsYourNotes").text('Skip');
            }
        });

        $('#btnwhatsYourNotes').on('click', function(){
            var whatsYourNotesField = $('#whatsYourNotesField').val();

            if(whatsYourNotesField!=''){
                $("#hidden_short_notes").val(whatsYourNotesField);
            }

            @if((!empty($delivery_charges) && ($settings['delivery_method']==1)) || (!empty($pickup_points) && ($settings['pickup_method']==1)))
                $("#deliveryDiv").css("display", "");

                $("html, body").animate({
                    scrollTop: $("#deliveryDiv").offset().top-100
                }, 1000);
            @else
                $("#paymentDiv").css("display", "");

                $("html, body").animate({
                    scrollTop: $("#paymentDiv").offset().top-100
                }, 1000);
            @endif
        });

        /*$('#btnwhatsYourNotes').on('click', function(){
            var whatsYourNotesField = $('#whatsYourNotesField').val();

            if(whatsYourNotesField!=''){
                $("#hidden_short_notes").val(whatsYourNotesField);
            }

            $("#paymentDiv").css("display", "");

            $("html, body").animate({
                scrollTop: $("#paymentDiv").offset().top-100
            }, 1000);
        });*/

        $('#delivery_type_radio input:radio').click(function() {
            $("#hidden_delivery_type").val($(this).val());

            $("#confirmOrderDiv").css("display", 'none');
            $("#payment_type_radio input:radio").prop('checked', false);

            if($(this).val()=='take_pickup'){

                $("#delivery_address_radio input:radio").prop('checked', false);

                $("#paymentDiv").css("display", "none");
                $("#deliveryAddressDiv").css("display", "none");
                $("#pickupDiv").css("display", "");

                $("html, body").animate({
                    scrollTop: $("#pickupDiv").offset().top-100
                }, 1000);

            } else if($(this).val()=='take_delivery'){

                $("#pickup_type_radio input:radio").prop('checked', false);

                $("#paymentDiv").css("display", "none");
                $("#deliveryAddressDiv").css("display", "");
                $("#pickupDiv").css("display", "none");

                $("html, body").animate({
                    scrollTop: $("#deliveryAddressDiv").offset().top-100
                }, 1000);

                
            }
        });

        $('#delivery_address_radio input:radio').click(function() {
            $("#hidden_delivery_address_id").val($(this).val());

            $("#pickuptxtnameType").html('Delivery');
            $("#pickuptxtnameTypeAjax").html('Delivery');
            
            $("#pickuptxtname").text($(this).attr('data-value'));
            $("#pickuptxtnameAjax").text($(this).attr('data-value'));

            $("#paymentDiv").css("display", "");

            $("html, body").animate({
                scrollTop: $("#paymentDiv").offset().top-100
            }, 1000);
        });

        $('#pickup_type_radio input:radio').click(function() {
            $("#hidden_pickup_type").val($(this).val());

            $("#pickuptxtnameType").html('Pickup - <span>Free</span>');
            $("#pickuptxtnameTypeAjax").html('Pickup - <span>Free</span>');
            
            $("#pickuptxtname").text($(this).attr('data-value'));
            $("#pickuptxtnameAjax").text($(this).attr('data-value'));

            $("#paymentDiv").css("display", "");

            $("html, body").animate({
                scrollTop: $("#paymentDiv").offset().top-100
            }, 1000);
        });

        $('#payment_type_radio input:radio').click(function() {
            $("#hidden_payment_type").val($(this).val());

            var delivery_address_id = $('#hidden_delivery_address_id').val();
            var pickup_type = $('#hidden_pickup_type').val();

            $.ajax({
                url: "{{route('getConfirmOrderSection')}}",
                type: "post",
                data: {'_token' : $('meta[name=_token]').attr('content'), 'delivery_address_id': delivery_address_id, 'pickup_type': pickup_type },
                success: function(data){
                    $("#itemList").html(data);

                    var timingVar = $("#hidden_order_date").val()+' '+$("#hidden_order_time").val();

                    $("#selectedTimeAjax").text(dateFormat(timingVar, "ddd, mmm dS, yyyy, h:MM TT"));
                }
            });

            $("#confirmOrderDiv").css("display", "");

            if($(this).val()=='gcash'){
                $("#payment_notes1").css("display", 'none');
                $("#payment_notes").css("display", "");
                $("#paymentNoteDiv").css("display", "");

                $("html, body").animate({
                    scrollTop: $("#paymentNoteDiv").offset().top-100
                }, 1000);

            } else if($(this).val()=='bank'){
                $("#payment_notes").css("display", 'none');
                $("#payment_notes1").css("display", "");
                $("#paymentNoteDiv").css("display", "");

                $("html, body").animate({
                    scrollTop: $("#paymentNoteDiv").offset().top-100
                }, 1000);
            } else {
                $("#payment_notes").css("display", 'none');
                $("#payment_notes1").css("display", 'none');
                $("#paymentNoteDiv").css("display", "none");

                $("html, body").animate({
                    scrollTop: $("#confirmOrderDiv").offset().top-100
                }, 1000);
            }
        });
            
        function btnPreviewCart() {
            $("#calendarDiv").css("display", "");

            $("html, body").animate({
                scrollTop: $("#calendarDiv").offset().top
            }, 1000);
        }

        $('#confirmOrder').on('click', function(){
            var order_date = $('#hidden_order_date').val();
            var order_time = $('#hidden_order_time').val();
            var customer_name = $('#hidden_customer_name').val();
            var customer_email = $('#hidden_customer_email').val();
            var customer_phone = $('#hidden_customer_phone').val();
            var short_notes = $('#hidden_short_notes').val();
            var payment_type = $('#hidden_payment_type').val();
            var delivery_type = $('#hidden_delivery_type').val();
            var pickup_type = $('#hidden_pickup_type').val();
            var delivery_address_id = $('#hidden_delivery_address_id').val();

            if(customer_phone.length!=11){

                $("html, body").animate({
                    scrollTop: $("#whatsYourPhone").offset().top-100
                }, 1000);

                $("#whatsYourPhoneFieldError").text('Please enter valid number.');
                return false;
            }

            if(order_date=='' || order_time=='' || customer_name=='' || customer_phone=='' || payment_type=='' || delivery_type==''){
                selectionCheck(1);
            }  else {

                /*if(payment_type == 'gcash'){
                    processPayPalPayment();
                } else {*/
                    $.ajax({
                        url: "{{route('addOrder')}}",
                        type: "post",
                        data: {'order_date': order_date, 'order_time': order_time, 'customer_name': customer_name, 'customer_email': customer_email, 'customer_phone': customer_phone, 'short_notes': short_notes, 'payment_type': payment_type, 'delivery_type': delivery_type, 'pickup_type': pickup_type, 'delivery_address_id': delivery_address_id, '_token' : $('meta[name=_token]').attr('content') },
                        success: function(data){
                            if(data.status == 0){
                                window.location.href = "{{url('/login')}}";
                            }else if(data.status == 1){
                                $("#errorMsg").css("display", "");

                                $("#errorMsgAlert").html('<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button>Sorry, you do not have any product in the cart. Please add the product to the cart.</div>');


                                $("html, body").animate({
                                    scrollTop: $("#ourexclusivecakes").offset().top
                                }, 1000);
                            } else if(data.status == 2){
                                window.location.href = "{{ url('orderPlaced')}}/"+data.order_id;


                                /*$("#ourexclusivecakes").css("display", "none");
                                $("#cartMainListDiv").css("display", "none");
                                $("#calendarDiv").css("display", "none");
                                $("#whatsYourName").css("display", "none");
                                $("#whatsYourEmail").css("display", "none");
                                $("#whatsYourPhone").css("display", "none");
                                $("#whatsYourNotes").css("display", "none");
                                $("#paymentDiv").css("display", "none");
                                $("#confirmOrderDiv").css("display", "none");

                                $("#orderPlacedDiv").css("display", "");

                                $("html, body").animate({
                                    scrollTop: $("#orderPlacedDiv").offset().top
                                }, 1000);*/
                            } else if(data.status == 3){
                                $("#paymentDiv").css("display", "");

                                $("#whatsYourPaymentFieldError").text('for now, Only GCash payment method implemented.');

                                $("html, body").animate({
                                    scrollTop: $("#paymentDiv").offset().top-100
                                }, 1000);
                            }
                        }
                    });
                //}
            }
        });

        function processPayPalPayment(){
            $("#ordersFormData").submit();
        }

        var dateFormat = function () {
            var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
                timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
                timezoneClip = /[^-+\dA-Z]/g,
                pad = function (val, len) {
                    val = String(val);
                    len = len || 2;
                    while (val.length < len) val = "0" + val;
                    return val;
                };

            // Regexes and supporting functions are cached through closure
            return function (date, mask, utc) {
                var dF = dateFormat;

                // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
                if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
                    mask = date;
                    date = undefined;
                }

                // Passing date through Date applies Date.parse, if necessary
                date = date ? new Date(date) : new Date;
                if (isNaN(date)) throw SyntaxError("invalid date");

                mask = String(dF.masks[mask] || mask || dF.masks["default"]);

                // Allow setting the utc argument via the mask
                if (mask.slice(0, 4) == "UTC:") {
                    mask = mask.slice(4);
                    utc = true;
                }

                var _ = utc ? "getUTC" : "get",
                    d = date[_ + "Date"](),
                    D = date[_ + "Day"](),
                    m = date[_ + "Month"](),
                    y = date[_ + "FullYear"](),
                    H = date[_ + "Hours"](),
                    M = date[_ + "Minutes"](),
                    s = date[_ + "Seconds"](),
                    L = date[_ + "Milliseconds"](),
                    o = utc ? 0 : date.getTimezoneOffset(),
                    flags = {
                        d:    d,
                        dd:   pad(d),
                        ddd:  dF.i18n.dayNames[D],
                        dddd: dF.i18n.dayNames[D + 7],
                        m:    m + 1,
                        mm:   pad(m + 1),
                        mmm:  dF.i18n.monthNames[m],
                        mmmm: dF.i18n.monthNames[m + 12],
                        yy:   String(y).slice(2),
                        yyyy: y,
                        h:    H % 12 || 12,
                        hh:   pad(H % 12 || 12),
                        H:    H,
                        HH:   pad(H),
                        M:    M,
                        MM:   pad(M),
                        s:    s,
                        ss:   pad(s),
                        l:    pad(L, 3),
                        L:    pad(L > 99 ? Math.round(L / 10) : L),
                        t:    H < 12 ? "a"  : "p",
                        tt:   H < 12 ? "am" : "pm",
                        T:    H < 12 ? "A"  : "P",
                        TT:   H < 12 ? "AM" : "PM",
                        Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                        o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                        S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
                    };

                return mask.replace(token, function ($0) {
                    return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
                });
            };
        }();

        // Some common format strings
        dateFormat.masks = {
            "default":      "ddd mmm dd yyyy HH:MM:ss",
            shortDate:      "m/d/yy",
            mediumDate:     "mmm d, yyyy",
            longDate:       "mmmm d, yyyy",
            fullDate:       "dddd, mmmm d, yyyy",
            shortTime:      "h:MM TT",
            mediumTime:     "h:MM:ss TT",
            longTime:       "h:MM:ss TT Z",
            isoDate:        "yyyy-mm-dd",
            isoTime:        "HH:MM:ss",
            isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
            isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
        };

        // Internationalization strings
        dateFormat.i18n = {
            dayNames: [
                "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
                "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
            ],
            monthNames: [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
                "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
            ]
        };

        // For convenience...
        Date.prototype.format = function (mask, utc) {
            return dateFormat(this, mask, utc);
        };

        function editCartFunction() {
            $('#previewCart').trigger("click");
            $("#cartMainListDiv").css("display", "");
            $("html, body").animate({
                scrollTop: $("#cartMainListDiv").offset().top-200
            }, 1000);
        }
    </script>
@endsection
