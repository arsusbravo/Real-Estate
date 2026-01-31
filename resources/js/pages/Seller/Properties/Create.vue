<script setup lang="ts">
import PropertyForm from './Form.vue';

type SelectOption = { value: string; name: string };

type Props = {
    propertyTypes: SelectOption[];
    listingTypes: SelectOption[];
    certificateTypes: SelectOption[];
    furnishingTypes: SelectOption[];
    facingDirections?: SelectOption[];
    availableFeatures?: string[];
    cities?: string[];
};

defineProps<Props>();
</script>

<template>
    <PropertyForm
        :property-types="propertyTypes"
        :listing-types="listingTypes"
        :certificate-types="certificateTypes"
        :furnishing-types="furnishingTypes"
    />
</template>
