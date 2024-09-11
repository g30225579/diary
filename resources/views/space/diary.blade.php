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
                                    <div style="display:flex;flex-wrap:wrap;">
                                        <span class="time">{{$v->create_time}}</span>
                                        <div style="margin-left:.4rem">
                                            @foreach($v->tags as $tag)
                                                <span class="layui-badge {{$tagMap[$tag]['badge']}}" style="transform:scale(.9)">{{$tag}}</span>
                                            @endforeach
                                        </div>
                                    </div>
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
                    <form class="layui-form" action="/space" style="display:flex;margin-bottom:1rem;">
                        <div class="layui-inline" style="width:100px">
                            <select name="keyword_type">
                                <option value="">全部</option>
                                <option value="1" @if(request()->input('keyword_type')==1) selected @endif>标题</option>
                                <option value="2" @if(request()->input('keyword_type')==2) selected @endif>内容</option>
                            </select>
                        </div>
                        <input type="text" name="keyword" value="{{request()->input('keyword')}}" lay-verify="required" placeholder="关键词" autocomplete="off" class="layui-input" />
                        <input type="submit" style="display:none" />
                    </form>
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
            let params = getUrlParams();
            let url = window.location.pathname + '?';
            let queryString = '';
            for(const k in params){
                if(k === 'page') continue;
                let value = params[k] !== undefined ? params[k] : '';
                queryString += `&${k}=${value}`;
            }

            let laypage = layui.laypage;
            let page = {{$pageList->currentPage()}};

            let layout = ['count', 'prev', 'page', 'next'];
            if(isMobile){
                layout = ['count', 'prev', 'next'];
            }
            var pageParams = {
                count: {{$pageList->total()}},
                curr: page,
                limit: {{$pageList->perPage()}},
                layout: layout,
                jump: function(obj, first){
                    if(first) return;
                    if(page === obj.curr) return;

                    queryString += '&page=' + obj.curr;
                    window.location = url + queryString.substring(1);
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
