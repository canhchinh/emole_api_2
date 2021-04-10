@extends('admin.layouts.default')

@section('content')
<div class="contain-users">
    <div class="row mb-2">
        <div class="col-8 search">
            <form action="" method="get">
            <img class="search-img" src="{{asset('/assets/images/user_search_icon.png')}}" id="user_search_icon">
            <input type="text" class="search-input" name="search-key" placeholder="件名を入力" value="{{ $searchKey }}">
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="block-filter">
                <div class="contain-filter">
                    @php
                        $params = request()->input();
                        unset($params['status']);
                        unset($params['page']);
                    @endphp
                    <select name="" id="filter-notify-status">
                        <option value="{{ route('admin.notify.list', array_merge(['status' => 'all'], $params)) }}">配信状況</option>
                        <option value="{{ route('admin.notify.list', array_merge(['status' => \App\Entities\Notification::STATUS_PUBLIC], $params)) }}"
                                @if ($notifyStatus == \App\Entities\Notification::STATUS_PUBLIC) selected @endif
                        >公開</option>
                        <option value="{{ route('admin.notify.list', array_merge(['status' => \App\Entities\Notification::STATUS_DRAFT], $params)) }}"
                                @if ($notifyStatus == \App\Entities\Notification::STATUS_DRAFT) selected @endif
                        >非公開</option>
                    </select>
                </div>
                <div class="contain-sort">
                    <div class="contain-sort_total">
                        <span class="only">{{ count($notifications) }}</span><span>件</span>
                    </div>
                    <a href="{{ \App\Helpers\Params::buildSortDescAsc(($arrange == 'desc') ? 'asc' : 'desc')  }}">
                        <img src="{{asset('/assets/images/sort-'.$arrange.'.png')}}" alt="sort">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row block-content">
        <div class="col-md-8">

            @foreach ($notifications as $notify)
            <div class="item">
                <div class="head">
                    <div class="action-notify">
                        <div class="name-notify">
                            配信名称：{{ $notify->subject }}
                        </div>
                        <div class="desc-notify">
                            {{ $notify->delivery_name }}
                        </div>
                        <div class="content-notify">
                            {{ $notify->delivery_contents }}
                        </div>
                    </div>
                    <div class="info-notify">
                        <div class="contain-notify">
                            <div class="contain-notify_title">
                                配信対象
                            </div>
                            <div class="contain-notify_job">
                                @php
                                $ids = explode(",", $notify->career_ids);
                                @endphp

                                @if (in_array(0, $ids))
                                    <div class="tag">All user</div>
                                @else
                                    @foreach($careersList as $career)
                                        @if(in_array($career->id, $ids))
                                            <div class="tag">{{ $career->title }}</div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="account-notify">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">{{ $notify->id }}</span>
                        </div>
                        <div class="account-email">
                            <span class="title-item">配信日時</span>
                            <span class="content-item">{{ \Illuminate\Support\Carbon::parse($notify->created_at)->format(\App\Helpers\Constants::FORMAT_DATE_TIME) }}</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">URL</span>
                            <span class="content-item"><a href="{{ $notify->url }}"
                                                          target="_blank">{{ $notify->url }}</a></span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <a class="item-button detail" href="{{ route('admin.notify.view', ['id' => $notify->id]) }}?back={{  url()->full() }}">詳細</a>
                    <div class="contain-filter">
                        <select name="change-status-notify" class="change-status-notify" data-method="put" data-url-change-status="{{ route('admin.notify.update.status', ['id' => $notify->id]) }}">
                            <option value="{{ \App\Entities\Notification::STATUS_PUBLIC }}"
                            @if ($notify->status == \App\Entities\Notification::STATUS_PUBLIC) selected @endif
                                >公開</option>
                            @if ($notify->status == \App\Entities\Notification::STATUS_DRAFT)
                            <option value="{{ \App\Entities\Notification::STATUS_DRAFT }}"
                                    @if ($notify->status == \App\Entities\Notification::STATUS_DRAFT) selected @endif
                            >非公開</option>
                            @endif
                        </select>
                    </div>
                    <a class="item-button delete js-click"
                       data-method="DELETE"
                       data-redirect="{{ \App\Helpers\Params::buildUrl(false, 'page') }}"
                       href="{{ route('admin.notify.delete', ['id' => $notify->id]) }}">削除</a>
                </div>
            </div>
            @endforeach

            <div class="admin-pagination">
                {{ $notifications->links('admin.pagination.custom') }}
                @php
                $total = $notifications->total();
                $perPage = $notifications->perPage();
                $currentPage = $notifications->currentPage();
                $from = 1;
                $to = ($perPage > $total) ? $total : $perPage;
                if ($currentPage > 1) {
                    $from = (($currentPage - 1) * $perPage) + 1;
                    $calcTo = $currentPage * $perPage;
                    $to = ($calcTo > $total) ? $total : $calcTo;
                }
                @endphp
                <p>{{ $total }}件のお知らせ中{{ $from }}-{{ $to }}件</p>
            </div>
        </div>
    </div>
</div>
@stop
