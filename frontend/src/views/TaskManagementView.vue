<template>
    <div class="container mt-5">
      <h2 class="text-center mb-4">Task Management</h2>
      
      <!-- Add Task Form -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">Add New Task</h5>
          <form @submit.prevent="addTask">
            <div class="mb-3">
              <input v-model="task.title" type="text" class="form-control" placeholder="Task Title" required />
            </div>
            <div class="mb-3">
              <textarea v-model="task.body" class="form-control" placeholder="Task Description" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100" :disabled="loading">
              {{ loading ? "Adding..." : "Add Task" }}
            </button>
          </form>
        </div>
      </div>
  
      <!-- Task List -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">{{ userName ? userName : 'Guest' }}'s Tasks</h5>
          <ul class="list-group">
            <li v-for="t in tasks" :key="t.id" class="list-group-item justify-content-between">
              <div class="row">
                <div class="text-start col-8">
                  <span :class="{ 'text-decoration-line-through': t.completed }">{{ t.title }}</span>
                  <p class="text-muted mb-0">{{ t.body }}</p>
                </div>

                <div class="col-4">
                  <button class="btn btn-success btn-sm me-2" @click="toggleComplete(t)">
                    {{ t.completed ? "Undo" : "Complete" }}
                  </button>
                  <button :disabled="t.completed" class="btn btn-warning btn-sm me-2" @click="editTask(t)">Edit</button>
                  <button class="btn btn-danger btn-sm" @click="deleteTask(t.id)">Delete</button>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
  
      <!-- Edit Task Modal -->
      <div v-if="editingTask" class="modal fade show d-block" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Task</h5>
              <button type="button" class="btn-close" @click="cancelEdit"></button>
            </div>
            <div class="modal-body">
              <input v-model="editingTask.title" type="text" class="form-control mb-2" required />
              <textarea v-model="editingTask.body" class="form-control mb-2" required></textarea>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="cancelEdit">Cancel</button>
              <button class="btn btn-primary" @click="updateTask">Update Task</button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="editingTask" class="modal-backdrop fade show"></div>

      <div class="m-2">
        <LogoutButton />
      </div>
  
    </div>
</template>
  
<script setup>
  import { ref, onMounted, computed } from "vue";
  import { useStore } from "vuex";
  import axios from '../services/axios';
  import LogoutButton from "@/components/LogoutButton.vue";

  const tasks = ref([]);
  const task = ref({ title: "", body: "" });
  const editingTask = ref(null);

  const loading = ref(false);
  const store = useStore();
  const userName = computed(() => {
      const user = store.getters.getUser;
      return user ? user.name : '';
  });

  const fetchTasks = async () => {
    try {
      const response = await axios.get("/tasks");
      tasks.value = response.data;
    } catch (error) {
      console.error("Error fetching tasks:", error);
    }
  };

  const addTask = async () => {
    if (!task.value.title || !task.value.body) return;
    loading.value = true;
    try {
      const response = await axios.post("/tasks", task.value);
      tasks.value.push(response.data);
      task.value = { title: "", body: "" };
    } catch (error) {
      console.error("Error adding task:", error);
    } finally {
      loading.value = false;
    }
  };

  const editTask = (t) => {
    editingTask.value = { ...t };
  };

  const updateTask = async () => {
    if (!editingTask.value) return;
    try {
      const response = await axios.put(`/tasks/${editingTask.value.id}`, editingTask.value);
      if(response) {
        fetchTasks();
      }
      editingTask.value = null;
    } catch (error) {
      console.error("Error updating task:", error);
    }
  };

  const deleteTask = async (id) => {
    try {
      const response = await axios.delete(`/tasks/${id}`);
      if(response) {
        fetchTasks();
      }
    } catch (error) {
      console.error("Error deleting task:", error);
    }
  };

  const toggleComplete = async (t) => {
    try {
      await axios.put(`/tasks/${t.id}`, { ...t, completed: !t.completed });
      t.completed = !t.completed;
    } catch (error) {
      console.error("Error toggling task:", error);
    }
  };

  const cancelEdit = () => {
    editingTask.value = null;
  };

  onMounted(fetchTasks);
</script>
  