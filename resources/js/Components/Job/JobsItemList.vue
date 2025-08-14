<script setup>
import sharedComposable from "@/Composables/sharedComposable";
import { useForm } from "@inertiajs/vue3";
import trans from '@/Composables/transComposable';
import { replaceTerminology } from '@/Utils/terminologyMapping';
const { formatNumber, textExcerpt } = sharedComposable();
defineProps({
  items: {
    type: Object,
    default: [],
  },
});

const toggleBookmark = (job) => {
  let form = useForm({});
  // Use the new opportunity routes but maintain backward compatibility
  const routeName = job.type && job.type.startsWith('job_') ? 'jobs.bookmark' : 'opportunities.bookmark';
  form.post(route(routeName, job), {
    preserveScroll: true,
    onSuccess: () => {},
  });
};
</script>
<template>
  <div
    v-for="job in items"
    :key="job.id"
    class="mb-20 job-list-one style-two position-relative border-style"
    :class="{ favourite: job.featured_expire_at }"
  >
    <div class="row justify-content-between align-items-center">
      <div class="col-md-5">
        <div class="job-title d-flex align-items-center">
          <!-- Use dynamic route based on opportunity type -->
          <Link :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)" class="logo">
            <img
              v-lazy="
                job.user?.avatar == null
                  ? `https://ui-avatars.com/api/?name=${job.user.name}&rounded=true&background=random&color=#fff`
                  : `${job.user?.avatar}`
              "
              alt="avatar"
              class="m-auto candidate-avatar-rounded"
            />
          </Link>
          <div class="split-box1">
            <!-- Use dynamic route based on opportunity type -->
            <Link :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)" class="job-duration fw-500">
              {{ replaceTerminology(job.type) }}
            </Link>
            <Link :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)" class="title fw-500 tran3s">
              {{ textExcerpt(job.title, 50) }}</Link
            >
          </div>
        </div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="job-location" v-if="!job.country?.[0]?.name">
          {{ replaceTerminology(trans("Remote")) }}
        </div>
        <div class="job-location" v-else>
          <Link :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)">
            {{ job.country?.[0]?.name }}, {{ job.state?.[0]?.name }}</Link
          >
        </div>
        <!-- Conditional display based on opportunity type -->
        <div class="job-salary" v-if="job.type && job.type.startsWith('job_')">
          <span
            class="fw-500 text-dark"
            v-if="
              job.salary_range &&
              job.salary_range.split('-')[0] > 0 &&
              job.salary_range.split('-')[1] > 0
            "
          >
            {{ formatNumber(job.salary_range.split("-")[0], 0) }}
            -
            {{ formatNumber(job.salary_range.split("-")[1], 0) }}
          </span>
          <span class="fw-500 text-dark" v-else>{{ replaceTerminology(trans("Negotiable")) }}</span>
          / {{ replaceTerminology(job.salary_type) }} .
          {{ replaceTerminology(job.experience) }}
        </div>
        <!-- Scholarship-specific display -->
        <div class="job-salary" v-else-if="job.type === 'scholarship'">
          <span class="fw-500 text-dark" v-if="job.fields?.award_amount">
            {{ replaceTerminology('Award Amount') }}: {{ formatNumber(job.fields.award_amount, 0) }}
          </span>
          <span v-if="job.fields?.eligibility_criteria" class="d-block">
            {{ replaceTerminology('Eligibility') }}: {{ textExcerpt(job.fields.eligibility_criteria, 30) }}
          </span>
        </div>
        <!-- Grant-specific display -->
        <div class="job-salary" v-else-if="job.type === 'grant'">
          <span class="fw-500 text-dark" v-if="job.fields?.grant_amount">
            {{ replaceTerminology('Grant Amount') }}: {{ formatNumber(job.fields.grant_amount, 0) }}
          </span>
          <span v-if="job.fields?.funding_source" class="d-block">
            {{ replaceTerminology('Source') }}: {{ job.fields.funding_source }}
          </span>
        </div>
        <!-- Training-specific display -->
        <div class="job-salary" v-else-if="job.type === 'training'">
          <span class="fw-500 text-dark" v-if="job.fields?.duration">
            {{ replaceTerminology('Duration') }}: {{ job.fields.duration }}
          </span>
          <span v-if="job.fields?.format" class="d-block">
            {{ replaceTerminology('Format') }}: {{ replaceTerminology(job.fields.format) }}
          </span>
        </div>
        <!-- Internship-specific display -->
        <div class="job-salary" v-else-if="job.type === 'internship'">
          <span class="fw-500 text-dark" v-if="job.fields?.stipend">
            {{ replaceTerminology('Stipend') }}: {{ formatNumber(job.fields.stipend, 0) }}
          </span>
          <span v-if="job.fields?.internship_duration" class="d-block">
            {{ replaceTerminology('Duration') }}: {{ job.fields.internship_duration }}
          </span>
        </div>
        <!-- Default display for other types -->
        <div class="job-salary" v-else>
          <span class="fw-500 text-dark">
            {{ replaceTerminology(job.type) }}
          </span>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="btn-group d-flex align-items-center justify-content-sm-end xs-mt-20">
          <button
            type="button"
            @click="toggleBookmark(job)"
            class="text-center save-btn rounded-circle tran3s me-3"
            :class="{ 'bg-success': job.is_bookmarked }"
            :title="replaceTerminology('Save Job')"
          >
            <i class="bi bi-bookmark-dash"></i>
          </button>
          <Link :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)" class="text-center apply-btn tran3s">
            {{ replaceTerminology(trans("Details")) }}
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
