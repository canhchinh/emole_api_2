@extends('admin.layouts.default')

@section('content')
<div class="contain-users">
    <div class="row mb-2">
        <div class="col-8 search">
            <img class="search-img" src="{{asset('/assets/images/user_search_icon.png')}}" id="user_search_icon">
            <input type="text" class="search-input" name="" placeholder="作品名やタグなどを入力">
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="block-filter">
                <div class="contain-filter">
                    <select name="" id="">
                        <option>活動内容</option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
                    <select name="" id="">
                        <option>カテゴリー</option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
                </div>
                <div class="contain-sort">
                    <div class="contain-sort_total">
                        <span class="only">189</span><span>件</span>
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
                    <div class="avatar-portfolio">
                        <img src="{{asset('/assets/images/detail-portfolio.svg')}}" alt="detail-portfolio.svg">
                    </div>
                    <div class="action-portfolio">
                        <div class="action-portfolio_title">【LINEドラム広告】昔好きだった人からLINEが届いた。</div>
                        <div class="info-small">
                            <div class="avatar-small">
                                <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                            </div>
                            <div class="contain-name">
                                <div class="contain-name_title">
                                    藍 美帆
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
                            <span class="content-item">¥900,000</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">リーチ数</span>
                            <span class="content-item">285,000pv / 1ヶ月</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">再生数</span>
                            <span class="content-item">1,000,000回</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">いいね数</span>
                            <span class="content-item">80,000件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">コメント数</span>
                            <span class="content-item">433件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">CPA</span>
                            <span class="content-item">約 3,900</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
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
                    <div class="avatar-portfolio">
                        <img src="{{asset('/assets/images/detail-portfolio.svg')}}" alt="detail-portfolio.svg">
                    </div>
                    <div class="action-portfolio">
                        <div class="action-portfolio_title">【LINEドラム広告】昔好きだった人からLINEが届いた。</div>
                        <div class="info-small">
                            <div class="avatar-small">
                                <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                            </div>
                            <div class="contain-name">
                                <div class="contain-name_title">
                                    藍 美帆
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
                            <span class="content-item">¥900,000</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">リーチ数</span>
                            <span class="content-item">285,000pv / 1ヶ月</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">再生数</span>
                            <span class="content-item">1,000,000回</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">いいね数</span>
                            <span class="content-item">80,000件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">コメント数</span>
                            <span class="content-item">433件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">CPA</span>
                            <span class="content-item">約 3,900</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
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
                    <div class="avatar-portfolio">
                        <img src="{{asset('/assets/images/detail-portfolio.svg')}}" alt="detail-portfolio.svg">
                    </div>
                    <div class="action-portfolio">
                        <div class="action-portfolio_title">【LINEドラム広告】昔好きだった人からLINEが届いた。</div>
                        <div class="info-small">
                            <div class="avatar-small">
                                <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                            </div>
                            <div class="contain-name">
                                <div class="contain-name_title">
                                    藍 美帆
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
                            <span class="content-item">¥900,000</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">リーチ数</span>
                            <span class="content-item">285,000pv / 1ヶ月</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">再生数</span>
                            <span class="content-item">1,000,000回</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">いいね数</span>
                            <span class="content-item">80,000件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">コメント数</span>
                            <span class="content-item">433件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">CPA</span>
                            <span class="content-item">約 3,900</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
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
                    <div class="avatar-portfolio">
                        <img src="{{asset('/assets/images/detail-portfolio.svg')}}" alt="detail-portfolio.svg">
                    </div>
                    <div class="action-portfolio">
                        <div class="action-portfolio_title">【LINEドラム広告】昔好きだった人からLINEが届いた。</div>
                        <div class="info-small">
                            <div class="avatar-small">
                                <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                            </div>
                            <div class="contain-name">
                                <div class="contain-name_title">
                                    藍 美帆
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
                            <span class="content-item">¥900,000</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">リーチ数</span>
                            <span class="content-item">285,000pv / 1ヶ月</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">再生数</span>
                            <span class="content-item">1,000,000回</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">いいね数</span>
                            <span class="content-item">80,000件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">コメント数</span>
                            <span class="content-item">433件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">CPA</span>
                            <span class="content-item">約 3,900</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
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
                    <div class="avatar-portfolio">
                        <img src="{{asset('/assets/images/detail-portfolio.svg')}}" alt="detail-portfolio.svg">
                    </div>
                    <div class="action-portfolio">
                        <div class="action-portfolio_title">【LINEドラム広告】昔好きだった人からLINEが届いた。</div>
                        <div class="info-small">
                            <div class="avatar-small">
                                <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                            </div>
                            <div class="contain-name">
                                <div class="contain-name_title">
                                    藍 美帆
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
                            <span class="content-item">¥900,000</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">リーチ数</span>
                            <span class="content-item">285,000pv / 1ヶ月</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">再生数</span>
                            <span class="content-item">1,000,000回</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">いいね数</span>
                            <span class="content-item">80,000件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">コメント数</span>
                            <span class="content-item">433件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">CPA</span>
                            <span class="content-item">約 3,900</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
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
                    <div class="avatar-portfolio">
                        <img src="{{asset('/assets/images/detail-portfolio.svg')}}" alt="detail-portfolio.svg">
                    </div>
                    <div class="action-portfolio">
                        <div class="action-portfolio_title">【LINEドラム広告】昔好きだった人からLINEが届いた。</div>
                        <div class="info-small">
                            <div class="avatar-small">
                                <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                            </div>
                            <div class="contain-name">
                                <div class="contain-name_title">
                                    藍 美帆
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
                            <span class="content-item">¥900,000</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">リーチ数</span>
                            <span class="content-item">285,000pv / 1ヶ月</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">再生数</span>
                            <span class="content-item">1,000,000回</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">いいね数</span>
                            <span class="content-item">80,000件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">コメント数</span>
                            <span class="content-item">433件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">CPA</span>
                            <span class="content-item">約 3,900</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
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
                    <div class="avatar-portfolio">
                        <img src="{{asset('/assets/images/detail-portfolio.svg')}}" alt="detail-portfolio.svg">
                    </div>
                    <div class="action-portfolio">
                        <div class="action-portfolio_title">【LINEドラム広告】昔好きだった人からLINEが届いた。</div>
                        <div class="info-small">
                            <div class="avatar-small">
                                <img src="{{asset('/assets/images/avatar.svg')}}" alt="avatar">
                            </div>
                            <div class="contain-name">
                                <div class="contain-name_title">
                                    藍 美帆
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
                            <span class="content-item">¥900,000</span>
                        </div>
                        <div class="info-bod">
                            <span class="title-item">リーチ数</span>
                            <span class="content-item">285,000pv / 1ヶ月</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">再生数</span>
                            <span class="content-item">1,000,000回</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">いいね数</span>
                            <span class="content-item">80,000件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">コメント数</span>
                            <span class="content-item">433件</span>
                        </div>
                        <div class="info-base">
                            <span class="title-item">CPA</span>
                            <span class="content-item">約 3,900</span>
                        </div>
                    </div>
                    <div class="account">
                        <div class="account-id">
                            <span class="title-item">ID</span>
                            <span class="content-item">dsjfkd;jsahfklvfioud</span>
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