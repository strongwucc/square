<?php

namespace App\Admin\Controllers;

use App\Models\SdbB2cOrders;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SdbB2cOrdersController extends Controller
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
            ->header('商户订单列表')
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
        $grid = new Grid(new SdbB2cOrders);

        $grid->order_id('Order id');
        $grid->seller_order_id('Seller order id');
        $grid->total_amount('Total amount');
        $grid->final_amount('Final amount');
        $grid->pay_status('Pay status');
        $grid->ship_status('Ship status');
        $grid->is_delivery('Is delivery');
        $grid->createtime('Createtime');
        $grid->last_modified('Last modified');
        $grid->payment('Payment');
        $grid->shipping_id('Shipping id');
        $grid->shipping('Shipping');
        $grid->member_id('Member id');
        $grid->platform_member_id('Platform member id');
        $grid->store_id('Store id');
        $grid->confirm_status('Confirm status');
        $grid->confirm_time('Confirm time');
        $grid->pickself_status('Pickself status');
        $grid->pickself_time('Pickself time');
        $grid->pickself_id('Pickself id');
        $grid->operator_id('Operator id');
        $grid->weixinscan_qrcode('Weixinscan qrcode');
        $grid->alipay_qrcode('Alipay qrcode');
        $grid->unionpay_qrcode('Unionpay qrcode');
        $grid->qrcode('Qrcode');
        $grid->promotion_type('Promotion type');
        $grid->status('Status');
        $grid->confirm('Confirm');
        $grid->ship_area('Ship area');
        $grid->ship_name('Ship name');
        $grid->weight('Weight');
        $grid->tostr('Tostr');
        $grid->itemnum('Itemnum');
        $grid->ip('Ip');
        $grid->ship_addr('Ship addr');
        $grid->ship_zip('Ship zip');
        $grid->ship_tel('Ship tel');
        $grid->ship_email('Ship email');
        $grid->ship_time('Ship time');
        $grid->ship_mobile('Ship mobile');
        $grid->cost_item('Cost item');
        $grid->is_tax('Is tax');
        $grid->tax_type('Tax type');
        $grid->tax_content('Tax content');
        $grid->cost_tax('Cost tax');
        $grid->tax_company('Tax company');
        $grid->is_protect('Is protect');
        $grid->cost_protect('Cost protect');
        $grid->cost_payment('Cost payment');
        $grid->currency('Currency');
        $grid->cur_rate('Cur rate');
        $grid->score_u('Score u');
        $grid->score_g('Score g');
        $grid->discount('Discount');
        $grid->pmt_goods('Pmt goods');
        $grid->pmt_order('Pmt order');
        $grid->payed('Payed');
        $grid->memo('Memo');
        $grid->disabled('Disabled');
        $grid->displayonsite('Displayonsite');
        $grid->mark_type('Mark type');
        $grid->mark_text('Mark text');
        $grid->cost_freight('Cost freight');
        $grid->extend('Extend');
        $grid->order_refer('Order refer');
        $grid->addon('Addon');
        $grid->source('Source');
        $grid->source_name('Source name');
        $grid->merchant_bn('Merchant bn');

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
        $show = new Show(SdbB2cOrders::findOrFail($id));

        $show->order_id('Order id');
        $show->seller_order_id('Seller order id');
        $show->total_amount('Total amount');
        $show->final_amount('Final amount');
        $show->pay_status('Pay status');
        $show->ship_status('Ship status');
        $show->is_delivery('Is delivery');
        $show->createtime('Createtime');
        $show->last_modified('Last modified');
        $show->payment('Payment');
        $show->shipping_id('Shipping id');
        $show->shipping('Shipping');
        $show->member_id('Member id');
        $show->platform_member_id('Platform member id');
        $show->store_id('Store id');
        $show->confirm_status('Confirm status');
        $show->confirm_time('Confirm time');
        $show->pickself_status('Pickself status');
        $show->pickself_time('Pickself time');
        $show->pickself_id('Pickself id');
        $show->operator_id('Operator id');
        $show->weixinscan_qrcode('Weixinscan qrcode');
        $show->alipay_qrcode('Alipay qrcode');
        $show->unionpay_qrcode('Unionpay qrcode');
        $show->qrcode('Qrcode');
        $show->promotion_type('Promotion type');
        $show->status('Status');
        $show->confirm('Confirm');
        $show->ship_area('Ship area');
        $show->ship_name('Ship name');
        $show->weight('Weight');
        $show->tostr('Tostr');
        $show->itemnum('Itemnum');
        $show->ip('Ip');
        $show->ship_addr('Ship addr');
        $show->ship_zip('Ship zip');
        $show->ship_tel('Ship tel');
        $show->ship_email('Ship email');
        $show->ship_time('Ship time');
        $show->ship_mobile('Ship mobile');
        $show->cost_item('Cost item');
        $show->is_tax('Is tax');
        $show->tax_type('Tax type');
        $show->tax_content('Tax content');
        $show->cost_tax('Cost tax');
        $show->tax_company('Tax company');
        $show->is_protect('Is protect');
        $show->cost_protect('Cost protect');
        $show->cost_payment('Cost payment');
        $show->currency('Currency');
        $show->cur_rate('Cur rate');
        $show->score_u('Score u');
        $show->score_g('Score g');
        $show->discount('Discount');
        $show->pmt_goods('Pmt goods');
        $show->pmt_order('Pmt order');
        $show->payed('Payed');
        $show->memo('Memo');
        $show->disabled('Disabled');
        $show->displayonsite('Displayonsite');
        $show->mark_type('Mark type');
        $show->mark_text('Mark text');
        $show->cost_freight('Cost freight');
        $show->extend('Extend');
        $show->order_refer('Order refer');
        $show->addon('Addon');
        $show->source('Source');
        $show->source_name('Source name');
        $show->merchant_bn('Merchant bn');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SdbB2cOrders);

        $form->text('order_id', 'Order id');
        $form->text('seller_order_id', 'Seller order id');
        $form->decimal('total_amount', 'Total amount')->default(0.000);
        $form->decimal('final_amount', 'Final amount')->default(0.000);
        $form->text('pay_status', 'Pay status');
        $form->text('ship_status', 'Ship status');
        $form->text('is_delivery', 'Is delivery')->default('Y');
        $form->number('createtime', 'Createtime');
        $form->number('last_modified', 'Last modified');
        $form->text('payment', 'Payment');
        $form->number('shipping_id', 'Shipping id');
        $form->text('shipping', 'Shipping');
        $form->number('member_id', 'Member id');
        $form->text('platform_member_id', 'Platform member id');
        $form->number('store_id', 'Store id');
        $form->text('confirm_status', 'Confirm status');
        $form->number('confirm_time', 'Confirm time');
        $form->text('pickself_status', 'Pickself status');
        $form->number('pickself_time', 'Pickself time');
        $form->number('pickself_id', 'Pickself id');
        $form->number('operator_id', 'Operator id');
        $form->text('weixinscan_qrcode', 'Weixinscan qrcode');
        $form->text('alipay_qrcode', 'Alipay qrcode');
        $form->text('unionpay_qrcode', 'Unionpay qrcode');
        $form->text('qrcode', 'Qrcode');
        $form->text('promotion_type', 'Promotion type')->default('normal');
        $form->text('status', 'Status')->default('active');
        $form->text('confirm', 'Confirm')->default('N');
        $form->text('ship_area', 'Ship area');
        $form->text('ship_name', 'Ship name');
        $form->decimal('weight', 'Weight');
        $form->textarea('tostr', 'Tostr');
        $form->number('itemnum', 'Itemnum');
        $form->ip('ip', 'Ip');
        $form->textarea('ship_addr', 'Ship addr');
        $form->text('ship_zip', 'Ship zip');
        $form->text('ship_tel', 'Ship tel');
        $form->text('ship_email', 'Ship email');
        $form->text('ship_time', 'Ship time');
        $form->text('ship_mobile', 'Ship mobile');
        $form->decimal('cost_item', 'Cost item')->default(0.000);
        $form->text('is_tax', 'Is tax')->default('false');
        $form->text('tax_type', 'Tax type')->default('false');
        $form->text('tax_content', 'Tax content');
        $form->decimal('cost_tax', 'Cost tax')->default(0.000);
        $form->text('tax_company', 'Tax company');
        $form->text('is_protect', 'Is protect')->default('false');
        $form->decimal('cost_protect', 'Cost protect')->default(0.000);
        $form->decimal('cost_payment', 'Cost payment');
        $form->text('currency', 'Currency');
        $form->decimal('cur_rate', 'Cur rate')->default(1.0000);
        $form->decimal('score_u', 'Score u')->default(0.000);
        $form->decimal('score_g', 'Score g')->default(0.000);
        $form->decimal('discount', 'Discount')->default(0.000);
        $form->decimal('pmt_goods', 'Pmt goods');
        $form->decimal('pmt_order', 'Pmt order');
        $form->decimal('payed', 'Payed')->default(0.000);
        $form->textarea('memo', 'Memo');
        $form->text('disabled', 'Disabled')->default('false');
        $form->text('displayonsite', 'Displayonsite')->default('true');
        $form->text('mark_type', 'Mark type')->default('b1');
        $form->textarea('mark_text', 'Mark text');
        $form->decimal('cost_freight', 'Cost freight')->default(0.000);
        $form->text('extend', 'Extend')->default('false');
        $form->text('order_refer', 'Order refer')->default('local');
        $form->textarea('addon', 'Addon');
        $form->text('source', 'Source')->default('pc');
        $form->text('source_name', 'Source name');
        $form->text('merchant_bn', 'Merchant bn');

        return $form;
    }
}
