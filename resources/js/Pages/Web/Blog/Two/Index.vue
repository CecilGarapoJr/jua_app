<script setup>
import DefaultLayout from '@/Layouts/Default.vue'
import JobIntro from '@/Pages/Web/Candidate/Partials/JobIntro.vue'
import SingleBlogPostStandard from '@/Components/SingleBlogPostStandard.vue'
import BlogSidebarRight from '@/Pages/Web/Blog/Partials/BlogSidebarRight.vue'
import WebPaginate from '@/Components/WebPaginate.vue'
// Removed Breadcrumb; using custom banner with subtitle only

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
  <section class="blog-section pt-100 lg-pt-80 pb-120 lg-pb-80">
    <div class="container">
      <div class="row">
        <div class="m-auto col-xxl-11">
          <div class="row">
            <div class="col-lg-8">
              <div class="row">
                <template v-if="blogs.total">
                  <SingleBlogPostStandard class="col-md-6" v-for="post in blogs.data" :key="post.id" :post="post" />
                </template>
                <h3 v-else class="text-center text-muted">
                  {{ trans('No blog posts') }}
                </h3>
              </div>
              <WebPaginate :links="props.blogs.links" />
            </div>

            <div class="col-lg-4">
              <BlogSidebarRight
                :categories="props.categories"
                :tags="props.tags"
                :recent-blogs="props.recent_blogs"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ./blog-section -->

  <JobIntro />
</template>
