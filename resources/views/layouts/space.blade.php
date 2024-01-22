<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '家园')</title>
    <link rel="stylesheet" href="/npm/layui@2.6.8/dist/css/layui.css">
    <link rel="stylesheet" href="/css/space.css">
    @yield('style')
</head>
<body>
<div class="header">
    <div class="layui-fluid">
        <div class="layui-row">
            <div class="layui-col-xs8">
                <span class="layui-breadcrumb" lay-separator="|">
                    <a href="/space">Home</a>
                    <a href="/space/diary/create"><button class="layui-btn layui-btn-primary layui-border-red layui-btn-xs"><i class="layui-icon layui-icon-addition"></i>写记事</button></a>
                </span>
            </div>
            <div class="layui-col-xs4 name">
                <b>{{auth()->user()->name}}</b>
                <a class="layui-icon layui-icon-logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();"></a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
        <hr>
    </div>
</div>
<div class="main">
    @yield('content')
</div>
<div class="footer"></div>
</body>
<script src="/npm/layui@2.6.8/dist/layui.min.js"></script>
<script>
    window.$ = layui.$;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<script src="/js/space.js"></script>
@yield('script')
</html>
