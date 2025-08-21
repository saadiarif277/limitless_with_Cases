<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\DocumentType;
use App\Models\LawFirm;
use App\Models\Referral;
use App\Models\User;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReferralRepository
{
    /**
     * Get all the referrals that the authenticated user can access.
     */
    public function getItems(Request $request = null): Collection
    {
        $user = auth()->user();

        return Referral::query()
            ->with([
                'appointment',
                'attorneyUser',
                'clinic',
                'doctorUser',
                'patientUser',
                'referralStatus',
            ])
            ->leftJoin('users as attorney_users', 'referrals.attorney_user_id', '=', 'attorney_users.user_id')
            ->leftJoin('users as doctor_users', 'referrals.doctor_user_id', '=', 'doctor_users.user_id')
            ->leftJoin('users as patient_users', 'referrals.patient_user_id', '=', 'patient_users.user_id')
            ->leftJoin('clinics', 'referrals.clinic_id', '=', 'clinics.clinic_id')

            /**
             * Filter by the search query, if available.
             */
            ->when($request && $request->has('searchQuery') && $request->get('searchQuery'), function ($query) use ($request) {
                $searchTerm = strtolower($request->get('searchQuery'));
                $query->where(function ($subquery) use ($searchTerm) {
                    $subquery
                        ->orWhereRaw('LOWER(attorney_users.name) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(attorney_users.email) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(doctor_users.name) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(doctor_users.email) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(patient_users.name) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(patient_users.email) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(clinics.name) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(clinics.email) LIKE ?', ["%{$searchTerm}%"]);
                });
            })

            /**
             * Filter results by the selected referral status ID, if available.
             */
            ->when($request && $request->has('referralStatusId') && $request->get('referralStatusId'), function ($query) use ($request) {
                $query->where('referrals.referral_status_id', $request->get('referralStatusId'));
            })

            /**
             * Filter the results by the user's access.
             */
            ->where(function ($groupQuery) use ($user) {
                $groupQuery
                    ->orWhereIn('clinics.clinic_id', $user->clinics->pluck('clinic_id')->toArray())
                    ->orWhere('referrals.attorney_user_id', $user->getKey())
                    ->orWhere('referrals.patient_user_id', $user->getKey())
                    ->orWhere('referrals.doctor_user_id', $user->getKey())
                    ->orWhere('referrals.source_user_id', $user->getKey())
                    ->orWhereHas('attorneyUser', function ($query) use ($user) {
                        $query->where('users.law_firm_id', $user->law_firm_id);
                    });
            })
            ->orderBy('referral_date', 'desc')
            ->orderBy('referrals.referral_id', 'desc')
            ->get();
    }

    /**
     * Create a new instance of the resource.
     */
    public function create(StoreReferralRequest $request): Referral
    {
        DB::beginTransaction();

        try {
            $currentUser = auth()->user();

            /**
             * Prepare the payload for the referral.
             */
            $payload = $request->safe()->only([
                'appointment_id',
                'attorney_user_id',
                'clinic_id',
                'doctor_user_id',
                'injury_date',
                'referral_date',
                'referral_status_id',
                'state_id',
                'source_user_id',
            ]);

            /**
             * Ensure that the referral will always has a source user.
             */
            if (!$request->filled('source_user_id')) {
                if ($currentUser) {
                    $payload['source_user_id'] = $currentUser->getKey();
                } else {
                    $payload['source_user_id'] = User::SYSTEM;
                }
            }

            /**
             * Get the doctor user that belongs to the referral.
             */
            $doctorUser = $this->getDoctorUser($request);
            $payload['doctor_user_id'] = $doctorUser->getKey();

            /**
             * Get the clinic that belongs to the referral.
             */
            $clinic = $this->getClinic($request, $doctorUser);
            $payload['clinic_id'] = $clinic->getKey();

            /**
             * Get the patient user that belongs to the referral.
             */
            $patientUser = $this->getPatientUser($request);
            $payload['patient_user_id'] = $patientUser->getKey();

            /**
             * Get the attorney user that belongs to the referral.
             */
            $attorneyUser = $this->getAttorneyUser($request);
            $payload['attorney_user_id'] = $attorneyUser->getKey();

            /**
             * Get the law firm that belongs to the referral.
             */
            $lawFirm = $this->getLawFirm($request, $attorneyUser);
            // $payload['law_firm_id'] = $lawFirm->getKey();

            /**
             * Create the referral.
             */
            $referral = Referral::create($payload);

            /**
             * Load additional data for the referral.
             */
            $referral->load([
                'patientUser',
            ]);

            /**
             * Create Documents.
             */
            $documents = $request->validated()['documents'];
            $this->createDocuments($documents, $referral);

            /**
             * Sync the referral reasons with the referral.
             */
            $referralReasonIds = $request->validated()['referral_reason_ids'];
            $referral->referralReasons()->sync($referralReasonIds);

            DB::commit();
            return $referral->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an existing instance of the resource.
     */
    public function update(UpdateReferralRequest $request, Referral $referral): Referral
    {
        DB::beginTransaction();

        try {
            /**
             * Prepare the payload for the referral.
             */
            $payload = $request->safe()->only([
                'referral_date',
                'referral_status_id',
                'source_user_id',
            ]);

            /**
             * Update the referral.
             */
            $referral->update($payload);

            /**
             * Create Documents.
             */
            $documents = $request->validated()['documents'];
            $this->createDocuments($documents, $referral);

            DB::commit();
            return $referral->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return $referral;
    }

    /**
     * Create the documents related to the referral.
     */
    public function createDocuments(array $documents = [], Referral $referral): void
    {
        foreach($documents as $documentTypeId => $document)
        {
            // Ensure that the document is an uploaded file.
            if (!method_exists($document, 'getClientOriginalExtension')) {
                continue;
            }

            // Get the document type to be used.
            $documentType = DocumentType::findOrFail($documentTypeId);

            // Generate a file name for the PDF
            $fileName = "";
            $fileName .= Str::slug($referral->patientUser->name);
            $fileName .= '-'.Str::slug($documentType->name);
            $fileName .= '-'.now()->format('Y-m-d-H:i:s');
            $fileName .= '-REF#'.$referral->getKey();
            $fileName .= ".{$document->getClientOriginalExtension()}";

            // Upload the file to the specified path and get the stored file path
            $path = $document->storeAs('public/uploads/documents', $fileName);

            // Find the current document, if there is one.
            $currentDocument = $referral
                ->documents()
                ->where('document_type_id', $documentTypeId)
                ->first();

            if ($currentDocument) {
                $currentDocument->update([
                    'name' => $fileName,
                    'path' => $path,
                ]);
            } else {
                $referral->documents()->create([
                    'document_type_id' => $documentTypeId,
                    'name' => $fileName,
                    'path' => $path,
                ]);
            }
        }
    }

    /**
     * Get the law firm for the attorney.
     */
    public function getLawFirm($request, User $attorneyUser): LawFirm
    {
        if ($request->filled('law_firm_id')) {
            return LawFirm::findOrFail($request->validated()['law_firm_id']);
        }

        if ($attorneyUser->lawFirm) {
            return $attorneyUser->lawFirm;
        }

        $lawFirmData = $request->validated()['attorney']['law_firm'];

        $lawFirm = LawFirm::updateOrCreate([
            'email' => $lawFirmData['email'],
        ], [
            'name' => $lawFirmData['name'],
            'phone_number' => $lawFirmData['phone_number'],
            'address_line_1' => $lawFirmData['address_line_1'],
            'address_line_2' => $lawFirmData['address_line_2'],
            'city' => $lawFirmData['city'],
            'state_id' => $lawFirmData['state_id'],
            'zip_code' => $lawFirmData['zip_code'],
        ]);

        $attorneyUser->update([
            'law_firm_id' => $lawFirm->getKey(),
        ]);

        return $lawFirm;
    }

    /**
     * Get the clinic for the referral.
     */
    public function getClinic($request, User $doctorUser): Clinic
    {
        if ($request->filled('clinic_id')) {
            return Clinic::findOrFail($request->validated()['clinic_id']);
        }

        $clinicData = $request->validated()['doctor']['clinic'];

        $clinic = Clinic::updateOrCreate([
            'email' => $clinicData['email'],
        ], [
            'name' => $clinicData['name'],
            'phone_number' => $clinicData['phone_number'],
            'address_line_1' => $clinicData['address_line_1'],
            'address_line_2' => $clinicData['address_line_2'],
            'city' => $clinicData['city'],
            'state_id' => $clinicData['state_id'],
            'zip_code' => $clinicData['zip_code'],
        ]);

        $doctorUser->clinics()->syncWithoutDetaching($clinic->getKey());

        return $clinic;
    }

    /**
     * Get the attorney user for the referral.
     */
    public function getAttorneyUser($request): User
    {
        if ($request->filled('attorney_user_id')) {
            return User::findOrFail($request->validated()['attorney_user_id']);
        }

        $userData = $request->validated()['attorney'];

        $user = User::updateOrCreate([
            'email' => $userData['email'],
        ], array_merge($userData, [
            'password' => bcrypt(Str::random(16)),
        ]));

        $user->assignRole('Attorney');

        return $user;
    }

    /**
     * Get the doctor user for the referral.
     */
    public function getDoctorUser($request): User
    {
        if ($request->filled('doctor_user_id')) {
            return User::findOrFail($request->validated()['doctor_user_id']);
        }

        $userData = $request->validated()['doctor'];

        $user = User::updateOrCreate([
            'email' => $userData['email'],
        ], array_merge($userData, [
            'password' => bcrypt(Str::random(16)),
        ]));

        $user->assignRole('Doctor');

        return $user;
    }

    /**
     * Get the patient user for the referral.
     */
    public function getPatientUser($request): User
    {
        if ($request->filled('patient_user_id')) {
            return User::findOrFail($request->validated()['patient_user_id']);
        }

        $userData = $request->validated()['patient'];

        $user = User::updateOrCreate([
            'email' => $userData['email'],
        ], array_merge($userData, [
            'password' => bcrypt(Str::random(16)),
        ]));

        $user->assignRole('Patient');

        return $user;
    }
}
