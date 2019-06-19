<?php

namespace App\Admin\Controllers;

use App\Models\O2oCoupon;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

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
        $grid = new Grid(new O2oCoupon);

        $grid->pcid('编号');
//        $grid->cid('Cid');
        $grid->mer_id('适用商户');
//        $grid->promotion_id('Promotion id');
//        $grid->brand_name('Brand name');
        $grid->card_type('优惠券类型');
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
//        $grid->status('Status');
//        $grid->is_buy('Is buy');
//        $grid->market_price('Market price');
//        $grid->sale_price('Sale price');
//        $grid->createtime('Createtime');
//        $grid->last_modified('Last modified');
//        $grid->is_del('Is del');
        $grid->coupon_status('激活状态');

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

        $form->number('cid', 'Cid');
        $form->text('mer_id', 'Mer id');
        $form->text('promotion_id', 'Promotion id');
        $form->text('brand_name', 'Brand name');
        $form->text('card_type', 'Card type')->default('GENERAL_COUPON');
        $form->text('logo_url', 'Logo url');
        $form->text('is_sync', 'Is sync')->default('false');
        $form->textarea('wei_logo_url', 'Wei logo url');
        $form->text('code_type', 'Code type')->default('CODE_TYPE_QRCODE');
        $form->text('title', 'Title');
        $form->text('sub_title', 'Sub title');
        $form->color('color', 'Color');
        $form->text('notice', 'Notice');
        $form->textarea('description', 'Description');
        $form->number('grant_quantity', 'Grant quantity');
        $form->number('quantity', 'Quantity');
        $form->text('date_type', 'Date type')->default('DATE_TYPE_FIX_TIME_RANGE');
        $form->datetime('begin_timestamp', 'Begin timestamp')->default(date('Y-m-d H:i:s'));
        $form->datetime('end_timestamp', 'End timestamp')->default(date('Y-m-d H:i:s'));
        $form->switch('fixed_term', 'Fixed term');
        $form->switch('fixed_begin_term', 'Fixed begin term');
        $form->text('card_id', 'Card id');
        $form->text('bind_openid', 'Bind openid')->default('false');
        $form->text('service_phone', 'Service phone');
        $form->text('source', 'Source');
        $form->text('custom_url_name', 'Custom url name');
        $form->text('custom_url_sub_title', 'Custom url sub title');
        $form->text('custom_url', 'Custom url');
        $form->text('center_title', 'Center title');
        $form->text('center_sub_title', 'Center sub title');
        $form->text('center_url', 'Center url');
        $form->text('promotion_url_name', 'Promotion url name');
        $form->text('promotion_url', 'Promotion url');
        $form->text('promotion_url_sub_title', 'Promotion url sub title');
        $form->number('get_limit', 'Get limit');
        $form->text('use_custom_code', 'Use custom code')->default('false');
        $form->text('can_share', 'Can share')->default('false');
        $form->text('can_give_friend', 'Can give friend')->default('false');
        $form->textarea('deal_detail', 'Deal detail');
        $form->decimal('least_cost', 'Least cost')->default(0.00);
        $form->decimal('reduce_cost', 'Reduce cost')->default(0.00);
        $form->switch('discount', 'Discount');
        $form->text('gift', 'Gift');
        $form->text('default_detail', 'Default detail');
        $form->text('status', 'Status')->default('1');
        $form->text('is_buy', 'Is buy')->default('1');
        $form->decimal('market_price', 'Market price')->default(0.000);
        $form->decimal('sale_price', 'Sale price')->default(0.000);
        $form->number('createtime', 'Createtime');
        $form->text('last_modified', 'Last modified');
        $form->text('is_del', 'Is del');
        $form->text('coupon_status', 'Coupon status');

        return $form;
    }
}
