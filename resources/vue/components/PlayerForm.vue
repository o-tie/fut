<template>
  <div class="player-form-wrapper mt-5">
    <div class="player-form-inner m-auto" :class="!isMobile ? 'max-width-400' : ''">
      <div class="header-wrapper d-flex justify-content-between align-items-center">
        <div class="player-name-wrapper">
          <h3>{{ player.name }}</h3>
        </div>
        <div class="info-icon" @click="openDescriptionModal"><i class="fa fa-info-circle mx-3"></i></div>
      </div>
      <form @submit.prevent="submitForm">
        <div class="mb-3">
          <label for="pac" class="form-label">Швидкість</label>
          <input type="number" :min="min" :max="max" :placeholder="inputStatPlaceholder" class="form-control" id="pac" v-model="formData.pac" required />
        </div>

        <div class="mb-3">
          <label for="dri" class="form-label">Дриблінг</label>
          <input type="number" :min="min" :max="max" :placeholder="inputStatPlaceholder" class="form-control" id="dri" v-model="formData.dri" required />
        </div>

        <div class="mb-3">
          <label for="sho" class="form-label">Удари</label>
          <input type="number" :min="min" :max="max" :placeholder="inputStatPlaceholder" class="form-control" id="sho" v-model="formData.sho" required />
        </div>

        <div class="mb-3">
          <label for="pas" class="form-label">Паси</label>
          <input type="number" :min="min" :max="max" :placeholder="inputStatPlaceholder" class="form-control" id="pas" v-model="formData.pas" required />
        </div>

        <div class="mb-3">
          <label for="vis" class="form-label">Бачення поля</label>
          <input type="number" :min="min" :max="max" :placeholder="inputStatPlaceholder" class="form-control" id="vis" v-model="formData.vis" required />
        </div>

        <div class="mb-3">
          <label for="def" class="form-label">Захист</label>
          <input type="number" :min="min" :max="max" :placeholder="inputStatPlaceholder" class="form-control" id="def" v-model="formData.def" required />
        </div>

        <div class="mb-3">
          <label for="pos" class="form-label">Позиційна гра</label>
          <input type="number" :min="min" :max="max" :placeholder="inputStatPlaceholder" class="form-control" id="pos" v-model="formData.pos" required />
        </div>

        <div class="mb-3">
          <label for="phy" class="form-label">Витривалість</label>
          <input type="number" :min="min" :max="max" :placeholder="inputStatPlaceholder" class="form-control" id="phy" v-model="formData.phy" required />
        </div>

        <div class="d-flex justify-content-end">
            <button @click="$emit('form-close')" type="button" class="btn btn-secondary mx-3">Назад</button>
            <button type="submit" class="btn btn-primary">Зберегти</button>
        </div>
      </form>
    </div>

    <div class="modal fade" id="description-modal" ref="descriptionModal">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content h-100">
          <div class="modal-header">
            <h6 class="modal-title">Опис</h6>
            <button aria-label="Close"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    type="button">
            </button>
          </div>
          <div class="modal-body h-100">
            <div class="container">
              <ul>
                <li><strong>Швидкість:</strong> Здатність швидко рухатися по майданчику, яка включає в себе швидкість бігу та реакції на зміни напрямку.</li>
                <li><strong>Дриблінг:</strong> Майстерність у керуванні м'ячем під час його переміщення по полю, зокрема, вміння обходити супротивників.</li>
                <li><strong>Удари:</strong> Здатність точно та сильно ударяти по м'ячу.</li>
                <li><strong>Передачі:</strong> Майстерність передачі м'яча партнерам, використовуючи різні техніки передач, такі як короткі та довгі передачі.</li>
                <li><strong>Бачення поля:</strong> Здатність читати гру, аналізувати ситуацію на полі та приймати вірні рішення щодо стратегії та тактики гри.</li>
                <li><strong>Захист:</strong> Навички відстоювання позиції, блокування ударів супротивників та взаємодія з іншими гравцями для захисту власних воріт.</li>
                <li><strong>Позиційна гра:</strong> Здатність знаходитися у вірному місці на полі для ефективної гри в атаці або обороні, враховуючи тактичні аспекти гри.</li>
                <li><strong>Витривалість:</strong> Здатність тривалий час підтримувати високий темп гри без втрати якості гри, включаючи фізичну та психологічну витривалість.</li>
              </ul>
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

export default {
  name: 'PlayerForm',
  props: {
    player: {type: Object, required: true},
    isMobile: {type: Boolean, required: true},
  },
  data() {
    return {
      descriptionModal: '',
      inputStatPlaceholder: 'Від 40 до 99',
      min: 40,
      max: 99,
      formData: {
        pac: null,
        dri: null,
        sho: null,
        pas: null,
        vis: null,
        def: null,
        pos: null,
        phy: null,
      },
    };
  },
  methods: {
    submitForm() {
      // Отправка данных через axios на /stats
      axios.post("/api/players", {player: this.player, stats: this.formData},
          {
            headers: {
              'Content-Type': 'application/json',
            }
          })
          .then(response => {
            if (response.data.success) {
              this.$emit('form-close');
              this.$emit('player-updated');
            } else {
              alert("Щось пішло не так...");
            }
          })
          .catch(error => {
            console.error(error);
          });
    },
    fillFormData() {
      if (this.player.stats) {
        let stats = JSON.parse(this.player.stats);
        this.formData = { ...stats };
      }
    },
    openDescriptionModal() {
      const modalEl = this.$refs.descriptionModal;
      this.descriptionModal = new bootstrap.Modal(modalEl, { keyboard: false });
      this.descriptionModal.show();
    }
  },
  mounted() {
    this.fillFormData();
  }
};
</script>

<style scoped>

</style>