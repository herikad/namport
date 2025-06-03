@if(isset($post) && !empty($post))
    @if($post->type_of_post == config('custom.type_of_post.normal') ) 
        @if (isset($post->media_files) && count($post->media_files) > 0)

            <div id="post_img_block" class="carousel slide p-2" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($post->media_files as $k2 => $attach)
                        @php
                            $path = $attach->image_path;
                            $fileName = basename($path);
                            $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                        @endphp
                        <div class="pt-2 carousel-item {{ $k2 == 0 ? 'active' : '' }}">


                            @if (in_array($extension, config('custom.image')))
                                    <img src="{{ asset($attach->image_path) }}"
                                        class="carousel-item-image d-block post_image img-fluid d-flex mx-auto"
                                        onerror="this.src='{{ asset('/no_image.jpg') }}'">

                            @elseif (in_array($extension, config('custom.video')))
                                <video controls autoplay muted class="video_size">
                                    <source src="{{ asset($attach->image_path) }}"
                                    type="video/mp4">
                                </video>

                            @endif
                        </div>
                    @endforeach
                </div>
                @if (count($post->media_files) > 1)
                    <button class="carousel-control-prev" type="button"
                        data-bs-target="#post_img_block" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button"
                        data-bs-target="#post_img_block" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        @else
            <div class="no_image p-2">
                <img src="{{ asset('no_image.jpg') }}" 
                class="carousel-item-image d-block post_image img-fluid d-flex mx-auto">
            </div>
        @endif
    @elseif($post->type_of_post == config('custom.type_of_post.upload_score'))
    
        <div id="post_image_block" >
            <div class="card shadow-none">
                <div class="card-body d-flex  p-1 justify-content-center">
                    @if(isset($post->score_details) &&  isset($post->score_details['score']))
                
                        @foreach ($post->score_details['score'] as $k2 => $v2 )
                        
                            <div class="card col-6 m-1">
                                <div class="card-header h5">
                                    {{ $v2['team_name'] }}
                                </div>
                                <div class="card-body d-flex justify-content-center">
                                    @if(isset($v2['players']))
                                        @foreach ( $v2['players'] as $k3 => $v3 )
                                            <div class="justify-content-center align-items-center m-2 text-center">
                                                <img class="rounded-circle"
                                                src="{{ isset($v3->player_profile) ? config('custom.image_base_url') . $v3->player_profile : asset('/no_image.jpg') }}"
                                                alt="" onerror="this.src='{{ asset('/no_image.jpg') }}'"
                                                class="object-fit-md-contain rounded-2" height="60" width="60"
                                                style="object-fit: cover;">
                                                
                                                <h6 class="m-2"> {{ $v3->player_name }}</h6>
                                                <p class="font-weight-normal"> {{ isset($v3->avg_rating) ? $v3->avg_rating : '0' }}</p>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="card-footer p-0">
                                    <div class="row m-0">
                                    @if(isset($v2['score']))
                                        @foreach ( $v2['score'] as $k4 => $v4 )
                                                <div class="col-md-4 text-center"><h5>{{ $v4 }}</h5></div>
                                        @endforeach
                                    @endif
                                </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @elseif($post->type_of_post == config('custom.type_of_post.interest_activity'))
  
        <div id="post_image_block" class="carousel slide p-2" data-bs-ride="carousel">
            <img src="{{ asset($post->club_details['profile_pic']) }}"
            class="carousel-item-image d-block post_image img-fluid d-flex mx-auto"
            onerror="this.src='{{ asset('/no_image.jpg') }}'">
        </div>
    @endif
    <div class="comment text-start p-2">
        {{ isset($post->comment) ? $post->comment : '' }}
    </div>
    <div class="comment text-start p-2 d-flex justify-content-between">
        <div class=""  data-id="{{$post->post_id}}">
            <i class="bx bx-heart fs-4"></i> {{ isset($post->total_likes) ? $post->total_likes : '0' }} Likes
        </div>
        <div class="" data-id="{{$post->post_id}}">
            <i class="bx bx-comment-dots fs-4"></i> {{ isset($post->total_comment) ? $post->total_comment : '0' }} Comments
        </div>
        @if($post->type_of_post == config('custom.type_of_post.interest_activity'))
            <div class="" data-id="{{$post->post_id}}">
                <i class="bx bx-plus-circle fs-4"></i> {{ isset($post->total_interested_user) ? $post->total_interested_user : '0' }} Interested
            </div>
        @endif
        
    </div>

@endif