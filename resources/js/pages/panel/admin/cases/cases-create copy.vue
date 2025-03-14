<template>
 <v-inertia-head title="Create Referral" />

<div class="h-full flex flex-col">
    <v-content-body class="border-b border-gray-200">
        <v-section-heading>
            <template #title>
                    Create Cases
                </template>
            </v-section-heading>
        </v-content-body>
    <div class="container mx-auto py-6">
      <div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Create a New Case</h1>

        <form @submit.prevent="submitForm">
          <!-- Patient Selection -->
          <div class="mb-4">
            <label for="patient_id" class="block text-sm font-semibold text-gray-600">Patient</label>
            <select id="patient_id" v-model="formData.patient_id" class="w-full p-2 border rounded-md" required>
              <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                {{ patient.name }}
              </option>
            </select>
          </div>

          <!-- Attorney Selection -->
          <div class="mb-4">
            <label for="attorney_id" class="block text-sm font-semibold text-gray-600">Attorney</label>
            <select id="attorney_id" v-model="formData.attorney_id" class="w-full p-2 border rounded-md">
              <option value="">Select Attorney (Optional)</option>
              <option v-for="attorney in attorneys" :key="attorney.id" :value="attorney.id">
                {{ attorney.name }}
              </option>
            </select>
          </div>

          <!-- Piloting Physician Selection -->
          <div class="mb-4">
            <label for="piloting_physician_id" class="block text-sm font-semibold text-gray-600">Piloting Physician</label>
            <select id="piloting_physician_id" v-model="formData.piloting_physician_id" class="w-full p-2 border rounded-md">
              <option value="">Select Physician (Optional)</option>
              <option v-for="physician in physicians" :key="physician.id" :value="physician.id">
                {{ physician.name }}
              </option>
            </select>
          </div>

          <!-- Primary Referral Selection -->
          <div class="mb-4">
            <label for="primary_referral_id" class="block text-sm font-semibold text-gray-600">Primary Referral</label>
            <select id="primary_referral_id" v-model="formData.primary_referral_id" class="w-full p-2 border rounded-md" required>
              <option v-for="referral in referrals" :key="referral.referral_id" :value="referral.referral_id">
                {{ referral.referral_number }}
              </option>
            </select>
          </div>

          <!-- Multi-select for Referrals -->
          <div class="mb-4">
            <label for="referral_ids" class="block text-sm font-semibold text-gray-600">Additional Referrals</label>
            <select id="referral_ids" v-model="formData.referral_ids" multiple class="w-full p-2 border rounded-md">
              <option v-for="referral in referrals" :key="referral.referral_id" :value="referral.referral_id">
                {{ referral.referral_number }}
              </option>
            </select>
          </div>

          <!-- Service Billed -->
          <div class="mb-4">
            <label for="service_billed" class="block text-sm font-semibold text-gray-600">Service Billed</label>
            <input type="number" id="service_billed" v-model="formData.service_billed" class="w-full p-2 border rounded-md" required />
          </div>

          <!-- CMS1500 Generated -->
          <div class="mb-4">
            <label for="is_cms1500_generated" class="block text-sm font-semibold text-gray-600">CMS1500 Generated</label>
            <input type="checkbox" id="is_cms1500_generated" v-model="formData.is_cms1500_generated" />
          </div>

          <!-- Case Won -->
          <div class="mb-4">
            <label for="case_won" class="block text-sm font-semibold text-gray-600">Case Won</label>
            <input type="checkbox" id="case_won" v-model="formData.case_won" />
          </div>

          <!-- Outcome -->
          <div class="mb-4">
            <label for="outcome" class="block text-sm font-semibold text-gray-600">Outcome</label>
            <input type="text" id="outcome" v-model="formData.outcome" class="w-full p-2 border rounded-md" />
          </div>

          <!-- Reduction Accepted -->
          <div class="mb-4">
            <label for="reduction_accepted" class="block text-sm font-semibold text-gray-600">Reduction Accepted</label>
            <input type="checkbox" id="reduction_accepted" v-model="formData.reduction_accepted" />
          </div>

          <!-- Is Closed -->
          <div class="mb-4">
            <label for="is_closed" class="block text-sm font-semibold text-gray-600">Is Closed</label>
            <input type="checkbox" id="is_closed" v-model="formData.is_closed" required />
          </div>

          <!-- Closed At -->
          <div class="mb-4" v-if="formData.is_closed">
            <label for="closed_at" class="block text-sm font-semibold text-gray-600">Closed At</label>
            <input type="datetime-local" id="closed_at" v-model="formData.closed_at" class="w-full p-2 border rounded-md" />
          </div>

          <div class="flex justify-end mt-6">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded-lg">Create Case</button>
          </div>
        </form>
      </div>
    </div>

    </div>
  </template>


  <script>
  import Layout from "@/layouts/panel/admin/index.vue";

  export default {
    data() {

      return {
        name: "CasesCreate",
        layout: Layout,
        formData: {
          patient_id: '',
          attorney_id: '',
          piloting_physician_id: '',
          primary_referral_id: '',
          referral_ids: [],  // Store multiple referral IDs here
          service_billed: '',
          is_cms1500_generated: false,
          case_won: false,
          outcome: '',
          reduction_accepted: false,
          is_closed: false,
          closed_at: '',
        },
        patients: [],
        attorneys: [],
        physicians: [],
        referrals: [],
      };
    },
    mounted() {
      // Load referral data (from API or local resource)
      this.loadReferrals();
    },
    methods: {
      async loadReferrals() {
        // Fetch referrals from your API and assign them
        const response = await fetch('/api/referrals');
        this.referrals = await response.json();
      },
      async submitForm() {
        const response = await fetch('/api/cases', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.formData),
        });
        const result = await response.json();
        if (response.ok) {
          alert('Case created successfully');
        } else {
          alert('Error creating case');
        }
      }
    },
  };
  </script>
