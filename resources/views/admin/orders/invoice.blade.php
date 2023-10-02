<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Invoice Example</title>
        <link rel="stylesheet" href="{{ asset('assets/dist/css/style.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/dist/css/bootstrap.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}" />
    </head>
    <body>
        <style type="text/css">
            .text-right{text-align: right !important;}
            .text-left{text-align: left !important;}
            .pr-2{padding-right: 2px;}
            td, th {padding: 5px; vertical-align: top;border: 1px solid #dee2e6;}
        </style>
        <div class="invoice-box">
            <table class="table table-bordered">
                <tbody id="order-products">
                    <tr class="top">
                        <td colspan="5">
                            <table>
                                <tr>
                                    <td class="title">
                                        <img src="{{ url('storage/'.$invoice['logo'])}}" style="width: 100%; max-width: 88px" />
                                    </td>
                                    <td>
                                        <b>Invoice #:</b> {{ $orderData['unique_id'] }}<br />
                                        <b>Created:</b> {{ date('M d, Y h:i A') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="information">
                        <td colspan="5">
                            <table>
                                <tr>
                                    <td>
                                        {{ $orderData['customer_name'] }}<br />
                                        {{ $orderData['customer_address'] }}

                                    </td>
                                    <td>
                                        {{ $orderData['customer_name'] }}<br />
                                        {{ $orderData['customer_email'] }}<br />
                                        {{ $orderData['customer_phone'] }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="heading">
                        <th colspan="3">Payment Method</th>
                        <th colspan="2"># Transaction Id</th>
                    </tr>
                    <tr class="details">
                        <td colspan="3">{{ strtoupper($orderData['payment_type']) }}</td>
                        <td colspan="2">{{ $orderData['transaction_id'] }}</td>
                    </tr>
                    <tr class="heading">
                        <th class="text-left">Product Name</th>
                        <th class="text-left">SKU</th>
                        <th class="text-right" style="width: 10%;">Quantity</th>
                        <th class="text-right" style="width: 15%;">Unit Price</th>
                        <th class="text-right" style="width: 15%;">Total</th>
                    </tr>
                    @if(!empty($orderData['OrderItems']))
                        @foreach ($orderData['OrderItems'] as $key => $invoiceItem)
                            <tr class="item @if($loop->last) last @endif">
                                <td class="text-left">{{ $invoiceItem['name'] }}</td>
                                <td class="text-left">{{ $invoiceItem['sku'] }}</td>
                                <td class="text-right">{{ $invoiceItem['quantity'] }}</td>
                                <td class="text-right">₱ {{ $invoiceItem['price'] }}</td>
                                <td class="text-right">₱ {{ $invoiceItem['total'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr class="total">
                        <td></td>
                        <td></td>
                        <td></td>
                        <th class="text-right">Total</th>
                        <td class="text-right">₱ {{ number_format($orderData['order_total'], 2, '.', '') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>