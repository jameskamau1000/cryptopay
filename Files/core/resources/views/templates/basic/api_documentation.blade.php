@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <!-- documentation section start -->
    <div class="pt-50 pb-50 documentation-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <button class="sidebar-menu-open-btn mb-5 text-dark"><i class="las la-bars"></i>
                        @lang('Menu')</button>
                    <div class="documentation-menu-wrapper">
                        <button class="sidebar-close-btn"><i class="las la-times"></i></button>
                        <nav class="sidebar-menu">
                            <ul class="menu">
                                <li class="has_child"><a href="#introduction-section">@lang('Get started')</a>
                                    <ul class="drp-menu">
                                        <li class="active"><a href="#introduction">@lang('Introduction')</a></li>
                                        <li><a href="#currency">@lang('Supported Currencies')</a></li>
                                        <li><a href="#api-key">@lang('Get Api Key')</a></li>
                                        <li><a href="#initiate">@lang('Initiate Payment')</a></li>
                                        <li><a href="#ipn">@lang('IPN and Get Payment')</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="doc-body">
                        <div class="d-flex justify-content-end gap-2 mb-3">
                            <a href="{{ route('developer.documentation') }}" class="btn btn--base btn--sm">@lang('Open New Docs')</a>
                            <a href="{{ route('api.reference') }}" class="btn btn--dark btn--sm">@lang('API Reference')</a>
                        </div>
                        <div class="doc-section" id="introduction-section">
                            <div class="doc-content">
                                <section id="introduction">
                                    <h3>@lang('CryptoPay Developer Platform')</h3>
                                    <p class="mt-2">@lang('Build custom crypto payment experiences with') <strong
                                            class="text--base">{{ __(gs('site_name')) }}</strong>
                                        @lang('using secure APIs, webhooks, and payout workflows.')
                                    </p>
                                    <hr>
                                    <p class="text-justify">
                                        <strong class="text--base">{{ __(gs('site_name')) }}</strong> @lang('The CryptoPay API is simple to integrate into your company\'s software. Our API takes cURL requests, has well-formatted URLs, and produces JSON responses.')
                                    </p>
                                    <p class="text-justify">
                                        @lang('The API can be used in test mode without affecting your real data. The request is authenticated using the API key, which also establishes whether the payment is legitimate or not. For test mode just use the sandbox URL and In case of live mode use the live URL from section') <a href="#initiate" class="anchor-color">@lang('Initiate Payment')</a> .
                                    </p>
                                </section>
                            </div><!-- doc-content end -->
                        </div><!-- doc-section end -->
                        <div class="doc-section" id="currency">
                            <div class="doc-content">
                                <section id="">
                                    <h3>@lang('Supported Currencies')</h3>
                                    <p class="mt-2">@lang('The supported currencies by') <strong
                                            class="text--base">{{ __(gs('site_name')) }}</strong> @lang('are given below.')</p>
                                    <hr>
                                </section>
                                <section id="setting-two">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Currency')</th>
                                                    <th>@lang('Currency Symbol')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($allCurrency as $currency)
                                                    <tr>
                                                        <td>{{ $currency->currency }}</td>
                                                        <td>{{ $currency->symbol }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div><!-- table-responsive end -->
                                </section>
                            </div><!-- doc-content end -->
                        </div><!-- doc-section end -->
                        <div class="doc-section" id="api-key">
                            <div class="doc-content">
                                <section id="">
                                    <h3>@lang('Get The Api Key')</h3>
                                    <p class="mt-2">@lang('How to obtain the api key is explained in this part.')</p>
                                    <hr>
                                    <p class="text-justify">@lang('To access your') <strong
                                            class="text--base">{{ __(gs('site_name')) }}</strong> @lang('merchant account, please log in. In case you don\'t have an account, you can') <a
                                            target="_blank" class="anchor-color"
                                            href=" {{ route('user.login') }} ">@lang('Click Here')</a>.</p>
                                    <p>@lang('Now go to the') <span class="text--base fw-bold">@lang('Account > Settings > API Key')</span>
                                        @lang('from the merchant panel.')</p>
                                    <p class="text-justify">@lang('The api keys can be found there which is') <strong>@lang('Public key and Secret key.')</strong>
                                        @lang('Use these keys to initiate the API request. Every time you can generate new API key by clicking')
                                        <span class="text--base">@lang('Generate Api Key')</span>
                                        @lang('button. Remember do not share these keys with anyone.')
                                    </p>
                                </section>
                            </div><!-- doc-content end -->
                        </div><!-- doc-section end -->
                        <div class="doc-section" id="initiate">
                            <div class="doc-content">
                                <section id="">
                                    <h3>@lang('Initiate Payment')</h3>
                                    <p class="mt-2">@lang('In this section, the procedure for initiating the payment is explained.')</p>
                                    <hr>
                                    <p>
                                        @lang('To begin the payment process, use the sample code provided, and pay close attention to the parameters. The API endpoints mentioned below will need to be used to make the request.')
                                    </p>
                                    <p>
                                        <strong>@lang('Live End Point:')</strong>
                                        <span class="text--base"> {{ route('payment.initiate') }} </span>
                                    </p>
                                    <p class="d-flex align-items-center flex-wrap gap-2">
                                        <strong>@lang('Test End Point:')</strong>
                                        <span class="text--base responsive-text"> {{ route('test.payment.initiate') }}
                                        </span>
                                    </p>
                                </section>
                                <section id="setting-two">
                                    <p>@lang('Request to the end point with the following parameters below.')</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Param Name')</th>
                                                    <th>@lang('Param Type')</th>
                                                    <th>@lang('Description')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>public_key</td>
                                                    <td>string (50)</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Your Public API key')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>identifier</td>
                                                    <td>string (20)</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Identifier is basically for identify payment at your end')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>currency</td>
                                                    <td>string (4)</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Currency Code, Must be in Upper Case. e.g. USD,EUR')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>amount</td>
                                                    <td>decimal</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Payment amount')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>details</td>
                                                    <td>string (100)</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Details of your payment or transaction')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ipn_url</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('The url of instant payment notification')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>success_url</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Payment success redirect url')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>cancel_url</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Payment cancel redirect url')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>site_name</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Your business site name')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>site_logo</td>
                                                    <td>string/url</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Your business site logo')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>checkout_theme</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Checkout form theme dark/light. Default theme is light')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="100%">
                                                        <span
                                                            class="justify-content-center d-flex fw-bold">@lang('Customer')</span>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td><span class="fw-bold">customer[]</span></td>
                                                    <td>@lang('array')</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('customer must be an array')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>customer.first_name</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Customer\'s first name')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>customer.last_name</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Customer\'s last name')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>customer.email</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Customer\'s valid email')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>customer.mobile</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--danger font-size--12px">@lang('Required')</span>
                                                        @lang('Customer\'s valid mobile')
                                                    </td>
                                                </tr>


                                                <td colspan="100%">
                                                    <span
                                                        class="justify-content-center d-flex fw-bold">@lang('Shipping info')</span>
                                                </td>
                                                <tr>
                                                    <td><span class="fw-bold">shipping_info[]</span></td>
                                                    <td>@lang('array')</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('shipping_info must be an array')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.address_one</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Customer\'s address one')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.address_two</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Customer\'s address two')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.area</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Shipping area of customer')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.city</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Shipping city of customer')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.sub_city</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Shipping sub city of customer')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.state</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Shipping state')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.postcode</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Shipping postcode')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.country</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Shipping country')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>shipping_info.others</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Others info')
                                                    </td>
                                                </tr>


                                                <td colspan="100%">
                                                    <span
                                                        class="justify-content-center d-flex fw-bold">@lang('Billing info')</span>
                                                </td>
                                                <tr>
                                                    <td><span class="fw-bold">billing_info[]</span></td>
                                                    <td>@lang('array')</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('billing_info must be an array')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.address_one</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Customer\'s address one')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.address_two</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Customer\'s address two')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.area</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Billing area of customer')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.city</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Billing city of customer')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.sub_city</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Billing sub city of customer')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.state</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Billing state')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.postcode</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Billing postcode')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.country</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Billing country')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>billing_info.others</td>
                                                    <td>string</td>
                                                    <td>
                                                        <span
                                                            class="badge badge--info font-size--12px">@lang('Optional')</span>
                                                        @lang('Others info')
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><!-- table-responsive end -->

                                </section>
                            </div><!-- doc-content end -->
                            <div class="doc-code">
                                <div class="doc-code-inner">
                                    <div class="code-block">
                                        <button class="clipboard-btn"
                                            data-clipboard-target="#php">@lang('copy')</button>
                                        <div class="code-block-header">@lang('Example PHP code')</div>

                                        <pre><code class="language-php" id="php">&lt;?php
    $parameters = [
        'identifier' => 'DFU80XZIKS',
        'currency' => 'USD',
        'amount' => 11.00,
        'gateway_methods' => [
            // Please write the name of the gateway method you want to use
        ],
        'details' => 'Purchase T-shirt',
        'ipn_url' => 'http://example.com/ipn_url.php',
        'cancel_url' => 'http://example.com/cancel_url.php',
        'success_url' => 'http://example.com/success_url.php',
        'public_key' => 'your_public_key',
        'site_name' => 'your_site_name',
        'site_logo' => 'http://yoursite.com/logo.png',
        'checkout_theme' => 'light',
        'customer'=>[
            'first_name'=>'John',
            'last_name'=>'Doe',
            'email'=>'joan@gmail.com',
            'mobile'=>'12345789',
        ],
        'shipping_info'=>[
            'address_one'=>'',
            'address_two'=>'',
            'area'=>'',
            'city'=>'',
            'sub_city'=>'',
            'state'=>'',
            'postcode'=>'',
            'country'=>'',
            'others'=>'',
        ],
        'billing_info'=>[
            'address_one'=>'',
            'address_two'=>'',
            'area'=>'',
            'city'=>'',
            'sub_city'=>'',
            'state'=>'',
            'postcode'=>'',
            'country'=>'',
            'others'=>'',
        ]
    ];

    $parameters = http_build_query($parameters);

    //live end point
    $url = '{{ route('payment.initiate') }}';

    //test end point
    $url = '{{ route('test.payment.initiate') }}';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

?&gt;</code></pre>

                                    </div><!-- code-block end -->
                                    <div class="code-block">
                                        <button class="clipboard-btn"
                                            data-clipboard-target="#response">@lang('copy')</button>
                                        <div class="code-block-header">@lang('Example Responses')</div>

                                        <pre><code class="language-php" id="response">//Error Response.
{
    "status": "error",
    "message": [
        "Invalid api key"
    ]
}

//Success Response.
{
    "status": "success",
    "message": [
        "Payment initiated"
    ],
    "redirect_url": "https://example.com/payment/checkout?payment_trx=eyJpdiI6IkFyNllSNU1lOFdkYTlPTW52cytPNGc9PSIsInZhbHVlIjoiWWowRTRjdzZ1S1BBRm4ydS81OWR1WjdXeFIxcjE1WkZRVE9BcmZYeXpzND0iLCJtYWMiOiJjNDdhODUzYzY2NmZlZGJjZTI5ODQyMmRkYzdjYjRmM2NiNjg4M2RiMWZjN2EyMzFkODI4OWMwYjk3ZWYwNGQwIiwidGFnIjoiIn0%3D"
}
</code></pre>

                                    </div><!-- code-block end -->
                                </div>
                            </div>
                        </div><!-- doc-section end -->

                        <div class="doc-section" id="ipn">
                            <div class="doc-content">
                                <section id="">
                                    <h3>@lang('Validate The Payment and IPN')</h3>
                                    <p class="mt-2">@lang('This section describes the process to get your instant payment notification.')</p>
                                    <hr>
                                    <p>
                                        @lang('To initiate the payment follow the example code and be careful with the perameters. You will need to make request with these following API end points.')
                                    </p>
                                    <p>
                                        <strong>@lang('End Point:')</strong> <span
                                            class="text--base">@lang('Your business application ipn url.')</span>
                                    </p>
                                    <p><strong>@lang('Request Method:')</strong> <span class="text--base">POST</span></p>
                                </section>
                                <section id="setting-two">
                                    <p>@lang('You will get following parameters below.')</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Param Name')</th>
                                                    <th>@lang('Description')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>status</td>
                                                    <td>@lang('Payment success status.')</td>
                                                </tr>
                                                <tr>
                                                    <td>identifier</td>
                                                    <td>@lang('Identifier is basically for identify payment at your end.')</td>
                                                </tr>
                                                <tr>
                                                    <td>signature</td>
                                                    <td>@lang('A hash signature to verify your payment at your end.')</td>
                                                </tr>
                                                <tr>
                                                    <td>data</td>
                                                    <td> @lang('Data contains some basic information with charges, amount, currency, payment transaction id etc.')</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><!-- table-responsive end -->
                                </section>
                            </div><!-- doc-content end -->
                            <div class="doc-code">
                                <div class="doc-code-inner">
                                    <div class="code-block">
                                        <button class="clipboard-btn"
                                            data-clipboard-target="#ipn-s">@lang('copy')</button>
                                        <div class="code-block-header">@lang('Example PHP code')</div>

                                        <pre><code class="language-php" id="ipn-s">&lt;?php
    //Receive the response parameter
    $status = $_POST['status'];
    $signature = $_POST['signature'];
    $identifier = $_POST['identifier'];
    $data = $_POST['data'];

    // Generate your signature
    $customKey = $data['amount'].$identifier;
    $secret = 'YOUR_SECRET_KEY';
    $mySignature = strtoupper(hash_hmac('sha256', $customKey , $secret));

    $myIdentifier = 'YOUR_GIVEN_IDENTIFIER';

    if($status == &quot;success&quot; &amp;&amp; $signature == $mySignature &amp;&amp;  $identifier ==  $myIdentifier){
        //your operation logic
    }
?&gt;</code></pre>

                                    </div><!-- code-block end -->
                                </div>
                            </div>
                        </div><!-- doc-section end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- documentation section end -->
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'frontend/css/api_documentation.css') }}">
@endpush

@push('header-script-lib')
    <script src="{{ asset($activeTemplateTrue . 'frontend/js/highlight.min.js') }}"></script>
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'frontend/js/clipboard.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'frontend/js/menu-spy.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'frontend/js/jquery.easing.min.js') }}"></script>
@endpush

@push('script')
    <script>
        'use strict';

        hljs.highlightAll();

        //jQuery for page scrolling feature - requires jQuery Easing plugin
        $('.sidebar-menu ul.menu').each(function() {
            $('.sidebar-menu ul.menu li a').on('click', function(event) {
                var $anchor = $(this);
                $('html, body').stop().animate({
                    scrollTop: $($anchor.attr('href')).offset().top - 100
                }, 300, 'easeInOutExpo');
                event.preventDefault();
            });
        });

        // spy scroll menu activation
        const elm = document.querySelector('.sidebar-menu');
        const ms = new MenuSpy(elm, {
            // menu selector
            menuItemSelector: 'a[href^="#"]',
            // CSS class for active item
            activeClass: 'active',
            // amount of space between your menu and the next section to be activated.
            threshold: 0,
            // timeout to apply browser's hash location.
            hashTimeout: 500,
            // called every time a new menu item activates.
            callback: null
        });

        new ClipboardJS('.clipboard-btn');

        const sidebarWrapper = document.querySelector('.documentation-menu-wrapper');
        const sidebarOpenBtn = document.querySelector('.sidebar-menu-open-btn');
        const sidebarCloseBtn = document.querySelector('.sidebar-close-btn');

        sidebarOpenBtn.addEventListener('click', function() {
            sidebarWrapper.classList.add('open');
        });

        sidebarCloseBtn.addEventListener('click', function() {
            sidebarWrapper.classList.remove('open');
        });
    </script>
@endpush
