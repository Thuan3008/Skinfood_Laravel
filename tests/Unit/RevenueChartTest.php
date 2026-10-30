<?php

namespace Tests\Unit;

use App\Support\RevenueChart;
use PHPUnit\Framework\TestCase;

class RevenueChartTest extends TestCase
{
    public function test_daily_chart_preserves_values_and_fills_only_empty_days_with_zero(): void
    {
        $orders = collect([(object)['NgayDat'=>'2026-10-01 10:00:00','TongTien'=>100000],(object)['NgayDat'=>'2026-10-01 15:00:00','TongTien'=>200000]]);
        $chart = RevenueChart::build($orders,'2026-10-01','2026-10-03');
        $this->assertSame('day',$chart['unit']);
        $this->assertCount(3,$chart['rows']);
        $this->assertEquals([300000,0,0],array_column($chart['rows'],'revenue'));
        $this->assertEquals([2,0,0],array_column($chart['rows'],'orders'));
    }

    public function test_weekly_and_monthly_grouping_preserves_total_revenue_and_order_count(): void
    {
        $orders = collect([(object)['NgayDat'=>'2026-01-01','TongTien'=>150000],(object)['NgayDat'=>'2026-02-15','TongTien'=>250000]]);
        foreach (['2026-03-01'=>'week','2026-12-31'=>'month'] as $end=>$unit) {
            $chart = RevenueChart::build($orders,'2026-01-01',$end);
            $this->assertSame($unit,$chart['unit']);
            $this->assertEquals(400000,array_sum(array_column($chart['rows'],'revenue')));
            $this->assertEquals(2,array_sum(array_column($chart['rows'],'orders')));
        }
    }

    public function test_empty_range_has_no_fabricated_chart_points(): void
    {
        $this->assertSame([],RevenueChart::build(collect(),'2026-10-01','2026-10-20')['rows']);
    }
}
