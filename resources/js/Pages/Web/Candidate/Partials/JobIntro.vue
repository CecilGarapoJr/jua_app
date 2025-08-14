<script setup>
import { replaceTerminology } from '@/Utils/terminologyMapping';
import { router } from '@inertiajs/vue3';

const isOpportunityPage = router.page.url.includes('opportunity') || router.page.props.job?.type && !router.page.props.job.type.startsWith('job_');
</script>

<template>
  <section class="job-portal-intro" :class="{'opportunity-portal-intro': isOpportunityPage}">
    <div class="container">
      <div class="wrapper bottom-border pt-65 md-pt-50 pb-65 md-pb-50">
        <div class="row align-items-center">
          <div class="col-lg-7">
            <div class="text-center text-lg-start">
              <h2>{{ replaceTerminology(trans(isOpportunityPage ? "Most complete opportunity portal." : "Most complete job portal.")) }}</h2>
              <p class="text-md m0 md-pb-20">
                {{ replaceTerminology(trans(isOpportunityPage ? "Signup and start find your opportunity or talents." : "Signup and start find your job or talents.")) }}
              </p>
            </div>
          </div>
          <div class="col-lg-5">
            <ul
              class="btn-group style-none d-flex flex-wrap justify-content-center justify-content-lg-end"
            >
              <li class="me-2">
                <Link :href="isOpportunityPage ? '/opportunities' : '/jobs'" class="btn-three">{{ 
                  replaceTerminology(trans(isOpportunityPage ? "Looking for opportunity?" : "Looking for job?"))
                }}</Link>
              </li>
              <li class="ms-2">
                <Link :href="isOpportunityPage ? '/employer/opportunities/create' : '/employer/jobs/create'" class="btn-four">{{ 
                  replaceTerminology(trans(isOpportunityPage ? "Post an opportunity" : "Post a job"))
                }}</Link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
