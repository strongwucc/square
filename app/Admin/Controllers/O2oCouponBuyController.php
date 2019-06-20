<?php

namespace App\Admin\Controllers;

use App\Models\O2oCouponBuy;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class O2oCouponBuyController extends Controller
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
            ->header('已发放优惠券列表')
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
        $grid = new Grid(new O2oCouponBuy);

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->disableCreateButton();

        $grid->pcid('优惠券编号');
        $grid->qrcode('核销码');
//        $grid->order_id('Order id');
        $grid->from_order_id('来源订单号');
//        $grid->cid('Cid');
        $grid->member_id('会员昵称');
//        $grid->openid('Openid');
//        $grid->cashier_id('Cashier id');
//        $grid->pay_status('Pay status');
//        $grid->buy_status('Buy status');
        $grid->use_status('使用状态');
        $grid->createtime('领取时间');
//        $grid->last_modified('Last modified');
//        $grid->platform_member_id('Platform member id');

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
        $show = new Show(O2oCouponBuy::findOrFail($id));

        $show->pcid('Pcid');
        $show->qrcode('Qrcode');
        $show->order_id('Order id');
        $show->from_order_id('From order id');
        $show->cid('Cid');
        $show->member_id('Member id');
        $show->openid('Openid');
        $show->cashier_id('Cashier id');
        $show->pay_status('Pay status');
        $show->buy_status('Buy status');
        $show->use_status('Use status');
        $show->createtime('Createtime');
        $show->last_modified('Last modified');
        $show->platform_member_id('Platform member id');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oCouponBuy);

        $form->text('pcid', 'Pcid');
        $form->text('qrcode', 'Qrcode');
        $form->text('order_id', 'Order id');
        $form->text('from_order_id', 'From order id');
        $form->number('cid', 'Cid');
        $form->text('member_id', 'Member id');
        $form->text('openid', 'Openid');
        $form->text('cashier_id', 'Cashier id');
        $form->text('pay_status', 'Pay status');
        $form->text('buy_status', 'Buy status');
        $form->text('use_status', 'Use status');
        $form->text('platform_member_id', 'Platform member id');

        return $form;
    }
}