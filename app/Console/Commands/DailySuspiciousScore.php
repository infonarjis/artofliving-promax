<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MemberRiskScore;

class DailySuspiciousScore extends Command
{
    protected $signature = 'suspicious:decay';

    public function handle()
    {
        MemberRiskScore::query()->each(function ($risk) {
            $risk->risk_score = max(0, $risk->risk_score - 20);
            $risk->save();
        });
    }
}