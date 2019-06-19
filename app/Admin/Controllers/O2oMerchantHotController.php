<?php

namespace App\Admin\Controllers;

use App\Models\O2oMerchant;
use App\Models\O2oMerchantHot;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class O2oMerchantHotController extends Controller
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
            ->header('热门商户列表')
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
        $grid = new Grid(new O2oMerchantHot);

//        $grid->id('ID');
        $grid->mer_id('商户编号');
        $grid->mer_name('商户名称');
        $grid->mer_pic('商户主页编号');
        $grid->hot_percent('热门权重');
        $grid->per_cost('人均消费');
//        $grid->details('Details');

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
        $show = new Show(O2oMerchantHot::findOrFail($id));

        $show->id('ID');
        $show->mer_id('商户编号');
        $show->mer_name('商户名称');
        $show->mer_pic('商户图片');
        $show->hot_percent('热门权重');
        $show->per_cost('人均消费');
        $show->details('商户详情');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oMerchantHot);

        $merchant_model = new O2oMerchant();

        $form->select('mer_id', '商户')->options($merchant_model->selectHotOptions());

        $form->number('hot_percent', '热门权重');

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) use ($merchant_model) {
            $mer_id = $form->mer_id;
            $merchant = $merchant_model->where('mer_id', $mer_id)->first();
            $form->model()->mer_name = $merchant->mer_name;
            $form->model()->mer_pic = $merchant->mer_pic;
            $form->model()->per_cost = $merchant->per_cost;
            $form->model()->details = $merchant->details;
        });

        return $form;
    }
}
