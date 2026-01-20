<template>
  <div class="max-w-3xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Announcements 
        <button
            @click="showModal = true"
            class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600">
            Add Announcement
        </button></h2>

    <transition name="modal">
      <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="modal-container bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col">
          <!-- Modal Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-white flex-shrink-0">
            <h2 class="text-2xl font-bold text-gray-900">
              Create New Announcement
            </h2>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-3xl font-light transition-colors">
              ×
            </button>
          </div>

          <!-- Modal Body with Form - Scrollable -->
          <div class="overflow-y-auto flex-1">
            <div class="p-6">
              <announcement-form @submitted="onAnnouncementSubmitted"></announcement-form>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <ul v-if="announcements.length" class="space-y-6 max-h-96 pr-2 scroll-container overflow-y-auto">
      <li
        v-for="announcement in announcements"
        :key="announcement.id"
        class="bg-white shadow-lg rounded-xl p-5"
      >
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01M12 4h.01M4 12h.01M20 12h.01M16 12h.01M8 12h.01" />
            </svg>
            {{ announcement.title }}
          </h3>
          <span class="text-sm text-gray-500">{{ formatDate(announcement.date) }}</span>
        </div>
        <p class="text-gray-700 mb-3">{{ announcement.body }}</p>
        <div class="flex flex-wrap gap-2">
          <span
            v-for="tag in announcement.tags"
            :key="tag"
            class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full"
          >
            {{ tag }}
          </span>
        </div>
      </li>
    </ul>

    <div v-else-if="loading" class="scroll-container space-y-6 max-h-96 overflow-y-auto pr-2">
      <div v-for="i in 3" :key="i" class="bg-white shadow-lg rounded-xl p-5 animate-pulse">
        <div class="flex items-center justify-between mb-2">
          <div class="h-6 bg-gray-300 rounded w-48"></div>
          <div class="h-4 bg-gray-300 rounded w-24"></div>
        </div>
        <div class="h-4 bg-gray-300 rounded w-full mb-3"></div>
        <div class="h-4 bg-gray-300 rounded w-5/6 mb-4"></div>
        <div class="flex flex-wrap gap-2">
          <div class="h-6 bg-gray-300 rounded-full w-16"></div>
          <div class="h-6 bg-gray-300 rounded-full w-16"></div>
        </div>
      </div>
    </div>

    <p v-else class="text-gray-500">No announcements found.</p>
  </div>
</template>

<script>
import announcements, { show } from "@/routes/announcements";
import AnnouncementForm from './AnnouncementForm.vue';
import axios from "axios";

export default {
  name: "AnnouncementsList",
  components: {
    AnnouncementForm,
  },
  data() {
    return {
      announcements: [],
      loading: false,
      lastScrollTop: 0,
      showModal: false,
    };
  },

  mounted() {
    this.fetchAnnouncements();
  },

  methods: {
    fetchAnnouncements() {
      this.loading = true;

      axios
        .get("/api/announcements", {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                Accept: 'application/json',
            },
        })
        .then(response => {
          this.announcements = response.data.map(r => ({ ...r, tags: r.tags.split(', ') || [] }));
        })
        .catch(error => {
          console.error("Error fetching announcements:", error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    formatDate(date) {
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(date).toLocaleDateString(undefined, options);
    },
    onAnnouncementSubmitted() {
      this.showModal = false;
      this.fetchAnnouncements();
    }
  },
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.modal-container {
  height: 75vh;
}
</style>