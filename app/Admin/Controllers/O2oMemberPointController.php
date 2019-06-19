<?php

namespace App\Admin\Controllers;

use App\Models\O2oMemberPoint;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class O2oMemberPointController extends Controller
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
            ->header('会员积分信息列表')
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
        $grid = new Grid(new O2oMemberPoint);

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

        $grid->id('ID');
//        $grid->operation_score_id('Operation score id');
        $grid->platform_member_id('会员ID');
        $grid->point('积分阶段总结');
        $grid->change_point('改变积分');
        $grid->consume_point('单笔积分消耗的积分值');
        $grid->addtime('添加时间');
        $grid->expiretime('过期时间');
        $grid->reason('理由');
        $grid->remark('备注');
//        $grid->related_id('Related id');
        $grid->type('操作类型');
//        $grid->operator('Operator');
//        $grid->pay_reason('Pay reason');
//        $grid->pay_info('Pay info');

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
        $show = new Show(O2oMemberPoint::findOrFail($id));

        $show->id('Id');
        $show->operation_score_id('Operation score id');
        $show->platform_member_id('Platform member id');
        $show->point('Point');
        $show->change_point('Change point');
        $show->consume_point('Consume point');
        $show->addtime('Addtime');
        $show->expiretime('Expiretime');
        $show->reason('Reason');
        $show->remark('Remark');
        $show->related_id('Related id');
        $show->type('Type');
        $show->operator('Operator');
        $show->pay_reason('Pay reason');
        $show->pay_info('Pay info');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new O2oMemberPoint);

        $form->text('operation_score_id', 'Operation score id');
        $form->text('platform_member_id', 'Platform member id');
        $form->number('point', 'Point');
        $form->number('change_point', 'Change point');
        $form->number('consume_point', 'Consume point');
        $form->datetime('addtime', 'Addtime')->default(date('Y-m-d H:i:s'));
        $form->datetime('expiretime', 'Expiretime')->default(date('Y-m-d H:i:s'));
        $form->text('reason', 'Reason');
        $form->text('remark', 'Remark');
        $form->text('related_id', 'Related id');
        $form->switch('type', 'Type')->default(2);
        $form->text('operator', 'Operator');
        $form->text('pay_reason', 'Pay reason')->default('01');
        $form->text('pay_info', 'Pay info');

        return $form;
    }
}
