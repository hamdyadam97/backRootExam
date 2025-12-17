<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    @yield('title')
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--ar bootstrap-->

    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


    <style>
        .brands img {
            width: 50px;
            height: 50px;
        }
        .cnpBillingCheckoutWrapper {position:relative;}
        .cnpBillingCheckoutHeader {width:100%;border-bottom: 1px solid #c0c0c0;margin-bottom:10px;}
        .cnpBillingCheckoutLeft {width:240px;margin-left: 5px;margin-bottom: 10px;border: 1px solid #c0c0c0;display:inline-block;vertical-align: top;padding:10px;}
        .cnpBillingCheckoutRight {width:50%;margin-left: 5px;border: 1px solid #c0c0c0;display:inline-block;vertical-align: top;padding:10px;}
        .cnpBillingCheckoutOrange {font-size:110%;color: rgb(255, 60, 22);font-weight:bold;}
        div.wpwl-wrapper, div.wpwl-label, div.wpwl-sup-wrapper { width: 100% }
        div.wpwl-group-expiry, div.wpwl-group-brand { width: 30%; float:left }
        div.wpwl-group-cvv { width: 68%; float:left; margin-left:2% }
        div.wpwl-group-cardHolder, div.wpwl-sup-wrapper-street1, div.wpwl-group-expiry { clear:both }
        div.wpwl-sup-wrapper-street1 { padding-top: 1px }
        div.wpwl-wrapper-brand { width: auto }
        div.wpwl-sup-wrapper-state, div.wpwl-sup-wrapper-city { width:32%;float:left;margin-right:2% }
        div.wpwl-sup-wrapper-postcode { width:32%;float:left }
        div.wpwl-sup-wrapper-country { width: 66% }
        div.wpwl-wrapper-brand, div.wpwl-label-brand, div.wpwl-brand { display: none;}
        div.wpwl-group-cardNumber { width:60%; float:left; font-size: 20px;  }
        div.wpwl-group-brand { width:35%; float:left; margin-top:28px }
        div.wpwl-brand-card  { width: 65px }
        div.wpwl-brand-custom  { margin: 0px 5px; background-image: url("https://test.oppwa.com/v1/paymentWidgets/img/brand.png") }
        div.wpwl-group-cardNumber{
            width: 100%;
        }
        /*.wpwl-wrapper-cardNumber input {*/
        /*    direction: ltr!important;*/
        /*}*/
        .wpwl-group.wpwl-group-brand{
            display: none;
        }
        /*.wpwl-wrapper>.wpwl-icon{*/
        /*    left: 0.5625em;*/
        /*    right: initial;*/
        /*}*/
        /*.wpwl-wrapper-cardNumber,.wpwl-control-expiry{*/
        /*    direction: ltr;*/
        /*    text-align: right;*/
        /*}*/
    </style>
    <script src="{{env('HYPERPAY_URL')}}/v1/paymentWidgets.js?checkoutId={{$id}}"></script>


</head>
<body >

<script>
    var wpwlOptions = {
        style: "plain",
        /*  billingAddress: {
              country: "US",
              state: "NY",
              city: "New York",
              street1: "111 6th Avenu",
              street2: "",
              postcode: "12312"
          },*/
        // forceCardHolderEqualsBillingName: true,
        // locale: "ar",
        paymentTarget: "_top",
        showCVVHint: true,
        brandDetection: true,
        numberFormatting:false,
        onReady: function(){
            $(".wpwl-group-cardNumber").after($(".wpwl-group-brand").detach());
            $(".wpwl-group-cvv").after( $(".wpwl-group-cardHolder").detach());
            @if($type=='MADA')
            var visa = $(".wpwl-brand:first").clone().removeAttr("class").attr("class", "wpwl-brand-card wpwl-brand-custom wpwl-brand-VISA")
            // var master = $(visa).clone().removeClass("wpwl-brand-VISA").addClass("wpwl-brand-MASTER");
            // var mada = $(visa).clone().removeClass("wpwl-brand-VISA").addClass("wpwl-brand-MADA");
            // $(".wpwl-brand:first").after( $(master)).after( $(visa));
            // $(".wpwl-brand:first").after( $(mada)).after( $(master));
            @else
            var visa = $(".wpwl-brand:first").clone().removeAttr("class").attr("class", "wpwl-brand-card wpwl-brand-custom wpwl-brand-VISA")
            var master = $(visa).clone().removeClass("wpwl-brand-VISA").addClass("wpwl-brand-MASTER");
            var mada = $(visa).clone().removeClass("wpwl-brand-VISA").addClass("wpwl-brand-MADA");
            $(".wpwl-brand:first").after( $(master)).after( $(visa));
            $(".wpwl-brand:first").after( $(mada)).after( $(master));
            @endif
            // var visa = $(".wpwl-brand:first").clone().removeAttr("class").attr("class", "wpwl-brand-card wpwl-brand-custom wpwl-brand-VISA")
            // var master = $(visa).clone().removeClass("wpwl-brand-VISA").addClass("wpwl-brand-MASTER");
            // var mada = $(visa).clone().removeClass("wpwl-brand-VISA").addClass("wpwl-brand-MADA");
            // $(".wpwl-brand:first").after( $(master)).after( $(visa));
            // $(".wpwl-brand:first").after( $(mada)).after( $(master));

        },
        onChangeBrand: function(e){
            $(".wpwl-brand-custom").css("opacity", "0.3");
            $(".wpwl-brand-" + e).css("opacity", "1");
        }
    }

</script>
<div class="text-center  d-flex justify-content-center align-items-center">
    <div class="  row ">
        <div class="d-flex col-md-6 brands">
            @if($type=='MADA')
                <img src="{{ asset('images/mada.jpg') }}" >
            @else
                <img src="{{ asset('images/Master-Card.png') }}"style="padding: 5px" >
                <img src="{{ asset('images/visa.png') }}" >
            @endif
        </div>
        <div class="col-md-6">
            @if(isset($price))
                <strong class="border d-block rounded p-2">{{$price}} JOD </strong>
            @endif
        </div>
    </div>
</div>
<form
    action="{!! $url !!}"
    class="paymentWidgets"  data-brands="{{$type}}">

</form>



</body>
</html>




