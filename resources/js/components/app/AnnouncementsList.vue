<template>
  <div class="max-w-3xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Announcements</h2>

    <ul v-if="announcements.length" class="space-y-6 max-h-96 pr-2 scroll-container">
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
import announcements from "@/routes/announcements";
import axios from "axios";

export default {
  name: "AnnouncementsList",
  data() {
    return {
      announcements: [],
      loading: false,
      lastScrollTop: 0,
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
    }
  },
};
</script>
