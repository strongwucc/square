<?php

namespace App\Admin\Controllers;

use App\Models\O2oPicType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class O2oPicTypeController extends Controller
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
        return $content
            ->header('商户图片类型列表')
//            ->description('description')
            ->body($this->grid());
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
        $grid = new Grid(new O2oPicType);

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('type_name', '类型名称');

        });

        $grid->disableExport();

        $grid->id('ID');
        $grid->type_name('类型名称');
//        $grid->type_code('Type code');
        $grid->is_del('是否启用')->display(function ($is_del) {
            return $is_del ? '<span class="label label-danger">否</span>' : '<span class="label label-success">是</span>';
        });

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
        $show = new Show(O2oPicType::findOrFail($id));

        $show->id('ID');
        $show->type_name('类型名称');
//        $show->type_code('Type code');
        $show->is_del('是否启用')->as(function ($is_del) {
            return $is_del ? '否' : '是';
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oPicType);

        $form->text('type_name', '类型名称');
//        $form->text('type_code', 'Type code');
        $form->radio('is_del', '是否启用')->options(['0' => '是', '1'=> '否'])->default('0');

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $type_model = new O2oPicType();
            $form->model()->type_code = $type_model->getTypeCode();
        });

        return $form;
    }
}
