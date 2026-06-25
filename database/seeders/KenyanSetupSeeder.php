<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\User;
use App\Models\Userprofile;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KenyanSetupSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure Kenyan geographical data is enabled (status = 1)
        $this->command->info('Enabling Kenyan geographical data...');
        
        // Country: Kenya (ID: 113)
        $country = Country::find(113);
        if ($country) {
            $country->status = 1;
            $country->save();
            $this->command->info('✓ Kenya country enabled (ID: 113)');
        } else {
            $this->command->error('✗ Kenya country not found in database!');
        }

        // Enable all states in Kenya
        State::where('country_id', 113)->update(['status' => 1]);
        $this->command->info('✓ All Kenyan states/counties enabled');

        // Enable all cities in Kenya
        City::where('country_id', 113)->update(['status' => 1]);
        $this->command->info('✓ All Kenyan cities enabled');

        // 2. Create the Nairobi church if not exists
        $this->command->info('Setting up Kenyan Central Church...');
        
        $church = Church::firstOrCreate(
            ['slug' => 'nairobi-community-church'],
            [
                'name' => 'Nairobi Community Church',
                'address' => 'Harambee Avenue',
                'country_id' => 113,
                'state_id' => 2237, // Nairobi City
                'city_id' => 69711,  // Nairobi
                'pincode' => '00100',
                'status' => 1,
            ]
        );
        $this->command->info('✓ Nairobi Community Church setup complete (ID: ' . $church->id . ')');

        // Ensure roles exist (just in case)
        $churchSubadminRole = Role::firstOrCreate(
            ['name' => 'church-subadmin'],
            ['display_name' => 'Church Sub-Admin', 'description' => 'Full church management except financial admin']
        );
        
        $staffRole = Role::firstOrCreate(
            ['name' => 'staff'],
            ['display_name' => 'Staff', 'description' => 'Day-to-day operational tasks']
        );

        // 3. Create Super Admin/Church Admin user (g.nanguti@gmail.com / 123123)
        $this->command->info('Creating Super Admin/Church Admin user (g.nanguti@gmail.com)...');
        
        $adminUser = User::where('email', 'g.nanguti@gmail.com')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'g.nanguti',
                'email' => 'g.nanguti@gmail.com',
                'password' => Hash::make('123123'),
                'mobile_no' => '0712345678',
                'mobile_country_code' => '254',
                'church_id' => $church->id,
                'usergroup_id' => 3, // ChurchAdmin
            ]);
        } else {
            $adminUser->password = Hash::make('123123');
            $adminUser->church_id = $church->id;
            $adminUser->usergroup_id = 3;
            $adminUser->save();
        }

        // Attach Laratrust role
        if (!$adminUser->hasRole('church-subadmin')) {
            $adminUser->attachRole($churchSubadminRole);
        }

        // Create or update profile
        Userprofile::updateOrCreate(
            ['user_id' => $adminUser->id],
            [
                'church_id' => $church->id,
                'firstname' => 'George',
                'lastname' => 'Nanguti',
                'gender' => 'male',
                'profession' => 'admin',
                'status' => 'active',
                'membership_type' => 'member',
                'membership_start_date' => now()->toDateString(),
                'country_id' => 113,
                'state_id' => 2237,
                'city_id' => 69711,
            ]
        );
        $this->command->info('✓ Super Admin setup complete');

        // 4. Create Staff user (staff@gmail.com / staff123)
        $this->command->info('Creating Staff user (staff@gmail.com)...');
        
        $staffUser = User::where('email', 'staff@gmail.com')->first();
        if (!$staffUser) {
            $staffUser = User::create([
                'name' => 'staff',
                'email' => 'staff@gmail.com',
                'password' => Hash::make('staff123'),
                'mobile_no' => '0787654321',
                'mobile_country_code' => '254',
                'church_id' => $church->id,
                'usergroup_id' => 4, // ChurchSubadmin
            ]);
        } else {
            $staffUser->password = Hash::make('staff123');
            $staffUser->church_id = $church->id;
            $staffUser->usergroup_id = 4;
            $staffUser->save();
        }

        // Attach Laratrust role
        if (!$staffUser->hasRole('staff')) {
            $staffUser->attachRole($staffRole);
        }

        // Create or update profile
        Userprofile::updateOrCreate(
            ['user_id' => $staffUser->id],
            [
                'church_id' => $church->id,
                'firstname' => 'Church',
                'lastname' => 'Staff',
                'gender' => 'male',
                'profession' => 'admin',
                'status' => 'active',
                'membership_type' => 'member',
                'membership_start_date' => now()->toDateString(),
                'country_id' => 113,
                'state_id' => 2237,
                'city_id' => 69711,
            ]
        );
        $this->command->info('✓ Staff setup complete');

        // 5. Update .env file and write installed marker
        $this->command->info('Finalizing installation files...');
        
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $env = file_get_contents($envPath);
            if (strpos($env, 'PRIMARY_CHURCH_ID=') === false) {
                $env .= "\nPRIMARY_CHURCH_ID={$church->id}";
            } else {
                $env = preg_replace('/PRIMARY_CHURCH_ID=.*/', 'PRIMARY_CHURCH_ID=' . $church->id, $env);
            }
            file_put_contents($envPath, $env);
            $this->command->info('✓ PRIMARY_CHURCH_ID updated in .env');
        }

        $markerFile = storage_path('installed');
        file_put_contents($markerFile, date('Y-m-d H:i:s'));
        $this->command->info('✓ Installation marker file created');
    }
}
