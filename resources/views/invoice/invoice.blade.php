<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>@lang('default.invoice')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css">
        /* reset */

        *[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

        /* heading */

        h1 { font: bold 100% Ubuntu, Arial, sans-serif; text-align: left; text-transform: uppercase; }

        /* page */

        html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; }
        html { background: #fff; cursor: default; }

        body { box-sizing: border-box; margin:0;}
        #wrapper{ margin: 0 auto; width: 19cm; }
        body { background: #FFF;}
        /* header */
        header { margin: 0 0 1.5em; }
        header:after { clear: both; content: ""; display: table; }
        header h1 { background: #fff; border-radius: 0.25em; color: #000; margin: 0; margin-bottom: -15px; padding: 0.5em 0; font-size: 40px;}
        header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 0 0; }
        header address p { margin: 0 0 0em; }
        header span, header img { display: block; float: right; }

        /*header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }*/

        /* article */

        article, article address, table.meta { margin: 0 0 1.5em; }
        article:after { clear: both; content: ""; display: table; }
        article h1 { clip: rect(0 0 0 0); position: absolute; }

        article address { float: left; font-size: 125%; font-weight: bold; }

        /* table */

        table { font-size: 75%; table-layout: fixed; width: 100%; }
        table { border-collapse: collapse; border-spacing: 0px; }
        table.inventory, table.balance{ background-color: #f9f9f9; border: #fff;}

        th, td {  padding:0 0.5em; position: relative; text-align: left; }
        table.meta th, table.meta td {  padding: 0.2em;}
        table.balance th, table.balance td {  padding-bottom: 0.2em;}

        th { background: #000;color:#fff }
        td { border-color: #ff0000; }


        table.meta, table.balance { float: right; width: 25%;}
        table.meta:after, table.balance:after { clear: both; content: ""; display: table; }

        /* table meta */

        table.meta th { width: 40%; }
        table.meta td { width: 60%; }
        table.meta  { margin-right: -35px; }

        /* table items */

        table.inventory { clear: both; width: 100%; }
        table.inventory tr { border-bottom: 2px solid #000}
        table.inventory th { font-weight: bold; border-right: 2px solid #fff }
        table.inventory td { border-right: 1px solid #fff; border-bottom: 2px solid #fff;}

        table.inventory th:nth-child(1) {  border-left: 2px solid #fff }
        table.inventory td:nth-child(1) { border-left: 2px solid #fff }

        /* table balance */

        table.balance th, table.balance td { width: 50%; background-color: #f9f9f9}
        table.balance td { text-align: right; }

        /*custom classes*/
        .ml-10{
            margin-left: 10px;
        }
        .ml-10{
            margin-left: 5px;
        }
        .custom-th{
            background-color:#fff;color:#000; font-weight: bold;
            padding: 5px 0;
        }

        .custom-p{
            font-size: 14px;font-weight: normal;margin-bottom: 0px;margin-top: 0;
        }

    </style>

</head>
<body>
        <span>
        <?php $image_path = public_path() .'/uploads/invoice/'.$invoiceLogo;  ?>
        <img src="{{$image_path }}" style=" display:block;position: fixed;top: 30px; right: 25%; max-height:200px;height: auto; width: auto; max-width: 200px;">
        </span>
<div id="wrapper">
    <header>
        <address contenteditable>
            <h1 style="text-align: left">@lang('default.invoice')</h1>
            <p style="text-align: left;font-size: 125%; font-weight: bold">{{$companyName}}</p>
            <p>{{$companyInfo}}</p>
        </address>
    </header>
    <article style="margin-top: 0px">
        <address contenteditable style="font-style: normal">

            <p style="font-size: 18px;font-weight: bold;margin-bottom: 2px;margin-top:0px">@lang('default.invoice_to')</p>
            <p class="custom-p">{{$name}}</p>
            <p class="custom-p">{{$email}}</p>
            <p class="custom-p">{{$phone}}</p>
        </address>

        <table class="meta">
            <tr>
                <th class="custom-th"><span contenteditable>@lang('default.invoice_id'):</span></th>
                <td><span contenteditable>{{$invoiceId}}</span></td>
            </tr>
            <tr>
                <th class="custom-th"><span contenteditable>@lang('default.date'):</span></th>
                <td><span contenteditable>{{$invoiceCreatedAt}}</span></td>
            </tr>
        </table>
        <table class="inventory" style="">
            <thead>
            <tr style="margin-bottom: 1px ; border-bottom: 1px solid #000!important;">
                <th style="text-align: left;" colspan="2"><p >@lang('default.service')</p></th>
                <th style="text-align: right"><p >@lang('default.unit_price')</p></th>
                <th style="text-align: right"> <p >@lang('default.quantity')</p></th>
                <th style="text-align: right"><p >@lang('default.price')</p></th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoiceItemData as $invoiceItem)
            <tr >
                <td style="" colspan="2"><p >{{$invoiceItem->service_title}}</p>
                    <p class="ml-5">{{$invoiceItem->booking_date}} {{$invoiceItem->booking_time}}</p>
                </td>
                <td style="text-align: right;"><p >{{$invoiceItem->unit_price}}</p></td>
                <td style="text-align: right;"><p >{{$invoiceItem->quantity}}</p></td>
                <td style="text-align: right;"><p>{{$invoiceItem->total}}</p></td>
            </tr>
                @endforeach
            </tbody>
        </table>

        <table class="balance">
            <tr>
                <th class="custom-th" style="text-align: left"><span contenteditable>@lang('default.total')</span></th>
                <td style="text-align: right;"><span>{{$total}}</span></td>
            </tr>
            <tr>
                <th class="custom-th" style="text-align: left"><span contenteditable>@lang('default.amount_paid')</span></th>
                <td style="text-align: right;"><span >{{$paid}}</span></td>
            </tr>
            <tr>
                <th class="custom-th" style="text-align: left"><span  contenteditable>@lang('default.balance_due')</span></th>
                <td style="text-align: right;"><span>{{$due}}</span></td>
            </tr>
        </table>
    </article>
    {{--<address contenteditable>--}}
        {{--<h1 style="padding-left: 0px;margin-left: -10px">Gain Booking</h1>--}}
        {{--<div style="padding-left:2px">--}}
            {{--<p class="custom-p">Jonathan Neal</p>--}}
            {{--<p class="custom-p">101 E. Chapman Ave<br>Orange, CA 92866</p>--}}
            {{--<p class="custom-p">(800) 555-1234</p>--}}
        {{--</div>--}}
    {{--</address>--}}
    <aside>

    </aside>
</div>
</body>
</html>