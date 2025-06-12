@if($page == 1 && count($list) == 0)
    <div class="card shadow-none border p-2 mb-3">
        <p class="text-center mt-3">Data Unavailable! </p>
    </div>
@else
    @foreach ($list as $k =>  $connection)
    <div class="border connection d-flex p-2 mb-3">
        <div class="flex-shrink-0 me-3 mt-3">
            <div id="profile_pic_1_preview" class="image-fixed flex-shrink-0">
                <img class="rounded-circle"
                    src="{{ isset($connection->profile_pic) ? config('custom.image_base_url') . $connection->profile_pic : asset('/no_image.jpg') }}"
                    alt=""
                    onerror="this.src='{{ asset('/no_image.jpg') }}'"
                    class="object-fit-md-contain rounded-2"
                    height="40" width="40"
                    style="object-fit: cover;">
            </div>
        </div>
    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
        <div class="row col-md-12">
            <div class="col-md-8">
                <div class="me-2 mt-3">
                    <p class="mb-0 lh-1"> 
                        <a class="primary-text-color" href="{{ route('members.view', $connection->member_id) }}">{{ $connection->member_name }}</a>
                    </p>
                    <p>{{ $connection->nickname }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="me-2 mt-3">
                    <p>
                        @if (isset($connection->avg_rating))
                            {{ $connection->avg_rating }}
                        @else
                            0
                        @endif
                        <i class="fas fa-star star_color"></i>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
    @endforeach

@endif