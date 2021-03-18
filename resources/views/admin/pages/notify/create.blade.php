@extends('admin.layouts.default2')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-6 col-md-12">
            <div class="contain-form">
                <div class="contain-form_title">
                    お知らせ配信
                </div>
                <div class="form">
                    <form action="" method="POST" id="">
                        <div class="contain">
                            <label for="username">配信名称</label><br>
                            <input type="text" id="username" name="username" placeholder="配信名称を入力してください。" required>
                        </div>
                        <div class="contain">
                            <label for="username">配信対象</label><br>
                            <input type="text" id="username" name="username" placeholder="配信対象を選択してください" required>
                        </div>
                        <div class="contain">
                            <label for="username">配信内容</label><br>
                            <input type="text" id="username" name="username" placeholder="配信内容を入力" required>
                        </div>
                        <div class="contain">
                            <label for="username">件名</label><br>
                            <textarea type="text" id="username" name="username" rows="8"
                                placeholder="件名を入力してください"></textarea>
                        </div>
                        <div class="contain">
                            <label for="username">遷移先URL</label><br>
                            <input type="text" id="username" name="username" placeholder="URLを入力してください" required>
                        </div>
                        <div class="footer">
                            <button class="button-outline mr-2" type="submit">下書き保存</button>
                            <button type="submit">配信</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop