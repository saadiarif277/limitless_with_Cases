# Reduction Request System - Complete Guide

## Overview
The reduction request system allows attorneys to request bill reductions from doctors and enables doctors to respond with accept/reject decisions and counter offers.

## User Roles & Access

### 🔐 **Attorneys**
- **Can View**: All reduction requests for their cases
- **Can Create**: New reduction requests for any referral in their cases
- **Can Track**: Status and doctor responses
- **Access**: Via sidebar menu "Reduction Requests"

### 🩺 **Doctors**
- **Can View**: Reduction requests for their referrals only
- **Can Respond**: Accept, reject, or provide counter offers
- **Can Add**: Notes and explanations
- **Access**: Via sidebar menu "Reduction Requests"

### 👥 **Case Managers & Admins**
- **Can View**: All reduction requests across all cases
- **Can Monitor**: System-wide reduction request status
- **Access**: Via case view pages

## System Flow

### 1. **Attorney Creates Reduction Request**
```
Attorney → Case View → "New Reduction Request" Button → Modal Form → Submit
```

**Required Fields:**
- Case ID
- Referral ID  
- Reduction Amount
- Supporting Document (optional)
- Notes (optional)

**Backend Validation:**
- Attorney must own the case
- Referral must belong to the case
- Amount must be positive number
- File must be PDF/DOC/DOCX (max 2MB)

### 2. **Doctor Receives & Reviews Request**
```
Doctor → Reduction Requests Page → View Request Details → Respond
```

**Response Options:**
- **Accept**: Agree to the reduction amount
- **Reject**: Decline the reduction
- **Counter Offer**: Suggest different amount
- **Notes**: Add explanation or comments

### 3. **Attorney Views Response**
```
Attorney → Reduction Requests Page → View Doctor's Decision → Take Action
```

**Response Information:**
- Doctor's decision (accepted/rejected)
- Counter offer amount (if provided)
- Doctor's notes
- Response timestamp

## Technical Implementation

### Backend Routes
```php
// View reduction requests
GET /referrals/reduction-requests → ReferralController@reductionRequests

// Create new reduction request  
POST /referrals/reduction-requests → ReferralController@storeReductionRequest

// Update doctor decision
PUT /referrals/reduction-requests/{id}/decision → ReferralController@updateReductionDecision

// Case-specific reduction requests
POST /cases/{case}/reduction-requests → CasesController@createReductionRequest
PUT /cases/{case}/reduction-requests/{id}/decision → CasesController@updateReductionDecision
```

### Database Schema
```sql
reduction_requests table:
- id (primary key)
- case_id (foreign key to cases)
- referral_id (foreign key to referrals)  
- amount (decimal, reduction amount)
- file_path (string, uploaded document path)
- referral_status (string, request status)
- doctor_decision (string: pending/accepted/rejected)
- counter_offer (decimal, doctor's counter)
- notes (text, additional information)
- created_at, updated_at (timestamps)
```

### Frontend Components
- **`reduction-requests.vue`**: Main page for viewing/creating requests
- **`case-view.vue`**: Case-specific reduction request handling
- **`_sidebar-menu.vue`**: Navigation menu with role-based visibility

## UI Features

### For Attorneys
✅ **Create New Requests**: Modal form with all required fields
✅ **Track Status**: View all requests and their current status  
✅ **Monitor Responses**: See doctor decisions and counter offers
✅ **File Management**: Upload supporting documents
✅ **Notes System**: Add context and explanations

### For Doctors
✅ **Request Dashboard**: View all pending requests
✅ **Response Forms**: Accept/reject with optional counter offers
✅ **File Review**: Download and review attorney documents
✅ **Notes System**: Provide explanations for decisions
✅ **Status Tracking**: Monitor request lifecycle

## Status Management

### Request Statuses
- **`pending`**: Initial state when created
- **`reduction_request_sent`**: Request submitted to doctor
- **`accepted`**: Doctor accepted the reduction
- **`rejected`**: Doctor rejected the reduction
- **`counter_offered`**: Doctor provided counter offer

### Doctor Decision States
- **`pending`**: Awaiting doctor response
- **`accepted`**: Doctor agreed to reduction
- **`rejected`**: Doctor declined reduction

## Security & Permissions

### Role-Based Access Control
- **Attorneys**: Can only access their own cases
- **Doctors**: Can only access their own referrals
- **Case Managers**: Full access to all cases
- **Admins**: System-wide access

### Data Validation
- **Case Ownership**: Attorneys can only create requests for their cases
- **Referral Association**: Requests must be linked to valid case-referral pairs
- **File Upload**: Secure file storage with type/size validation
- **Amount Validation**: Positive numbers only with decimal precision

## Error Handling

### Common Scenarios
- **Invalid Case/Referral**: 403 Forbidden if not owned by user
- **Missing Fields**: Validation errors with user-friendly messages
- **File Upload Issues**: Clear error messages for file problems
- **Permission Denied**: Role-based access control enforcement

### User Feedback
- **Success Messages**: Confirmation when actions complete
- **Error Messages**: Clear explanation of what went wrong
- **Loading States**: Visual feedback during processing
- **Form Validation**: Real-time field validation

## Future Enhancements

### Planned Features
1. **Email Notifications**: Automatic alerts for status changes
2. **Bulk Operations**: Handle multiple requests simultaneously
3. **Advanced Filtering**: Search and sort by various criteria
4. **Reporting**: Analytics on reduction request patterns
5. **Mobile Optimization**: Responsive design for mobile devices

### Technical Improvements
1. **Real-time Updates**: WebSocket integration for live status
2. **File Compression**: Optimize document storage
3. **Audit Logging**: Track all system interactions
4. **API Rate Limiting**: Prevent abuse and ensure performance

## Testing Recommendations

### User Role Testing
- **Attorney Flow**: Create → Track → View Response
- **Doctor Flow**: Receive → Review → Respond
- **Admin Flow**: Monitor → Manage → Oversee

### Edge Cases
- **Invalid Data**: Test with malformed inputs
- **Permission Tests**: Verify role-based access
- **File Handling**: Test various file types and sizes
- **Concurrent Access**: Multiple users accessing same data

## Troubleshooting

### Common Issues
1. **Menu Not Visible**: Check user role and permissions
2. **Requests Not Loading**: Verify case/referral ownership
3. **File Upload Fails**: Check file size and type restrictions
4. **Permission Errors**: Ensure user has correct role assignment

### Debug Information
- **Browser Console**: Check for JavaScript errors
- **Network Tab**: Monitor API request/response
- **Laravel Logs**: Backend error tracking
- **Database Queries**: Verify data relationships

## Conclusion

The reduction request system provides a comprehensive workflow for attorneys and doctors to negotiate bill reductions in a secure, role-based environment. The system ensures data integrity, user privacy, and efficient communication between parties while maintaining audit trails and proper validation throughout the process.
