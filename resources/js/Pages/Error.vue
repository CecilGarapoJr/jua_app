<template>
  <div class="error-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">Error</div>
            <div class="card-body">
              <h1>Oops! Something went wrong.</h1>
              <p>We're sorry, but we couldn't find the page you were looking for.</p>
              <div class="mt-4">
                <Link href="/" class="btn btn-primary">Return to Home</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.error-container {
  padding: 100px 0;
}
</style>
