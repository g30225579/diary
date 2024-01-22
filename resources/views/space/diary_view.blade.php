@extends('layouts.space')

@section('title', $diary->title)

@section('content')
    <div class="layui-fluid diary_home_list diary_view">
        <div class="layui-row">
            <h1 class="title"><i class="layui-icon layui-icon-note"></i> {{$diary->title}}</h1>
            <div class="sub_title">
                <span class="time">创建 {{date('Y年m月d日 H:i', $diary->create_time)}}</span>
                @if($diary->create_time != $diary->update_time)
                    <span class="time" style="margin-left:1rem">最后编辑 {{date('Y年m月d日 H:i', $diary->update_time)}}</span>
                @endif
                <a href="/space/diary/{{$diary->id}}/edit" class="layui-btn layui-btn-primary layui-border-red layui-btn-xs" style="margin-left:1rem">编辑</a>
            </div>
            <div class="layui-panel">
                <div class="content">{!! $diary->content !!}</div>
            </div>
        </div>
        <div class="diary_view_other">
            <div> @if($last) 上一篇：<a href="/space/diary/{{$last->id}}/view">{{$last->title}}</a> @endif </div>
            <div> @if($next) 下一篇：<a href="/space/diary/{{$next->id}}/view">{{$next->title}}</a> @endif</div>
        </div>
    </div>
@endsection
