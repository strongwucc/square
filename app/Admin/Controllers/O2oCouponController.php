<?php

namespace App\Admin\Controllers;

use App\Models\O2oCoupon;
use App\Http\Controllers\Controller;
use App\Models\O2oCouponMerchant;
use App\Models\O2oMerchant;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets;
use Illuminate\Support\MessageBag;

class O2oCouponController extends Controller
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
            ->header('优惠券模板列表')
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
//        $coupon = new Coupon();
//        return $content->header('网站设置')
//            ->body($coupon->render());
        $tab = new Widgets\Tab();
        $tab->add('折扣券', $this->discount_form()->render());
        $tab->add('代金券', $this->cash_form()->render());
        return $content
            ->body($tab);
//        return $content
//            ->header('Create')
//            ->description('description')
//            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new O2oCoupon);

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('pcid', '券编号');
            $filter->equal('card_type', '类型')->radio([
                ''                          => '全部',
                'GROUPON'                   => '团购券',
                'CASH'                      => '代金券',
                'DISCOUNT'                  => '折扣券',
                'GIFT'                      => '礼品券',
                'GENERAL_COUPON'            => '优惠券'
            ]);

            $filter->equal('coupon_status', '是否激活')->radio([
                ''   => '全部',
                '0'           => '是',
                '1'           => '否'
            ]);
            $filter->equal('status', '状态')->radio([
                ''  => '全部',
                '1' => '进行中',
                '2' => '未开始',
                '3' => '已结束'
            ]);

        });

        $grid->pcid('编号');
//        $grid->cid('Cid');
        $grid->mer_id('适用商户');
//        $grid->promotion_id('Promotion id');
//        $grid->brand_name('Brand name');
        $grid->card_type('优惠券类型')->display(function ($card_type) {
            if ($card_type == 'GROUPON') {
                return '团购券';
            }
            if ($card_type == 'CASH') {
                return '代金券';
            }
            if ($card_type == 'DISCOUNT') {
                return '折扣券';
            }
            if ($card_type == 'GIFT') {
                return '礼品券';
            }
            if ($card_type == 'FULL_REDUCTION') {
                return '满减券';
            }
        });
//        $grid->logo_url('Logo url');
//        $grid->is_sync('Is sync');
//        $grid->wei_logo_url('Wei logo url');
//        $grid->code_type('Code type');
        $grid->title('优惠券名称');
//        $grid->sub_title('Sub title');
//        $grid->color('Color');
//        $grid->notice('Notice');
//        $grid->description('Description');
//        $grid->grant_quantity('Grant quantity');
        $grid->quantity('库存数量');
//        $grid->date_type('Date type');
//        $grid->begin_timestamp('Begin timestamp');
//        $grid->end_timestamp('End timestamp');
//        $grid->fixed_term('Fixed term');
//        $grid->fixed_begin_term('Fixed begin term');
//        $grid->card_id('Card id');
//        $grid->bind_openid('Bind openid');
//        $grid->service_phone('Service phone');
//        $grid->source('Source');
//        $grid->custom_url_name('Custom url name');
//        $grid->custom_url_sub_title('Custom url sub title');
//        $grid->custom_url('Custom url');
//        $grid->center_title('Center title');
//        $grid->center_sub_title('Center sub title');
//        $grid->center_url('Center url');
//        $grid->promotion_url_name('Promotion url name');
//        $grid->promotion_url('Promotion url');
//        $grid->promotion_url_sub_title('Promotion url sub title');
//        $grid->get_limit('Get limit');
//        $grid->use_custom_code('Use custom code');
//        $grid->can_share('Can share');
//        $grid->can_give_friend('Can give friend');
//        $grid->deal_detail('Deal detail');
//        $grid->least_cost('Least cost');
//        $grid->reduce_cost('Reduce cost');
//        $grid->discount('Discount');
//        $grid->gift('Gift');
//        $grid->default_detail('Default detail');
        $grid->status('状态')->display(function ($status) {
            if ($status == '1') {
                return '进行中';
            }
            if ($status == '2') {
                return '未开始';
            }
            if ($status == '3') {
                return '已结束';
            }
        });
//        $grid->is_buy('Is buy');
//        $grid->market_price('Market price');
//        $grid->sale_price('Sale price');
//        $grid->createtime('Createtime');
//        $grid->last_modified('Last modified');
//        $grid->is_del('Is del');
        $grid->coupon_status('是否激活')->display(function ($coupon_status) {
            return $coupon_status == '0' ? '是' : '否';
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
        $show = new Show(O2oCoupon::findOrFail($id));

        $show->pcid('Pcid');
        $show->cid('Cid');
        $show->mer_id('Mer id');
        $show->promotion_id('Promotion id');
        $show->brand_name('Brand name');
        $show->card_type('Card type');
        $show->logo_url('Logo url');
        $show->is_sync('Is sync');
        $show->wei_logo_url('Wei logo url');
        $show->code_type('Code type');
        $show->title('Title');
        $show->sub_title('Sub title');
        $show->color('Color');
        $show->notice('Notice');
        $show->description('Description');
        $show->grant_quantity('Grant quantity');
        $show->quantity('Quantity');
        $show->date_type('Date type');
        $show->begin_timestamp('Begin timestamp');
        $show->end_timestamp('End timestamp');
        $show->fixed_term('Fixed term');
        $show->fixed_begin_term('Fixed begin term');
        $show->card_id('Card id');
        $show->bind_openid('Bind openid');
        $show->service_phone('Service phone');
        $show->source('Source');
        $show->custom_url_name('Custom url name');
        $show->custom_url_sub_title('Custom url sub title');
        $show->custom_url('Custom url');
        $show->center_title('Center title');
        $show->center_sub_title('Center sub title');
        $show->center_url('Center url');
        $show->promotion_url_name('Promotion url name');
        $show->promotion_url('Promotion url');
        $show->promotion_url_sub_title('Promotion url sub title');
        $show->get_limit('Get limit');
        $show->use_custom_code('Use custom code');
        $show->can_share('Can share');
        $show->can_give_friend('Can give friend');
        $show->deal_detail('Deal detail');
        $show->least_cost('Least cost');
        $show->reduce_cost('Reduce cost');
        $show->discount('Discount');
        $show->gift('Gift');
        $show->default_detail('Default detail');
        $show->status('Status');
        $show->is_buy('Is buy');
        $show->market_price('Market price');
        $show->sale_price('Sale price');
        $show->createtime('Createtime');
        $show->last_modified('Last modified');
        $show->is_del('Is del');
        $show->coupon_status('Coupon status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oCoupon);

        $form->ignore(['mer_ids']);

        $form->text('title', '优惠券名称')->rules('required');
        $form->number('get_limit', '领取限制')->rules('required')->help('单个用户领取数量限制');
        $form->text('description', '优惠券描述')->rules('required');
        $form->decimal('sale_price', '售价')->default(0.000);
        $form->datetime('begin_timestamp', '开始时间')->default(date('Y-m-d H:i:s'));
        $form->datetime('end_timestamp', '结束时间')->default(date('Y-m-d H:i:s'));
        $form->multipleSelect('mer_ids', '适用商户')->options(O2oMerchant::all()->pluck('mer_name', 'mer_id'))->rules('required');
        $form->image('logo_url', '券展示图')->crop(100, 100, 10, 10)->rules('required');
        $form->number('quantity', '库存数量')->rules('required');
        $form->number('discount', '打折额度百分比')->rules('required')->help('打折额度百分比，填30就是七折');
        $form->number('least_cost', '最低消费金额')->rules('required');
        $form->number('reduce_cost', '减免金额')->rules('required')->help('100元代金券，填写数字100');
        $form->mobile('service_phone', '客服电话')->rules('required');
        $form->text('notice', '使用须知')->rules('required');

        //保存前回调
        $form->saving(function (Form $form) {
            $coupon_model = new O2oCoupon();
            $pcid = $coupon_model->getPcid();
            $form->model()->pcid = $pcid;
            foreach ($form->merchants as $mer_id) {
                if ($mer_id) {
                    $coupon_merchant_model = new O2oCouponMerchant();
                    $coupon_merchant_model->mer_id = $mer_id;
                    $coupon_merchant_model->pcid = $pcid;
                    $coupon_merchant_model->save();
                }
            }
        });

        return $form;
    }

    protected function discount_form()
    {
        $form = new Form(new O2oCoupon);

        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            $tools->disableList();

            // 去掉`删除`按钮
            $tools->disableDelete();

            // 去掉`查看`按钮
            $tools->disableView();
        });

        $form->text('title', '优惠券名称')->rules('required');
        $form->number('get_limit', '领取限制')->rules('required')->help('单个用户领取数量限制');
        $form->text('description', '优惠券描述')->rules('required');
        $form->decimal('sale_price', '售价')->default(0.000);
        $form->datetime('begin_timestamp', '开始时间')->default(date('Y-m-d H:i:s'));
        $form->datetime('end_timestamp', '结束时间')->default(date('Y-m-d H:i:s'));
        $form->multipleSelect('merchants', '适用商户')->options(O2oMerchant::all()->pluck('mer_name', 'mer_id'))->rules('required');
        $form->image('logo_url', '券展示图')->crop(100, 100, 10, 10)->rules('required');
        $form->number('quantity', '库存数量')->rules('required');
        $form->number('discount', '打折额度百分比')->rules('required')->help('打折额度百分比，填30就是七折');
        $form->mobile('service_phone', '客服电话')->rules('required');
        $form->text('notice', '使用须知')->rules('required');

        return $form;
    }

    protected function cash_form()
    {
        $form = new Form(new O2oCoupon);

        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            $tools->disableList();

            // 去掉`删除`按钮
            $tools->disableDelete();

            // 去掉`查看`按钮
            $tools->disableView();
        });

        $form->text('title', '优惠券名称')->rules('required');
        $form->number('get_limit', '领取限制')->rules('required')->help('单个用户领取数量限制');
        $form->text('description', '优惠券描述')->rules('required');
        $form->decimal('sale_price', '售价')->default(0.000);
        $form->datetime('begin_timestamp', '开始时间')->default(date('Y-m-d H:i:s'));
        $form->datetime('end_timestamp', '结束时间')->default(date('Y-m-d H:i:s'));
        $form->multipleSelect('merchants', '适用商户')->options(O2oMerchant::all()->pluck('mer_name', 'mer_id'))->rules('required');
        $form->image('logo_url', '券展示图')->crop(100, 100, 10, 10)->rules('required');
        $form->number('quantity', '库存数量')->rules('required');
        $form->number('least_cost', '最低消费金额')->rules('required');
        $form->number('reduce_cost', '减免金额')->rules('required')->help('100元代金券，填写数字100');
        $form->mobile('service_phone', '客服电话')->rules('required');
        $form->text('notice', '使用须知')->rules('required');

        return $form;
    }
}
