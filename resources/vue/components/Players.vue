<template>
<div class="players-wrapper">
  <div class="" v-if="tableData && playersPage">
    <div class="">
      <button class="btn btn-primary" @click="confirmCreateSquads">Створити склади</button>
    </div>
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
        <th class="w-60">Імʼя</th>
        <th class="w-10 text-center">Голосів</th>
        <th class="w-10 text-center">Рейтинг</th>
        <th class="w-10 text-center">Моя оцінка</th>
        <th class="w-10 text-center"><span class="info-icon" @click="showStatsDescription"><i class="fa fa-info-circle"></i></span></th>
      </tr>
      </thead>
    </DataTable>
  </div>
  <div class="" v-if="playerFormPage">
    <PlayerForm :player="player" :is-mobile="isMobile" @form-close="closePlayerForm" @player-updated="index"></PlayerForm>
  </div>

  <div class="modal fade" id="player-stats-modal" ref="playerStatsModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content h-100">
        <div class="modal-header">
          <h6 class="modal-title">Стати</h6>
          <button aria-label="Close"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  type="button">
          </button>
        </div>
        <div class="modal-body h-100">
          <div class="container">
            <p class="mb-0"><b>Кількість корегувань з останньої гри: </b><b class="text-primary">{{ playerCorrections }}</b></p>
            <p class="text-danger">*статистика корегувань в тестовому режимі</p>
            <table class="table table-bordered text-center">
              <thead class="table-light">
              <tr>
                <th class="align-middle" v-for="(value, stat) in playerStats" :key="stat">{{ stat }}</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td class="align-middle" v-for="(value, stat) in playerStats" :key="stat">{{ value }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal"
          >
            Закрити
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="stats-description-modal" ref="statsDescriptionModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content h-100">
        <div class="modal-header">
          <h6 class="modal-title">Опис статів</h6>
          <button aria-label="Close"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  type="button">
          </button>
        </div>
        <div class="modal-body h-100">
          <div class="container">
            <p><span><b>pac</b></span> - <span>Швидкість</span></p>
            <p><span><b>dri</b></span> - <span>Дриблінг</span></p>
            <p><span><b>sho</b></span> - <span>Удари</span></p>
            <p><span><b>pas</b></span> - <span>Паси</span></p>
            <p><span><b>vis</b></span> - <span>Бачення поля</span></p>
            <p><span><b>def</b></span> - <span>Захист</span></p>
            <p><span><b>pos</b></span> - <span>Позиційна гра</span></p>
            <p><span><b>phy</b></span> - <span>Витривалість</span></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal"
          >
            Закрити
          </button>
        </div>
      </div>
    </div>
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
      playersPage: true,
      playerFormPage: false,
      isMobile: false,
      players: null,
      player: null,
      playerStats: null,
      playerCorrections: null,
      table: null,
      playerStatsModal: '',
      statsDescriptionModal: '',
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
        {data: 'votes' , class:'w-10 text-center'},
        {data: 'overall' , class:'w-10 text-center'},
        {data: 'overallUser' , class:'w-10 text-center'},
        {data: null, orderable: false, class:'w-10 text-center',
          render: (data) => {
            const iconClass = data.stats !== null ? 'fas fa-user-check stats-check-icon' : 'fas fa-user-edit stats-edit-icon';
            return `<div class="d-flex justify-content-center align-items-center"><i class="fa-regular fa-eye player-stats-button cursor-pointer" data-id='${data.id}'></i><i class="${iconClass} edit-player-button cursor-pointer" data-id='${data.id}'></i></div>`;
          }
        },
      ],
      confirmMessage: "Дійсно хочеш змінити стати?"
    }
  },
  methods: {
    confirmCreateSquads() {
      if (this.isMobile) {
        const isConfirmed = window.confirm("Мобільної версії сторінки немає, точно хочеш перейти?");
        if (isConfirmed) {
          // Перейдите по ссылке /squads
          window.location.replace('/squads');
        }
      }
      window.location.replace('/squads');
    },
    editPlayerStats(e) {
      let id = e.target.dataset.id;
      let player = this.players.find(obj => obj.id === Number(id));
      if (player.stats !== null) {
        if (confirm(this.confirmMessage)) {
          this.showPlayerFormPage(player);
        }
      } else {
        this.showPlayerFormPage(player);
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
    unsetPages() {
      this.playersPage = false;
      this.playerFormPage = false;
    },
    showPlayerFormPage(player) {
      this.unsetPages();
      this.player = player;
      this.playerFormPage = true;
    },
    showPlayersPage() {
      this.unsetPages();
      this.playersPage = true;
    },
    closePlayerForm() {
      this.playerFormPage = false;
      this.playersPage = true;
    },
    detectMobile() {
      return window.innerWidth <= 768;
    },
    handleResize() {
      this.isMobile = this.detectMobile();
    },
    showPlayerStatsModal(e) {
      this.playerStats = null;
      this.playerCorrections = null;

      const id = e.target.dataset.id;
      this.preparePlayerStatsData(id);
      const modalEl = this.$refs.playerStatsModal;
      this.descriptionModal = new bootstrap.Modal(modalEl, { keyboard: false });
      this.descriptionModal.show();
    },
    preparePlayerStatsData(id) {
      const player = this.players.find(obj => obj.id === Number(id));
      if (player !== undefined) {
        this.playerStats = player.overallStats || {};
        this.playerCorrections = player.corrections || 0;
      }
    },
    showStatsDescription() {
      const modalEl = this.$refs.statsDescriptionModal;
      this.descriptionModal = new bootstrap.Modal(modalEl, { keyboard: false });
      this.descriptionModal.show();
    },
  },
  mounted() {
    document.body.addEventListener('click', (e) => {
      if (e.target.classList.contains('edit-player-button')) {
        this.editPlayerStats(e);
      }
    });
    document.body.addEventListener('click', (e) => {
      if (e.target.classList.contains('player-stats-button')) {
        this.showPlayerStatsModal(e);
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