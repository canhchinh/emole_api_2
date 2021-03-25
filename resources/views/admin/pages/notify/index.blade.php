@extends('admin.layouts.default')

@section('content')
<div class="contain-users">
    <div class="row mb-2">
        <div class="col-8 search">
            <img class="search-img" src="{{asset('/assets/images/user_search_icon.png')}}" id="user_search_icon">
            <input type="text" class="search-input" name="" placeholder="件名を入力">
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="block-filter">
                <div class="contain-filter">
                    <select name="" id="">
                        <option>配信状況</option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
                </div>
                <div class="contain-sort">
                    <div class="contain-sort_total">
                        <span class="only">3</span><span>件</span>
                    </div>
                    <a href="#">
                        <img src="{{asset('/assets/images/sort.svg')}}" alt="sort">
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
                                <div class="tag">演者</div>
                                <div class="tag">モデル</div>
                                <div class="tag">フォトグラファー</div>
                                <div class="tag">ビデオグ</div>
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
                            <span class="content-item">{{ \Illuminate\Support\Carbon::parse($notify->created_at)->format(env('FORMAT_DATE_TIME')) }}</span>
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
                    <a class="item-button detail" href="{{ route('admin.notify.view', ['id' => $notify->id]) }}">詳細</a>
                    <div class="contain-filter">
                        <select name="" id="">
                            <option>公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <a class="item-button delete js-click" data-method="DELETE" href="{{ route('admin.notify.delete', ['id' => $notify->id]) }}">削除</a>
                </div>
            </div>
            @endforeach

            <div class="admin-pagination">
                {{ $notifications->links('admin.pagination.custom') }}
                <p>{{ $notifications->total() }}件のお知らせ中1-3件</p>
            </div>
        </div>
    </div>
</div>
@stop
