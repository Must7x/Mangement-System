<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'لابتوب' => 'laptop',
            'طابعة' => 'printer',
            'حاسوب مكتبي' => 'desktop',
            'شاشة' => 'monitor',
            'هاتف' => 'phone',
            'أثاث' => 'furniture',
            'شبكات' => 'networking',
        ];

        foreach (DB::table('assets')->get(['id', 'type']) as $asset) {
            if (isset($map[$asset->type])) {
                DB::table('assets')->where('id', $asset->id)->update(['type' => $map[$asset->type]]);
            }
        }
    }

    public function down(): void
    {
        $map = [
            'laptop' => 'لابتوب',
            'printer' => 'طابعة',
            'desktop' => 'حاسوب مكتبي',
            'monitor' => 'شاشة',
            'phone' => 'هاتف',
            'furniture' => 'أثاث',
            'networking' => 'شبكات',
        ];

        foreach (DB::table('assets')->get(['id', 'type']) as $asset) {
            if (isset($map[$asset->type])) {
                DB::table('assets')->where('id', $asset->id)->update(['type' => $map[$asset->type]]);
            }
        }
    }
};
