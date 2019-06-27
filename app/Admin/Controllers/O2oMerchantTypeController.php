<?php

namespace App\Admin\Controllers;

use App\Models\O2oMerchantType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class O2oMerchantTypeController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
//        return $content
//            ->header('商户类型管理')
//            ->description('description')
//            ->body($this->grid());
        return Admin::content(function (Content $content) {
            $content->header('商户类型管理');
            $content->body(O2oMerchantType::tree(function ($tree) {
                $tree->branch(function ($branch) {
                    $is_del = $branch['is_del'] ? '<span style="color: red">未启用</span>' : '<span style="color: green">已启用</span>';
                    return "{$branch['type_name']} - {$is_del}";
                });
            }));
        });
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
//            ->header('Detail')
//            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
//            ->header('Edit')
//            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
//            ->header('Create')
//            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new O2oMerchantType);

        $grid->disableExport();

        $grid->id('ID');
        $grid->type_name('类型名称');
//        $grid->type_code('Type code');
//        $grid->pcode('Pcode');
//        $grid->tag_pic('Tag pic');
//        $grid->jump_url('Jump url');
//        $grid->sort_rank('Sort rank');
        $grid->is_del('状态');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(O2oMerchantType::findOrFail($id));

        $show->id('Id');
        $show->type_name('Type name');
        $show->type_code('Type code');
        $show->pcode('Pcode');
        $show->tag_pic('Tag pic');
        $show->jump_url('Jump url');
        $show->sort_rank('Sort rank');
        $show->is_del('Is del');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oMerchantType);

        $form->text('type_name', '类型名称');
//        $form->text('type_code', 'Type code');
//        $form->text('pcode', 'Pcode');
//        $form->select('pcode','父级类型')->options('/api/users');
        $form->select('pcode', '父级类型')->options(function ($type_code) {
            $type = O2oMerchantType::find($type_code);
            if ($type) {
                return [$type->type_code => $type->type_name];
            }
        })->ajax('/admin/api/merchant_types');
        $form->image('tag_pic', '图片');
        $form->text('jump_url', '点击链接');
        $form->number('sort_rank', '排序');
//        $form->text('is_del', '是否启用');
        $form->radio('is_del', '是否启用')->options(['0' => '是', '1'=> '否'])->default('0');

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $type_model = new O2oMerchantType();
            $form->model()->type_code = $type_model->getTypeCode();
        });

        return $form;
    }

    // 定义下拉框搜索接口
    public function apiTypes(Request $request)
    {
        // 用户输入的值通过 q 参数获取
        $search = $request->input('q');
        $result = O2oMerchantType::query()
            ->where('type_name', 'like', '%'.$search.'%')
            ->paginate();

        // 把查询出来的结果重新组装成 Laravel-Admin 需要的格式
        $result->setCollection($result->getCollection()->map(function (O2oMerchantType $type) {
            return ['id' => $type->type_code, 'text' => $type->type_name];
        }));

        $result->setCollection($result->getCollection()->prepend(['id' => '0', 'text' => '无', 'selected'=>true]));

        return $result;
    }
}
