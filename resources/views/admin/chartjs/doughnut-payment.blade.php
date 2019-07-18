<canvas id="doughnut-payment" style="width: 100%;"></canvas>
<script>
    $(function () {
        function randomScalingFactor() {
            return Math.floor(Math.random() * 100)
        }
        window.chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };
        var config = {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                    ],
                    backgroundColor: [
                        window.chartColors.red,
                        window.chartColors.orange,
                        window.chartColors.yellow,
                        window.chartColors.green,
                        window.chartColors.blue,
                        window.chartColors.purple,
                        window.chartColors.grey,
                    ],
                    label: 'Dataset 1'
                }],
                labels: [
                    '现金支付',
                    '会员卡支付',
                    '支付宝支付',
                    '银联支付',
                    '微信支付',
                    '刷卡支付',
                    '一卡通支付',
                    '优惠券抵扣',
                ]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: '支付方式'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        };
        var ctx = document.getElementById('doughnut-payment').getContext('2d');
        new Chart(ctx, config);
    });
</script>