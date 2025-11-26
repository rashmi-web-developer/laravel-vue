<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="text-h6">Loans</span>
      <v-btn size="small" variant="tonal" :loading="loading" @click="loadLoans">
        Refresh
      </v-btn>
    </v-card-title>

    <v-data-table
      class="elevation-1"
      :headers="headers"
      :items-per-page-options="[25, 50, 100]"
      :items-per-page="25"
      :items="loans"
      :loading="loading"
    >
      <template #item.loaned_at="{item}">
        {{ moment(item.loaned_at).format('MMM Do YYYY \\a\\t h:mm A') }}
      </template>

      <template #item.returned_at="{item}">
        {{ item.returned_at ? moment(item.returned_at).format('MMM Do YYYY \\a\\t h:mm A') : '-' }}
      </template>

      <template #item.due_at="{item}">
        {{ item.due_at ? moment(item.due_at).format('MMM Do YYYY \\a\\t h:mm A') : '-' }}
      </template>

      <template #item.actions="{item}">
        <v-btn size="small" variant="text" icon="mdi-pencil" @click="extendLoan(item.id)">
          <v-icon icon="mdi-pencil" />
        </v-btn>
      </template>
      <template #loading>
        <v-sheet class="pa-4 text-center">Loading loans...</v-sheet>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import { toast } from 'vue3-toastify';
import axios from 'axios';
import moment from 'moment';

export default {
  name: 'LoansTab',

  data () {
    return {
      moment,

      loading: false,
      loans: [],
      headers: [
        { title: 'ID', key: 'id' },
        { title: 'User', key: 'user.name' },
        { title: 'Book', key: 'book.title' },
        { title: 'Loan Date', key: 'loaned_at' },
        { title: 'Return Date', key: 'returned_at' },
        { title: 'Due Date', key: 'due_at' },
        { title: 'Actions', key: 'actions', sortable: false },
      ],
    };
  },

  methods: {
    loadLoans () {
      this.loading = true;

      return axios.get('/api/v1/loans')
        .then(r => this.loans = r.data)
        .catch(e => {
          toast(e.response?.data?.message || e.response?.statusText || 'Error', {type: 'error'});
          console.error(e);
        })
        .finally(() => this.loading = false);
    },
    formateDueDate(dueAt) 
    {
      if (!dueAt) return '-';
      const now = new Date();
      const dueDate = new Date(dueAt);
      if (dueDate < now) return 'Overdue';
      const diffm = due - now;
      const diffDays = Math.ceil(diffm / (1000 * 60 * 60 * 24));
      return `+${diffDays} days`;
    },
    extendLoan(id) {
      axios.put(`/api/v1/loans/extend/${id}`, {
        additional_days: 14
      })
      .then(r => {
        toast(r.data.message, {type: 'success'});
        this.loadLoans();
      })
      .catch(e => {
        toast(e.response?.data?.message || e.response?.statusText || 'Error', {type: 'error'});
        console.error(e);
      });
    }
  },

  mounted () {
    this.loadLoans();
  },
};
</script>
