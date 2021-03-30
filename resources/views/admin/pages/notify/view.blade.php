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
                            <input type="text" id="delivery_name" name="delivery_name" value="{{ $notify->delivery_name }}" readonly>
                        </div>
                        <div class="contain">

                            <label for="username">配信対象</label><br>
                            <select name="career_ids[]" class="form-control selectizeSelect selectLocked" readonly
                                    data-placeholder="配信対象を選択してください"
                                    multiple
                            >
                                @if ($notify->career_ids == 0)
                                <option selected value="0">All user</option>
                                @else
                                    @php
                                    $ids = explode(',', $notify->career_ids);
                                    @endphp

                                    @foreach($delivery_target as $target)
                                        <option @if (in_array($target->id, $ids)) selected @endif value="{{ $target->id }}">{{ $target->title }}</option>
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
                                placeholder="配信内容を入力" readonly>{{ $notify->delivery_contents }}</textarea>
                        </div>
                        <div class="contain">
                            <label for="username">件名</label><br>
                            <input type="text" id="subject" name="subject" value="{{ $notify->subject }}" readonly>
                        </div>
                        <div class="contain">
                            <label for="username">遷移先URL</label><br>
                            <input type="text" id="url" name="url" value="{{ $notify->url }}" readonly>
                        </div>
                        <div class="footer">
                            <button data-href="{{ $backUrl }}" class="btn-href">バック</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
