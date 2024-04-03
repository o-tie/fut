<template>
  <div class="make-squads-wrapper mt-5">
    <div class="header-buttons mb-2 d-flex">
      <div class="">
        <a href="/players" class="btn btn-primary">Назад</a>
      </div>
      <div class="d-flex justify-content-around count-teams-wrapper">
        <label for="teamCount" class="mx-2">Кількість команд:</label>
        <select v-model="selectedTeamCount" @change="createSquads" class="form-select" id="teamCount">
          <option :value="0">Обери кількість</option>
          <option v-for="option in teamCountOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
        </select>
      </div>
      <div class="share-button-wrapper mx-5" v-if="Object.entries(squads).length > 0">
        <button class="btn btn-success" @click="saveSquadsAsJPG">Поділитися складами</button>
      </div>
    </div>
    <div class="squads-wrapper mb-3" ref="squadsShareElement">
      <div v-for="teamIndex in selectedTeamCount" :key="teamIndex" class="squad-wrapper my-3 border">
        <div class="squad-header d-flex mx-3 py-2">
          <h6 class="align-self-center mb-0">Команда {{ teamIndex }}</h6>
          <div class="squad-header-panel d-flex">
            <select :key="teamIndex" v-model="selectedPlayers[teamIndex]" class="form-select border-0"
                    :disabled="!selectedTeamCount || isFullTeam(teamIndex) ? 'disabled' : null">
              <option :value="null" disabled>Оберіть гравця</option>
              <option v-for="player in players"
                      :key="player.id"
                      :value="player">{{ player.name }}</option>
            </select>
            <button class="btn btn-primary" @click="addPlayer(teamIndex)"
                    :disabled="!selectedPlayers[teamIndex] || isFullTeam(teamIndex) ? 'disabled' : null">
            <i class="fa fa-plus"></i>
            </button>
          </div>
        </div>
        <div class="squad-body">
          <div class="squad-table-wrapper" v-if="squads[teamIndex]">
            <DataTable
                :columns="columnsSquad"
                :data="squads[teamIndex]"
                :options="tableOptionsSquad"
                class="table border-top border-left table-bordered mb-0"
                width="100%"
                ref="table"
                :key="Date.now()"
            >
              <thead>
              <tr>
                <th class="w-60">Імʼя</th>
                <th class="w-10 text-center">Рейтинг</th>
                <th class="w-10 text-center" v-for="(value, stat) in players[0].overallStats">{{ stat }}</th>
              </tr>
              </thead>
              <tfoot v-if="squads[teamIndex] && squads[teamIndex].length > 1">
              <tr>
                <th class="w-60 text-primary">Тотал</th>
                <th class="w-10 text-center text-primary">{{ squads[teamIndex].total.sumOverall }} - {{ squads[teamIndex].total.avgOverall }}</th>
                <th class="w-10 text-center text-primary" v-for="value in squads[teamIndex].total.avgOverallStats">{{ value }}</th>
              </tr>
              </tfoot>
            </DataTable>
          </div>
        </div>
      </div>
    </div>
    <div class="make-squads-table-wrapper" v-if="tableData">
      <DataTable
          :columns="columnsPlayers"
          :data="tableData"
          :options="tableOptionsPlayers"
          class="table border-top border-left table-bordered my-3"
          width="100%"
          ref="table"
      >
        <thead>
        <tr>
          <th class="w-60">Імʼя</th>
          <th class="w-10 text-center">Рейтинг</th>
          <th class="w-10 text-center" v-for="(value, stat) in tableData[0].overallStats">{{ stat }}</th>
        </tr>
        </thead>
      </DataTable>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import DataTable from "datatables.net-vue3";
import html2canvas from "html2canvas";

export default {
  name: 'MakeSquads',
  components: {DataTable},
  props: {
    isMobile: {type: Boolean, required: true},
  },
  data() {
    return {
      selectedPlayers: {},
      squads: {},
      selectedTeamCount: null,
      teamCountOptions: [{value:2, label: 'Дві'}, {value: 3, label: 'Три'}, {value: 4, label: 'Чотири'}],
      players: undefined,
      tableData: null,
      tableOptionsPlayers : {
        responsive: true,
        select: true,
        searching: false,
        lengthChange: false,
        paging: false,
        info: false,
      },
      tableOptionsSquad : {
        responsive: true,
        select: true,
        searching: false,
        lengthChange: false,
        paging: false,
        info: false,
      },
      filters: {},
      columnsSquad: [
        {
          data: null, class: 'w-40 squad-column-name', render: (data) => {
            return `<i class="fa fa-minus mx-2 cursor-pointer text-danger drop-player-button" data-id="${data.id}"></i><span>${data.name}</span>`
          }
        },
        {data: 'overall' , class:'w-10 text-center'},
        {data: 'pac' , class:'text-center'},
        {data: 'dri' , class:'text-center'},
        {data: 'sho' , class:'text-center'},
        {data: 'pas' , class:'text-center'},
        {data: 'vis' , class:'text-center'},
        {data: 'def' , class:'text-center'},
        {data: 'pos' , class:'text-center'},
        {data: 'phy' , class:'text-center'},
      ],
      columnsPlayers: [
        {data: 'name' , class:'w-40'},
        {data: 'overall' , class:'w-10 text-center'},
        {data: 'pac' , class:'text-center'},
        {data: 'dri' , class:'text-center'},
        {data: 'sho' , class:'text-center'},
        {data: 'pas' , class:'text-center'},
        {data: 'vis' , class:'text-center'},
        {data: 'def' , class:'text-center'},
        {data: 'pos' , class:'text-center'},
        {data: 'phy' , class:'text-center'},
      ],
    };
  },
  methods: {
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
              this.sortPlayersDesc();
            }
          })
          .catch(error => {
            console.log(error);
          });
    },
    createSquads() {
      this.squads = {};
      this.selectedPlayers = {}; // new object for store selected players
      for (let i = 1; i <= this.selectedTeamCount; i++) {
        this.squads[i] = [];
        this.squads[i].total = {};
        this.selectedPlayers[i] = null; // started player value for each team
      }
    },
    addPlayer(teamIndex) {
      const selectedPlayer = this.selectedPlayers[teamIndex];
      if (selectedPlayer) {
        // Remove selected player from players array
        const playerIndex = this.players.findIndex(player => player.id === selectedPlayer.id);
        if (playerIndex !== -1) {
          this.players.splice(playerIndex, 1);
        }
        // Add player to the team
        this.squads[teamIndex].push(selectedPlayer);
        // Update average rate in squads
        this.squads[teamIndex].total = this.calculateTeamTotal(this.squads[teamIndex]);
      }
      this.selectedPlayers[teamIndex] = null; // reset value for selected player
    },
    dropPlayer(e) {
      const playerId = parseInt(e.target.dataset.id); // convert to number format
      const teamIndex = Object.keys(this.squads).find(index => {
        return this.squads[index].some(player => player.id === playerId);
      });

      if (teamIndex) {
        const playerIndex = this.squads[teamIndex].findIndex(player => player.id === playerId);
        if (playerIndex !== -1) {
          const droppedPlayer = this.squads[teamIndex].splice(playerIndex, 1)[0];
          this.squads[teamIndex].total = this.calculateTeamTotal(this.squads[teamIndex]);
          this.players.push(droppedPlayer);
        }
      }
      this.sortPlayersDesc();
    },
    isFullTeam(teamIndex) {
      return (this.squads[teamIndex] && this.squads[teamIndex].length === 5);
    },
    sortPlayersDesc() {
      this.players.sort((a, b) => b.overall - a.overall);
    },
    calculateTeamTotal(squad) {
      const total = {};
      const numPlayers = squad.length;

      if (numPlayers === 0) {
        return { sumOverall: 0, avgOverall: 0, avgOverallStats: {} };
      }
      // Calc sum and avg overall
      total.sumOverall = squad.reduce((sum, player) => sum + player.overall, 0);
      total.avgOverall = Math.round(total.sumOverall / numPlayers);

      // init overall stats
      const sumOverallStats = Object.fromEntries(
          Object.keys(squad[0].overallStats).map(stat => [stat, 0])
      );

      // get sum by each stat
      squad.forEach(player => {
        Object.keys(player.overallStats).forEach(stat => {
          sumOverallStats[stat] += player.overallStats[stat];
        });
      });

      // calc avg by each stat
      total.avgOverallStats = Object.fromEntries(
          Object.entries(sumOverallStats).map(([stat, sum]) => [stat, Math.round(sum / numPlayers)])
      );

      return total;
    },
    saveSquadsAsJPG() {
      const element = this.$refs.squadsShareElement;

      html2canvas(element).then((canvas) => {
        let imgData = canvas.toDataURL('image/jpeg');
        let link = document.createElement("a");
        link.href = imgData;
        link.download = 'squads.jpg';
        link.click();
      });
    },
  },
  mounted() {
    document.body.addEventListener('click', (e) => {
      if (e.target.classList.contains('drop-player-button')) {
        this.dropPlayer(e);
      }
    });
  },
  beforeMount() {
    this.index();
  }
};
</script>

<style scoped>

</style>

<style scoped>

</style>