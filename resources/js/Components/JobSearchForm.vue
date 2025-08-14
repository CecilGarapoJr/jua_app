<script setup>
import homeApiService from '@/Composables/homeApiService'
import LabelWithAjaxLoader from '@/Components/LabelWithAjaxLoader.vue'
import NiceSelect from '@/Components/NiceSelect.vue'
import { useJobFiltersStore } from '@/Store/jobFilterStore'
import { replaceTerminology } from '@/Utils/terminologyMapping'

const filter = useJobFiltersStore()
homeApiService.get('services').then((res) => {
  filter.services = res
})

// Define opportunity types for the dropdown
const opportunityTypes = [
  { value: '', label: replaceTerminology('All Opportunities') },
  { value: 'job_full_time', label: replaceTerminology('Full Time Job') },
  { value: 'job_part_time', label: replaceTerminology('Part Time Job') },
  { value: 'job_contract', label: replaceTerminology('Contract Job') },
  { value: 'job_temporary', label: replaceTerminology('Temporary Job') },
  { value: 'internship', label: replaceTerminology('Internship') },
  { value: 'scholarship', label: replaceTerminology('Scholarship') },
  { value: 'grant', label: replaceTerminology('Grant') },
  { value: 'training', label: replaceTerminology('Training') }
]

</script>

<template>
  <form @submit.prevent="filter.submit('clear')">
    <div class="row">
      <div class="col-md-4">
        <div class="input-box">
          <div class="label">{{ replaceTerminology(trans('What are you looking for?')) }}</div>
          <NiceSelect v-model="filter.filterForm.service" :options="filter.services" value-by="slug" label="title"
            :placeholder="replaceTerminology('Select Service')" @change="filter.getCategories" />
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-box border-left">
          <LabelWithAjaxLoader class="label" :text="replaceTerminology(trans('Category'))" :loading="filter.loading.categories" />
          <NiceSelect v-model="filter.filterForm.category" :options="filter.categories" label="title" value-by="slug"
            :placeholder="replaceTerminology('Select Category')" @change="() => filter.setCategory(filter.filterForm.category)" />
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-box border-left">
          <div class="label">{{ replaceTerminology(trans('Opportunity Type')) }}</div>
          <NiceSelect v-model="filter.filterForm.type" :options="opportunityTypes" label="label" value-by="value"
            :placeholder="replaceTerminology('All Types')" />
        </div>
      </div>
      <div class="col-md-2">
        <button type="submit" class="fw-500 text-uppercase h-100 tran3s search-btn" :disabled="filter.loading.categories">
          {{ replaceTerminology(trans('Search')) }}
        </button>
      </div>
    </div>
  </form>
</template>
