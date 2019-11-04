<?php

namespace App\Admin\Controllers;

use App\Models\O2oSearchKeyword;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class O2oSearchKeywordController extends Controller
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
            ->header('热搜标签列表')
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
        $grid = new Grid(new O2oSearchKeyword);

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('keyword', '关键字');

        });

        $grid->disableExport();

        $grid->id('ID');
        $grid->keyword('关键字');
        $grid->num('序号');
        $grid->status('是否启用')->display(function ($status) {
            return $status ? '<span class="label label-success">是</span>' : '<span class="label label-danger">否</span>';
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
        $show = new Show(O2oSearchKeyword::findOrFail($id));

        $show->id('ID');
        $show->keyword('关键字');
        $show->num('序号');
        $show->status('是否启用')->as(function ($status) {
            return $status ? '是' : '否';
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
        $form = new Form(new O2oSearchKeyword);

        $form->text('keyword', '关键字')->rules('required');
        $form->number('num', '序号')->rules('required')->attribute(['min' => 0])->default(0);
        $form->radio('status', '是否启用')->options(['0' => '否', '1'=> '是'])->default('1');

        return $form;
    }
}
