<template>
  <div>
    <!-- <h2 class="text-3xl font-bold mb-6 text-gray-800">
      {{ isEditing ? 'Edit Announcement' : 'Add Announcement' }}
    </h2> -->

    <form @submit.prevent="submitForm" class="space-y-6 scroll-container">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
          Title
        </label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          placeholder="Announcement title"
          required
        />
      </div>

      <div>
        <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
          Body
        </label>
        <textarea
          id="body"
          v-model="form.body"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          placeholder="Announcement content"
          rows="5"
          required
        ></textarea>
      </div>

      <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
          Date
        </label>
        <input
          id="date"
          v-model="form.date"
          type="datetime-local"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          required
        />
      </div>

      <div>
        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
          Tags (comma-separated)
        </label>
        <input
          id="tags"
          v-model="form.tags"
          type="text"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          placeholder="tag1, tag2, tag3"
        />
      </div>

      <div class="flex gap-4">
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 disabled:bg-gray-400"
        >
          {{ loading ? 'Saving...' : isEditing ? 'Update' : 'Create' }}
        </button>
        <button
          type="button"
          @click="closeModal"
          class="px-6 py-2 bg-gray-300 text-gray-800 font-medium rounded-lg hover:bg-gray-400"
        >
          Cancel
        </button>
      </div>

      <div v-if="error" class="p-4 bg-red-100 text-red-700 rounded-lg">
        {{ error }}
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AnnouncementForm',
  props: {
    announcementId: {
      type: [String, Number],
      default: null,
    },
    announcement: {
      type: Object,
      default: null,
    },
  },
  emits: ['submitted'],
  data() {
    return {
      form: {
        title: '',
        body: '',
        date: '',
        tags: '',
      },
      loading: false,
      error: null,
      showModal: false,
    };
  },
  computed: {
    isEditing() {
      return !!this.announcementId;
    },
  },
  mounted() {
    if (this.isEditing && this.announcement) {
      this.form = {
        title: this.announcement.title,
        body: this.announcement.body,
        date: new Date(this.announcement.date).toISOString().slice(0,16),
        tags: this.announcement.tags.join(', '),
      };
    }
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.error = null;

      try {
        const url = this.isEditing
          ? `/api/announcements/${this.announcementId}`
          : '/api/announcements';

        const method = this.isEditing ? 'patch' : 'post';

        await axios[method](url, this.form, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('access_token')}`,
                Accept: 'application/json',
            }
        });

        this.$emit('submitted');
      } catch (err) {
        this.error = err.response?.data?.message || 'An error occurred';
        console.error('Error submitting form:', err);
      } finally {
        this.loading = false;
      }
    },
    closeModal() {
        this.$emit('cancelled');
    },
    resetForm() {
      this.form = {
        title: '',
        body: '',
        date: '',
        tags: '',
      };
      this.error = null;
    },
  },
};
</script>

<style scoped>
.scroll-container {
 min-width: 32%;
 padding:4px;
}
</style>