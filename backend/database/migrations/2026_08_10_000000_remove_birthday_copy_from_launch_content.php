<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $block = DB::table('content_blocks')->where('slug', 'home.launch')->first();

        if ($block === null) {
            return;
        }

        $meta = json_decode((string) $block->meta, true) ?: [];

        DB::table('content_blocks')->where('id', $block->id)->update([
            'body' => 'Femi Owoyele will unveil his first book, Entrusted, in an intimate evening shaped around stewardship, responsibility, and the invitation to build beyond ourselves.',
            'meta' => json_encode(array_merge($meta, [
                'body_after' => 'Entrusted was unveiled before family, friends, mentors, and the builders who have shaped the work, opening a wider conversation about stewardship, responsibility, and meaningful contribution.',
                'occasion' => 'The Entrusted Launch',
            ])),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
