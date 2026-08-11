<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $block = DB::table('content_blocks')->where('slug', 'home.launch')->first();

        if ($block !== null) {
            $meta = json_decode((string) $block->meta, true) ?: [];

            foreach ([
                'body_after',
                'starts_at',
                'date_label',
                'time_label',
                'venue',
                'address',
                'rsvp_closes_at',
                'rsvp_closes_label',
                'rsvp_phone',
            ] as $key) {
                unset($meta[$key]);
            }

            DB::table('content_blocks')->where('id', $block->id)->update([
                'body' => 'Entrusted brings together years of writing, teaching, mentoring, building, and service into a clear invitation to steward what has been placed in our hands.',
                'meta' => json_encode(array_merge($meta, [
                    'occasion' => 'Stewardship. Responsibility. Purpose.',
                    'tagline' => 'Stewardship. Responsibility. Purpose.',
                ])),
                'updated_at' => now(),
            ]);
        }

        DB::table('pickup_points')
            ->where(function ($query): void {
                $query
                    ->where('name', 'like', '%Watercress%')
                    ->orWhere('name', 'like', '%Launch evening%')
                    ->orWhere('address', 'like', '%5 Alade%')
                    ->orWhere('opening_hours', 'like', '%18 August%')
                    ->orWhere('note', 'like', '%unveiling%');
            })
            ->update([
                'name' => 'Collection details to be confirmed',
                'address' => 'To be confirmed',
                'city' => null,
                'opening_hours' => null,
                'note' => 'Buyers will receive collection details by email.',
                'is_active' => false,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
