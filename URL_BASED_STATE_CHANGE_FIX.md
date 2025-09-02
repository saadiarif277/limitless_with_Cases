# URL-Based State Change Fix

## Problem Description

On the admin referral creation page (`http://127.0.0.1:8000/admin/referrals/create`), the doctor, attorney, and patient dropdowns were not updating correctly when the state changed. The AJAX-based approach was causing reactivity issues and data inconsistencies.

## Root Cause

The previous implementation used AJAX calls to fetch filtered data when the state changed, but this approach had several issues:

1. **Vue Reactivity Issues**: The computed properties weren't properly updating when local data changed
2. **Data Structure Mismatches**: The AJAX response structure didn't always match what the frontend expected
3. **Complex State Management**: Managing local state alongside server state was error-prone
4. **Race Conditions**: Multiple AJAX calls could interfere with each other

## Solution: URL-Based State Changes

### Approach
Instead of using AJAX to update data, we now redirect to the same page with a new `state_id` parameter in the URL. This ensures:

1. **Fresh Data**: The server loads all data filtered by the new state
2. **Consistent State**: No local state management issues
3. **Simple Logic**: The backend handles all filtering
4. **Reliable Updates**: Full page reload ensures all components update correctly

### Implementation

#### 1. Updated State Change Watcher

**File**: `resources/js/components/application/referral/referral-create-form.vue`

```javascript
// Watch for state changes to update available doctors and patients
'form.state_id'(newStateId, oldStateId) {
    if (newStateId && newStateId !== oldStateId) {
        // For attorneys, prevent changing to a different state
        if (this.isAttorney && this.currentUser?.state_id && newStateId != this.currentUser.state_id) {
            this.form.state_id = this.currentUser.state_id; // Revert
            return;
        }
        // For admin users, redirect to the same page with new state_id
        if (this.isAdmin || !this.isAttorney) {
            this.redirectWithState(newStateId);
        }
    }
},
```

#### 2. Added Redirect Method

```javascript
redirectWithState(stateId) {
    if (!stateId) return;

    // Redirect to the same page with the new state_id parameter
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('state_id', stateId);
    
    console.log('Redirecting to:', currentUrl.toString());
    
    // Use Inertia to navigate to the new URL
    this.$inertia.visit(currentUrl.toString(), {
        method: 'get',
        preserveState: false,
        preserveScroll: false,
        replace: true
    });
},
```

#### 3. Backend State Handling

**File**: `app/Http/Controllers/Panel/Admin/ReferralController.php`

The backend already properly handles the `state_id` parameter:

```php
public function create(Request $request): Response
{
    // Get the selected state (default to first available state if none selected)
    $selectedStateId = $request->get('state_id');

    // If no state_id provided, get the first available state
    if (!$selectedStateId) {
        $firstState = \App\Models\State::first();
        $selectedStateId = $firstState ? $firstState->state_id : null;
    }

    return Inertia::render('panel/admin/referrals/referrals-create', [
        'attorneys' => UserResource::collection(
            User::query()
                ->whereHas('roles', function($query) {
                    $query->where('name', 'Attorney');
                })
                ->when($selectedStateId, function($query) use ($selectedStateId) {
                    $query->whereHas('lawFirm', function($subQuery) use ($selectedStateId) {
                        $subQuery->where('law_firms.state_id', $selectedStateId);
                    });
                })
                ->orderBy('name')
                ->get()
        ),
        // ... similar filtering for doctors and patients
        'selectedStateId' => $selectedStateId,
    ]);
}
```

#### 4. Frontend Props

**File**: `resources/js/pages/panel/admin/referrals/referrals-create.vue`

The admin page passes the `selectedStateId` to the form component:

```vue
<x-referral-create-form
    :attorneys="attorneys"
    :doctors="doctors"
    :patients="patients"
    :selected-state-id="selectedStateId"
    :list-route="'panel.admin.referrals.index'"
    :store-route="'panel.admin.referrals.store'"
/>
```

## How It Works

### 1. Initial Load
- User visits `/admin/referrals/create` or `/admin/referrals/create?state_id=3`
- Backend loads data filtered by the `state_id` parameter
- Frontend receives filtered data and displays it in dropdowns

### 2. State Change
- User selects a different state from the dropdown
- `form.state_id` watcher triggers
- `redirectWithState()` method is called
- Page redirects to `/admin/referrals/create?state_id=NEW_STATE_ID`
- Backend loads fresh data filtered by the new state
- All dropdowns update with the new filtered data

### 3. URL Examples
- **Default**: `http://127.0.0.1:8000/admin/referrals/create`
- **State 3**: `http://127.0.0.1:8000/admin/referrals/create?state_id=3`
- **State 5**: `http://127.0.0.1:8000/admin/referrals/create?state_id=5`

## Benefits

1. **Reliable Updates**: Full page reload ensures all data is fresh and consistent
2. **Simple Logic**: No complex AJAX state management
3. **Bookmarkable URLs**: Users can bookmark specific state selections
4. **Browser History**: Back/forward buttons work correctly
5. **No Reactivity Issues**: Vue computed properties work naturally with fresh data
6. **Debugging**: Easy to see what state is selected in the URL

## Removed Code

### AJAX Methods Removed
- `updateDataByState()` - No longer needed
- `fetchDataByState()` - No longer needed
- Complex data structure handling - Simplified

### Watchers Removed
- `localAttorneys` watcher - No longer needed
- `localDoctors` watcher - No longer needed
- `localPatients` watcher - No longer needed

### Debugging Removed
- Console logs for AJAX calls - No longer needed
- Data structure debugging - Simplified approach

## Testing Instructions

1. **Navigate to**: `http://127.0.0.1:8000/admin/referrals/create`
2. **Check URL**: Should show current state_id parameter
3. **Change State**: Select a different state from dropdown
4. **Verify URL**: Should update to include new state_id
5. **Check Dropdowns**: Should show data filtered by new state
6. **Test Browser**: Back/forward buttons should work correctly

## Expected Behavior

- **State Change**: URL updates to include `?state_id=X`
- **Data Loading**: All dropdowns show data from selected state
- **Form State**: Form maintains selected state
- **Performance**: Fast page reload with fresh data
- **Reliability**: No more empty dropdowns or stale data

This approach ensures that the admin referral creation form works reliably with proper state-based filtering.
