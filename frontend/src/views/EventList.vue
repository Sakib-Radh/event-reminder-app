<template>
    <div class="container mt-4">
      <h2 class="text-center mb-4">Your Events</h2>
  
      <!-- Form to Add Event -->
      <div class="card mb-4">
        <div class="card-body">
          <h3>Create New Event</h3>
          <form @submit.prevent="createEvent">
            <div class="mb-3">
              <label for="title" class="form-label">Title:</label>
              <input v-model="newEvent.title" type="text" class="form-control" id="title" required />
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description:</label>
              <textarea v-model="newEvent.description" class="form-control" id="description" required></textarea>
            </div>
            <div class="mb-3">
              <label for="event_time" class="form-label">Event Time:</label>
              <input v-model="newEvent.event_time" type="datetime-local" class="form-control" id="event_time" required />
            </div>
            <button type="submit" class="btn btn-primary">Add Event</button>
          </form>
        </div>
      </div>
  
      <!-- Event List -->
      <div v-if="events" class="card">
        <div class="card-body">
          <h3>Upcoming Events</h3>
          <ul class="list-group">
            <li v-for="event in events" :key="event.id" class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <h5>{{ event.title }}</h5>
                <p>{{ event.description }}</p>
                <p><strong>Event Time:</strong> {{ new Date(event.event_time).toLocaleString() }}</p>
              </div>
              <div>
                <button @click="deleteEvent(event.id)" class="btn btn-danger btn-sm me-2">Delete</button>
                <button @click="editEvent(event)" class="btn btn-warning btn-sm">Edit</button>
              </div>
            </li>
          </ul>
        </div>
      </div>
  
      <!-- Loading Spinner -->
      <div v-if="loading" class="d-flex justify-content-center mt-3">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
  
      <!-- Edit Event Modal -->
      <div v-if="editingEvent" class="modal fade show" tabindex="-1" style="display: block;">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Event</h5>
              <button type="button" class="btn-close" @click="cancelEdit"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="updateEvent">
                <div class="mb-3">
                  <label for="editTitle" class="form-label">Title:</label>
                  <input v-model="editingEvent.title" type="text" class="form-control" id="editTitle" required />
                </div>
                <div class="mb-3">
                  <label for="editDescription" class="form-label">Description:</label>
                  <textarea v-model="editingEvent.description" class="form-control" id="editDescription" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="editEventTime" class="form-label">Event Time:</label>
                  <input v-model="editingEvent.event_time" type="datetime-local" class="form-control" id="editEventTime" required />
                </div>
                <div class="d-flex justify-content-between">
                  <button type="submit" class="btn btn-success">Update Event</button>
                  <button type="button" class="btn btn-secondary" @click="cancelEdit">Cancel</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
  
<script setup>
  import { ref, onMounted } from 'vue';
  import axios from '../services/axios';
  
  // Reactive references for events and form data
  const events = ref([]);
  const newEvent = ref({
    title: '',
    description: '',
    event_time: '',
  });
  const editingEvent = ref(null);
  const loading = ref(false);
  
  // Fetch all events
  const fetchEvents = async () => {
    loading.value = true;
    try {
      const response = await axios.get('/event');
      events.value = response.data.data;
    } catch (error) {
      console.error('Error fetching events:', error);
    } finally {
      loading.value = false;
    }
  };
  
  // Create a new event
  const createEvent = async () => {
    loading.value = true;
    try {
      const response = await axios.post('/event', newEvent.value);
      events.value.push(response.data.data); // Add new event to the list
      newEvent.value = { title: '', description: '', event_time: '' }; // Reset form
    } catch (error) {
      console.error('Error creating event:', error);
    } finally {
      loading.value = false;
    }
  };
  
  // Delete an event
  const deleteEvent = async (eventId) => {
    const confirmation = confirm('Are you sure you want to delete this event?');
    if (!confirmation) return;
  
    loading.value = true;
    try {
      await axios.delete(`/event/${eventId}`);
      events.value = events.value.filter((event) => event.id !== eventId); // Remove event from list
    } catch (error) {
      console.error('Error deleting event:', error);
    } finally {
      loading.value = false;
    }
  };
  
  // Edit an event
  const editEvent = (event) => {
    editingEvent.value = { ...event }; // Copy event data to editing form
  };
  
  // Update an event
  const updateEvent = async () => {
    loading.value = true;
    try {
      const response = await axios.put(`/event/${editingEvent.value.id}`, editingEvent.value);
      const index = events.value.findIndex((event) => event.id === editingEvent.value.id);
      events.value.splice(index, 1, response.data.data); // Update the event in the list
      editingEvent.value = null; // Close the edit modal
    } catch (error) {
      console.error('Error updating event:', error);
    } finally {
      loading.value = false;
    }
  };
  
  // Cancel editing
  const cancelEdit = () => {
    editingEvent.value = null; // Close the edit modal
  };
  
  // Fetch events when component is mounted
  onMounted(fetchEvents);
</script>
  
  <style scoped>
  .container {
    max-width: 800px;
    margin: 0 auto;
  }
  
  .card {
    margin-bottom: 20px;
  }
  
  .spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.25em;
  }
  
  .modal.fade.show {
    display: block;
  }
  
  button {
    margin-top: 10px;
  }
  </style>
  