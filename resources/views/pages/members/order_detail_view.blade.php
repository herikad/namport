
@if(isset($order_detail_arr) && count($order_detail_arr) > 0)

    @foreach($order_detail_arr as $k => $order_detail) 
 
        <div class="row p-3 m-1 rounded col-12 border border-primary">
            @foreach($order_detail as $key => $value) 
            
                <div class="media w-100">
                
                    <div class="mt-1 mb-1">

                        <dl class="row m-0">

                            @if(isset($value->item_type) && $value->item_type == config('custom.cart_item_type.trainer_booking'))

                                <div class="col-2 mb-1">
                                    <dt class="text-bold-700 mb-25">Trainer Image</dt>
                                    <dd class="text-wrap">
                                        <img src="{{ isset($value->member_img) ? config('custom.image_base_url') . $value->member_img : asset('/no_image.jpg') }}"
                                        alt="" class="object-fit-md-contain rounded-2"
                                        height="50" width="50" onerror="this.src='{{ asset('/no_image.jpg') }}'"
                                        style="object-fit: cover;">
                                    </dd>
                                </div>
                                <div class="col-2 mb-1">
                                    <dt class="text-bold-700 mb-25">Trainer Name</dt>
                                    <dd class="text-wrap">{{ $value->member_name}}</dd>
                                </div>
                                
                            @endif

                            @if(isset($value->item_type) && $value->item_type == config('custom.cart_item_type.club_booking'))

                                <div class="col-2 mb-1">
                                    <dt class="text-bold-700 mb-25">Club Image</dt>
                                    <dd class="text-wrap">
                                        <img src="{{ isset($value->club_pic) ? config('custom.image_base_url') . $value->member_img : asset('/no_image.jpg') }}"
                                        alt="" class="object-fit-md-contain rounded-2"
                                        height="50" width="50" onerror="this.src='{{ asset('/no_image.jpg') }}'"
                                        style="object-fit: cover;">
                                    </dd>
                                </div>
                                
                                <div class="col-2 mb-1">
                                    <dt class="text-bold-700 mb-25">Club Name</dt>
                                    <dd class="text-wrap"><a href="{{ route('club.view',['club_id' => $value->club_id]) }}" target="_blank" class="primary-text-color" title="View Club"> {{ $value->club_name}} </a></dd>
                                </div>

                                <div class="col-2 mb-1">
                                    <dt class="text-bold-700 mb-25">Court Title</dt>
                                    <dd class="text-wrap"><a href="{{ route('court.view',['court_id' => $value->court_id]) }}" target="_blank" class="primary-text-color" title="View Court"> {{$value->court_title}} </a></dd>
                                </div>
                            @endif

                            <div class="col-2 mb-1">
                                <dt class="text-bold-700 mb-25">Total Amount</dt>
                                <dd class="text-wrap">{{config('custom.default_currency')}}{{ number_format($value->total_amount,2)}}</dd>
                            </div>

                            <div class="col-2 mb-1">
                                <dt class="text-bold-700 mb-25">Discount Amount</dt>
                                <dd class="text-wrap">{{config('custom.default_currency')}}{{ number_format($value->discount_amount,2)}}</dd>
                            </div>

                            <div class="col-2 mb-1">
                                <dt class="text-bold-700 mb-25">SubTotal Amount</dt>
                                <dd class="text-wrap">{{config('custom.default_currency')}}{{ number_format($value->subtotal_amount,2)}}</dd>
                            </div>

                            <div class="col-2 mb-1">
                                <dt class="text-bold-700 mb-25">Order Status</dt>
                                <dd class="text-wrap">{!! \Helper::showOrderStatusBadge($value->order_item_status) !!}</dd>
                            </div>

                        </dl>

                    </div>
                </div>
                @if(count($order_detail) > 1)
                    <hr>
                @endif
            @endforeach
        </div>
    @endforeach
@else
   <div class="row shadow-none p-50 m-0 bg-white rounded col-12 border border-primary text-center">
        <div class="media w-100">  
            <div class="mt-1 mb-1">
                Data Not Available!
            </div>
        </div>
    </div>
@endif