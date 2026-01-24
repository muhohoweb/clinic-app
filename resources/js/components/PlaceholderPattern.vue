<script setup lang="ts">
import { computed } from 'vue';

const patternId = computed(
    () => `pattern-${Math.random().toString(36).substring(2, 9)}`,
);
</script>

<template>
  <div>

  </div>
</template>
