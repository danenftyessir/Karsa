<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Company;
use App\Models\Student;
use App\Models\Institution;
use Illuminate\Console\Command;

class FixMissingProfiles extends Command
{
    protected $signature = 'fix:missing-profiles';
    protected $description = 'Create missing profiles for users with user_type but no corresponding profile';

    public function handle()
    {
        $this->info('Checking for users with missing profiles...');
        $this->newLine();

        // Fix company users
        $this->fixCompanyProfiles();

        // Fix student users
        $this->fixStudentProfiles();

        // Fix institution users
        $this->fixInstitutionProfiles();

        $this->newLine();
        $this->info('Done!');
    }

    private function fixCompanyProfiles()
    {
        $companyUsers = User::where('user_type', 'company')
            ->doesntHave('company')
            ->get();

        if ($companyUsers->isEmpty()) {
            $this->info('✓ No company users with missing profiles');
            return;
        }

        $this->warn("Found {$companyUsers->count()} company users without profiles");

        foreach ($companyUsers as $user) {
            // Extract company name from email (e.g., hr@greenpeace.com -> Greenpeace)
            $email = $user->email;
            $companyName = $this->extractCompanyName($email);

            Company::create([
                'user_id' => $user->id,
                'name' => $companyName,
                'industry' => 'Technology', // Default industry
                'description' => "Company profile for {$companyName}",
            ]);

            $this->line("  ✓ Created company profile for: {$user->email}");
        }
    }

    private function fixStudentProfiles()
    {
        $studentUsers = User::where('user_type', 'student')
            ->doesntHave('student')
            ->get();

        if ($studentUsers->isEmpty()) {
            $this->info('✓ No student users with missing profiles');
            return;
        }

        $this->warn("Found {$studentUsers->count()} student users without profiles");

        foreach ($studentUsers as $user) {
            // Split name into first and last name
            $nameParts = explode(' ', $user->name, 2);
            $firstName = $nameParts[0] ?? $user->name;
            $lastName = $nameParts[1] ?? '';

            Student::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'nim' => 'NIM-' . str_pad($user->id, 8, '0', STR_PAD_LEFT),
                'university_id' => 1, // Default university (assuming ID 1 exists)
                'major' => 'Computer Science', // Default major
                'semester' => 6,
                'phone' => '081234567890', // Default phone
            ]);

            $this->line("  ✓ Created student profile for: {$user->email}");
        }
    }

    private function fixInstitutionProfiles()
    {
        $institutionUsers = User::where('user_type', 'institution')
            ->doesntHave('institution')
            ->get();

        if ($institutionUsers->isEmpty()) {
            $this->info('✓ No institution users with missing profiles');
            return;
        }

        $this->warn("Found {$institutionUsers->count()} institution users without profiles");

        foreach ($institutionUsers as $user) {
            // Extract institution name from email
            $institutionName = $this->extractInstitutionName($user->email);

            Institution::create([
                'user_id' => $user->id,
                'name' => $institutionName,
                'type' => 'government', // Default type
                'province_id' => 32, // Default to West Java
                'regency_id' => 3201, // Default to Bogor
                'address' => 'Bandung, Jawa Barat', // Default address
                'email' => $user->email,
                'phone' => '022-1234567', // Default phone
                'pic_name' => 'Administrator', // Default PIC name
                'pic_position' => 'Admin', // Default PIC position
                'pic_phone' => '081234567890', // Default PIC phone
            ]);

            $this->line("  ✓ Created institution profile for: {$user->email}");
        }
    }

    private function extractCompanyName($email)
    {
        // Extract domain from email (e.g., hr@greenpeace.com -> greenpeace)
        preg_match('/@([^.]+)/', $email, $matches);

        if (isset($matches[1])) {
            // Convert to title case (e.g., greenpeace -> Greenpeace)
            return ucwords(str_replace(['indonesia', 'id', 'com', 'org'], '', $matches[1]));
        }

        return 'Unknown Company';
    }

    private function extractInstitutionName($email)
    {
        // Extract from email (e.g., admin@desa-karanganyar.id -> Desa Karanganyar)
        preg_match('/@([^.]+)/', $email, $matches);

        if (isset($matches[1])) {
            // Replace hyphens with spaces and convert to title case
            return ucwords(str_replace(['-', 'admin', 'go', 'id'], ' ', $matches[1]));
        }

        return 'Unknown Institution';
    }
}
