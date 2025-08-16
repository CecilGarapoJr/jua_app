<script setup>
import DefaultLayout from '@/Layouts/Default.vue'
import JobIntro from '@/Pages/Web/Candidate/Partials/JobIntro.vue'
import WebPaginate from '@/Components/WebPaginate.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'
// Removed Breadcrumb; using custom banner with subtitle only
const { textExcerpt } = sharedComposable()

defineOptions({ layout: DefaultLayout })

const props = defineProps(['blogs', 'categories', 'recent_blogs', 'tags', 'meta'])
</script>

<template>
  <Seo :metaData="meta" />
  <div class="inner-banner-one position-relative">
    <div class="container">
      <div class="position-relative">
        <div class="row">
          <div class="col-xl-8 m-auto text-center">
            <div class="title-two">
              <h2 class="text-white">Pathway</h2>
              <p class="text-white opacity-75 mt-10">
                Welcome to our blog taliored to give the best advice to help you succeed
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <img src="/assets/images/shape/shape_02.svg" alt="" class="lazy-img shapes shape_01" />
    <img src="/assets/images/shape/shape_03.svg" alt="" class="lazy-img shapes shape_02" />
  </div>

  <section class="blog-section bg-color pt-100 lg-pt-80 pb-120 lg-pb-80">
    <div class="container">
      <div class="row gx-xl-5" v-if="blogs.total">
        <div v-for="(post, index) in props.blogs.data" :key="index" class="col-md-6">
          <article class="blog-meta-two box-layout mb-50 lg-mb-30">
            <figure class="post-img m0">
              <Link :href="route('blogs.show', post)" class="w-100 d-block">
                <img v-lazy="post.preview?.value" alt="preview" class="lazy-img w-100 tran4s" />
              </Link>
              <Link :href="route('blogs.show', post)" class="tags fw-500">{{
                post.categories[0]?.title
              }}</Link>
            </figure>
            <div class="post-data mt-35">
              <div class="date">
                <span class="fw-500 text-dark" v-show="post.featured"
                  >{{ trans('Featured') }} -
                </span>
                <Link :href="route('blogs.show', post)">{{
                  moment(post.created_at).format('DD MMM, YYYY')
                }}</Link>
              </div>
              <Link :href="route('blogs.show', post)">
                <h4 class="tran3s blog-title">{{ textExcerpt(post.title, 60) }}</h4>
              </Link>
              <Link
                :href="route('blogs.show', post)"
                class="continue-btn tran3s d-flex align-items-center"
              >
                <span class="fw-500 me-2">{{ trans('Continue Reading') }}</span>
                <i class="bi bi-arrow-right"></i>
              </Link>
            </div>
          </article>
          <!-- /.blog-meta-two -->
        </div>
      </div>

      <h3 v-else class="text-muted text-center">
        {{ trans('No blog posts') }}
      </h3>

      <WebPaginate :links="props.blogs.links" />
    </div>
  </section>
  <JobIntro />
</template>
