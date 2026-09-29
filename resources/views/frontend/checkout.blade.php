@extends('frontend.master', ['activePage' => 'checkout'])
@section('title', __('Checkout'))
@section('content')

    <style>
        .tab-button.active {
            @apply text-blue-500 border-blue-500;
        }
    </style>

    @php
        $phone = \DB::table('country')->get();
    @endphp

    {{-- content --}}
    <div class="pb-20 bg-scroll min-h-screen"
        style="background-image: url('{{ asset('images/events.png') }}')">

        {{-- scroll --}}
        <div id="stripe_message" class="bg-danger text-white text-center p-2 hidden"></div>

        <div class="mr-4 flex justify-end z-30">
            <a type="button"
                href="{{ url('#') }}"
                class="scroll-up-button bg-primary rounded-full p-4 fixed z-20 2xl:mt-[49%] xl:mt-[59%] xlg:mt-[68%] lg:mt-[75%] xxmd:mt-[83%] md:mt-[90%]
                xmd:mt-[90%] sm:mt-[117%] msm:mt-[125%] xsm:mt-[160%]">

                <img src="{{ asset('images/downarrow.png') }}"
                    alt=""
                    class="w-3 h-3 z-20">

            </a>
        </div>

        <input type="hidden"
            name="totalAmountTax"
            id="totalAmountTax"
            value="{{ $data->totalAmountTax }}">

        <input type="hidden"
            name="totalPersTax"
            id="totalPersTax"
            value="{{ $data->totalPersTax }}">

        <input type="hidden"
            name="flutterwave_key"
            value="{{ \App\Models\PaymentSetting::find(1)->ravePublicKey }}">

        <input type="hidden"
            name="email"
            value="{{ Auth::guard('appuser')->check() ? auth()->guard('appuser')->user()->email : '' }}">

        <input type="hidden"
            name="phone"
            value="{{ Auth::guard('appuser')->check() ? auth()->guard('appuser')->user()->phone : '' }}">

        <input type="hidden"
            name="name"
            value="{{ Auth::guard('appuser')->check() ? auth()->guard('appuser')->user()->name : '' }}">

        <input type="hidden"
            name="flutterwave_key"
            value="{{ \App\Models\PaymentSetting::find(1)->ravePublicKey }}">

        <div id="ticketorder">

            @csrf

            <input type="hidden"
                id="razor_key"
                name="razor_key"
                value="{{ \App\Models\PaymentSetting::find(1)->razorPublishKey }}">

            <input type="hidden"
                id="stripePublicKey"
                name="stripePublicKey"
                value="{{ \App\Models\PaymentSetting::find(1)->stripePublicKey }}">

            <input type="hidden"
                value="{{ $data->ticket_per_order }}"
                name="tpo"
                id="tpo">

            <input type="hidden"
                value="{{ $data->available_qty }}"
                name="available"
                id="available">

            <input type="hidden"
                name="price"
                id="ticket_price"
                value="{{ $data->price }}">

            <input type="hidden"
                name="tax"
                id="tax_total"
                value="{{ strtoupper($data->type) == 'FREE' ? 0 : $data->tax_total }}">

            <input type="hidden"
                name="payment"
                id="payment"
                value="{{ strtoupper($data->type) == 'FREE' ? 0 : $data->price + $data->tax_total }}">

            @php
                $price = $data->price + $data->tax_total;

                if (
                    $data->currency_code == 'USD' ||
                    $data->currency_code == 'EUR' ||
                    $data->currency_code == 'INR'
                ) {
                    $price = $price * 100;
                }
            @endphp

            <input type="hidden"
                name="stripe_payment"
                id="stripe_payment"
                value="{{ strtoupper($data->type) == 'FREE' ? 0 : $price }}">

            <input type="hidden"
                name="currency_code"
                id="currency_code"
                value="{{ $data->currency_code }}">

            <input type="hidden"
                name="currency"
                id="currency"
                value="{{ $currency }}">

            <input type="hidden"
                name="payment_token"
                id="payment_token">

            <input type="hidden"
                name="ticket_id"
                id="ticket_id"
                value="{{ $data->id }}">

            <input type="hidden"
                name="selectedSeats"
                id="selectedSeats">

            <input type="hidden"
                name="selectedSeatsId"
                id="selectedSeatsId">

            <input type="hidden"
                name="coupon_id"
                id="coupon_id"
                value="">

            <input type="hidden"
                name="coupon_discount"
                id="coupon_discount"
                value="0">

            <input type="hidden"
                name="subtotal"
                id="subtotal"
                value="">

            <input type="hidden"
                name="add_ticket"
                value="">

            <input type="hidden"
                class="tax_data"
                id="tax_data"
                name="tax_data"
                value="{{ $data->tax }}">

            <input type="hidden"
                name="event_id"
                value="{{ $data->event_id }}">

            <input type="hidden"
                name="ticketname"
                id="ticketname"
                value="{{ $data->name }}">

            <div
                class="mt-10 3xl:mx-52 2xl:mx-28 1xl:mx-28 xl:mx-36 xlg:mx-32 lg:mx-36 xxmd:mx-24 xmd:mx-32 md:mx-28 sm:mx-20 msm:mx-16 xsm:mx-10 xxsm:mx-5 z-10 relative">

                <div
                    class="flex sm:space-x-6 msm:space-x-0 xxsm:space-x-0 xlg:flex-row lg:flex-col xmd:flex-col xxsm:flex-col">

                    <div class="xlg:w-[75%] xxmd:w-full xxsm:w-full">

                        <div
                            class="flex 3xl:flex-row 2xl:flex-nowrap 1xl:flex-nowrap xl:flex-nowrap xlg:flex-wrap flex-wrap justify-between 3xl:pt-5 xl:pt-5 gap-x-5 xl:w-full xlg:w-full">

                            <div class="">

                                <div
                                    class="w-full shadow-lg p-5 rounded-lg flex 3xl:flex-nowrap md:flex-wrap xxmd:flex-nowrap sm:flex-wrap msm:flex-wrap xsm:flex-wrap xxsm:flex-wrap bg-white xlg:w-full xmd:w-full 3xl:mb-0 xl:mb-0 xlg:mb-5 xxsm:mb-5">

                                    <img src="{{ asset('images/upload/' . $data->event->image) }}"
                                        alt=""
                                        class="rounded-lg w-56 h-56 object-cover">

                                    <div
                                        class="ml-4 2xl:w-[60%] xl:w-[80%] xlg:w-[80%] xmd:w-full xxmd:w-[80%] xxsm:ml-0 lg:ml-4">

                                        <p
                                            class="font-poppins font-bold text-4xl leading-8 text-left pt-3 text-black-100 xxsm:text-xl md:text-4xl">

                                            {{ $data->event->name }}

                                        </p>

                                        <p
                                            class="font-poppins font-normal text-sm text-gray-200 leading-8 text-left pt-2">

                                            {{ __('Date') }}

                                        </p>

                                        <p
                                            class="font-poppins font-medium text-base leading-7 text-gray text-left">

                                            {{ Carbon\Carbon::parse($data->event->start_time)->format('d M Y') }}
                                            -
                                            {{ Carbon\Carbon::parse($data->event->end_time)->format('d M Y') }}

                                        </p>

                                        <p
                                            class="font-poppins font-normal text-sm text-gray-200 leading-8 text-left pt-2">

                                            {{ __('Location') }}

                                        </p>

                                        <p
                                            class="font-poppins font-medium text-base leading-7 text-gray text-left w-[50%] xxsm:w-full lg:w-[92%] xl:w-[95%] lx3:w-[85%]">

                                            {{ $data->event->address }}

                                        </p>

                                        @if ($data->allday == 0)

                                            <div>

                                                <input type="date"
                                                    name="ticket_date"
                                                    id="onetime"
                                                    placeholder="mm/dd/yy"
                                                    class="mt-3 border p-2 border-gray-light">

                                                @if ($errors->has('ticket_date'))

                                                    <div class="text-danger">
                                                        {{ $errors->first('ticket_date') }}
                                                    </div>

                                                @endif

                                                <div class="ticket_date text-danger"></div>

                                            </div>

                                        @endif

                                    </div>

                                    <div class="md:w-[15%] sm:w-full pt-14 xxsm:pt-5">

                                        @if ($data->type == 'paid')

                                            <p
                                                class="font-poppins font-medium text-sm leading-7 text-danger text-left">

                                                {{ __('Paid') }}

                                            </p>

                                        @else

                                            <p
                                                class="font-poppins font-medium text-sm leading-7 text-success text-left">

                                                {{ __('Free') }}

                                            </p>

                                        @endif

                                        @if ($data->type == 'paid')

                                            <p
                                                class="font-poppins font-semibold text-3xl leading-7 text-primary text-left pt-5">

                                                {{ $data->currency . $data->price }}

                                            </p>

                                        @endif

                                        @if ($data->seatmap_id == null || $data->module->is_enable == 0)

                                            <p
                                                class="font-poppins font-medium text-base leading-7 text-black text-left pt-10">

                                                {{ __('Quantity') }}

                                            </p>

                                            <div
                                                class="flex flex-row h-10 w-full rounded-lg relative bg-transparent mt-1 pro-qty">

                                                <button
                                                    data-mdb-ripple="true"
                                                    id="dec-{{ $data->id }}"
                                                    data-mdb-ripple-color="light"
                                                    data-action="decrement"
                                                    type="button"
                                                    class="border-l dec qtybtn border-t border-b border-primary bg-primary-light text-primary hover:text-black-700 h-8 w-9 cursor-pointer">

                                                    <span class="m-auto text-2xl font-thin">
                                                        −
                                                    </span>

                                                </button>

                                                <div class="text-center">

                                                    <input
                                                        type="number"
                                                        id="quantity"
                                                        readonly
                                                        name="quantity"
                                                        value="1"
                                                        class="cursor-default bg-primary-light outline-none focus:outline-none text-center w-8 font-semibold text-md
                                                        hover:text-black focus:text-black md:text-basecursor-default flex items-center text-primary h-8">

                                                </div>

                                                <button
                                                    data-mdb-ripple="true"
                                                    data-mdb-ripple-color="light"
                                                    data-action="increment"
                                                    id="inc-{{ $data->id }}"
                                                    type="button"
                                                    class="border-r inc qtybtn border-t border-b border-primary bg-primary-light text-primary hover:text-black-700 h-8 w-9 cursor-pointer">

                                                    <span class="m-auto text-2xl font-thin">
                                                        +
                                                    </span>

                                                </button>

                                            </div>

                                            <div
                                                class="font-poppins font-medium text-base leading-7 text-danger"
                                                id="quantityMsg">
                                            </div>

                                        @endif

                                    </div>

                                </div>

                                @if ($data->seatmap_id != null && $data->module->is_install == 1 && $data->module->is_enable == 1)

                                    @include('seatmap::seatmapView', [
                                        'seat_map' => $data->seat_map,
                                        'rows' => $data->rows,
                                        'seatsByRow' => $data->seatsByRow,
                                        'seatLimit' => $data->ticket_per_order,
                                    ])

                                @endif

                                <input type="hidden"
                                    name="usr_auth"
                                    value="{{ Auth::guard('appuser')->check() }}">

                                @if (!Auth::guard('appuser')->check())

                                    <div
                                        class="card w-full shadow-lg p-5 rounded-lg bg-white xlg:w-full xmd:w-full 3xl:mb-0 xl:mb-0 xlg:mb-5 xxsm:mb-5 mt-5">

                                        <p
                                            class="font-poppins font-semibold text-2xl leading-8 text-black">

                                            Customer Details

                                        </p>

                                        <div class="card-body mt-2">

                                            <div>

                                                <div
                                                    class="flex border-b border-gray-200"
                                                    id="tabs">

                                                    <button
                                                        data-tab="tab1"
                                                        class="tab-button px-4 py-2 text-medium font-medium text-gray-500 focus:outline-none border-b-2">

                                                        Enter your details

                                                    </button>

                                                </div>

                                                <div class="mt-4">

                                                    <div
                                                        id="tab1"
                                                        class="tab-content p-4 bg-gray-50 border border-gray-200 rounded">

                                                        <div
                                                            class="grid grid-cols-2 gap-5 sm:grid-cols-2 msm:grid-cols-2 xxsm:grid-cols-1">

                                                            <div class="pt-5">

                                                                <label
                                                                    class="text-base font-medium leading-6 text-black font-poppins">

                                                                    First Name

                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="usr_first_name"
                                                                    required
                                                                    class="w-full text-sm font-poppins font-normal text-black block p-3 rounded-lg border border-gray-light focus:outline-none"
                                                                    placeholder="First Name">

                                                            </div>

                                                            <div class="pt-5">

                                                                <label
                                                                    class="text-base font-medium leading-6 text-black font-poppins">

                                                                    Last Name

                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="usr_last_name"
                                                                    required
                                                                    class="w-full text-sm font-poppins font-normal text-black block p-3 rounded-lg border border-gray-light focus:outline-none"
                                                                    placeholder="Last Name">

                                                            </div>

                                                            <div class="pt-5">

                                                                <label
                                                                    class="text-base font-medium leading-6 text-black font-poppins">

                                                                    Email

                                                                </label>

                                                                <input
                                                                    type="email"
                                                                    name="usr_email"
                                                                    required
                                                                    class="w-full text-sm font-poppins font-normal text-black block p-3 rounded-lg border border-gray-light focus:outline-none"
                                                                    placeholder="Your Email">

                                                            </div>

                                                            <div class="pt-5">

                                                                <label
                                                                    class="text-base font-medium leading-6 text-black font-poppins">

                                                                    Phone Number

                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="usr_phone"
                                                                    required
                                                                    class="w-full text-sm font-poppins font-normal text-black block p-3 rounded-lg border border-gray-light focus:outline-none"
                                                                    placeholder="Phone Number">

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @endif

                                @if ($data->available_qty > 0)

                                    <div
                                        class="w-full shadow-lg p-5 rounded-lg bg-white xlg:w-full xmd:w-full 3xl:mb-0 xl:mb-0 xlg:mb-5 xxsm:mb-5 mt-5">

                                        @if (!Auth::guard('appuser')->check())

                                            <div
                                                id="alert"
                                                class="hidden flex items-center p-4 mb-0 border rounded-lg bg-blue-50 border-blue-300 text-blue-800"
                                                role="alert">

                                                <svg
                                                    aria-hidden="true"
                                                    class="flex-shrink-0 w-5 h-5 mr-2"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">

                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zm-8 3a1 1 0 01-.707-.293l-3-3a1 1 0 111.414-1.414L10 10.586l2.293-2.293c.781-.781 2.048-.781 2.828 0s.781 2.048 0 1.414 0 1.414l-3 3A1 1 0 0110 13zm0-10a7 7 0 100 14 7 7 0 000-14z"
                                                        clip-rule="evenodd">
                                                    </path>

                                                </svg>

                                                <span class="sr-only">Info</span>

                                                <div>

                                                    <span class="font-medium">
                                                        Info alert!
                                                    </span>

                                                    Before choosing your payment method, ensure you provide your account
                                                    details.

                                                </div>

                                            </div>

                                        @endif

                                        <p
                                            class="font-poppins font-semibold text-2xl leading-8 text-black pb-3 pt-5">

                                            {{ __('Payment Methods') }}

                                        </p>

                                        <div
                                            class="flex md:space-x-5 md:flex-row md:space-y-0 sm:flex-col sm:space-x-0 sm:space-y-5 xxsm:flex-col xxsm:space-x-0 xxsm:space-y-5 mb-5 payments">

                                            <?php $setting = App\Models\PaymentSetting::find(1); ?>

                                            @if (strtoupper($data->type) == 'FREE')

                                                {{-- FREE OPTION --}}
                                                <div
                                                    class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex items-center">

                                                    <input
                                                        id="Free"
                                                        type="radio"
                                                        value="FREE"
                                                        name="payment_type"
                                                        class="h-5 w-5 mr-2 border border-gray-light hover:border-gray-light focus:outline-none">

                                                    <label
                                                        for="Free"
                                                        class="font-poppins font-medium text-black cursor-pointer">

                                                        FREE

                                                    </label>

                                                </div>

                                                {{-- M-PESA OPTION --}}
                                                <div
                                                    class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex items-center">

                                                    <input
                                                        id="Mpesa"
                                                        type="radio"
                                                        value="MPESA"
                                                        name="payment_type"
                                                        class="h-5 w-5 mr-2 border border-gray-light hover:border-gray-light focus:outline-none">

                                                    <label
                                                        for="Mpesa"
                                                        class="font-poppins font-medium text-black cursor-pointer">

                                                        M-Pesa

                                                    </label>

                                                </div>

                                            @else

                                                {{-- M-PESA FOR PAID TICKETS --}}
                                                <div
                                                    class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex items-center">

                                                    <input
                                                        id="Mpesa"
                                                        required
                                                        type="radio"
                                                        value="MPESA"
                                                        name="payment_type"
                                                        class="h-5 w-5 mr-2 border border-gray-light hover:border-gray-light focus:outline-none">

                                                    <label
                                                        for="Mpesa"
                                                        class="font-poppins font-medium text-black cursor-pointer">

                                                        M-Pesa

                                                    </label>

                                                </div>

                                                @if ($setting->paypal == 1)

                                                    <div
                                                        class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex align-middle">

                                                        <input
                                                            id="Paypal"
                                                            required
                                                            type="radio"
                                                            value="PAYPAL"
                                                            name="payment_type"
                                                            class="h-5 w-5 mr-2 border border-gray-light hover:border-gray-light focus:outline-none">

                                                        <label for="Paypal">

                                                            <img
                                                                src="{{ asset('images/payments/paypal.svg') }}"
                                                                alt=""
                                                                class="object-contain">

                                                        </label>

                                                    </div>

                                                @endif

                                                @if ($setting->razor == 1)

                                                    <div
                                                        class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex">

                                                        <input
                                                            id="Razor"
                                                            required
                                                            type="radio"
                                                            value="RAZOR"
                                                            name="payment_type"
                                                            class="h-5 w-5 mr-2 border border-gray-light hover:border-gray-light focus:outline-none">

                                                        <label for="Razor">

                                                            <img
                                                                src="{{ asset('images/payments/razorpay.svg') }}"
                                                                alt=""
                                                                class="object-contain">

                                                        </label>

                                                    </div>

                                                @endif

                                                @if ($setting->paystack == 1)

                                                    <div
                                                        class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex">

                                                        <input
                                                            id="Paystack"
                                                            required
                                                            type="radio"
                                                            value="PAYSTACK"
                                                            name="payment_type"
                                                            class="h-5 w-5 mr-2 border border-gray-light hover:border-gray-light focus:outline-none">

                                                        <input
                                                            type="hidden"
                                                            name="paystack_key"
                                                            value="{{ \App\Models\PaymentSetting::find(1)->paystackPublicKey ?? '' }}">

                                                        <label for="paystack">

                                                            <img
                                                                src="{{ url('images/payments/paystack.svg') }}"
                                                                alt=""
                                                                class="object-contain">

                                                        </label>

                                                    </div>

                                                @endif

                                                @if ($setting->stripe == 1)

                                                    <div
                                                        class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex">

                                                        <input
                                                            id="Stripe"
                                                            required
                                                            type="radio"
                                                            value="STRIPE"
                                                            name="payment_type"
                                                            class="h-5 w-5 mr-2 border border-gray-light hover:border-gray-light focus:outline-none">

                                                        <label for="Stripe">

                                                            <img
                                                                src="{{ url('images/payments/stripe.svg') }}"
                                                                alt=""
                                                                class="object-contain">

                                                        </label>

                                                    </div>

                                                @endif

                                                @if ($setting->flutterwave == 1)

                                                    <div
                                                        class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex">

                                                        <input
                                                            id="Flutterwave"
                                                            required
                                                            type="radio"
                                                            value="FLUTTERWAVE"
                                                            name="payment_type">

                                                        <label for="Flutterwave">

                                                            <img
                                                                src="{{ url('images/payments/flutterwave.svg') }}"
                                                                alt=""
                                                                class="object-contain">

                                                        </label>

                                                    </div>

                                                @endif

                                                @if ($setting->wallet == 1)

                                                    <div
                                                        class="border border-gray-light p-5 rounded-lg text-gray-100 w-full font-normal font-poppins text-base leading-6 flex">

                                                        <input
                                                            id="wallet"
                                                            type="radio"
                                                            value="wallet"
                                                            name="payment_type"
                                                            class="h-5 w-5 mr-2 border border-gray-light hover:border-gray-light focus:outline-none">

                                                        <label for="wallet">

                                                            <img
                                                                src="{{ url('images/payments/wallet.svg') }}"
                                                                alt=""
                                                                class="object-contain">

                                                        </label>

                                                    </div>

                                                @endif

                                            @endif

                                        </div>


                                        {{-- ========================================================= --}}
                                        {{-- M-PESA INFORMATION - HIDDEN UNTIL M-PESA IS SELECTED --}}
                                        {{-- ========================================================= --}}

                                        <div
                                            id="mpesa-section"
                                            class="hidden mt-4 p-5 border border-gray-light rounded-lg bg-gray-50">

                                            <label
                                                for="mpesa_phone"
                                                class="font-poppins font-medium text-black text-base">

                                                M-Pesa Phone Number

                                            </label>

                                            <input
                                                type="text"
                                                id="mpesa_phone"
                                                name="mpesa_phone"
                                                placeholder="2547XXXXXXXX"
                                                value=""
                                                class="w-full mt-3 p-3 rounded-lg border border-gray-light focus:outline-none">

                                            <p class="text-sm text-gray-500 mt-2">

                                                Enter your Safaricom number, e.g. 254708374149

                                            </p>


                                            @if (strtoupper($data->type) == 'FREE')

                                                <label
                                                    for="mpesa_amount"
                                                    class="font-poppins font-medium text-black text-base block mt-4">

                                                    Amount to Pay

                                                </label>

                                                <input
                                                    type="number"
                                                    id="mpesa_amount"
                                                    name="mpesa_amount"
                                                    min="1"
                                                    step="1"
                                                    placeholder="Enter amount in KES"
                                                    class="w-full mt-3 p-3 rounded-lg border border-gray-light focus:outline-none">

                                                <p class="text-sm text-gray-500 mt-2">

                                                    This ticket is free. You can either get it for FREE or choose an amount
                                                    to pay using M-Pesa.

                                                </p>

                                            @else

                                                <input
                                                    type="hidden"
                                                    id="mpesa_amount"
                                                    name="mpesa_amount"
                                                    value="{{ $data->price + $data->tax_total }}">

                                            @endif


                                            <div
                                                id="mpesa-message"
                                                class="mt-3 text-sm">
                                            </div>

                                        </div>


                                        <div class="paypal-button-section mt-4 mx-auto">

                                            <div
                                                id="paypal-button-container"
                                                class="hidden">
                                            </div>

                                        </div>


                                        <div
                                            class="card stripeCard hidden"
                                            id="stripeform">

                                            <div
                                                class="bg-danger text-white hidden stripe_alert rounded-lg py-5 px-6 mb-3 text-base text-red-700 inline-flex items-center w-full"
                                                role="alert">

                                                <svg
                                                    aria-hidden="true"
                                                    focusable="false"
                                                    data-prefix="fas"
                                                    data-icon="times-circle"
                                                    class="w-4 h-4 mr-2 fill-current"
                                                    role="img"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 512 512">

                                                    <path
                                                        fill="currentColor"
                                                        d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-17 0-17l39.6-39.6c12.3-12.3 32.3-12.3 44.6 0l39.6 39.6c12.3 12.3 32.3 12.3 44.6 0l39.6-39.6c12.3-12.3 32.3-12.3 44.6 0l39.6 39.6c12.3 12.3 12.3 17 0 17L377.6 256l65.6 65.1z">
                                                    </path>

                                                </svg>

                                                <div class="stripeText"></div>

                                            </div>

                                            <div class="card-body">

                                                <form
                                                    method="post"
                                                    class="require-validation customform xxxl:w-[680px] s:w-[225px] m:w-[300px] l:w-[400px] sm:w-[320px] md:w-[450px] lg:w-[300px] xl:w-[540px] xxl:w-[550px]"
                                                    data-cc-on-file="false"
                                                    id="stripe-payment-form">

                                                    @csrf

                                                    <div>

                                                        <div class="mb-3">

                                                            <div class="form-group">

                                                                <label
                                                                    for="email"
                                                                    class="font-poppins font-medium text-black text-base tracking-wide">

                                                                    {{ __('Email') }}

                                                                </label>

                                                                <input
                                                                    type="email"
                                                                    name="card_email"
                                                                    title="Enter Your Email"
                                                                    placeholder="Email"
                                                                    class="email form-control required border border-gray-light focus:outline-none rounded-lg p-3 w-full mt-3" />

                                                            </div>

                                                        </div>

                                                        <div>

                                                            <div class="form-group">

                                                                <label
                                                                    for="card-number"
                                                                    class="font-poppins font-medium text-black text-base tracking-wide">

                                                                    {{ __('Card Information') }}

                                                                </label>

                                                                <div class="form-group">
                                                                    <div id="card-number"></div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <div id="card-expiry"></div>
                                                                </div>

                                                                <input
                                                                    type="hidden"
                                                                    class="card-expiry-month required form-control"
                                                                    name="card-expiry-month" />

                                                                <input
                                                                    type="hidden"
                                                                    class="card-expiry-year required form-control"
                                                                    name="card-expiry-year" />

                                                                <div class="form-group">
                                                                    <div id="card-cvc"></div>
                                                                </div>

                                                            </div>

                                                            <div class="form-group mt-3">

                                                                <label
                                                                    class="font-poppins font-medium text-black text-base tracking-wide">

                                                                    {{ __('Name on card') }}

                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    class="required form-control border border-gray-light focus:outline-none rounded-lg p-3 w-full mt-3"
                                                                    name="card_name"
                                                                    placeholder="Name"
                                                                    title="Name on Card"
                                                                    required />

                                                            </div>

                                                        </div>

                                                        <div class="form-group text-start">

                                                            <button
                                                                type="submit"
                                                                class="bg-primary l:w-[250px] h-[47px] s:w-full px-5 p-2 rounded-md cursor-pointer font-poppins font-medium text-white text-lg mt-4 btn-submit">

                                                                {{ __('Pay with stripe') }}

                                                            </button>

                                                        </div>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>


                                        <div class="mt-3">

                                            <button
                                                type="button"
                                                id="form_submit"
                                                onclick="processCheckout(event)"
                                                class="font-poppins font-medium text-lg leading-6 text-white bg-primary w-full rounded-md py-3">

                                                <div id="formtext">

                                                    <i class="fa pr-2 fa-check-square"></i>

                                                    {{ __('Place Order') }}

                                                </div>

                                                <div
                                                    id="formloader"
                                                    class="hidden mx-auto animate-spin rounded-full border-t-2 border-blue-500 border-solid h-7 w-7">
                                                </div>

                                            </button>

                                        </div>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                    @if ($data->type == 'paid')

                        <div class="xlg:w-[25%] xxmd:w-full xxsm:w-full">

                            <div class="p-4 bg-white shadow-lg rounded-md space-y-5">

                                <p
                                    class="font-poppins font-semibold text-2xl leading-8 text-black pb-3">

                                    {{ __('Payment Summary') }}

                                </p>

                                <div
                                    class="flex justify-between border border-primary rounded-md py-5 xxsm:flex-wrap sm:flex-nowrap xlg:px-0">

                                    <input
                                        type="text"
                                        value=""
                                        name="coupon_code"
                                        id="coupon_id"
                                        class="focus:outline-none font-poppins font-normal text-base leading-6 text-white-100 ml-5 1xl:w-44 xl:w-36 xlg:w-28"
                                        placeholder="{{ __('Coupon Code') }}">

                                    <button
                                        type="button"
                                        id="apply"
                                        name="apply"
                                        class="font-poppins font-medium text-base leading-6 text-primary focus:outline-none mr-5">

                                        {{ __('Apply') }}

                                    </button>

                                </div>

                                <div class="couponerror"></div>

                                @if (count($data->tax) >= 1)

                                    <p
                                        class="font-poppins font-semibold text-base leading-8 text-black">

                                        {{ __('Taxes and Charges') }}

                                    </p>

                                    <div class="taxes border border-primary rounded-md p-2">

                                        @foreach ($data->tax as $key => $item)

                                            <input
                                                type="hidden"
                                                class="amount_type"
                                                name="amount_type"
                                                value="{{ $item->amount_type }}">

                                            <div class="flex justify-between">

                                                <p
                                                    class="font-poppins font-normal text-lg leading-7 text-gray-200">

                                                    {{ $item->name }}

                                                    @if ($item->amount_type == 'percentage')
                                                        ({{ $item->price . '%' }})
                                                    @endif

                                                </p>

                                                <p
                                                    class="font-poppins font-medium text-lg leading-7 text-gray-300">

                                                    @if ($item->amount_type == 'percentage')

                                                        @php
                                                            $result = ($data->price * $item->price) / 100;
                                                            $formattedResult = round($result, 2);
                                                        @endphp

                                                        {{ $currency }} {{ $formattedResult }}

                                                    @else

                                                        {{ $currency }} {{ $item->price }}

                                                    @endif

                                                </p>

                                            </div>

                                        @endforeach

                                    </div>

                                @endif

                                <div class="flex justify-between">

                                    <p
                                        class="font-poppins font-normal text-lg leading-7 text-gray-200">

                                        {{ __('Total Tax amount') }}

                                    </p>

                                    <p
                                        class="font-poppins font-medium text-lg leading-7 text-gray-300 totaltax">

                                        {{ $currency }}{{ $data->tax_total }}

                                    </p>

                                </div>

                                <div class="flex justify-between">

                                    <p
                                        class="font-poppins font-normal text-lg leading-7 text-gray-200">

                                        {{ __('Tickets amount') }}

                                    </p>

                                    <p
                                        class="font-poppins font-medium text-lg leading-7 text-gray-300">

                                        {{ $currency }}{{ $data->price }}

                                    </p>

                                </div>

                                <div
                                    class="flex justify-between border-dashed border-b border-gray-light pb-5">

                                    <p
                                        class="font-poppins font-normal text-lg leading-7 text-gray-200">

                                        {{ __('Coupon discount') }}

                                    </p>

                                    <p
                                        class="font-poppins font-medium text-lg leading-7 text-gray-300 discount">

                                        00.00

                                    </p>

                                </div>

                                <div class="flex justify-between">

                                    <p
                                        class="font-poppins font-semibold text-xl leading-7 text-primary xlg:text-lg 1xl:text-xl">

                                        {{ __('Total amount') }}

                                    </p>

                                    <p
                                        class="font-poppins font-semibold text-2xl leading-7 text-primary xlg:text-lg 1xl:text-2xl subtotal">

                                        @if ($data->seatmap_id == null || $data->module->is_enable == 0)

                                            {{ $currency }}{{ $data->price + $data->tax_total }}

                                        @endif

                                    </p>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CHECKOUT JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        function processCheckout(event) {

            event.preventDefault();

            const paymentType =
                $('input[name="payment_type"]:checked').val();

            if (!paymentType) {

                alert('Please select a payment method.');

                return;

            }


            // FREE TICKET
            if (paymentType === 'FREE') {

                guestFreeOrder(event);

                return;

            }


            // M-PESA
            if (paymentType === 'MPESA') {

                mpesaPayment(event);

                return;

            }


            alert(
                'This payment method is handled by the existing payment gateway.'
            );

        }


        /*
         * =========================================================
         * FREE TICKET
         * =========================================================
         */

        function guestFreeOrder(event) {

            event.preventDefault();

            const firstName =
                $('input[name="usr_first_name"]').val();

            const lastName =
                $('input[name="usr_last_name"]').val();

            const email =
                $('input[name="usr_email"]').val();

            const phone =
                $('input[name="usr_phone"]').val();


            if (!firstName || !lastName || !email || !phone) {

                alert(
                    'Please enter First Name, Last Name, Email and Phone Number.'
                );

                return;

            }


            const button = $('#form_submit');

            button.prop('disabled', true);

            $('#formtext').addClass('hidden');

            $('#formloader').removeClass('hidden');


            let formData =
                $('#ticketorder')
                .find('input, select, textarea')
                .serializeArray();


            formData.push({
                name: 'payment_type',
                value: 'FREE'
            });


            formData.push({
                name: 'payment',
                value: '0'
            });


            $.ajax({

                url: '{{ url('/createOrder') }}',

                type: 'POST',

                data: $.param(formData),


                success: function(data) {

                    console.log(
                        'ORDER SUCCESS:',
                        data
                    );


                    if (data.success) {

                        window.location.href =
                            '/show-details/' +
                            data.order_id;

                    } else {

                        alert(
                            data.message ||
                            'Order failed.'
                        );

                        resetOrderButton();

                    }

                },


                error: function(xhr) {

                    console.error(
                        'ORDER ERROR:',
                        xhr.status,
                        xhr.responseText
                    );


                    let message =
                        'Order failed.';


                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;

                    }


                    alert(message);

                    resetOrderButton();

                }

            });


            function resetOrderButton() {

                button.prop(
                    'disabled',
                    false
                );

                $('#formtext')
                    .removeClass('hidden');

                $('#formloader')
                    .addClass('hidden');

            }

        }


        /*
         * =========================================================
         * M-PESA PAYMENT
         * =========================================================
         */

        function mpesaPayment(event) {

            event.preventDefault();


            const firstName =
                $('input[name="usr_first_name"]').val();

            const lastName =
                $('input[name="usr_last_name"]').val();

            const email =
                $('input[name="usr_email"]').val();

            const phone =
                $('input[name="usr_phone"]').val();


            const mpesaPhone =
                $('#mpesa_phone')
                .val()
                .trim();


            const mpesaAmount =
                parseFloat(
                    $('#mpesa_amount').val()
                );


            if (!firstName || !lastName || !email || !phone) {

                alert(
                    'Please enter First Name, Last Name, Email and Phone Number.'
                );

                return;

            }


            if (!/^2547\d{8}$/.test(mpesaPhone)) {

                alert(
                    'Please enter a valid Safaricom number in the format 2547XXXXXXXX.'
                );

                return;

            }


            if (!mpesaAmount || mpesaAmount <= 0) {

                alert(
                    'Please enter a valid M-Pesa amount.'
                );

                return;

            }


            const button =
                $('#form_submit');


            button.prop(
                'disabled',
                true
            );


            $('#formtext')
                .addClass('hidden');


            $('#formloader')
                .removeClass('hidden');


            $('#mpesa-message')
                .removeClass(
                    'text-danger text-success'
                )
                .addClass(
                    'text-blue-600'
                )
                .text(
                    'Sending M-Pesa payment request...'
                );


            let formData =
                $('#ticketorder')
                .find('input, select, textarea')
                .serializeArray();


            formData.push({

                name: 'payment_type',

                value: 'MPESA'

            });


            formData.push({

                name: 'payment',

                value: mpesaAmount

            });


            formData.push({

                name: 'mpesa_phone',

                value: mpesaPhone

            });


            formData.push({

                name: 'mpesa_amount',

                value: mpesaAmount

            });


            $.ajax({

                url: '{{ url('/mpesa/initiate') }}',

                type: 'POST',

                data: $.param(formData),


                success: function(data) {

                    console.log(
                        'MPESA INITIATE:',
                        data
                    );


                    if (
                        !data.success ||
                        !data.checkout_request_id
                    ) {

                        $('#mpesa-message')
                            .removeClass(
                                'text-blue-600'
                            )
                            .addClass(
                                'text-danger'
                            )
                            .text(
                                data.message ||
                                'Unable to start M-Pesa payment.'
                            );


                        resetMpesaButton();

                        return;

                    }


                    $('#mpesa-message')
                        .removeClass(
                            'text-blue-600'
                        )
                        .addClass(
                            'text-success'
                        )
                        .text(
                            'M-Pesa request sent. Please check your phone and enter your M-Pesa PIN.'
                        );


                    checkMpesaPayment(
                        data.checkout_request_id,
                        0
                    );

                },


                error: function(xhr) {

                    console.error(
                        'MPESA INITIATE ERROR:',
                        xhr.status,
                        xhr.responseText
                    );


                    let message =
                        'Unable to start M-Pesa payment.';


                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;

                    }


                    $('#mpesa-message')
                        .removeClass(
                            'text-blue-600'
                        )
                        .addClass(
                            'text-danger'
                        )
                        .text(message);


                    alert(message);


                    resetMpesaButton();

                }

            });


            /*
             * CHECK PAYMENT
             */

            function checkMpesaPayment(
                checkoutRequestId,
                attempt
            ) {

                if (attempt >= 12) {

                    $('#mpesa-message')
                        .removeClass(
                            'text-blue-600 text-success'
                        )
                        .addClass(
                            'text-danger'
                        )
                        .text(
                            'M-Pesa payment timed out. Please try again.'
                        );


                    resetMpesaButton();

                    return;

                }


                setTimeout(function() {

                    $.ajax({

                        url: '{{ url('/mpesa/query') }}',

                        type: 'POST',

                        data: {

                            _token:
                                $('input[name="_token"]').val(),

                            checkout_request_id:
                                checkoutRequestId

                        },


                        success: function(data) {

                            console.log(
                                'MPESA QUERY:',
                                data
                            );


                            /*
                             * PAYMENT SUCCESS
                             */

                            if (
                                data.success &&
                                data.status === 'paid'
                            ) {

                                $('#mpesa-message')
                                    .removeClass(
                                        'text-blue-600'
                                    )
                                    .addClass(
                                        'text-success'
                                    )
                                    .text(
                                        'Payment successful. Creating your ticket...'
                                    );


                                if (data.order_id) {

                                    window.location.href =
                                        '/show-details/' +
                                        data.order_id;

                                }

                                return;

                            }


                            /*
                             * USER CANCELLED
                             */

                            if (
                                String(data.result_code) === '1032'
                            ) {

                                $('#mpesa-message')
                                    .removeClass(
                                        'text-blue-600 text-success'
                                    )
                                    .addClass(
                                        'text-danger'
                                    )
                                    .text(
                                        'M-Pesa payment was cancelled.'
                                    );


                                resetMpesaButton();

                                return;

                            }


                            /*
                             * STILL WAITING
                             */

                            if (
                                String(data.result_code) === '1037'
                            ) {

                                $('#mpesa-message')
                                    .removeClass(
                                        'text-danger text-success'
                                    )
                                    .addClass(
                                        'text-blue-600'
                                    )
                                    .text(
                                        'Waiting for payment confirmation...'
                                    );


                                checkMpesaPayment(
                                    checkoutRequestId,
                                    attempt + 1
                                );

                                return;

                            }


                            /*
                             * OTHER / PENDING
                             */

                            checkMpesaPayment(
                                checkoutRequestId,
                                attempt + 1
                            );

                        },


                        error: function(xhr) {

                            console.error(
                                'MPESA QUERY ERROR:',
                                xhr.status,
                                xhr.responseText
                            );


                            checkMpesaPayment(
                                checkoutRequestId,
                                attempt + 1
                            );

                        }

                    });

                }, 5000);

            }


            function resetMpesaButton() {

                button.prop(
                    'disabled',
                    false
                );

                $('#formtext')
                    .removeClass('hidden');

                $('#formloader')
                    .addClass('hidden');

            }

        }

    </script>

@endsection


@section('js')

    {{-- DATE PICKER --}}
    <script>

        $(document).ready(function() {

            $("#onetime").flatpickr({

                dateFormat: 'Y-m-d',

                minDate:
                    '{{ $data->event->start_time }}',

                maxDate:
                    '{{ $data->event->end_time }}'

            });

        });

    </script>


    <script src="https://checkout.flutterwave.com/v3.js"></script>


    <script type="text/javascript">

        document.addEventListener(
            'DOMContentLoaded',
            () => {


                /*
                 * =====================================================
                 * TABS
                 * =====================================================
                 */

                const tabButtons =
                    document.querySelectorAll(
                        '.tab-button'
                    );


                const tabContents =
                    document.querySelectorAll(
                        '.tab-content'
                    );


                const localStorageKey =
                    'activeTab';


                tabButtons.forEach(button => {

                    button.addEventListener(
                        'click',
                        () => {

                            tabButtons.forEach(
                                btn =>
                                    btn.classList.remove(
                                        'active'
                                    )
                            );


                            tabContents.forEach(
                                content =>
                                    content.classList.add(
                                        'hidden'
                                    )
                            );


                            button.classList.add(
                                'active'
                            );


                            const tabId =
                                button.getAttribute(
                                    'data-tab'
                                );


                            document
                                .getElementById(tabId)
                                .classList.remove(
                                    'hidden'
                                );

                        }
                    );

                });


                /*
                 * ALERT
                 */

                const alertBox =
                    document.getElementById(
                        'alert'
                    );


                if (alertBox) {

                    alertBox.classList.remove(
                        'hidden'
                    );

                }


                /*
                 * DISMISS BUTTONS
                 */

                const dismissButtons =
                    document.querySelectorAll(
                        '[data-dismiss-target]'
                    );


                dismissButtons.forEach(
                    button => {

                        button.addEventListener(
                            'click',
                            () => {

                                const targetId =
                                    button.getAttribute(
                                        'data-dismiss-target'
                                    );


                                const targetElement =
                                    document.querySelector(
                                        targetId
                                    );


                                if (targetElement) {

                                    targetElement.classList.add(
                                        'hidden'
                                    );

                                }

                            }
                        );

                    }
                );


                /*
                 * FIRST TAB
                 */

                if (tabButtons.length > 0) {

                    tabButtons[0].classList.add(
                        'active'
                    );

                }


                if (tabContents.length > 0) {

                    tabContents[0].classList.remove(
                        'hidden'
                    );

                }


                /*
                 * PASSWORD
                 */

                const togglePassword =
                    document.querySelector(
                        "#togglePassword"
                    );


                if (togglePassword) {

                    const password =
                        document.querySelector(
                            "#password"
                        );


                    togglePassword.addEventListener(
                        "click",
                        function(e) {

                            const type =
                                password.getAttribute(
                                    "type"
                                ) === "password"
                                    ? "text"
                                    : "password";


                            password.setAttribute(
                                "type",
                                type
                            );


                            this.classList.toggle(
                                "fa-eye-slash"
                            );

                        }
                    );

                }


                /*
                 * TAB ACTIVATION
                 */

                const activateTab =
                    (tabId) => {

                        tabButtons.forEach(
                            btn =>
                                btn.classList.remove(
                                    'active'
                                )
                        );


                        tabContents.forEach(
                            content =>
                                content.classList.add(
                                    'hidden'
                                )
                        );


                        const activeButton =
                            document.querySelector(
                                `[data-tab="${tabId}"]`
                            );


                        const activeContent =
                            document.getElementById(
                                tabId
                            );


                        if (
                            activeButton &&
                            activeContent
                        ) {

                            activeButton.classList.add(
                                'active'
                            );


                            activeContent.classList.remove(
                                'hidden'
                            );

                        }


                        localStorage.setItem(
                            localStorageKey,
                            tabId
                        );

                    };


                tabButtons.forEach(button => {

                    button.addEventListener(
                        'click',
                        () => {

                            const tabId =
                                button.getAttribute(
                                    'data-tab'
                                );


                            activateTab(
                                tabId
                            );

                        }
                    );

                });


                const savedTab =
                    localStorage.getItem(
                        localStorageKey
                    ) || 'tab1';


                activateTab(
                    savedTab
                );


                /*
                 * =====================================================
                 * M-PESA SHOW / HIDE
                 * =====================================================
                 */

                const paymentInputs =
                    document.querySelectorAll(
                        'input[name="payment_type"]'
                    );


                const mpesaSection =
                    document.getElementById(
                        'mpesa-section'
                    );


                paymentInputs.forEach(
                    input => {

                        input.addEventListener(
                            'change',
                            function() {

                                if (
                                    this.value === 'MPESA'
                                ) {

                                    /*
                                     * SHOW M-PESA FIELDS
                                     */

                                    if (mpesaSection) {

                                        mpesaSection.classList.remove(
                                            'hidden'
                                        );

                                    }

                                } else {

                                    /*
                                     * HIDE M-PESA FIELDS
                                     */

                                    if (mpesaSection) {

                                        mpesaSection.classList.add(
                                            'hidden'
                                        );

                                    }


                                    /*
                                     * CLEAR OLD M-PESA DATA
                                     */

                                    $('#mpesa_phone')
                                        .val('');

                                    $('#mpesa_amount')
                                        .val('');

                                    $('#mpesa-message')
                                        .text('')
                                        .removeClass(
                                            'text-danger text-success text-blue-600'
                                        );

                                }

                            }
                        );

                    }
                );


                /*
                 * IMPORTANT:
                 * M-PESA IS HIDDEN WHEN PAGE FIRST LOADS.
                 */

                if (mpesaSection) {

                    mpesaSection.classList.add(
                        'hidden'
                    );

                }

            }

        );

    </script>


    <script>

        $('#location').select2();

    </script>


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endsection



