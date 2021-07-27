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
                    <form action="" method="POST" id="create-noti-form">
                        @csrf
                        <div class="contain">
                            <label for="username">配信名称</label><br>
                            <input type="text" id="delivery_name" name="delivery_name" placeholder="配信名称を入力してください。"
                                value="{{$notify->delivery_name}}">
                            @error('delivery_name')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="contain">
                            <label for="username">配信対象</label><br>
                            <select id="career_id" name="career_ids[]" class="form-control selectizeSelect"
                                data-placeholder="配信対象を選択してください" multiple>

                                @if ($notify->career_ids == 0)
                                <option selected value="0">All user</option>
                                @else
                                @php
                                $ids = explode(',', $notify->career_ids);
                                @endphp

                                @foreach($delivery_target as $target)
                                <option @if (in_array($target->id, $ids)) selected @endif
                                    value="{{ $target->id }}">{{ $target->title }}</option>
                                @endforeach
                                @endif
                            </select>
                            @error('career_ids')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="contain">
                            <label for="username">配信内容</label><br>
                            <textarea type="text" id="delivery_contents" name="delivery_contents" rows="8"
                                placeholder="配信内容を入力">{{$notify->delivery_contents}}</textarea>
                            @error('delivery_contents')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="contain">
                            <label for="username">件名</label><br>
                            <input type="text" id="subject" name="subject" placeholder="件名を入力してください"
                                value="{{$notify->subject}}">
                            @error('subject')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="contain">
                            <label for="username">遷移先URL</label><br>
                            <input type="text" id="url" name="url" placeholder="URLを入力してください" value="{{$notify->url}}">
                            @error('url')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="footer">
                            <input type="hidden" value="draft" name="storingType">
                            <button name="draftSubmit" value="on" class="button-outline mr-2 once-click-disabled"
                                type="submit">下書き保存</button>
                            <button name="storingSubmit" value="on" class="once-click-disableds"
                                type="submit">配信</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) {
    $('#create-noti-form').on('click', 'button[name="storingSubmit"]', function() {
        $('input[name="storingType"]').val('public');
    });
});
</script>
@stop