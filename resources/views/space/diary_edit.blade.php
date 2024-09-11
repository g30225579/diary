@extends('layouts.space')

@section('title', '写记事')

@section('style')
    <style>
        .layui-form-pane .layui-form-checkbox{margin:0;margin-right:10px;margin-top:2px;margin-bottom:2px}
    </style>
@endsection

@section('content')
    <div class="layui-fluid diary_edit">
        <div class="layui-row">
            <form method="post" class="layui-form layui-form-pane" action="{{$pageData['formAction']}}">
                <div class="layui-form-item layui-form-text">
                    <div class="layui-input-block">
                        <input type="text" name="title" value="{{data_get($diary,'title')}}" placeholder="标题" required  lay-verify="required" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <div style="display:flex;align-items:center;flex-wrap:wrap">
                        @foreach($tagMap as $tag=>$tagCount)
                            <input type="checkbox" name="tags[]" title="{{$tag}}" value="{{$tag}}" lay-skin="tag" @if(in_array($tag,$diaryTags)) checked @endif />
                        @endforeach
                        <input type="text" name="tag_str" placeholder="自定义标签，多个逗号分隔" class="layui-input" style="max-width:200px;" />
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <div class="layui-input-block">
                        <textarea class="c_tinymce_editor" name="content" placeholder="正文">{{data_get($diary,'content')}}</textarea>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-primary layui-border-red" lay-submit>完成</button>
                    </div>
                </div>
                @csrf
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="/static/lib/tinymce/tinymce.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/spark-md5@3.0.2/spark-md5.min.js"></script>
    <script>
        bucketUrl = '{{\App\Helpers\Aliyun\AliyunOss::getBucketUrl()}}';

        loadTinymceEditor('c_tinymce_editor', "{{\App\Enums\Common\UPLOAD_TYPE::DIARY}}");
    </script>
@endsection
