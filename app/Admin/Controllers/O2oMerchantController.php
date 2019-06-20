<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\CheckRow;
use App\Admin\Extensions\Tools\SetHot;
use App\Models\O2oMerchant;
use App\Http\Controllers\Controller;
use App\Models\O2oMerchantHot;
use App\Models\O2oMerchantType;
use App\Models\O2oTitleType;
use Dingo\Blueprint\Annotation\Method\Post;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

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

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->add('设为热门商户', new SetHot());
            });
        });

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('mer_id', '商户编号');
            $filter->like('mer_name', '商户名称');
            $filter->like('contact_person', '联系人');
            $filter->like('contact_mobile', '联系电话');
            $filter->equal('status', '是否激活')->radio([
                ''   => '所有',
                0    => '已激活',
                1    => '未激活',
            ]);

        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
//            $actions->disableEdit();
            // 添加操作
//            $actions->append(new CheckRow($actions->getKey()));
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
        $show->type_code('商户类型')->as(function ($type_code) {
            $type_model = new O2oMerchantType();
            $type = $type_model->where('type_code', $type_code)->first();
            return $type->type_name;
        });
        $show->mer_addr('商户地址');
        $show->mer_pic('商户图片');
        $show->contact_mobile('联系人手机号');
        $show->contact_person('联系人姓名');
        $show->title('标签')->as(function ($title) {
            $title_model = new O2oTitleType();
            $titles = $title_model->whereIn('type_code', $title)->get();
            $title = '';
            foreach ($titles as $title_item) {
                $title .= $title_item->type_name . ',';
            }
            return rtrim($title, ',');
        });
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

        $form->tools(function (Form\Tools $tools) {

            // 去掉`删除`按钮
            $tools->disableDelete();
        });

        $form->select('type_code', '商户类型')->options(O2oMerchantType::selectOptions(null, '请选择'));
        $form->multipleSelect('title', '标签')->options(O2oTitleType::all()->pluck('type_name', 'type_code'));
        $form->image('mer_pic', '商户图片');

        return $form;
    }

    public function hot(Request $request)
    {
        $hot_model = new O2oMerchantHot();
        foreach (O2oMerchant::find($request->get('ids')) as $merchant) {
            if ($hot_model->where('mer_id',$merchant->mer_id)->count() == 0) {
                $hot_model->mer_id = $merchant->mer_id;
                $hot_model->mer_name = $merchant->mer_name;
                $hot_model->mer_pic = $merchant->mer_pic;
                $hot_model->per_cost = $merchant->per_cost;
                $hot_model->save();
            }
        }
    }
}
