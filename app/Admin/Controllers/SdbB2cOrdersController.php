<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Filters\TimestampBetween;
use App\Models\O2oMerchant;
use App\Models\SdbB2cOrders;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use App\Admin\Extensions\Exporters\B2cOrdersExporter;

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

        $grid->model()->orderBy('createtime', 'desc');

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

//        $grid->disableExport();
        $grid->disableCreateButton();
        $grid->exporter(new B2cOrdersExporter());

//        $grid->disableRowSelector();

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('order_id', '订单号');
            $filter->like('merchant_bn', '商户号');
            $filter->like('merchant.mer_name', '商户名称');
            $filter->like('platform_member_id', '会员ID');
            $filter->equal('source', '订单来源')->radio([
                ''              => '全部',
                'pc'            => '网站',
                'wap'           => '移动端',
                'cashier'       => '堂食',
                'paycode'       => '扫码',
                'eleme'         => '饿了么',
                'meituan'       => '美团'
            ]);
            $filter->equal('payment', '支付方式')->radio([
                ''   => '全部',
                'offline'           => '现金支付',
                'deposit'           => '会员卡支付',
                'alipaynative'      => '支付宝支付',
                'unionpaynative'    => '银联支付',
                'wxpaynative'       => '微信支付',
                'cardpay'           => '刷卡支付',
                'yktpay'            => '一卡通支付',
                '-1'                => '优惠券抵扣'
            ]);
            $filter->equal('pay_status', '付款状态')->radio([
                ''   => '全部',
                '0'    => '未支付',
                '1'    => '已支付',
                '2'    => '已付款至担保方',
                '3'    => '部分付款',
                '4'    => '部分退款',
                '5'    => '全额退款'
            ]);
            $filter->use(new TimestampBetween('createtime','下单时间'))->date();

        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $actions->prepend('<a class="btn btn-xs" href="/admin/b2c_orders/' . $actions->row->seller_order_id . '">查看详情</a>');
        });

        $grid->seller_order_id('订单号')->expand(function ($model) {

            $items = $model->items()->get()->map(function ($item) {
                return $item->only(['name', 'cost', 'price', 'nums', 'amount']);
            });

            return new Table(['商品名称', '成本价', '销售价', '商品数量', '总额'], $items->toArray());
        });
//        $grid->seller_order_id('Seller order id');
        $grid->total_amount('订单金额')->display(function ($total_amount) {
            return number_format($total_amount, '2', '.', '');
        });
//        $grid->final_amount('Final amount');
        $grid->pay_status('付款状态')->display(function ($pay_status) {
            if ($pay_status == '0') {
                return '未支付';
            }
            if ($pay_status == '1') {
                return '已支付';
            }
            if ($pay_status == '2') {
                return '已付款至担保方';
            }
            if ($pay_status == '3') {
                return '部分付款';
            }
            if ($pay_status == '4') {
                return '部分退款';
            }
            if ($pay_status == '5') {
                return '全额退款';
            }
        });
//        $grid->ship_status('Ship status');
//        $grid->is_delivery('Is delivery');
//        $grid->last_modified('Last modified');
        $grid->payment('支付方式')->display(function ($payment) {
            if ($payment == 'offline') {
                return '现金支付';
            }
            if ($payment == 'deposit') {
                return '会员卡支付';
            }
            if ($payment == '-1') {
                return '优惠券抵扣';
            }
            if ($payment == 'alipaynative') {
                return '支付宝支付';
            }
            if ($payment == 'wxpaynative') {
                return '微信支付';
            }
            if ($payment == 'cardpay') {
                return '刷卡支付';
            }
            if ($payment == 'yktpay') {
                return '一卡通支付';
            }
        });
//        $grid->shipping_id('Shipping id');
//        $grid->shipping('Shipping');
//        $grid->member_id('Member id');
//        $grid->platform_member_id('Platform member id');
//        $grid->store_id('Store id');
//        $grid->confirm_status('Confirm status');
//        $grid->confirm_time('Confirm time');
//        $grid->pickself_status('Pickself status');
//        $grid->pickself_time('Pickself time');
//        $grid->pickself_id('Pickself id');
//        $grid->operator_id('Operator id');
//        $grid->weixinscan_qrcode('Weixinscan qrcode');
//        $grid->alipay_qrcode('Alipay qrcode');
//        $grid->unionpay_qrcode('Unionpay qrcode');
//        $grid->qrcode('Qrcode');
//        $grid->promotion_type('Promotion type');
//        $grid->status('Status');
//        $grid->confirm('Confirm');
//        $grid->ship_area('Ship area');
//        $grid->ship_name('Ship name');
//        $grid->weight('Weight');
//        $grid->tostr('Tostr');
//        $grid->itemnum('Itemnum');
//        $grid->ip('Ip');
//        $grid->ship_addr('Ship addr');
//        $grid->ship_zip('Ship zip');
//        $grid->ship_tel('Ship tel');
//        $grid->ship_email('Ship email');
//        $grid->ship_time('Ship time');
//        $grid->ship_mobile('Ship mobile');
//        $grid->cost_item('Cost item');
//        $grid->is_tax('Is tax');
//        $grid->tax_type('Tax type');
//        $grid->tax_content('Tax content');
//        $grid->cost_tax('Cost tax');
//        $grid->tax_company('Tax company');
//        $grid->is_protect('Is protect');
//        $grid->cost_protect('Cost protect');
//        $grid->cost_payment('Cost payment');
//        $grid->currency('Currency');
//        $grid->cur_rate('Cur rate');
//        $grid->score_u('Score u');
//        $grid->score_g('Score g');
//        $grid->discount('Discount');
//        $grid->pmt_goods('Pmt goods');
//        $grid->pmt_order('Pmt order');
//        $grid->payed('Payed');
//        $grid->memo('Memo');
//        $grid->disabled('Disabled');
//        $grid->displayonsite('Displayonsite');
//        $grid->mark_type('Mark type');
//        $grid->mark_text('Mark text');
//        $grid->cost_freight('Cost freight');
//        $grid->extend('Extend');
//        $grid->order_refer('Order refer');
//        $grid->addon('Addon');
        $grid->source('订单来源')->display(function ($source) {
            if ($source == 'pc') {
                return '平台网站';
            }
            if ($source == 'wap') {
                return '移动端';
            }
            if ($source == 'pc') {
                return '微信';
            }
            if ($source == 'cashier') {
                return '堂食';
            }
            if ($source == 'paycode') {
                return '扫码';
            }
            if ($source == 'eleme') {
                return '饿了么';
            }
            if ($source == 'meituan') {
                return '美团';
            }
        });
//        $grid->source_name('Source name');
        $grid->merchant_bn('商户号');
        $grid->column('merchant.mer_name', '商户名称');
        $grid->column('merchant.contact_person', '联系人');
        $grid->column('merchant.contact_mobile', '联系人手机号');
        $grid->createtime('下单时间')->display(function ($createtime) {
            return date('Y-m-d H:i:s', $createtime);
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
        $order_model = new SdbB2cOrders();
        $order = $order_model->where('order_id', $id)->first();
        $show = new Show($order);

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

//        $show->order_id('订单号');
        $show->seller_order_id('商户订单号');
        $show->total_amount('订单金额');
//        $show->final_amount('Final amount');
        $show->pay_status('支付状态')->as(function ($pay_status) {
            if ($pay_status == '0') {
                return '未支付';
            }
            if ($pay_status == '1') {
                return '已支付';
            }
            if ($pay_status == '2') {
                return '已付款至担保方';
            }
            if ($pay_status == '3') {
                return '部分付款';
            }
            if ($pay_status == '4') {
                return '部分退款';
            }
            if ($pay_status == '5') {
                return '全额退款';
            }
        });
//        $show->ship_status('Ship status');
//        $show->is_delivery('Is delivery');
        $show->createtime('下单时间')->as(function ($createtime) {
            return date('Y-m-d H:i:s', $createtime);
        });
//        $show->last_modified('Last modified');
        $show->payment('支付方式')->as(function ($payment) {
            if ($payment == 'offline') {
                return '现金支付';
            }
            if ($payment == 'deposit') {
                return '会员卡支付';
            }
            if ($payment == '-1') {
                return '优惠券抵扣';
            }
            if ($payment == 'alipaynative') {
                return '支付宝支付';
            }
            if ($payment == 'wxpaynative') {
                return '微信支付';
            }
            if ($payment == 'cardpay') {
                return '刷卡支付';
            }
            if ($payment == 'yktpay') {
                return '一卡通支付';
            }
        });
//        $show->shipping_id('Shipping id');
//        $show->shipping('Shipping');
//        $show->member_id('Member id');
        $show->platform_member_id('会员ID');
//        $show->store_id('Store id');
        $show->confirm_status('确认状态')->as(function ($confirm_status) {
            if ($confirm_status == '0') {
                return '未接单';
            }
            if ($confirm_status == '1') {
                return '已接单';
            }
            if ($confirm_status == '2') {
                return '已拒绝';
            }
            if ($confirm_status == '3') {
                return '已完成';
            }
        });
        $show->confirm_time('确认时间');
        $show->pickself_status('自提状态')->as(function ($pickself_status) {
            if ($pickself_status == '0') {
                return '未自提';
            }
            if ($pickself_status == '1') {
                return '已自提';
            }
        });
        $show->pickself_time('自提时间');
//        $show->pickself_id('Pickself id');
//        $show->operator_id('Operator id');
        $show->weixinscan_qrcode('微信付款码');
        $show->alipay_qrcode('支付宝付款码');
        $show->unionpay_qrcode('银联付款码');
        $show->qrcode('核销码');
//        $show->promotion_type('Promotion type');
        $show->status('订单状态')->as(function ($status) {
            if ($status == 'active') {
                return '活动订单';
            }
            if ($status == 'dead') {
                return '已作废';
            }
            if ($status == 'finished') {
                return '已完成';
            }
        });
//        $show->confirm('Confirm');
//        $show->ship_area('Ship area');
//        $show->ship_name('Ship name');
//        $show->weight('Weight');
//        $show->tostr('Tostr');
//        $show->itemnum('Itemnum');
//        $show->ip('Ip');
//        $show->ship_addr('Ship addr');
//        $show->ship_zip('Ship zip');
//        $show->ship_tel('Ship tel');
//        $show->ship_email('Ship email');
//        $show->ship_time('Ship time');
//        $show->ship_mobile('Ship mobile');
        $show->cost_item('订单商品总价格')->as(function ($cost_item) {
            return number_format($cost_item, 2, '.', '');
        });
        $show->is_tax('是否需要开发票')->as(function ($is_tax) {
            return $is_tax == 'false' ? '否' : '是';
        });
        $show->tax_type('发票类型')->as(function ($tax_type) {
            if ($tax_type == 'personal') {
                return '个人';
            }
            if ($tax_type == 'company') {
                return '公司';
            }
            return '未知';
        });
        $show->tax_content('发票内容');
        $show->cost_tax('订单税率');
        $show->tax_company('发票抬头');
        $show->is_protect('是否还有保价费')->as(function ($is_protect) {
            return $is_protect == 'false' ? '否' : '是';
        });
        $show->cost_protect('保价费')->as(function ($cost_protect) {
            return number_format($cost_protect, 2, '.', '');
        });
        $show->cost_payment('支付费用')->as(function ($cost_payment) {
            return number_format($cost_payment, 2, '.', '');
        });
//        $show->currency('Currency');
//        $show->cur_rate('Cur rate');
        $show->score_u('订单使用积分')->as(function ($score_u) {
            return number_format($score_u, 2, '.', '');
        });
        $show->score_g('订单获得积分')->as(function ($score_g) {
            return number_format($score_g, 2, '.', '');
        });
        $show->discount('订单减免')->as(function ($discount) {
            return number_format($discount, 2, '.', '');
        });
        $show->pmt_goods('商品促销优惠')->as(function ($pmt_goods) {
            return number_format($pmt_goods, 2, '.', '');
        });
        $show->pmt_order('订单促销优惠')->as(function ($pmt_order) {
            return number_format($pmt_order, 2, '.', '');
        });
        $show->payed('订单支付金额')->as(function ($payed) {
            return number_format($payed, 2, '.', '');
        });
//        $show->memo('Memo');
//        $show->disabled('Disabled');
//        $show->displayonsite('Displayonsite');
//        $show->mark_type('Mark type');
//        $show->mark_text('Mark text');
//        $show->cost_freight('Cost freight');
//        $show->extend('Extend');
//        $show->order_refer('Order refer');
//        $show->addon('Addon');
        $show->source('订单来源')->as(function ($source) {
            if ($source == 'pc') {
                return '平台网站';
            }
            if ($source == 'wap') {
                return '移动端';
            }
            if ($source == 'pc') {
                return '微信';
            }
            if ($source == 'cashier') {
                return '堂食';
            }
            if ($source == 'paycode') {
                return '扫码';
            }
            if ($source == 'eleme') {
                return '饿了么';
            }
            if ($source == 'meituan') {
                return '美团';
            }
        });
//        $show->source_name('Source name');
        $show->merchant_bn('商户号');

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
