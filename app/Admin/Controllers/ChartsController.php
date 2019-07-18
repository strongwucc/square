<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\O2oMember;
use App\Models\SdbB2cOrders;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\Tab;

class ChartsController extends Controller
{
    public function index($action, Content $content)
    {
//        $content->title('商圈统计');
//        $this->showFormParameters($content);
//        $tab = new Tab();
//
//        $bar = view('admin.chartjs.doughnut');
//
//        $tab->add('本日', new Box('饼状图', $bar));
//        $tab->add('本月', '本月统计');
//        $tab->add('本年', '本年统计');
//
//        $content->row($tab);
//
//        return $content;

        $order_model = new SdbB2cOrders();
        $member_model = new O2oMember();

        return $content
            ->title($title = '商圈统计')
            ->row($this->btns($action))
            ->row(function (Row $row) use ($order_model, $member_model, $action) {

                $time_today = strtotime('today');
                $time_month = strtotime(date('Y-m-01'));
                $time_year = strtotime(date('Y-01-01'));

                $total_amount = 0.00;
                $order_sum = 0;
                $member_sum = 0;
                $new_member_sum = 0;

                // 本日
                if ($action == 'day') {
                    $total_amount = $order_model->where('pay_status', '1')->where('createtime', '>=', $time_today)->sum('total_amount');
                    $order_sum = $order_model->where('pay_status', '1')->where('createtime', '>=', $time_today)->count();
                    $member_sum = $member_model->count();
                    $new_member_sum = $member_model->where('regtime', '>=', $time_today)->count();
                }

                // 本月
                if ($action == 'month') {
                    $total_amount = $order_model->where('pay_status', '1')->where('createtime', '>=', $time_month)->sum('total_amount');
                    $order_sum = $order_model->where('pay_status', '1')->where('createtime', '>=', $time_month)->count();
                    $member_sum = $member_model->count();
                    $new_member_sum = $member_model->where('regtime', '>=', $time_month)->count();
                }

                // 本年
                if ($action == 'year') {
                    $total_amount = $order_model->where('pay_status', '1')->where('createtime', '>=', $time_year)->sum('total_amount');
                    $order_sum = $order_model->where('pay_status', '1')->where('createtime', '>=', $time_year)->count();
                    $member_sum = $member_model->count();
                    $new_member_sum = $member_model->where('regtime', '>=', $time_year)->count();
                }

                $total_amount = $total_amount ? number_format($total_amount, 2, '.', '') : 0.00;
                $order_sum = $order_sum ? $order_sum : 0;
                $member_sum = $member_sum ? $member_sum : 0;
                $new_member_sum = $new_member_sum ? $new_member_sum : 0;

                $row->column(1/4, new Box('收入', $total_amount . ' 元'));
                $row->column(1/4, new Box('订单', $order_sum . ' 笔'));
                $row->column(1/4, new Box('会员总数', $member_sum . ' 个'));
                $row->column(1/4, new Box('新增会员', $new_member_sum . ' 个'));
            })
            ->row(function (Row $row) use ($order_model, $action) {

                $labels = [];
                $data = [];

                if ($action == 'day') {
                    $labels = ['0时', '1时', '2时', '3时', '4时', '5时', '6时', '7时', '8时', '9时', '10时', '11时', '12时', '13时', '14时', '15时', '16时', '17时', '18时', '19时', '20时', '21时', '22时',
                        '23时', '24时'];

                }

                if ($action == 'month') {
                    $labels = ['1日', '2日', '3日', '4日', '5日', '6日', '7日', '8日', '9日', '10日', '11日', '12日', '13日', '14日', '15日', '16日', '17日', '18日', '19日', '20日', '21日', '22日', '23日', '24日', '25日', '26日', '27日', '28日', '29日', '30日', '31日'];
                }

                if ($action == 'year') {
                    $labels = ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'];
                }

                $bar = view('admin.chartjs.bar', ['labels' => json_encode($labels), 'data' => $data]);
                $row->column(1/3, new Box('订单收入统计', $bar));
                $doughnut_source = view('admin.chartjs.doughnut-source');
                $row->column(1/3, new Box('订单来源统计', $doughnut_source));
                $doughnut_payment = view('admin.chartjs.doughnut-payment');
                $row->column(1/3, new Box('支付方式统计', $doughnut_payment));
//                $scatter = view('admin.chartjs.scatter');
//                $row->column(1/3, new Box('Scatter chart', $scatter));
//                $bar = view('admin.chartjs.line');
//                $row->column(1/3, new Box('Line chart', $bar));
            })->row(function (Row $row) {
//                $bar = view('admin.chartjs.doughnut');
//                $row->column(1/3, new Box('Doughnut chart', $bar));
//                $scatter = view('admin.chartjs.combo-bar-line');
//                $row->column(1/3, new Box('Chart.js Combo Bar Line Chart', $scatter));
//                $bar = view('admin.chartjs.line-stacked');
//                $row->column(1/3, new Box('Chart.js Line Chart - Stacked Area', $bar));
            });
    }
    protected function info($url, $title)
    {
        $content = "<a href=\"{$url}\" target='_blank'>{$url}</a>";
        return new Callout($content, $title, 'info');
    }

    protected function btns($action)
    {
        $day = <<<EOT
        <a class="btn btn-primary" href="/admin/charts/day" role="button">本日</a>
        <a class="btn btn-default" href="/admin/charts/month" role="button">本月</a>
        <a class="btn btn-default" href="/admin/charts/year" role="button">本年</a>
EOT;
        $month = <<<EOT
        <a class="btn btn-default" href="/admin/charts/day" role="button">本日</a>
        <a class="btn btn-primary" href="/admin/charts/month" role="button">本月</a>
        <a class="btn btn-default" href="/admin/charts/year" role="button">本年</a>
EOT;
        $year = <<<EOT
        <a class="btn btn-default" href="/admin/charts/day" role="button">本日</a>
        <a class="btn btn-default" href="/admin/charts/month" role="button">本月</a>
        <a class="btn btn-primary" href="/admin/charts/year" role="button">本年</a>
EOT;

        return $$action;

    }

    protected function showFormParameters($content)
    {
        $parameters = request()->except(['_pjax', '_token']);
        if (!empty($parameters)) {
            ob_start();
            dump($parameters);
            $contents = ob_get_contents();
            ob_end_clean();
            $content->row(new Box('Form parameters', $contents));
        }
    }
}
