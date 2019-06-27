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
            ->header('商圈订单列表')
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
        $grid = new Grid(new O2oOrder);

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('order_no', '订单号');
            $filter->like('mer_id', '商户号');
            $filter->like('member_id', '会员ID');
            $filter->equal('source', '订单来源')->radio([
                ''   => '全部',
                '01'    => '停车缴费',
                '02'    => '优惠券购买'
            ]);
            $filter->equal('pay_type', '支付方式')->radio([
                ''   => '全部',
                '02'    => '支付宝支付',
                '03'    => '微信支付'
            ]);
            $filter->equal('pay_result', '付款状态')->radio([
                ''   => '全部',
                '0000'    => '支付成功',
                '1111'    => '支付初始状态',
                '8888'    => '未支付',
                '9999'    => '支付失败'
            ]);
            $filter->between('tran_time', '下单时间')->datetime();

        });

        $grid->disableRowSelector();
        $grid->disableActions();
        $grid->disableExport();

//        $grid->id('Id');
        $grid->order_no('订单号');
        $grid->pay_amount('订单金额')->display(function ($pay_amount) {
            return number_format($pay_amount / 100, 2, '.', '');
        });
        $grid->source('订单来源')->display(function ($source) {
            return $source == '01' ? '停车缴费' : '优惠券购买';
        });
        $grid->pay_type('支付方式')->display(function ($pay_type) {
            return $pay_type == '02' ? '支付宝支付' : '微信支付';
        });
        $grid->pay_result('付款状态')->display(function ($pay_result) {
           if ($pay_result == '0000') {
               return '支付成功';
           }
           if ($pay_result == '1111') {
               return '支付初始状态';
           }
           if ($pay_result == '8888') {
               return '未支付';
           }
           if ($pay_result == '9999') {
               return '支付失败';
           }
        });
        $grid->tran_time('下单时间');
        $grid->mch_id('商户号');
        $grid->member_id('会员ID');
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
