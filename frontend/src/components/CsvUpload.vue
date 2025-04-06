<template>
    <div class="csv-upload mb-4">
      <h3>Upload Events via CSV</h3>
      <form @submit.prevent="uploadCsv">
        <div class="mb-3">
          <label for="csv_file" class="form-label">Choose CSV File</label>
          <input
            type="file"
            id="csv_file"
            class="form-control"
            @change="handleFileUpload"
            accept=".csv"
          />
        </div>
        <button type="submit" class="btn btn-success" :disabled="!csvFile">
          Upload CSV
        </button>
      </form>

      <!-- Success/Error Message -->
      <div v-if="csvMessage" :class="`alert ${csvMessage.type}`" class="mt-3">
        {{ csvMessage.text }}
      </div>
    </div>
</template>

<script setup>
  import { ref } from 'vue';
  import axios from '@/services/axios';

  /* eslint-disable no-undef */
  const emit = defineEmits(['updateEventList']);

  const csvFile = ref(null);
  const csvMessage = ref(null);
  const loading = ref(false);

  //Handle csv file
  const handleFileUpload = (event) => {
    csvFile.value = event.target.files[0];
  };

  // Upload CSV
  const uploadCsv = async () => {
    if (!csvFile.value) return;

    const formData = new FormData();
    formData.append('csv_file', csvFile.value);

    try {
      loading.value = true;
      csvMessage.value = null;

      const response = await axios.post('/event/import-csv', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      }); 

      if(!response) {
        return;
      }

      csvMessage.value = {
        type: 'alert-success',
        text: 'CSV file uploaded successfully. Events added!',
      };

      emit('updateEventList');
    } catch (error) {
      console.error('Error uploading CSV:', error);
      csvMessage.value = {
        type: 'alert-danger',
        text: 'Error uploading CSV. Please try again.',
      };
    } finally {
      loading.value = false;
    }
  };
</script>