@extends('admin.layouts.default')

@section('content')
<div class="contain-users">
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
                    <select name="" id="">
                        <option>地域</option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
                    <select name="" id="">
                        <option>活動内容</option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
                </div>
                <div class="contain-sort">
                    <div class="contain-sort_total">
                        <span class="only">189</span><span>人</span>
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
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                藍 美帆
                            </div>
                            <div class="action-info_job">
                                <div class="tag">演者</div>
                                <div class="tag">モデル</div>
                                <div class="tag">フォトグラファー</div>
                                <div class="tag">ビデオグ</div>
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
                            <span class="content-item">女性</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">生年月日</span>
                            <span class="content-item">1994年12月10日</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">活動拠点</span>
                            <span class="content-item">東京都</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
                        </div>
                        <div class="account-email">
                            <span class="title-item">メールアドレス</span>
                            <span class="content-item">aiaiaiaiai@gmail.com</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">登録日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                        <div class="account-updated">
                            <span class="title-item">更新日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <button class="item-button detail">詳細</button>
                    <button class="item-button email">メール</button>
                    <div class="contain-filter">
                        <select name="" id="">
                            <option>公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <button class="item-button delete">削除</button>
                </div>
            </div>
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                藍 美帆
                            </div>
                            <div class="action-info_job">
                                <div class="tag">演者</div>
                                <div class="tag">モデル</div>
                                <div class="tag">フォトグラファー</div>
                                <div class="tag">ビデオグ</div>
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
                            <span class="content-item">女性</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">生年月日</span>
                            <span class="content-item">1994年12月10日</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">活動拠点</span>
                            <span class="content-item">東京都</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
                        </div>
                        <div class="account-email">
                            <span class="title-item">メールアドレス</span>
                            <span class="content-item">aiaiaiaiai@gmail.com</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">登録日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                        <div class="account-updated">
                            <span class="title-item">更新日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <button class="item-button detail">詳細</button>
                    <button class="item-button email">メール</button>
                    <div class="contain-filter">
                        <select name="" id="">
                            <option>公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <button class="item-button delete">削除</button>
                </div>
            </div>
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                藍 美帆
                            </div>
                            <div class="action-info_job">
                                <div class="tag">演者</div>
                                <div class="tag">モデル</div>
                                <div class="tag">フォトグラファー</div>
                                <div class="tag">ビデオグ</div>
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
                            <span class="content-item">女性</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">生年月日</span>
                            <span class="content-item">1994年12月10日</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">活動拠点</span>
                            <span class="content-item">東京都</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
                        </div>
                        <div class="account-email">
                            <span class="title-item">メールアドレス</span>
                            <span class="content-item">aiaiaiaiai@gmail.com</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">登録日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                        <div class="account-updated">
                            <span class="title-item">更新日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <button class="item-button detail">詳細</button>
                    <button class="item-button email">メール</button>
                    <div class="contain-filter">
                        <select name="" id="">
                            <option>公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <button class="item-button delete">削除</button>
                </div>
            </div>
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                藍 美帆
                            </div>
                            <div class="action-info_job">
                                <div class="tag">演者</div>
                                <div class="tag">モデル</div>
                                <div class="tag">フォトグラファー</div>
                                <div class="tag">ビデオグ</div>
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
                            <span class="content-item">女性</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">生年月日</span>
                            <span class="content-item">1994年12月10日</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">活動拠点</span>
                            <span class="content-item">東京都</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
                        </div>
                        <div class="account-email">
                            <span class="title-item">メールアドレス</span>
                            <span class="content-item">aiaiaiaiai@gmail.com</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">登録日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                        <div class="account-updated">
                            <span class="title-item">更新日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <button class="item-button detail">詳細</button>
                    <button class="item-button email">メール</button>
                    <div class="contain-filter">
                        <select name="" id="">
                            <option>公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <button class="item-button delete">削除</button>
                </div>
            </div>
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                藍 美帆
                            </div>
                            <div class="action-info_job">
                                <div class="tag">演者</div>
                                <div class="tag">モデル</div>
                                <div class="tag">フォトグラファー</div>
                                <div class="tag">ビデオグ</div>
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
                            <span class="content-item">女性</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">生年月日</span>
                            <span class="content-item">1994年12月10日</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">活動拠点</span>
                            <span class="content-item">東京都</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
                        </div>
                        <div class="account-email">
                            <span class="title-item">メールアドレス</span>
                            <span class="content-item">aiaiaiaiai@gmail.com</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">登録日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                        <div class="account-updated">
                            <span class="title-item">更新日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <button class="item-button detail">詳細</button>
                    <button class="item-button email">メール</button>
                    <div class="contain-filter">
                        <select name="" id="">
                            <option>公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <button class="item-button delete">削除</button>
                </div>
            </div>
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                藍 美帆
                            </div>
                            <div class="action-info_job">
                                <div class="tag">演者</div>
                                <div class="tag">モデル</div>
                                <div class="tag">フォトグラファー</div>
                                <div class="tag">ビデオグ</div>
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
                            <span class="content-item">女性</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">生年月日</span>
                            <span class="content-item">1994年12月10日</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">活動拠点</span>
                            <span class="content-item">東京都</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
                        </div>
                        <div class="account-email">
                            <span class="title-item">メールアドレス</span>
                            <span class="content-item">aiaiaiaiai@gmail.com</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">登録日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                        <div class="account-updated">
                            <span class="title-item">更新日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <button class="item-button detail">詳細</button>
                    <button class="item-button email">メール</button>
                    <div class="contain-filter">
                        <select name="" id="">
                            <option>公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <button class="item-button delete">削除</button>
                </div>
            </div>
            <div class="item">
                <div class="head">
                    <div class="avatar">
                        <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                    </div>
                    <div class="action">
                        <div class="action-info">
                            <div class="action-info_name">
                                藍 美帆
                            </div>
                            <div class="action-info_job">
                                <div class="tag">演者</div>
                                <div class="tag">モデル</div>
                                <div class="tag">フォトグラファー</div>
                                <div class="tag">ビデオグ</div>
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
                            <span class="content-item">女性</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">生年月日</span>
                            <span class="content-item">1994年12月10日</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">活動拠点</span>
                            <span class="content-item">東京都</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
                        </div>
                        <div class="account-email">
                            <span class="title-item">メールアドレス</span>
                            <span class="content-item">aiaiaiaiai@gmail.com</span>
                        </div>
                        <div class="account-created">
                            <span class="title-item">登録日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                        <div class="account-updated">
                            <span class="title-item">更新日時</span>
                            <span class="content-item">2020/03/23 13:45</span>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="footer">
                    <button class="item-button detail">詳細</button>
                    <button class="item-button email">メール</button>
                    <div class="contain-filter">
                        <select name="" id="">
                            <option>公開</option>
                            <option value="">非公開</option>
                        </select>
                    </div>
                    <button class="item-button delete">削除</button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
