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
                        @csrf
                        <div class="contain">
                            <label for="username">配信名称</label><br>
                            <input type="text" id="delivery_name" name="delivery_name" placeholder="配信名称を入力してください。" value="{{ old('delivery_name') }}">
                            @error('delivery_name')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="contain">
                            <label for="username">配信対象</label><br>
                            <select name="career_id" class="form-control">
                                <option value="0">配信対象を選択してください</option>
                                @foreach($delivery_target as $target)
                                <option @if (old('career_id') == $target->id) selected @endif value="{{ $target->id }}">{{ $target->title }}</option>
                                @endforeach
                            </select>
                            @error('career_id')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="contain">
                            <label for="username">配信内容</label><br>
                            <textarea type="text" id="delivery_contents" name="delivery_contents" rows="8"
                                placeholder="配信内容を入力">{{ old('delivery_contents') }}</textarea>
                            @error('delivery_contents')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="contain">
                            <label for="username">件名</label><br>
                            <input type="text" id="subject" name="subject" placeholder="件名を入力してください" value="{{ old('subject') }}">
                            @error('subject')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="contain">
                            <label for="username">遷移先URL</label><br>
                            <input type="text" id="url" name="url" placeholder="URLを入力してください" value="{{ old('url') }}">
                            @error('url')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="footer">
                            <button name="draftSubmit" value="on" class="button-outline mr-2" type="submit">下書き保存</button>
                            <button name="storingSubmit" value="on" type="submit">配信</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
