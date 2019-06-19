<?php

namespace App\Admin\Controllers;

use App\Models\O2oMember;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class O2oMemberController extends Controller
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
            ->header('会员列表')
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
        $grid = new Grid(new O2oMember);

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

//        $grid->member_id('Member id');
//        $grid->member_lv_id('Member lv id');
//        $grid->platform_member_id('Platform member id');
//        $grid->username('会员姓名');
//        $grid->password('Password');
//        $grid->crm_member_id('Crm member id');
//        $grid->operator_id('Operator id');
//        $grid->store_id('Store id');
        $grid->name('会员姓名');
        $grid->point('积分');
//        $grid->lastname('Lastname');
//        $grid->firstname('Firstname');
//        $grid->area('Area');
//        $grid->addr('Addr');
        $grid->mobile('手机号');
//        $grid->tel('Tel');
//        $grid->email('Email');
//        $grid->zip('Zip');
        $grid->order_num('累积订单数');
//        $grid->refer_id('Refer id');
//        $grid->refer_url('Refer url');
//        $grid->b_year('B year');
//        $grid->b_month('B month');
//        $grid->b_day('B day');
//        $grid->sex('Sex');
//        $grid->openid('Openid');
//        $grid->nickname('Nickname');
//        $grid->province('Province');
//        $grid->city('City');
//        $grid->country('Country');
//        $grid->headimgurl('Headimgurl');
//        $grid->unionid('Unionid');
//        $grid->addon('Addon');
//        $grid->wedlock('Wedlock');
//        $grid->education('Education');
//        $grid->vocation('Vocation');
//        $grid->interest('Interest');
//        $grid->advance('Advance');
//        $grid->advance_freeze('Advance freeze');
//        $grid->point_freeze('Point freeze');
//        $grid->point_history('Point history');
//        $grid->score_rate('Score rate');
//        $grid->reg_ip('Reg ip');
        $grid->regtime('注册时间');
//        $grid->state('State');
//        $grid->pay_time('Pay time');
//        $grid->biz_money('Biz money');
//        $grid->fav_tags('Fav tags');
//        $grid->custom('Custom');
//        $grid->cur('Cur');
//        $grid->lang('Lang');
//        $grid->unreadmsg('Unreadmsg');
//        $grid->disabled('Disabled');
//        $grid->remark('Remark');
//        $grid->remark_type('Remark type');
//        $grid->login_count('Login count');
//        $grid->experience('Experience');
//        $grid->foreign_id('Foreign id');
//        $grid->resetpwd('Resetpwd');
//        $grid->resetpwdtime('Resetpwdtime');
//        $grid->member_refer('Member refer');
//        $grid->source('Source');

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
        $show = new Show(O2oMember::findOrFail($id));

        $show->member_id('Member id');
        $show->member_lv_id('Member lv id');
        $show->platform_member_id('Platform member id');
        $show->username('Username');
        $show->password('Password');
        $show->crm_member_id('Crm member id');
        $show->operator_id('Operator id');
        $show->store_id('Store id');
        $show->name('Name');
        $show->point('Point');
        $show->lastname('Lastname');
        $show->firstname('Firstname');
        $show->area('Area');
        $show->addr('Addr');
        $show->mobile('Mobile');
        $show->tel('Tel');
        $show->email('Email');
        $show->zip('Zip');
        $show->order_num('Order num');
        $show->refer_id('Refer id');
        $show->refer_url('Refer url');
        $show->b_year('B year');
        $show->b_month('B month');
        $show->b_day('B day');
        $show->sex('Sex');
        $show->openid('Openid');
        $show->nickname('Nickname');
        $show->province('Province');
        $show->city('City');
        $show->country('Country');
        $show->headimgurl('Headimgurl');
        $show->unionid('Unionid');
        $show->addon('Addon');
        $show->wedlock('Wedlock');
        $show->education('Education');
        $show->vocation('Vocation');
        $show->interest('Interest');
        $show->advance('Advance');
        $show->advance_freeze('Advance freeze');
        $show->point_freeze('Point freeze');
        $show->point_history('Point history');
        $show->score_rate('Score rate');
        $show->reg_ip('Reg ip');
        $show->regtime('Regtime');
        $show->state('State');
        $show->pay_time('Pay time');
        $show->biz_money('Biz money');
        $show->fav_tags('Fav tags');
        $show->custom('Custom');
        $show->cur('Cur');
        $show->lang('Lang');
        $show->unreadmsg('Unreadmsg');
        $show->disabled('Disabled');
        $show->remark('Remark');
        $show->remark_type('Remark type');
        $show->login_count('Login count');
        $show->experience('Experience');
        $show->foreign_id('Foreign id');
        $show->resetpwd('Resetpwd');
        $show->resetpwdtime('Resetpwdtime');
        $show->member_refer('Member refer');
        $show->source('Source');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oMember);

        $form->number('member_id', 'Member id');
        $form->number('member_lv_id', 'Member lv id');
        $form->text('platform_member_id', 'Platform member id');
        $form->text('username', 'Username');
        $form->password('password', 'Password');
        $form->number('crm_member_id', 'Crm member id');
        $form->number('operator_id', 'Operator id');
        $form->number('store_id', 'Store id');
        $form->text('name', 'Name');
        $form->number('point', 'Point');
        $form->text('lastname', 'Lastname');
        $form->text('firstname', 'Firstname');
        $form->text('area', 'Area');
        $form->text('addr', 'Addr');
        $form->mobile('mobile', 'Mobile');
        $form->text('tel', 'Tel');
        $form->email('email', 'Email');
        $form->text('zip', 'Zip');
        $form->number('order_num', 'Order num');
        $form->text('refer_id', 'Refer id');
        $form->text('refer_url', 'Refer url');
        $form->number('b_year', 'B year');
        $form->switch('b_month', 'B month');
        $form->switch('b_day', 'B day');
        $form->text('sex', 'Sex')->default('1');
        $form->text('openid', 'Openid');
        $form->text('nickname', 'Nickname');
        $form->text('province', 'Province');
        $form->text('city', 'City');
        $form->text('country', 'Country');
        $form->text('headimgurl', 'Headimgurl');
        $form->text('unionid', 'Unionid');
        $form->textarea('addon', 'Addon');
        $form->text('wedlock', 'Wedlock');
        $form->text('education', 'Education');
        $form->text('vocation', 'Vocation');
        $form->textarea('interest', 'Interest');
        $form->decimal('advance', 'Advance')->default(0.000);
        $form->decimal('advance_freeze', 'Advance freeze')->default(0.000);
        $form->number('point_freeze', 'Point freeze');
        $form->number('point_history', 'Point history');
        $form->decimal('score_rate', 'Score rate');
        $form->text('reg_ip', 'Reg ip');
        $form->number('regtime', 'Regtime');
        $form->switch('state', 'State');
        $form->number('pay_time', 'Pay time');
        $form->decimal('biz_money', 'Biz money')->default(0.000);
        $form->textarea('fav_tags', 'Fav tags');
        $form->textarea('custom', 'Custom');
        $form->text('cur', 'Cur');
        $form->text('lang', 'Lang');
        $form->number('unreadmsg', 'Unreadmsg');
        $form->text('disabled', 'Disabled')->default('false');
        $form->textarea('remark', 'Remark');
        $form->text('remark_type', 'Remark type')->default('b1');
        $form->number('login_count', 'Login count');
        $form->number('experience', 'Experience');
        $form->text('foreign_id', 'Foreign id');
        $form->text('resetpwd', 'Resetpwd');
        $form->number('resetpwdtime', 'Resetpwdtime');
        $form->text('member_refer', 'Member refer')->default('local');
        $form->text('source', 'Source')->default('pc');

        return $form;
    }
}
