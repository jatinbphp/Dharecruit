{!! Form::hidden('redirects_to', URL::previous()) !!}

<div class="row">
    <div class="col-md-12">
        <div class="form-group{{ $errors->has('orderType') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="orderType">Order Type :<span class="text-red">*</span></label>
            <div class="col-md-12">
                @foreach (\App\Models\Orders::$orderType as $key1 => $value1)
                    @php
                        $checked = '';
                        if(!isset($order) && $key1 == 1){
                            $checked = 'checked';
                        }else{
                            if(isset($order)){
                                if($order['customer_id'] > 0 && $key1 == 1){
                                    $checked = 'checked';
                                }
                                if($order['customer_id'] == 0 && $key1 == 0){
                                    $checked = 'checked';
                                }
                            }
                        }
                    @endphp
                    <span class="mr-2">
                        {!! Form::radio('orderType', $key1, null, ['id' =>'type'.$key1, 'class' => 'customRadio', $checked]) !!}
                        <label for="type{{$key1}}">{{$value1}}</label>
                    </span>
                @endforeach
                @if ($errors->has('orderType'))
                    <span class="text-danger" id="orderType">
                        <strong>{{ $errors->first('orderType') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6" id="existingCustomer" @if(isset($order) && $order['customer_id'] == 0)) style="display: none" @endif>
        <div class="form-group{{ $errors->has('customer_id') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Customer :<span class="text-red">*</span></label>
            {!! Form::select('customer_id', $users, null, ['class' => 'form-control select2']) !!}
            @if ($errors->has('customer_id'))
                <span class="text-danger">
                    <strong>{{ $errors->first('customer_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6" id="guestCustomer" @if(isset($order) && $order['customer_id'] == 0)) style="display: block" @else style="display: none" @endif>
        <div class="form-group{{ $errors->has('customer_name') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Customer :<span class="text-red">*</span></label>
            {!! Form::text('customer_name', null, ['class' => 'form-control']) !!}
            @if ($errors->has('customer_name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('customer_name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('customer_phone') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Cellphone number :<span class="text-red">*</span></label>
            {!! Form::text('customer_phone', null, ['class' => 'form-control']) !!}
            @if ($errors->has('customer_phone'))
                <span class="text-danger">
                    <strong>{{ $errors->first('customer_phone') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('order_date') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Delivery Date :<span class="text-red">*</span></label>
            {!! Form::date('order_date', null, ['class' => 'form-control']) !!}
            @if ($errors->has('order_date'))
                <span class="text-danger">
                    <strong>{{ $errors->first('order_date') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('order_time') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Delviery Time :<span class="text-red">*</span></label>
            {!! Form::time('order_time', null, ['class' => 'form-control']) !!}
            @if ($errors->has('order_time'))
                <span class="text-danger">
                    <strong>{{ $errors->first('order_time') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

@if(!empty($order['OrderItems']))
    @foreach ($order['OrderItems'] as $key => $OrderItems)
        <div class="row" id="productDiv_{{$key}}">
            <div class="col-md-5">
                <div class="form-group{{ $errors->has('product_id') ? ' has-error' : '' }}">
                    @if($key==0)
                    <label class="control-label" for="name">Select Product :<span class="text-red">*</span></label>
                    @endif
                    {!! Form::select('product_id['.$key.']', $products, $OrderItems['product_id'], ['class' => 'form-control select2', 'id' => 'product_id_'.$key, $key==0?'required':'','onchange' => 'getProductPrice(this.value, '.$key.')']) !!}
                    @if ($errors->has('product_id'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('product_id') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                    @if($key==0)
                    <label class="control-label" for="name">Price (₱) :<span class="text-red">*</span></label>
                    @endif
                    {!! Form::text('price['.$key.']', $OrderItems['price'], ['class' => 'form-control', 'id' => 'price_'.$key, 'readonly' => true, $key==0?'required':'']) !!}
                    @if ($errors->has('price'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('price') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                    @if($key==0)
                    <label class="control-label" for="name">Quantity :<span class="text-red">*</span></label>
                    @endif
                    {!! Form::number('quantity['.$key.']', $OrderItems['quantity'], ['class' => 'form-control', 'id' => 'quantity_'.$key, 'min' => 1, $key==0?'required':'', 'onchange' => 'getTotalPrice(this.value, '.$key.')', 'onkeyup' => 'getTotalPrice(this.value, '.$key.')']) !!}
                    @if ($errors->has('quantity'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('quantity') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group{{ $errors->has('ordertotal') ? ' has-error' : '' }}">
                    @if($key==0)
                    <label class="control-label" for="name">Sub Total (₱) :<span class="text-red">*</span></label>
                    @endif
                    {!! Form::number('ordertotal['.$key.']', number_format(($OrderItems['quantity']*$OrderItems['price']), 2, '.', ''), ['class' => 'form-control', 'id' => 'ordertotal_'.$key, 'readonly' => true]) !!}
                </div>
            </div>

            <div class="col-md-1">
                @if($key==0)
                    <button style="margin-top: 31px;" type="button" class="btn btn-info" id="expBtn"><i class="fa fa-plus"></i> </button>
                @else
                    <button type="button" class="btn btn-danger deleteExp" onClick="removeRow({{ $key }})"><i class="fa fa-trash"></i></button>
                @endif
            </div>

        </div>
    @endforeach
@else
    <div class="row" id="productDiv_0">
        <div class="col-md-5">
            <div class="form-group{{ $errors->has('product_id') ? ' has-error' : '' }}">
                <label class="control-label" for="name">Select Product :<span class="text-red">*</span></label>
                {!! Form::select('product_id[0]', $products, null, ['class' => 'form-control select2', 'id' => 'product_id_0', 'required','onchange' => 'getProductPrice(this.value, 0)']) !!}
                @if ($errors->has('product_id'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('product_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                <label class="control-label" for="name">Price (₱) :<span class="text-red">*</span></label>
                {!! Form::text('price[0]', '0.00', ['class' => 'form-control', 'id' => 'price_0', 'readonly' => true, 'required']) !!}
                @if ($errors->has('price'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('price') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                <label class="control-label" for="name">Quantity :<span class="text-red">*</span></label>
                {!! Form::number('quantity[0]', 1, ['class' => 'form-control', 'id' => 'quantity_0', 'min' => 1, 'required', 'onkeyup' => 'getTotalPrice(this.value, 0)', 'onchange' => 'getTotalPrice(this.value, 0)']) !!}
                @if ($errors->has('quantity'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('quantity') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group{{ $errors->has('ordertotal') ? ' has-error' : '' }}">
                <label class="control-label" for="name">Sub Total (₱) :<span class="text-red">*</span></label>
                {!! Form::number('ordertotal[0]', '0.00', ['class' => 'form-control', 'id' => 'ordertotal_0', 'readonly' => true]) !!}
            </div>
        </div>

        <div class="col-md-1" style="margin-top: 31px;">
            <button type="button" class="btn btn-info" id="expBtn"><i class="fa fa-plus"></i> </button>
        </div>

    </div>
@endif

<div id="extraProduct" style="width: 100%;"></div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Address :<span class="text-red">*</span></label>
            {!! Form::text('address', null, ['class' => 'form-control']) !!}
            @if ($errors->has('address'))
                <span class="text-danger">
                    <strong>{{ $errors->first('address') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group{{ $errors->has('short_notes') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Short Notes :</label>
            {!! Form::textarea('short_notes', null, ['class' => 'form-control', 'rows' => '2']) !!}
            @if ($errors->has('short_notes'))
                <span class="text-danger">
                    <strong>{{ $errors->first('short_notes') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="status">Status :<span class="text-red">*</span></label>
            <div class="col-md-12">
                @foreach (\App\Models\Orders::$status as $key => $value)
                    @php $checked = !isset($order) && $key == 'pending'?'checked':''; @endphp
                    <label>
                        {!! Form::radio('status', $key, null, ['class' => 'flat-red',$checked]) !!} <span style="margin-right: 10px">{{ $value }}</span>
                    </label>
                @endforeach
                <br class="statusError">
                @if ($errors->has('status'))
                    <span class="text-danger" id="statusError">
                        <strong>{{ $errors->first('status') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Delivery Fee :<span class="text-red">*</span></label>
            {!! Form::text('delivery_fee', null, ['class' => 'form-control']) !!}
            @if ($errors->has('delivery_fee'))
                <span class="text-danger">
                    <strong>{{ $errors->first('delivery_fee') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

@section('jquery')
<script type="text/javascript">
    var counter = @if(isset($order) && !empty($order['OrderItems'])){{count($order['OrderItems'])}} @else 0 @endif;
    var shouldContinue = true;

    $('#expBtn').on('click', function(){
        counter = counter + 1;
        var exProduct = 100;

        var exProContent = '<div class="row" id="productDiv_'+counter+'">'+
            '<div class="col-md-5">'+
                '<div class="form-group">'+
                   '<select class="form-control select2" name="product_id['+counter+']" id="product_id_'+counter+'" onchange="getProductPrice(this.value, '+counter+')">'+
                        @foreach($products as $key => $product)
                            '<option value="{{$key}}">{{$product}}</option>'+
                        @endforeach
                    '</select>'+
                '</div>'+
            '</div>'+

            '<div class="col-md-2">'+
                '<div class="form-group">'+
                    '<input type="number" name="price['+counter+']" class="form-control" id="price_'+counter+'" value="0.00" readonly>'+
                '</div>'+
            '</div>'+

            '<div class="col-md-2">'+
                '<div class="form-group">'+
                    '<input type="number" name="quantity['+counter+']" class="form-control" id="quantity_'+counter+'"  value="1" min="1" onkeyup="getTotalPrice(this.value, '+counter+')" onchange="getTotalPrice(this.value, '+counter+')">'+
                '</div>'+
            '</div>'+

            '<div class="col-md-2">'+
                '<div class="form-group">'+
                    '<input type="text" name="ordertotal['+counter+']" class="form-control" id="ordertotal_'+counter+'"  value="0.00" readonly>'+
                '</div>'+
            '</div>'+

            '<div class="col-md-1">'+
                '<button type="button" class="btn btn-danger deleteExp" onClick="removeRow('+counter+')"><i class="fa fa-trash"></i></button>'+
            '</div>'+

        '</div>';


        $('#extraProduct').append(exProContent);

        $('.select2').select2();
    });

    function removeRow(divId){
        const removeRowAlert = createOptionAlert("Are you sure?", "Do want to delete this row", "warning");
        swal(removeRowAlert, function(isConfirm) {
            if (isConfirm) {
                deleteExp(divId);
                swal.close();
            } else{
                 swal("Cancelled", "Your data safe!", "error");
            }
        });
    }

    //remove the row
    function deleteExp(divId){
        $('#productDiv_'+divId).remove();
    }

    function createOptionAlert(title, text, type) {
        return {
            title: title,
            text: text,
            type: type,
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: "No, cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        };
    }

    function getProductPrice(product_id, id) {
        $.ajax({
            url: "{{route('orders.getProductPrice')}}",
            type: "POST",
            data: {product_id:product_id, _token: '{{csrf_token()}}'},
            success: function(data){
                $('#price_'+id).val(data);

                if($("#price_"+id).val()>0){
                    var numberformate = $("#price_"+id).val()*$("#quantity_"+id).val();
                    $("#ordertotal_"+id).val(numberformate.toFixed(2));
                }
            }
        });
    }

    function getTotalPrice(quantity, id) {

        if($("#price_"+id).val()>0){
            var numberformate = $("#price_"+id).val()*quantity;
            $("#ordertotal_"+id).val(numberformate.toFixed(2));
        }
    }

    $('.customRadio').on('change', function(){
        var ordertype = $(this).val();
        if(ordertype == 1){
            $('#existingCustomer').show();
            $('#guestCustomer').hide();
            $('.select2').select2();
        }else{
            $('#guestCustomer').show();
            $('#existingCustomer').hide();
        }
    });
</script>
@endsection
