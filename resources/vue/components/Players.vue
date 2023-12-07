<template>
<div class="players-wrapper">
  <div class="" v-if="tableData && !showForm">
    <DataTable
        :columns="columns"
        :data="tableData"
        :options="tableOptions"
        class="table border-top border-left table-bordered my-3"
        width="100%"
        ref="table"
    >
      <thead>
      <tr>
        <th class="70">Імʼя</th>
        <th class="w-10 text-center">Рейтинг (загальний)</th>
        <th class="w-10 text-center">Рейтинг (оцінка)</th>
        <th class="w-10 text-center"></th>
      </tr>
      </thead>
    </DataTable>
  </div>
  <div class="" v-if="showForm">
    <PlayerForm :player="player" :is-mobile="isMobile" @form-close="closePlayerForm" @player-updated="index"></PlayerForm>
  </div>
</div>
</template>

<script>
import axios from "axios";
import DataTable from 'datatables.net-vue3'
import DataTablesLib from 'datatables.net-bs5';
import PlayerForm from "./PlayerForm.vue";

DataTable.use(DataTablesLib);

export default {
  name: "Players",
  components: {PlayerForm, DataTable},
  data () {
    return {
      isMobile: false,
      showForm: false,
      players: null,
      player: null,
      table: null,
      tableOptions : {
        responsive: true,
        select: true,
        searching: true,
        lengthChange: false,
        pageLength: 15,
      },
      tableData : null,
      filters: {},
      columns: [
        {data: 'name' , class:'w-70'},
        {data: 'overall' , class:'w-10 text-center'},
        {data: 'overallUser' , class:'w-10 text-center'},
        {data: null, orderable: false, class:'w-10 text-center',
          render: (data) => {
            const iconClass = data.stats !== null ? 'fas fa-user-check stats-check-icon' : 'fas fa-user-edit stats-edit-icon';
            return `<div class="d-flex justify-content-center align-items-center"><i class="${iconClass} edit-player-button cursor-pointer" data-id='${data.id}'></i></div>`;
          }
        },
      ],
      confirmMessage: "Дійсно хочеш змінити стати?"
    }
  },
  methods: {
    editPlayerStats(e) {
      let id = e.target.dataset.id;
      let player = this.players.find(obj => obj.id === Number(id));
      if (player.stats !== null) {
        if (confirm(this.confirmMessage)) {
          this.showPlayerForm(player);
        }
      } else {
        this.showPlayerForm(player);
      }
    },
    index() {
      axios.get('/api/players', {params: {}},
          {
            headers: {
              'Content-Type': 'application/json',
            }
          })
          .then(response => {
            if (response.data.success) {
              this.tableData = response.data.records;
              this.players = response.data.records;
            }
          })
          .catch(error => {
            console.log(error);
          });
    },
    showPlayerForm(player) {
      this.player = player;
      this.showForm = true;
    },
    closePlayerForm() {
      this.showForm = false;
    },
    detectMobile() {
      return window.innerWidth <= 768;
    },
    handleResize() {
      this.isMobile = this.detectMobile();
    }
  },
  mounted() {
    document.body.addEventListener('click', (e) => {
      if (e.target.classList.contains('edit-player-button')) {
        this.editPlayerStats(e);
      }
    });

    window.addEventListener('resize', this.handleResize);
  },
  beforeMount() {
    this.handleResize();
    this.index();
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.handleResize);
  },
}
</script>

<style scoped lang="scss">
  table {
    tr {
      vertical-align: middle;
      td {
        vertical-align: middle;
      }
    }
  }
</style>