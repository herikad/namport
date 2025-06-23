@extends('layouts.layoutMaster')

{{-- title --}}
@section('title', 'Project Edit')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
  <h5 class="py-3 breadcrumb-wrapper mb-2">
    <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> /  </span><a href="{{route('project.index')}}">Project / </a>{{$project_details->projectname}}
  </h5>
    <section>
        <div class="card">

            <div class="card-header pt-75">
                <h4 class="card-title">  {{trans('pages.edit_with_attr', ['attribute' => $project_details->projectname])}}
                </h4>
            </div>
            @php

                $enc_project_id = \Helper::enc($project_details->project_id);
                $overviewTab = [
                    'key' => $static_tabs[0]['key'],
                    'label' => $static_tabs[0]['label'],
                    'dynamic' => false,
                    'url' => route('project.tab_static', ['project_id' => $enc_project_id,'tab' => $static_tabs[0]['key']])
                ];

                $dynamicLevels = $levels->map(function($level) use ($enc_project_id) {
                    return [
                        'key' => 'level_' . $level->level_no,
                        'label' => $level->level_name,
                        'dynamic' => true,
                        'url' => route('project.tab_level', ['project_id' => $enc_project_id,'level_no' => $level->level_no])
                    ];
                })->toArray();

                $otherStaticTabs = array_map(function($tab) use ($enc_project_id) {
                    return [
                        'key' => $tab['key'],
                        'label' => $tab['label'],
                        'dynamic' => false,
                        'url' => route('project.tab_static', ['project_id' => $enc_project_id,'tab' => $tab['key']])
                    ];
                }, array_slice($static_tabs, 1));

                $allSteps = array_merge([$overviewTab], $dynamicLevels, $otherStaticTabs);
            @endphp


            <ul class="nav nav-pills mb-3" role="tablist">
                @foreach ($allSteps as $index => $step)
                    <li class="nav-item" role="presentation">
                       <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="tab-{{ $step['key'] }}-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#tab-{{ $step['key'] }}"
                                type="button"
                                role="tab"
                                data-url="{{ $step['url'] }}"
                                aria-controls="tab-{{ $step['key'] }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            <div class="d-flex flex-column align-items-start">
                                <strong>{{ $loop->iteration }}. {{ $step['label'] }}</strong>
                            </div>
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach ($allSteps as $step)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                        id="tab-{{ $step['key'] }}"
                        role="tabpanel"
                        aria-labelledby="tab-{{ $step['key'] }}-tab">
                        @if ($loop->first)
                            <div class="text-center py-4">
                                <div class="spinner-border" role="status"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            

        </div>
    </section>
@endsection

{{-- vendor scripts --}}
@section('vendor-scripts')
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{ asset('extensions/ckeditor/ckeditor.js') }}" type="text/javascript"></script>

@include('scripts.project.edit_js')
@endsection
