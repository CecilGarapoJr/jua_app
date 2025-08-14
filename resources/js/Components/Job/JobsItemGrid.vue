<script setup>
import sharedComposable from "@/Composables/sharedComposable";
import { useForm } from "@inertiajs/vue3";
import trans from '@/Composables/transComposable';
import { replaceTerminology } from '@/Utils/terminologyMapping';
const { formatNumber, textExcerpt } = sharedComposable();
defineProps({
  items: {
    type: Object,
  },
  col: {
    default: 6,
  },
});

const toggleBookmark = (job) => {
  let form = useForm({});
  // Use the new opportunity routes but maintain backward compatibility
  const routeName = job.type && !job.type.startsWith('job_') ? 'opportunities.bookmark' : 'jobs.bookmark';
  form.post(route(routeName, job), {
    preserveScroll: true,
    onSuccess: () => {},
  });
};
</script>

<template>
  <div v-for="job in items" :key="job.id" class="mb-30" :class="`col-sm-${col}`">
    <div
      class="job-list-two style-two position-relative"
      :class="{ favourite: job.featured_expire_at }"
    >
      <!-- Use dynamic route based on opportunity type -->
      <Link :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)" class="logo"
        ><img
          v-lazy="
            job.user?.avatar == null
              ? `https://ui-avatars.com/api/?name=${job.user.name}`
              : `${job.user?.avatar}`
          "
          alt="avatar"
          class="m-auto lazy-img"
      /></Link>
      <button
        type="button"
        @click="toggleBookmark(job)"
        class="text-center save-btn rounded-circle tran3s me-3"
        :class="{ 'bg-success': job.is_bookmarked }"
        :title="replaceTerminology('Save Job')"
      >
        <i class="bi bi-bookmark-dash"></i>
      </button>
      <div>
        <!-- Use dynamic route based on opportunity type -->
        <a :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)" class="job-duration fw-500">
          {{ replaceTerminology(job.type) }}
        </a>
      </div>
      <div>
        <!-- Use dynamic route based on opportunity type -->
        <Link :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)" class="title fw-500 tran3s">
          {{ textExcerpt(job.title, 50) }}</Link
        >
      </div>
      
      <!-- Conditional display based on opportunity type -->
      <!-- Job-specific fields -->
      <div class="job-salary" v-if="job.type && job.type.startsWith('job_')">
        <span
          class="fw-500 text-dark"
          v-if="
            job.salary_range &&
            job.salary_range.split('-')[0] > 0 &&
            job.salary_range.split('-')[1] > 0
          "
        >
          {{ formatNumber(job.salary_range.split("-")[0]) }}
          -
          {{ formatNumber(job.salary_range.split("-")[1]) }}
        </span>
        <span class="fw-500 text-dark" v-else>{{ replaceTerminology(trans("Negotiable")) }}</span>
        /
        {{ replaceTerminology(job.salary_type) }}
      </div>
      
      <!-- Scholarship-specific display -->
      <div class="job-salary" v-else-if="job.type === 'scholarship'">
        <span class="fw-500 text-dark" v-if="job.fields?.award_amount">
          {{ replaceTerminology('Award Amount') }}: {{ formatNumber(job.fields.award_amount, 0) }}
        </span>
      </div>
      
      <!-- Grant-specific display -->
      <div class="job-salary" v-else-if="job.type === 'grant'">
        <span class="fw-500 text-dark" v-if="job.fields?.grant_amount">
          {{ replaceTerminology('Grant Amount') }}: {{ formatNumber(job.fields.grant_amount, 0) }}
        </span>
      </div>
      
      <!-- Training-specific display -->
      <div class="job-salary" v-else-if="job.type === 'training'">
        <span class="fw-500 text-dark" v-if="job.fields?.duration">
          {{ replaceTerminology('Duration') }}: {{ job.fields.duration }}
        </span>
      </div>
      
      <!-- Internship-specific display -->
      <div class="job-salary" v-else-if="job.type === 'internship'">
        <span class="fw-500 text-dark" v-if="job.fields?.stipend">
          {{ replaceTerminology('Stipend') }}: {{ formatNumber(job.fields.stipend, 0) }}
        </span>
      </div>
      
      <!-- Default display for other types -->
      <div class="job-salary" v-else>
        <span class="fw-500 text-dark">
          {{ replaceTerminology(job.type) }}
        </span>
      </div>
      
      <div class="mt-auto d-flex align-items-center justify-content-between">
        <div class="job-location" v-if="!job.country?.[0]?.name">
          {{ replaceTerminology(trans("Remote")) }}
        </div>
        <div class="job-location" v-else>
          {{ job.country?.[0]?.name ?? "" }},{{ job.state?.[0]?.name ?? "" }}
        </div>
        <!-- Use dynamic route based on opportunity type -->
        <Link :href="route(job.type && !job.type.startsWith('job_') ? 'opportunities.show' : 'jobs.show', job.slug)" class="text-center apply-btn tran3s">
          {{ replaceTerminology(trans("Details")) }}
        </Link>
      </div>
    </div>
    <!-- /.job-list-two -->
  </div>
</template>
