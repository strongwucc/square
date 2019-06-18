<?php

namespace App\Admin\Controllers;

use App\Models\O2oOrder;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class O2oOrderController extends Controller
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
            ->header('Index')
            ->description('description')
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
            ->header('Detail')
            ->description('description')
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
            ->header('Edit')
            ->description('description')
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new O2oOrder);

//        $grid->id('Id');
        $grid->order_no('订单号');
        $grid->pay_amount('订单金额');
        $grid->source('订单来源');
        $grid->pay_type('支付方式');
        $grid->pay_result('付款状态');
        $grid->tran_time('下单时间');
        $grid->mch_id('商户号');
//        $grid->member_id('Member id');
//        $grid->shopno('Shopno');
//        $grid->scan_pay_type('Scan pay type');
//        $grid->pay_info('Pay info');
//        $grid->etone_order_id('Etone order id');
//        $grid->remark('Remark');
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
//            $actions->disableView();
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
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
        $show = new Show(O2oOrder::findOrFail($id));

        $show->id('Id');
        $show->order_no('Order no');
        $show->mch_id('Mch id');
        $show->member_id('Member id');
        $show->source('Source');
        $show->shopno('Shopno');
        $show->pay_amount('Pay amount');
        $show->pay_type('Pay type');
        $show->scan_pay_type('Scan pay type');
        $show->pay_result('Pay result');
        $show->pay_info('Pay info');
        $show->tran_time('Tran time');
        $show->etone_order_id('Etone order id');
        $show->remark('Remark');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oOrder);

        $form->text('order_no', 'Order no');
        $form->text('mch_id', 'Mch id');
        $form->text('member_id', 'Member id');
        $form->text('source', 'Source')->default('01');
        $form->text('shopno', 'Shopno');
        $form->text('pay_amount', 'Pay amount');
        $form->text('pay_type', 'Pay type')->default('03');
        $form->text('scan_pay_type', 'Scan pay type')->default('01');
        $form->text('pay_result', 'Pay result')->default('1111');
        $form->text('pay_info', 'Pay info');
        $form->datetime('tran_time', 'Tran time')->default(date('Y-m-d H:i:s'));
        $form->text('etone_order_id', 'Etone order id');
        $form->text('remark', 'Remark');

        return $form;
    }
}
