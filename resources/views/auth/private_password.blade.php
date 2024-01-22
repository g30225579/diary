<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>二次验证</title>
</head>
<body>
    <form method="post" action="/common/private_password" id="d_form" style="visibility: hidden">
        <input type="text" id="d_private_password" name="private_password" value="">
        <input type="text" name="back_url" value="{{request()->input('back_url')}}">
        <input type="submit" value="提交">
        @csrf
    </form>
</body>
<script>
    document.getElementById('d_private_password').value = window.prompt('输入独立密码');
    document.getElementById('d_form').submit();
</script>
</html>
