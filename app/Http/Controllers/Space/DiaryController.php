<?php
namespace App\Http\Controllers\Space;

use App\Helpers\StringUtils;
use App\Http\Controllers\Controller;
use App\Service\TagService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * 记事管理
 */

class DiaryController extends Controller
{
    private $tagService;

    public function __construct()
    {
        $this->tagService = new TagService();
    }

    public function index()
    {
        $date = request()->input('date');
        $keywordType = request()->input('keyword_type');
        $keyword = request()->input('keyword');

        $nowYear = date('Y');

        //记事列表
        $builder = DB::table('diary')->where('user_id', auth()->id());

        $tree = $this->getTreeData($builder);

        //日期
        if($date){
            $builder->where(DB::raw("from_unixtime(create_time,'%Y-%m')"), $date);
        }
        //搜索
        if($keyword){
            $builder->where(function(Builder $query) use($keywordType,$keyword){
                if(!$keywordType || $keywordType==1){ //标题
                    $query->orWhere('title', 'like', '%'.$keyword.'%');
                }
                if(!$keywordType || $keywordType == 2){ //内容
                    $query->orWhere('content', 'like', '%'.$keyword.'%');
                }
            });
        }

        $pageList = $builder->orderByDesc('create_time')->paginate(20);

        collect($pageList->items())->map(function($v) use($nowYear,&$tags){
            if($nowYear == date('Y',$v->create_time)){
                $v->create_time = date('m月d日 H:i',$v->create_time);
            } else{
                $v->create_time = date('Y年m月d日 H:i',$v->create_time);
            }

            $v->content = StringUtils::getSummary($v->content, 100);

            $v->tags = $this->tagService->getTagsByJsonStr($v->tags);

            return $v;
        });

        $tagMap = $this->tagService->getTagMap();

        return view('space.diary', [
            'pageList' => $pageList,
            'tagMap' => $tagMap,
            'tree' => $tree
        ]);
    }

    //构造树形组件数据
    private function getTreeData(Builder $builder): array
    {
        $date = request()->input('date');
        $nowYear = date('Y');
        $cloneBuiler = clone $builder;

        //统计信息
        $resCount = $cloneBuiler
            ->select([
                DB::raw("from_unixtime(create_time,'%Y') as year"),
                DB::raw("from_unixtime(create_time,'%m') as month"),
                DB::raw('count(*) as total')
            ])
            ->groupBy(['year','month'])
            ->orderByDesc('year')->orderByDesc('month')
            ->get();
        //构造树形组件数据
        $tree = [];
        foreach ($resCount->groupBy('year') as $year=>$yearCollection){
            $node = [
                'title' => sprintf('%d年 (%d)', $year, $yearCollection->sum('total')),
                'spread' => in_array($year, [$nowYear,date('Y',strtotime($date))])
            ];

            $children = [];
            foreach ($yearCollection as $monthData){
                $id = sprintf('%s-%s', $monthData->year, $monthData->month);
                $children[] = [
                    'id' => $id,
                    'title' => sprintf('%d年%d月 (%d)', $monthData->year, intval($monthData->month), $monthData->total),
                    'disabled' => $id == $date
                ];
            }
            $node['children'] = $children;

            $tree[] = $node;
        }

        return $tree;
    }

    /**
     * 详情页
     */
    public function view(int $id)
    {
        $diary = DB::table('diary')->where('user_id', auth()->id())->where('id', $id)->first();

        $tagMap = [];
        foreach($this->tagService->getTagsByJsonStr($diary->tags) as $tag){
            $tagMap[$tag] = $this->tagService->getTagBadge($tag);
        }

        //上一篇
        $last = DB::table('diary')->where('user_id',auth()->id())->where('create_time','<',$diary->create_time)->orderByDesc('create_time')->first();
        //下一篇
        $next = DB::table('diary')->where('user_id',auth()->id())->where('create_time','>',$diary->create_time)->orderBy('create_time')->first();

        return view('space.diary_view', [
            'diary' => $diary,
            'tagMap' => $tagMap,
            'last' => $last,
            'next' => $next
        ]);
    }

    /**
     * 新增记事页
     */
    public function create()
    {
        $tagMap = array_slice($this->tagService->getTagMap(),0,30);

        return view('space.diary_edit',[
            'diary' => null,
            'pageData' => [
                'formAction' => '/space/diary/store',
            ],
            'tagMap' => $tagMap,
            'diaryTags' => []
        ]);
    }

    /**
     * 编辑记事页
     */
    public function edit(int $id)
    {
        $tagMap = array_slice($this->tagService->getTagMap(),0,30);

        $diary = DB::table('diary')->where('user_id', auth()->id())->where('id', $id)->first();

        //当前标签放置最前
        $diaryTags = $this->tagService->getTagsByJsonStr($diary->tags);

        return view('space.diary_edit',[
            'diary' => $diary,
            'pageData' => [
                'formAction' => '/space/diary/'.$diary->id.'/update',
            ],
            'tagMap' => $tagMap,
            'diaryTags' => $diaryTags,
        ]);
    }

    /**
     * 保存记事
     */
    public function save($id = null)
    {
        $title = request()->input('title');
        $content = request()->input('content');
        $tags = request()->input('tags',[]);
        $tagStr = request()->input('tag_str');

        //日志标签（中文逗号转为英文逗号）
        if($tagStr){
            $tagStr = str_replace('，',',',$tagStr);
            $tags = array_unique(array_merge($tags,explode(TagService::SEPARATOR,$tagStr)));
        }

        if(!$title){
            $title = '无标题';
        }

        if($id){
            DB::table('diary')->where('user_id', auth()->id())->where('id', $id)->update([
                'title' => $title,
                'content' => $content,
                'tags' => json_encode($tags),
                'update_time' => time()
            ]);
        } else{
            $id = DB::table('diary')->insertGetId([
                'user_id' => auth()->id(),
                'title' => $title,
                'content' => $content,
                'tags' => json_encode($tags),
                'create_time' => time(),
                'update_time' => time()
            ]);
        }

        //保存日志后清理标签缓存
        $this->tagService->tagMapClear();

        return redirect('/space/diary/'.$id.'/view');
    }

}
