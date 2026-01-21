<template>
  <div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-3xl font-bold text-gray-800">Announcements</h2>
      <div class="flex items-center gap-1">
        <button
          @click="toggleFilterInput"
          class="p-1.5 text-gray-600 hover:text-blue-500 hover:bg-blue-50 rounded-md transition-colors"
          title="Filter">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
        </button>
        <transition name="slide-fade">
          <input
            v-if="showFilterInput"
            v-model="filterText"
            @input="onFilterChange"
            type="text"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors"
            ref="filterInput"
          />
        </transition>
        <button
          @click="showModal = true"
          class="p-1.5 text-gray-600 hover:text-blue-500 hover:bg-blue-50 rounded-md transition-colors"
          title="Add Announcement">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </button>
      </div>
    </div>

    <transition name="modal">
      <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="modal-container rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col">
          <!-- Modal Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-white flex-shrink-0">
            <h2 class="text-2xl font-bold text-gray-900">
              {{ editingId ? 'Edit Announcement' : 'Create New Announcement' }}
            </h2>
            <button @click="closeFormModal" class="text-gray-400 hover:text-gray-600 text-3xl font-light transition-colors">
              ×
            </button>
          </div>

          <!-- Modal Body with Form - Scrollable -->
          <div class="overflow-y-auto flex-1 bg-gray-100">
            <div class="p-6">
              <announcement-form 
                :announcementId="editingId"
                :announcement="editingAnnouncement"
                @submitted="onAnnouncementSubmitted"
                @cancelled="closeFormModal">
              </announcement-form>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <ul v-if="announcements.length" class="space-y-6 max-h-96 pr-4 scroll-container overflow-y-auto">
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
        
        <!-- Tags and Action Buttons on same line -->
        <div class="flex flex-wrap gap-2 items-center justify-between">
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tag in announcement.tags"
              :key="tag"
              class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full"
            >
              {{ tag }}
            </span>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex gap-1">
            <button
              @click="editAnnouncement(announcement)"
              class="p-1.5 text-gray-600 hover:text-blue-500 hover:bg-blue-50 rounded transition-colors"
              title="Edit"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <button
              @click="deleteAnnouncement(announcement.id)"
              class="p-1.5 text-gray-600 hover:text-red-500 hover:bg-red-50 rounded transition-colors"
              title="Delete"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </li>
    </ul>

    <div v-else-if="loading" class="scroll-container space-y-6 max-h-96 overflow-y-auto pr-2">
      <div v-for="i in 5" :key="i" class="bg-white shadow-lg rounded-xl p-5 animate-pulse">
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
      allAnnouncements: [],
      announcements: [],
      loading: false,
      lastScrollTop: 0,
      showModal: false,
      editingId: null,
      editingAnnouncement: null,
      filterText: '',
      showFilterInput: false,
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
          this.allAnnouncements = this.announcements = response.data
            .map(r => ({ ...r, tags: r.tags?.split(', ') || [] }))
            .sort((a, b) => new Date(b.date) - new Date(a.date));
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
    editAnnouncement(announcement) {
      this.editingId = announcement.id;
      this.editingAnnouncement = announcement;
      this.showModal = true;
    },
    deleteAnnouncement(id) {
      if (confirm('Are you sure you want to delete this announcement?')) {
        axios
          .delete(`/api/announcements/${id}`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('access_token')}`,
              Accept: 'application/json',
            },
          })
          .then(() => {
            this.fetchAnnouncements();
          })
          .catch(error => {
            console.error("Error deleting announcement:", error);
          });
      }
    },
    onAnnouncementSubmitted() {
      this.closeFormModal();
      this.fetchAnnouncements();
    },
    closeFormModal() {
      this.showModal = false;
      this.editingId = null;
      this.editingAnnouncement = null;
    },
    onFilterChange(e) {
      var value = e.target.value;
      this.announcements = this.allAnnouncements.filter(announcement =>
        announcement.title.toLowerCase().includes(value.toLowerCase()) ||
        announcement.body.toLowerCase().includes(value.toLowerCase()) ||
        (announcement.tags && announcement.tags.some(tag => tag.toLowerCase().includes(value.toLowerCase())))
      );
    },
    toggleFilterInput() {
      this.showFilterInput = !this.showFilterInput;
      if (this.showFilterInput) {
        this.$nextTick(() => {
          this.$refs.filterInput?.focus();
        });
      } else {
        this.filterText = '';
        this.onFilterChange({ target: { value: '' } });
      }
    },
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

.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease;
}

.slide-fade-enter-from {
  transform: translateX(-10px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateX(-10px);
  opacity: 0;
}

.modal-container {
  height: 75vh;
}
</style>