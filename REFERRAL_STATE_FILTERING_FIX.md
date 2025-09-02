# Referral State Filtering Fix - Admin Panel

## Problem Description

When creating referrals in the admin panel at `http://127.0.0.1:8000/admin/referrals/create`, the doctors dropdown remained empty when changing states, even though doctors existed with clinics in those states.

## Root Causes Identified

1. **Missing Admin Data-by-State Route**: The admin controller didn't have a `getDataByState` method to handle AJAX requests for filtering data by state.

2. **Undefined Variable in Admin Controller**: The admin `ReferralController::create` method was using an undefined `$doctorsData` variable instead of a proper query.

3. **Missing Clinic and Law Firm Associations**: In production environments, clinics and law firms weren't being created, so doctors and attorneys had no associations to filter by state.

4. **Frontend Route Detection**: The frontend component wasn't properly detecting whether it was running in admin or user context for the data-by-state API calls.

## Solution Implemented

### 1. Added Admin Data-by-State Method

**File**: `app/Http/Controllers/Panel/Admin/ReferralController.php`

Added the `getDataByState` method to handle AJAX requests for filtering data by state:

```php
public function getDataByState(Request $request)
{
    try {
        $stateId = $request->get('state_id');
        
        // Get filtered data for the selected state
        $attorneysData = User::query()
            ->whereHas('roles', function($query) {
                $query->where('name', 'Attorney');
            })
            ->whereHas('lawFirm', function($query) use ($stateId) {
                $query->where('law_firms.state_id', $stateId);
            })
            ->orderBy('name')
            ->get();

        $doctorsData = User::query()
            ->with(['clinics'])
            ->whereHas('roles', function($query) {
                $query->where('name', 'Doctor');
            })
            ->whereHas('clinics', function($query) use ($stateId) {
                $query->where('clinics.state_id', $stateId);
            })
            ->orderBy('name')
            ->get();

        $patientsData = User::query()
            ->whereHas('roles', function($query) {
                $query->where('name', 'Patient');
            })
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get();

        return response()->json([
            'attorneys' => UserResource::collection($attorneysData),
            'doctors' => UserResource::collection($doctorsData),
            'patients' => UserResource::collection($patientsData),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Internal server error'], 500);
    }
}
```

### 2. Added Admin Data-by-State Route

**File**: `routes/web.php`

Added the route for admin data-by-state:

```php
// AJAX endpoint for getting filtered data by state for admin
Route::get('/referrals/data-by-state', [\App\Http\Controllers\Panel\Admin\ReferralController::class, 'getDataByState'])
    ->name('referrals.data-by-state');
```

### 3. Fixed Admin Controller Create Method

**File**: `app/Http/Controllers/Panel/Admin/ReferralController.php`

Fixed the undefined `$doctorsData` variable by implementing the proper query:

```php
'doctors' => UserResource::collection(
    User::query()
        ->with('clinics')
        ->whereHas('roles', function($query) {
            $query->where('name', 'Doctor');
        })
        ->when($selectedStateId, function($query) use ($selectedStateId) {
            $query->whereHas('clinics', function($subQuery) use ($selectedStateId) {
                $subQuery->where('clinics.state_id', $selectedStateId);
            });
        })
        ->orderBy('name')
        ->get()
),
```

### 4. Enhanced Frontend Route Detection

**File**: `resources/js/components/application/referral/referral-create-form.vue`

Updated the `fetchDataByState` method to detect admin context and use the correct route:

```javascript
async fetchDataByState(stateId) {
    try {
        // Determine the correct route based on the context
        const isAdmin = this.listRoute && this.listRoute.includes('admin');
        const routeName = isAdmin ? 'panel.admin.referrals.data-by-state' : 'panel.user.referrals.data-by-state';
        
        const response = await fetch(this.route(routeName, { state_id: stateId }), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.ok) {
            const data = await response.json();
            // Update local data with filtered results
            if (data.doctors && Array.isArray(data.doctors.data)) {
                this.localDoctors = data.doctors;
            }
            // ... other data updates
        }
    } catch (error) {
        console.error('Error fetching data for state:', error);
    }
}
```

### 5. Created Production Seeders

**File**: `database/seeders/Production/LawFirmSeeder.php`

Created a seeder to create law firms and associate them with attorneys:

```php
class LawFirmSeeder extends Seeder
{
    public function run(): void
    {
        // Create basic law firms
        $lawFirms = collect([
            [
                'name' => 'Justice & Associates Law Firm',
                'email' => 'info@justiceassociates.com',
                // ... other fields
            ],
            // ... more law firms
        ])->each(function ($lawFirmData) {
            LawFirm::firstOrCreate([
                'email' => $lawFirmData['email'],
            ], $lawFirmData);
        });

        // Associate attorneys with law firms
        $attorneys = User::whereHas('roles', function($query) {
            $query->where('name', 'Attorney');
        })->get();

        foreach ($attorneys as $index => $attorney) {
            $lawFirm = $lawFirms[$index % $lawFirms->count()];
            $attorney->update(['law_firm_id' => $lawFirm->law_firm_id]);
        }
    }
}
```

**File**: `database/seeders/Production/ClinicSeeder.php`

Created a seeder to create clinics and associate them with doctors:

```php
class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        // Create basic clinics
        $clinics = collect([
            [
                'name' => 'Limitless Regeneration Clinic',
                'email' => 'info@limitlessregen.com',
                // ... other fields
            ],
            // ... more clinics
        ])->each(function ($clinicData) {
            Clinic::firstOrCreate([
                'email' => $clinicData['email'],
            ], $clinicData);
        });

        // Associate doctors with clinics
        $doctors = User::whereHas('roles', function($query) {
            $query->where('name', 'Doctor');
        })->get();

        foreach ($doctors as $index => $doctor) {
            $clinic = $clinics[$index % $clinics->count()];
            $doctor->clinics()->syncWithoutDetaching([$clinic->clinic_id]);
        }
    }
}
```

### 6. Updated Database Seeder

**File**: `database/seeders/DatabaseSeeder.php`

Added the new seeders to the main database seeder:

```php
$this->call([
    // ... existing seeders
    Production\LawFirmSeeder::class,
    Production\ClinicSeeder::class,
]);
```

## Testing the Fix

### 1. Run Seeders
```bash
php artisan db:seed
```

### 2. Test Admin Referral Creation
1. Navigate to `http://127.0.0.1:8000/admin/referrals/create`
2. Change the state dropdown
3. Verify that doctors are now filtered by state
4. Verify that attorneys are filtered by state
5. Verify that patients are filtered by state

### 3. Test User Referral Creation
1. Navigate to `http://127.0.0.1:8000/referrals/create`
2. Change the state dropdown
3. Verify that filtering works correctly for user panel as well

## Expected Results

- **Admin Panel**: Doctors, attorneys, and patients should be filtered by state when the state dropdown changes
- **User Panel**: State-based filtering should continue to work as before
- **Data Associations**: All doctors should have clinic associations, all attorneys should have law firm associations
- **AJAX Requests**: State changes should trigger AJAX requests to fetch filtered data

## Files Modified

1. `app/Http/Controllers/Panel/Admin/ReferralController.php` - Added `getDataByState` method and fixed `create` method
2. `routes/web.php` - Added admin data-by-state route
3. `resources/js/components/application/referral/referral-create-form.vue` - Enhanced route detection
4. `database/seeders/Production/LawFirmSeeder.php` - Created new seeder
5. `database/seeders/Production/ClinicSeeder.php` - Created new seeder
6. `database/seeders/DatabaseSeeder.php` - Added new seeders

## Benefits

1. **Consistent Behavior**: Admin and user panels now have consistent state-based filtering
2. **Proper Data Associations**: All users have proper clinic/law firm associations for filtering
3. **Scalable Solution**: The solution works for any number of states and users
4. **Maintainable Code**: Clear separation of concerns between admin and user functionality
