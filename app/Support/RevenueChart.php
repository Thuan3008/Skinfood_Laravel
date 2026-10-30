<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class RevenueChart
{
    public static function build(Collection $orders, string $from, string $to): array
    {
        $start = CarbonImmutable::parse($from)->startOfDay();
        $end = CarbonImmutable::parse($to)->startOfDay();
        $span = $start->diffInDays($end) + 1;
        $unit = $span <= 31 ? 'day' : ($span <= 180 ? 'week' : 'month');
        $name = match ($unit) { 'day'=>'ngày', 'week'=>'tuần', default=>'tháng' };
        $periodStart = fn ($date) => match ($unit) { 'day'=>$date->startOfDay(), 'week'=>$date->startOfWeek(), default=>$date->startOfMonth() };
        $groups = $orders->groupBy(fn ($order) => $periodStart(CarbonImmutable::parse($order->NgayDat))->toDateString());
        $rows = [];
        if ($orders->isNotEmpty()) for ($cursor = $periodStart($start); $cursor <= $end; $cursor = match ($unit) { 'day'=>$cursor->addDay(), 'week'=>$cursor->addWeek(), default=>$cursor->addMonth() }) {
            $periodEnd = match ($unit) { 'day'=>$cursor, 'week'=>$cursor->addDays(6), default=>$cursor->endOfMonth()->startOfDay() };
            $left = $cursor->max($start);
            $right = $periodEnd->min($end);
            $items = $groups->get($cursor->toDateString(), collect());
            $rows[] = [
                'label'=>$unit === 'day' ? $left->format('d/m') : ($unit === 'month' ? $cursor->format('m/Y') : $left->format('d/m').'–'.$right->format('d/m')),
                'range'=>$left->format('d/m/Y').($left->equalTo($right) ? '' : ' – '.$right->format('d/m/Y')),
                'orders'=>$items->count(), 'revenue'=>(float) $items->sum('TongTien'),
            ];
        }
        return compact('unit','name','rows');
    }
}
