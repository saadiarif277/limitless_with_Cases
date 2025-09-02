# Referral Creation Logic - Doctors and Patients Data Flow

## Problem Fixed: "Attorney Data Not Found" Error

### Root Cause
The error "Attorney data not found. Please contact administrator" was occurring because:

1. **Data Structure Mismatch**: The frontend was looking for the current attorney user in the `attorneys` list, but the user might not be in that list due to filtering
2. **State-Based Filtering**: The backend filters attorneys by state, so if the current attorney's state doesn't match the selected state, they won't appear in the list
3. **Missing Fallback Logic**: There was no fallback to use the current user's data when not found in the filtered list

### Solution Implemented

**File**: `resources/js/components/application/referral/referral-create-form.vue`

1. **Enhanced Data Initialization**: Added fallback logic to use current user data if attorney is not found in the filtered list
2. **Improved Error Handling**: Removed the error toast and replaced with console warning
3. **Better Auto-Selection**: The form now auto-selects the current attorney even if they're not in the filtered list

```javascript
if (userRole === 'Attorney') {
    const attorneyData = (this.attorneys?.data || []).find(attorney => attorney.user_id === currentUser.user_id);
    if (attorneyData) {
        // Use data from attorneys list
        autoSelectedAttorney = { /* ... */ };
    } else {
        // Fallback: Use current user data
        console.warn('Attorney data not found in attorneys list, using current user data');
        autoSelectedAttorney = {
            name: currentUser.name,
            email: currentUser.email,
            user_id: currentUser.user_id,
            law_firm: {
                name: currentUser.lawFirm?.name || "",
                // ... other law firm data
            },
        };
    }
}
```

## Current Logic for Getting Doctors and Patients

### 1. Backend Data Loading (User-Side Controller)

**File**: `app/Http/Controllers/Panel/User/ReferralController.php`

#### State Selection Logic
```php
// Get the selected state (default to user's state if none selected)
$selectedStateId = $request->get('state_id', $user->state_id);

// If still no state_id, try to get from user's law firm or clinic
if (!$selectedStateId) {
    if ($userRole === 'Attorney' && $user->lawFirm) {
        $selectedStateId = $user->lawFirm->state_id;
    } elseif ($userRole === 'Doctor' && $user->clinics->count() > 0) {
        $selectedStateId = $user->clinics->first()->state_id;
    }
}

// Final fallback: if no state_id is available, get the first available state
if (!$selectedStateId) {
    $firstState = \App\Models\State::first();
    $selectedStateId = $firstState ? $firstState->state_id : null;
}
```

#### Doctors Data Query
```php
$doctorsData = User::query()
    ->with(['clinics']) // Eager load clinics relationship
    ->whereHas('roles', function($query) {
        $query->where('name', 'Doctor');
    })
    ->when($selectedStateId, function($query) use ($selectedStateId) {
        $query->whereHas('clinics', function($subQuery) use ($selectedStateId) {
            $subQuery->where('clinics.state_id', $selectedStateId);
        });
    })
    ->orderBy('name')
    ->get();
```

**Logic**: Get all doctors who have clinics in the selected state

#### Patients Data Query
```php
$patientsData = User::query()
    ->whereHas('roles', function($query) {
        $query->where('name', 'Patient');
    })
    ->when($selectedStateId, function($query) use ($selectedStateId) {
        $query->where('state_id', $selectedStateId);
    })
    ->orderBy('name')
    ->get();
```

**Logic**: Get all patients who are in the selected state

#### Attorneys Data Query
```php
$attorneysData = User::query()
    ->whereHas('roles', function($query) {
        $query->where('name', 'Attorney');
    })
    ->when($selectedStateId, function($query) use ($selectedStateId) {
        $query->whereHas('lawFirm', function($subQuery) use ($selectedStateId) {
            $subQuery->where('law_firms.state_id', $selectedStateId);
        });
    })
    ->orderBy('name')
    ->get();
```

**Logic**: Get all attorneys whose law firms are in the selected state

### 2. Frontend Data Processing

**File**: `resources/js/components/application/referral/referral-create-form.vue`

#### Role-Based Filtering Logic

**For Attorneys**:
```javascript
doctorsData() {
    // For attorneys, only show doctors from their state
    if (this.isAttorney && this.currentUser?.state_id) {
        const filteredDoctors = this.localDoctors?.data?.filter(doctor =>
            doctor.clinics?.some(clinic => clinic.state_id == this.currentUser.state_id)
        ) || [];
        return { data: filteredDoctors };
    }
    return this.localDoctors;
}
```

**Logic**: Attorneys can only see doctors whose clinics are in the attorney's state

**For Doctors**:
```javascript
attorneysData() {
    // For doctors, only show attorneys from their state
    if (this.isDoctor && this.currentUser?.state_id) {
        const filteredAttorneys = this.localAttorneys?.data?.filter(attorney =>
            attorney.law_firm?.state_id == this.currentUser.state_id
        ) || [];
        return { data: filteredAttorneys };
    }
    return this.localAttorneys;
}
```

**Logic**: Doctors can only see attorneys whose law firms are in the doctor's state

**For Patients**:
```javascript
patientsData() {
    // For attorneys, only show patients from their state
    if (this.isAttorney && this.currentUser?.state_id) {
        const filteredPatients = this.localPatients?.data?.filter(patient =>
            patient.state_id == this.currentUser.state_id
        ) || [];
        return { data: filteredPatients };
    }
    return this.localPatients;
}
```

**Logic**: Attorneys can only see patients from their state

### 3. State Change Logic

When the state dropdown changes:

1. **URL-Based Redirect**: The page redirects to include `?state_id=X` parameter
2. **Backend Reload**: The backend loads fresh data filtered by the new state
3. **Frontend Update**: All dropdowns update with the new filtered data

```javascript
'form.state_id'(newStateId, oldStateId) {
    if (newStateId && newStateId !== oldStateId) {
        // For attorneys, prevent changing to a different state
        if (this.isAttorney && this.currentUser?.state_id && newStateId != this.currentUser.state_id) {
            this.form.state_id = this.currentUser.state_id; // Revert
            return;
        }
        // For other users, redirect to the same page with new state_id
        if (this.isAdmin || !this.isAttorney) {
            this.redirectWithState(newStateId);
        }
    }
}
```

## Data Flow Summary

### 1. Initial Load
1. User visits `/referrals/create` or `/referrals/create?state_id=X`
2. Backend determines selected state (user state, law firm state, clinic state, or fallback)
3. Backend loads doctors, patients, and attorneys filtered by selected state
4. Frontend receives data and auto-selects current user if they're an attorney or doctor

### 2. State Change
1. User selects different state from dropdown
2. Frontend redirects to same page with new `state_id` parameter
3. Backend loads fresh data filtered by new state
4. All dropdowns update with new filtered data

### 3. Role-Based Restrictions

**Attorneys**:
- Cannot change state (restricted to their own state)
- Can only see doctors from their state
- Can only see patients from their state
- Auto-selected as the attorney for the referral

**Doctors**:
- Can change state freely
- Can only see attorneys from their state
- Can see all patients from selected state
- Auto-selected as the doctor for the referral

**Office Managers**:
- Can change state freely
- Can see all doctors, attorneys, and patients from selected state
- No auto-selection

**Admins**:
- Can change state freely
- Can see all doctors, attorneys, and patients from selected state
- No auto-selection

## Database Relationships

### Doctors → Clinics
- Many-to-many relationship via `pivot_clinics_users` table
- Doctors can belong to multiple clinics
- Clinics have a `state_id` for filtering

### Attorneys → Law Firms
- One-to-many relationship via `law_firm_id` in users table
- Attorneys belong to one law firm
- Law firms have a `state_id` for filtering

### Patients → States
- One-to-many relationship via `state_id` in users table
- Patients belong to one state

## Benefits of This Implementation

1. **State-Based Filtering**: Ensures users only see relevant data from their state
2. **Role-Based Restrictions**: Prevents unauthorized access to data from other states
3. **Auto-Selection**: Automatically fills in current user's information
4. **URL-Based Navigation**: Maintains state selection in URL for bookmarking
5. **Fallback Logic**: Handles cases where user data is not in filtered lists
6. **Error Prevention**: Eliminates "data not found" errors with proper fallbacks

## Testing the Fix

1. **Login as Attorney**: Should auto-select attorney data without errors
2. **Change State**: Should show appropriate data for selected state
3. **Form Submission**: Should work correctly with auto-selected data
4. **Error Handling**: Should not show "data not found" errors
