<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;
use \App\Models\User;

class DocumentSeeder extends Seeder
{
    public function run()
    {
        $adminUser = User::where('is_admin', true)->first();

        Document::create([
            'title' => 'School Policy Handbook',
            'category' => 'Policies',
            'filename' => 'policy_handbook.pdf',
            'filepath' => 'documents/policy_handbook.pdf',
            'status' => 'completed',
            'chunk_count' => 5,
            'uploaded_by' => 1, // assuming user ID 1 exists
        ]);

        Document::create([
            'title' => 'Academic Calendar 2023-2024',
            'category' => 'Academic',
            'filename' => 'calendar_2023.pdf',
            'filepath' => 'documents/calendar_2023.pdf',
            'status' => 'completed',
            'chunk_count' => 3,
            'uploaded_by' => $adminUser->id,
        ]);

        // Add more documents as needed
    }
}