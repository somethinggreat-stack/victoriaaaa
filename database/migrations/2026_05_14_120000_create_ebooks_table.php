<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebooks', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('title', 200);
            $table->string('subtitle', 255)->nullable();
            $table->decimal('price', 8, 2);
            $table->string('cover_image', 255)->nullable();
            $table->text('drive_link')->nullable();
            $table->json('features')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed with the 4 existing ebooks
        DB::table('ebooks')->insert([
            [
                'slug'         => '100k-funding-in-90-days',
                'title'        => 'Steps to Get $100K+ in 90 Days',
                'subtitle'     => 'The exact playbook clients use to unlock 6-figure business funding fast.',
                'price'        => 47.00,
                'cover_image'  => 'images/100kinfundingebookcover.png',
                'drive_link'   => null,
                'features'     => json_encode([
                    'The 90-day funding stack — step by step',
                    'How to position your profile for lender approval',
                    'Common mistakes that get applications denied',
                    'Lifetime updates as the lender list evolves',
                ]),
                'sort_order'   => 1,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'slug'         => 'hard-inquiries-gone',
                'title'        => 'Get Hard Inquiries Gone',
                'subtitle'     => 'Remove hard inquiries from your credit report in as little as one day.',
                'price'        => 7.47,
                'cover_image'  => 'images/hardinquiriesebookcover.png',
                'drive_link'   => null,
                'features'     => json_encode([
                    'The 1-day inquiry removal method',
                    'Letter templates you can send today',
                    'When to dispute vs. when to call directly',
                    'Avoid the mistakes that re-add inquiries',
                ]),
                'sort_order'   => 2,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'slug'         => 'real-estate-terms-cheat-sheet',
                'title'        => 'Real Estate Terms Exam Cheat Sheet',
                'subtitle'     => 'Pass the real estate licensing exam — terms decoded, no fluff.',
                'price'        => 19.47,
                'cover_image'  => 'images/realestatetermscheatsheetebookcover.png',
                'drive_link'   => null,
                'features'     => json_encode([
                    'Every key term you need on exam day',
                    'Plain-English definitions, fast to memorize',
                    'Common exam traps and how to spot them',
                    'Quick-recall mnemonics included',
                ]),
                'sort_order'   => 3,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'slug'         => 'realtor-roadmap-to-success',
                'title'        => 'The Realtor Roadmap to Success',
                'subtitle'     => 'The first-90-days playbook for new real estate agents who want to win.',
                'price'        => 24.47,
                'cover_image'  => 'images/realtorroadmaptosuccessebookcover.png',
                'drive_link'   => null,
                'features'     => json_encode([
                    'Your 90-day launch plan as a new agent',
                    'How to fill your pipeline without ad spend',
                    'Scripts for buyer + listing conversations',
                    'Build a 6-figure book of business',
                ]),
                'sort_order'   => 4,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ebooks');
    }
};
