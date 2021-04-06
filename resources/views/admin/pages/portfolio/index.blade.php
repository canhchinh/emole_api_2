@extends('admin.layouts.default')

@section('content')
    <div class="contain-users">
        <div class="row mb-2">
            <div class="col-8 search">
                <form method="GET" action="{{ \App\Helpers\Params::buildUrl(false, 'search-key') }}">
                    {!! \App\Helpers\Params::buildHiddenFields(['search-key']) !!}
                    <img class="search-img" src="{{asset('/assets/images/user_search_icon.png')}}"
                         id="user_search_icon">
                    <input type="text" class="search-input" name="search-key" placeholder="作品名やタグなどを入力" value="{{ $searchKey }}">
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="block-filter">
                    <div class="contain-filter">
                        <select name="filter-portfolio-careers" id="filter-portfolio-careers">
                            <option value="{{ \App\Helpers\Params::buildUrl(false, 'career_id') }}">活動内容</option>
                            @foreach($careersList as $career)
                                <option {{ \App\Helpers\Params::setSelected('career_id', $career->id) }} value="{{ \App\Helpers\Params::buildUrl($career->id, 'career_id') }}">{{ $career->title }}</option>
                            @endforeach
                        </select>
                        <select name="" id="">
                            <option>カテゴリー</option>
                            <option value=""></option>
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="contain-sort">
                        <div class="contain-sort_total">
                            <span class="only">{{ \App\Helpers\Format::numberFormat(count($portfolios)) }}</span><span>件</span>
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
                @foreach($portfolios as $portfolio)
                    <div class="item">
                        <div class="head">
                            <div class="avatar-portfolio">
                                <img src="{{asset('/assets/images/detail-portfolio.svg')}}" alt="detail-portfolio.svg">
                            </div>
                            <div class="action-portfolio">
                                <div class="action-portfolio_title">{{ $portfolio->title }}</div>
                                <div class="info-small">
                                    <div class="avatar-small">
                                        <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                                    </div>
                                    <div class="contain-name">
                                        <div class="contain-name_title">
                                            {{ $portfolio->u_given_name }}
                                        </div>
                                        <div class="contain-name_desc">
                                            女優・アーティスト
                                        </div>
                                    </div>
                                </div>
                                <div class="portfolio-footer">
                                    <div class="tag-black">CM</div>
                                </div>
                            </div>
                            <div class="info-portfolio">
                                <div class="info-gender">
                                    <span class="title-item">予算</span>
                                    <span class="content-item">¥{{ \App\Helpers\Format::formatCurrency($portfolio->budget) }}</span>
                                </div>
                                <div class="info-bod">
                                    <span class="title-item">リーチ数</span>
                                    <span class="content-item">{{ \App\Helpers\Format::numberFormat($portfolio->reach_number) }}pv / 1ヶ月</span>
                                </div>
                                <div class="info-base">
                                    <span class="title-item">再生数</span>
                                    <span class="content-item">{{ \App\Helpers\Format::numberFormat($portfolio->view_count) }}回</span>
                                </div>
                                <div class="info-base">
                                    <span class="title-item">いいね数</span>
                                    <span class="content-item">{{ \App\Helpers\Format::numberFormat($portfolio->like_count) }}件</span>
                                </div>
                                <div class="info-base">
                                    <span class="title-item">コメント数</span>
                                    <span class="content-item">{{ \App\Helpers\Format::numberFormat($portfolio->comment_count) }}件</span>
                                </div>
                                <div class="info-base">
                                    <span class="title-item">CPA</span>
                                    <span class="content-item">約 {{ \App\Helpers\Format::numberFormat($portfolio->cpa_count) }}</span>
                                </div>
                            </div>
                            <div class="account">
                                <div class="account-id">
                                    <span class="title-item">ID</span>
                                    <span class="content-item">{{ $portfolio->id }}</span>
                                </div>
                                <div class="account-created">
                                    <span class="title-item">登録日時</span>
                                    <span class="content-item">{{ \App\Helpers\DateTime::showDateTime($portfolio->created_at) }}</span>
                                </div>
                                <div class="account-updated">
                                    <span class="title-item">更新日時</span>
                                    <span class="content-item">{{ \App\Helpers\DateTime::showDateTime($portfolio->created_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="hr"></div>
                        <div class="footer">
                            <button class="item-button detail">詳細</button>
                            <button class="item-button email">メール</button>
                            <div class="contain-filter">
                                <select name="change-status-portfolio" class="change-status-portfolio"
                                        data-method="put" data-url-change-status="{{ route('admin.portfolio.update.status', ['id' => $portfolio->id]) }}">
                                    <option value="{{ \App\Entities\Portfolio::PUBLIC_YES }}"
                                            @if ($portfolio->is_public == \App\Entities\Portfolio::PUBLIC_YES) selected @endif
                                    >公開</option>
                                    <option value="{{ \App\Entities\Portfolio::PUBLIC_NO }}"
                                            @if ($portfolio->is_public == \App\Entities\Portfolio::PUBLIC_NO) selected @endif
                                    >非公開</option>
                                </select>
                            </div>
                            <a href="{{ route('admin.portfolio.delete', ['id' => $portfolio->id]) }}" data-method="DELETE" class="item-button delete js-delete-portfolio">削除</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop
