<?php

namespace App\Admin\Controllers;

use App\Models\SdbB2cOrderItems;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SdbB2cOrderItemsController extends Controller
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
        $grid = new Grid(new SdbB2cOrderItems);

        $grid->item_id('Item id');
        $grid->order_id('Order id');
        $grid->obj_id('Obj id');
        $grid->product_id('Product id');
        $grid->goods_id('Goods id');
        $grid->type_id('Type id');
        $grid->bn('Bn');
        $grid->name('Name');
        $grid->cost('Cost');
        $grid->price('Price');
        $grid->g_price('G price');
        $grid->amount('Amount');
        $grid->score('Score');
        $grid->weight('Weight');
        $grid->nums('Nums');
        $grid->sendnum('Sendnum');
        $grid->addon('Addon');
        $grid->item_type('Item type');
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
        $show = new Show(SdbB2cOrderItems::findOrFail($id));

        $show->item_id('Item id');
        $show->order_id('Order id');
        $show->obj_id('Obj id');
        $show->product_id('Product id');
        $show->goods_id('Goods id');
        $show->type_id('Type id');
        $show->bn('Bn');
        $show->name('Name');
        $show->cost('Cost');
        $show->price('Price');
        $show->g_price('G price');
        $show->amount('Amount');
        $show->score('Score');
        $show->weight('Weight');
        $show->nums('Nums');
        $show->sendnum('Sendnum');
        $show->addon('Addon');
        $show->item_type('Item type');
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
        $form = new Form(new SdbB2cOrderItems);

        $form->number('item_id', 'Item id');
        $form->text('order_id', 'Order id');
        $form->number('obj_id', 'Obj id');
        $form->number('product_id', 'Product id');
        $form->number('goods_id', 'Goods id');
        $form->number('type_id', 'Type id');
        $form->text('bn', 'Bn');
        $form->text('name', 'Name');
        $form->decimal('cost', 'Cost');
        $form->decimal('price', 'Price')->default(0.000);
        $form->decimal('g_price', 'G price')->default(0.000);
        $form->decimal('amount', 'Amount');
        $form->number('score', 'Score');
        $form->number('weight', 'Weight');
        $form->decimal('nums', 'Nums')->default(1);
        $form->decimal('sendnum', 'Sendnum');
        $form->textarea('addon', 'Addon');
        $form->text('item_type', 'Item type')->default('product');
        $form->text('source_name', 'Source name');
        $form->text('merchant_bn', 'Merchant bn');

        return $form;
    }
}
