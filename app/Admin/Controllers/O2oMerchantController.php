<?php

namespace App\Admin\Controllers;

use App\Models\O2oMerchant;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class O2oMerchantController extends Controller
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
            ->header('商户列表')
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
        $grid = new Grid(new O2oMerchant);

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->disableCreateButton();

//        $grid->id('Id');
        $grid->mer_id('商户编号');
        $grid->mer_name('商户名称');
//        $grid->type_code('Type code');
        $grid->mer_addr('商户地址');
//        $grid->mer_pic('Mer pic');
        $grid->contact_mobile('联系人电话');
        $grid->contact_person('联系人');
//        $grid->title('Title');
//        $grid->per_cost('Per cost');
//        $grid->details('Details');
        $grid->open_time('营业时间');
//        $grid->longitude('Longitude');
//        $grid->Latitude('Latitude');
//        $grid->mer_pay('Mer pay');
//        $grid->mer_pay_percent('Mer pay percent');
//        $grid->orders_total('Orders total');
//        $grid->orders_total_percent('Orders total percent');
//        $grid->last_time('Last time');
        $grid->create_user('创建人');
//        $grid->is_del('Is del');
//        $grid->status('Status');
//        $grid->sdm('Sdm');

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
        $show = new Show(O2oMerchant::findOrFail($id));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

//        $show->id('Id');
        $show->mer_id('商户编号');
        $show->mer_name('商户名称');
        $show->type_code('商户类型');
        $show->mer_addr('商户地址');
        $show->mer_pic('商户图片');
        $show->contact_mobile('联系人手机号');
        $show->contact_person('联系人姓名');
        $show->title('标签');
        $show->per_cost('人均消费');
        $show->details('详情');
        $show->open_time('营业时间');
        $show->longitude('纬度');
        $show->Latitude('经度');
        $show->mer_pay('商户支付商圈金额');
        $show->mer_pay_percent('金额权重');
        $show->orders_total('商户订单数');
        $show->orders_total_percent('商户订单统计权重');
        $show->last_time('最后修改时间');
        $show->create_user('创建人');
        $show->is_del('是否显示');
        $show->status('是否激活');
//        $show->sdm('Sdm');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oMerchant);

        $form->text('mer_id', 'Mer id');
        $form->text('mer_name', 'Mer name');
        $form->text('type_code', 'Type code');
        $form->text('mer_addr', 'Mer addr');
        $form->text('mer_pic', 'Mer pic');
        $form->text('contact_mobile', 'Contact mobile');
        $form->text('contact_person', 'Contact person');
        $form->text('title', 'Title');
        $form->number('per_cost', 'Per cost');
        $form->text('details', 'Details');
        $form->text('open_time', 'Open time');
        $form->text('longitude', 'Longitude');
        $form->text('Latitude', 'Latitude');
        $form->decimal('mer_pay', 'Mer pay');
        $form->text('mer_pay_percent', 'Mer pay percent');
        $form->number('orders_total', 'Orders total');
        $form->text('orders_total_percent', 'Orders total percent');
        $form->datetime('last_time', 'Last time')->default(date('Y-m-d H:i:s'));
        $form->number('create_user', 'Create user');
        $form->text('is_del', 'Is del');
        $form->text('status', 'Status');
        $form->text('sdm', 'Sdm');

        return $form;
    }
}
