@extends('admin.layouts.default')

@section('content')
<div class="contain-users" id="page-user-list">
    <div class="row mb-2">
        <div class="col-8 search">
            <img class="search-img" src="{{asset('/assets/images/user_search_icon.png')}}" id="user_search_icon">
            <input type="text" class="search-input" name="" placeholder="名前などを入力">
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="block-filter">
                <div class="contain-filter">
                    <select name="" id="">
                        <option>生年月日</option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
                    <input id="datepicker" type="text">
                    <select name="" id="">
                        <option>地域</option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
                    <select name="careersList-filter" id="careersList">
                        <option value="all">活動内容</option>
                        @foreach($careersList as $career)
                        <option value="{{ $career->title }}">{{ $career->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="contain-sort">
                    <div class="contain-sort_total">
                        <span class="only">{{ count($users) }}</span><span>人</span>
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
            @foreach($users as $user)
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{ \App\Helpers\ImageRender::userAvatar($user->avatar) }}" alt="avatar" class="rounded-circle">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                {{ $user->given_name }}
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
                                女優・アーティスト
                            </div>
                            <div class="action-social_list">
                                <div class="action-social_list-item"><img src="{{asset('/assets/images/icon-sm/tw.svg')}}" alt="twitter"> 1,343</div>
                                <div class="action-social_list-item"><img src="{{asset('/assets/images/icon-sm/insta.svg')}}" alt="twitter"> 4,840</div>
                                <div class="action-social_list-item"><img src="{{asset('/assets/images/icon-sm/youtube.svg')}}" alt="twitter"> 20,000
                                </div>
                                <div class="action-social_list-item"><img src="{{asset('/assets/images/icon-sm/tiktok.svg')}}" alt="twitter"> 8,394</div>
                            </div>
                        </div>
                        <div class="action-portfolio">
                            <img src="{{asset('/assets/images/1.svg')}}" alt="">
                            <img src="{{asset('/assets/images/2.svg')}}" alt="">
                            <img src="{{asset('/assets/images/3.svg')}}" alt="">
                            <img src="{{asset('/assets/images/4.svg')}}" alt="">
                        </div>
                    </div>
                    <div class="info">
                        <div class="info-gender">
                            <span class="title-item">性別</span>
                            <span class="content-item">{{ ($user->gender == 1) ? '男性' : '女性' }}</span>
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
                        <div class="account-email">
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
                    <button class="item-button detail">詳細</button>
                    <button class="item-button email sendEmail" data-user-id="{{ $user->id }}">メール</button>
                    <div class="contain-filter">
                        <select name="change-status" id="change-status">
                            <option value="">公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <a href="{{ route('admin.users.delete', ['id' => $user->id]) }}" data-method="DELETE" class="item-button delete js-delete">削除</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal" id="email-content" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="exampleModalLabel">Send email to this user</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.sendEmail') }}" method="POST">
                @csrf
                <input type="hidden" name="user-id" value="0">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email1">Email subject</label>
                        <input type="text" class="form-control" id="email-subject" name="email-subject" placeholder="Email subject">
                        <small id="emailHelp" class="form-text text-muted">Your information is safe with us.</small>
                    </div>
                    <div class="form-group">
                        <label for="email-content">Email content</label>
                        <textarea class="form-control" id="email-content" name="email-content" placeholder="Email content" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-center">
                    <button type="submit" class="btn btn-dark" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary send-email">Send email</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
