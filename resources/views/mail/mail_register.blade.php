<html>

<head>
    <style>
    p {
        color: #718096 !important;
    }

    a {
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
        border-radius: 4px;
        color: #fff !important;
        display: inline-block;
        overflow: hidden;
        text-decoration: none;
        background-color: #2d3748;
        border-bottom: 8px solid #2d3748;
        border-left: 18px solid #2d3748;
        border-right: 18px solid #2d3748;
        border-top: 8px solid #2d3748;
    }
    </style>
</head>

<body>
    <p>この度は、emoleへ登録していただき、 ありがとうございます。</p>
    <p>以下のボタンからメールアドレスを認証して 登録を完了してください。ボタン：</p>
    <a href="{{ $urlActive.$token }}">登録を完了する</a>
</body>

</html>