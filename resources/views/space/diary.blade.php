@extends('layouts.space')

@section('content')
    <div class="layui-fluid diary_home_list">
        <div class="layui-row">
            <div class="layui-col-xs12 layui-col-md9">
                <div class="pagination"></div>
                <ul class="layui-timeline">
                    @foreach($pageList->items() as $v)
                        <li class="layui-timeline-item">
                            <i class="layui-icon layui-timeline-axis">&#xe66e;</i>
                            <div class="layui-timeline-content layui-text">
                                <h3 class="layui-timeline-title"><a href="/space/diary/{{$v->id}}/view">{{$v->title}}</a></h3>
                                <div class="sub_title">
                                    <span class="time">{{$v->create_time}}</span>
                                    <a href="/space/diary/{{$v->id}}/edit" class="layui-btn layui-btn-primary layui-border-red layui-btn-xs">编辑</a>
                                </div>
                                <p class="content">{{$v->content}}</p>
                                <div class="item_foot"><a href="/space/diary/{{$v->id}}/view">阅读全文</a></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="pagination"></div>
            </div>
            <div class="layui-col-xs12 layui-col-md3 main_right">
                <div class="layui-bg-gray">
                    <div class="layui-card">
                        <div class="layui-card-header">归档</div>
                        <div class="layui-card-body">
                            <div id="tree"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        isMobile = {{checkMobile() ? 1 : 0}};

        layui.use('laypage', function(){
            var laypage = layui.laypage;

            //执行一个laypage实例
            let layout = ['count', 'prev', 'page', 'next'];
            if(isMobile){
                layout = ['count', 'prev', 'next'];
            }
            var pageParams = {
                count: {{$pageList->total()}},
                curr: {{$pageList->currentPage()}},
                limit: {{$pageList->perPage()}},
                layout: layout,
                jump: function(obj, first){
                    if(!first){
                        window.location = '/space?page=' + obj.curr;
                    }
                }
            }
            document.querySelectorAll('.pagination').forEach(function(obj){
                pageParams.elem = obj;
                laypage.render(pageParams);
            });
        });

        layui.use('tree', function(){
            var tree = layui.tree;

            //渲染
            tree.render({
                elem: '#tree',  //绑定元素
                data: @json($tree),
                showLine: false,
                click: function(obj){
                    if(obj.data.id){
                        window.location = '/space?date=' + obj.data.id;
                    }
                }
            });
        });
    </script>
@endsection
