<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>

<body>
    <form action="{{ route('admin.auth.post.newPassword') }}" method="POST">
        @csrf
        <input type="hidden" name="token_reset" value="{{token}}" />
        <input type="password" name="password" placeholder="password">
        <input type="email" name="email" placeholder="confirm password">
        <button type="submit">submit</button>
    </form>
</body>

</html>
