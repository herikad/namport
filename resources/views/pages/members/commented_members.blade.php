@if($page == 1 && count($list) == 0)
    <div class="card shadow-none border p-2 mb-3">
        <p class="text-center mt-3">Data Unavailable! </p>
    </div>
@else
    @foreach ($list as $k =>  $comment)
        <div class="border comment m-1 p-1">
    
            <div class="row m-0">
                <p class="text-end m-0">
                    {{ isset($comment->comment_on) ?   \Carbon\Carbon::parse($comment->comment_on)->format('m-d-Y') : '' }}
                </p>
            </div>
        
            <div class="row d-flex img_comment m-0">
                
                <div class="col-2">
                    <img class="rounded-circle"
                    src="{{ isset($comment->profile_pic) ? config('custom.image_base_url') . $comment->profile_pic : asset('/no_image.jpg') }}"
                    alt=""
                    onerror="this.src='{{ asset('/no_image.jpg') }}'"
                    class="object-fit-md-contain rounded-2"
                    height="40" width="40"
                    style="object-fit: cover;">
                </div>
                <div class="col-10 p-0">
                    <p class="m-0"> 
                        <a class="primary-text-color text-break" href="{{ route('members.view', $comment->member_id) }}">{{ $comment->member_name }}</a>
                    </p>
                    <p class="text-break">{{ $comment->comment }}</p>
                    
                </div>
                
              
            </div>

        </div>
    @endforeach

@endif