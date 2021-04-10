@extends('admin.layouts.default')

@section('content')
    <div class="contain-users">
        <div class="row mb-2">
            <div class="col-8 search">
                <form method="GET" action="{{ \App\Helpers\Params::buildUrl(false, 'search-key') }}">
                    {!! \App\Helpers\Params::buildHiddenFields(['search-key']) !!}
                    <img class="search-img" src="{{asset('/assets/images/user_search_icon.png')}}"
                         id="user_search_icon">
                    <input type="text" class="search-input" name="search-key" placeholder="作品名やタグなどを入力"
                           value="{{ $searchKey }}">
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
                                <option
                                    {{ \App\Helpers\Params::setSelected('career_id', $career->id) }} value="{{ \App\Helpers\Params::buildUrl($career->id, 'career_id') }}">{{ $career->title }}</option>
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
                            <span
                                class="only">{{ \App\Helpers\Format::numberFormat(count($portfolios)) }}</span><span>件</span>
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
                                <img src="{{ \App\Helpers\ImageRender::portfolioAvatar($portfolio->image[0]['path']) }}"
                                     alt="{{$portfolio->image[0]['alt']}}" width="100%" style="border-radius: 4px">
                            </div>
                            <div class="action-portfolio">
                                <div class="action-portfolio_title">{{ $portfolio->title }}</div>
                                <div class="info-small">
                                    <div class="avatar-small">
                                        <img src="{{ \App\Helpers\ImageRender::userAvatar($portfolio->u_avatar) }}"
                                             alt="avatar">
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
                                    <span
                                        class="content-item">¥{{ \App\Helpers\Format::formatCurrency($portfolio->budget) }}</span>
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
                                    <span
                                        class="content-item">約 {{ \App\Helpers\Format::numberFormat($portfolio->cpa_count) }}</span>
                                </div>
                            </div>
                            <div class="account">
                                <div class="account-id">
                                    <span class="title-item">ID</span>
                                    <span class="content-item">{{ $portfolio->id }}</span>
                                </div>
                                <div class="account-created">
                                    <span class="title-item">登録日時</span>
                                    <span
                                        class="content-item">{{ \App\Helpers\DateTime::showDateTime($portfolio->created_at) }}</span>
                                </div>
                                <div class="account-updated">
                                    <span class="title-item">更新日時</span>
                                    <span
                                        class="content-item">{{ \App\Helpers\DateTime::showDateTime($portfolio->created_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="hr"></div>
                        <div class="footer">
                            <button class="item-button detail">詳細</button>
                            <button class="item-button email sendEmailPortfolio"
                                    data-user-id="{{ $portfolio->user_id }}"
                                    data-user-name="{{ $portfolio->u_given_name }}"
                            >メール</button>
                            <div class="contain-filter">
                                <select name="change-status-portfolio" class="change-status-portfolio"
                                        data-method="put"
                                        data-url-change-status="{{ route('admin.portfolio.update.status', ['id' => $portfolio->id]) }}">
                                    <option value="{{ \App\Entities\Portfolio::PUBLIC_YES }}"
                                            @if ($portfolio->is_public == \App\Entities\Portfolio::PUBLIC_YES) selected @endif
                                    >公開
                                    </option>
                                    <option value="{{ \App\Entities\Portfolio::PUBLIC_NO }}"
                                            @if ($portfolio->is_public == \App\Entities\Portfolio::PUBLIC_NO) selected @endif
                                    >非公開
                                    </option>
                                </select>
                            </div>
                            <a href="{{ route('admin.portfolio.delete', ['id' => $portfolio->id]) }}"
                               data-method="DELETE" class="item-button delete js-delete-portfolio">削除</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="email-content-portfolio" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="exampleModalLabel">に電子メールを送信： <span class="username"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.portfolio.sendEmail') }}" method="POST" id="sendEmailToPortfolioForm">
                    @csrf
                    <input type="hidden" name="user_id" value="0">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email1">メールの件名</label>
                            <input type="text" class="form-control" id="email_subject" name="email_subject"
                                   placeholder="メールの件名" required minlength="2">
                        </div>
                        <div class="form-group">
                            <label for="email-content">メールの内容</label>
                            <textarea class="form-control" id="email_content" name="email_content" placeholder="メールの内容"
                                      rows="5" required minlength="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 d-flex justify-content-center">
                        <div class="w-100 ajax-response text-success text-center"></div>
                        <div class="w-100 ajax-response text-danger text-center"></div>
                        <button type="submit" class="btn btn-dark" data-dismiss="modal">キャンセル</button>
                        <input class="btn btn-primary" type="submit" value="送信">
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
