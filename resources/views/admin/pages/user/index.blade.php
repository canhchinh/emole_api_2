@extends('admin.layouts.default')

@section('content')
<div class="contain-users" id="page-user-list">
    <div class="row mb-2">
        <div class="col-8 search">
            <form method="GET" action="{{ \App\Helpers\Params::buildUrl(false, 'search-key') }}">
                {!! \App\Helpers\Params::buildHiddenFields(['search-key']) !!}
                <img class="search-img" src="{{asset('/assets/images/user_search_icon.png')}}" id="user_search_icon">
                <input type="text" class="search-input" name="search-key" placeholder="名前などを入力" value="{{ $searchKey }}">
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="block-filter">
                <div class="contain-filter">
                    <input id="datepicker-birthday" class="js-datepicker" type="text" style="width: 0; padding: 0; opacity: 0; position: relative; left: 30px; top: 5px" data-url="{{ \App\Helpers\Params::buildUrl('birthdayValue', 'birthday') }}"
                           data-current-value="{{ request()->input('birthday') }}"
                            value="{{ request()->input('birthday') }}">
                    <span data-focus-to="#datepicker-birthday">
                        <select name="" id="" disabled>
                            <option>生年月日</option>
                        </select>
                    </span>
                    <select name="area" id="area" class="js-href-value">
                        <option value="{{ \App\Helpers\Params::buildUrl(false, 'area') }}">地域</option>
                        @foreach($area as $ar)
                        <option
                            {{ \App\Helpers\Params::setSelected('area', $ar->id) }}
                            value="{{ \App\Helpers\Params::buildUrl($ar->id, 'area') }}">{{ $ar->title }}</option>
                        @endforeach
                    </select>
                    <select name="careersList-filter" id="careersList">
                        <option value="{{ \App\Helpers\Params::buildUrl(false, 'career_id') }}">活動内容</option>
                        @foreach($careersList as $career)
                        <option
                            {{ \App\Helpers\Params::setSelected('career_id', $career->id) }}
                                value="{{ \App\Helpers\Params::buildUrl($career->id, 'career_id') }}">{{ $career->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="contain-sort">
                    <div class="contain-sort_total">
                        <span class="only">{{ \App\Helpers\Format::numberFormat(count($users)) }}</span><span>人</span>
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
            @foreach($users as $user)
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{ \App\Helpers\ImageRender::userAvatar($user->avatar) }}" alt="avatar" class="rounded-circle" width="47" height="47" style="border-radius: 50%">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                {{ $user->given_name ?: '--' }}
                            </div>
                            <div class="action-info_job">
                                @php
                                    $ids = explode(",", $user->career_ids);
                                @endphp

                                @foreach($careersList as $career)
                                    @if(in_array($career->id, $ids))
                                        <div class="tag">{{ $career->title }}</div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="action-social">
                            <div class="action-social_type">
                                {{ $user->title ?: '--' }}
                            </div>
                            <div class="action-social_list">
                                <div class="action-social_list-item"><img src="{{asset('/assets/images/icon-sm/tw.svg')}}" alt="twitter"> {{ \App\Helpers\Format::numberFormat(isset($snsFollowersCount[$user->id]['twitter'])  ?: 0, true) }}</div>
                                <div class="action-social_list-item"><img src="{{asset('/assets/images/icon-sm/insta.svg')}}" alt="insta"> {{ \App\Helpers\Format::numberFormat(isset($snsFollowersCount[$user->id]['instagram']) ?: 0, true) }}</div>
                                <div class="action-social_list-item"><img src="{{asset('/assets/images/icon-sm/youtube.svg')}}" alt="youtube"> {{ \App\Helpers\Format::numberFormat(isset($snsFollowersCount[$user->id]['youtube']) ?: 0, true) }}
                                </div>
                                <div class="action-social_list-item"><img src="{{asset('/assets/images/icon-sm/tiktok.svg')}}" alt="tiktok"> {{ \App\Helpers\Format::numberFormat(isset($snsFollowersCount[$user->id]['tiktok']) ?: 0, true) }}</div>
                            </div>
                        </div>
                        <div class="action-portfolio">
                            @php
                            $images = \App\Helpers\ImageRender::parserPortfolioList($user->portfolios_image, 4);

                            @endphp
                            @foreach($images as $img)
                            <img src="{{ \App\Helpers\ImageRender::portfolioAvatar($img) }}" alt="" style="border-radius: 4px" width="60" height="47">
                            @endforeach
                        </div>
                    </div>
                    <div class="info">
                        <div class="info-gender">
                            <span class="title-item">性別</span>
                            <span class="content-item">{{ $user->gender? $user->gender : '' }}</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">生年月日</span>
                            <span class="content-item">{{ \App\Helpers\DateTime::showBirthDay($user->birthday) }}</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">活動拠点</span>
                            <span class="content-item">{{ $user->activity_base_title ?: '--' }}</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">{{ $user->id }}</span>
                        </div>
                        <div class="account-email" title="{{ $user->email ?: '' }}">
                            <span class="title-item">メールアドレス</span>
                            <span class="content-item">{{ $user->email ?: '--' }}</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">登録日時</span>
                            <span class="content-item">{{ \App\Helpers\DateTime::showDateTime($user->created_at) }}</span>
                        </div>
                        <div class="account-updated">
                            <span class="title-item">更新日時</span>
                            <span class="content-item">{{ \App\Helpers\DateTime::showDateTime($user->updated_at) }}</span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <a href="{{ \App\Helpers\Helper::concatStringBySlash(config('common.frontend_url'), $user->user_name) }}" class="item-button detail">詳細</a>
                    <button class="item-button email sendEmail"
                            data-user-id="{{ $user->id }}"
                            data-user-name="{{ $user->given_name ?: $user->username }}"
                    >メール</button>
                    <div class="contain-filter">
                        <select name="change-status" id="change-status" data-url="{{ route('admin.user.update.status', ['id' => $user->id]) }}">
                            <option value="{{ \App\Entities\User::STATUS_ACTIVE }}"  @if ($user->active == \App\Entities\User::STATUS_ACTIVE) selected @endif>公開</option>
                            <option value="{{ \App\Entities\User::STATUS_INACTIVE }}"  @if ($user->active == \App\Entities\User::STATUS_INACTIVE) selected @endif>非公開</option>
                        </select>
                    </div>
                    <a
                        href="{{ route('admin.users.delete', ['id' => $user->id]) }}"
                        data-redirect="{{ \App\Helpers\Params::buildUrl(false, 'page') }}"
                       data-method="DELETE"
                       class="item-button delete js-delete">削除</a>
                </div>
            </div>
            @endforeach

            @if(count($users) == 0)
                    <h4 class="text-center py-5 alert-secondary">データなし</h4>
            @endif
        </div>
    </div>
</div>
<script>
    jQuery(function ($) {
        $('select#change-status').on('change', function () {
            $.ajax({
                method: 'put',
                data: {status: $(this).val()},
                url: $(this).data('url')
            }).done(function (data) {
                if (data.hasOwnProperty('success') && data.success) {
                    window.location.reload();
                }
            });
        });
    });
</script>
<!-- Modal -->
<div class="modal" id="email-content" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="exampleModalLabel">に電子メールを送信： <span class="username"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.sendEmail') }}" method="POST" id="sendEmailToUserForm">
                @csrf
                <input type="hidden" name="user_id" value="0">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email1">メールの件名</label>
                        <input type="text" class="form-control" id="email_subject" name="email_subject" placeholder="メールの件名" required minlength="2">
                    </div>
                    <div class="form-group">
                        <label for="email-content">メールの内容</label>
                        <textarea class="form-control" id="email_content" name="email_content" placeholder="メールの内容" rows="5" required minlength="10"></textarea>
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
