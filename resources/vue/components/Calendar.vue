<template>
  <div class="">
    <iframe src="https://calendar.google.com/calendar/embed?src=44da8c46d97749d651c66c8bbbc82d86a72b3280bf8bcb6a8569445e96ac00cb%40group.calendar.google.com&ctz=Europe%2FKiev"
            style="border: 0"
            :width="bodyWidth"
            height="750" frameborder="0" scrolling="no">
    </iframe>
  </div>
</template>

<script>
import CommonMixin from "../../js/mixins/common-mixin";
export default {
  name: "Calendar",
  props: [],
  data () {
    return {
      deviceWidth: null,
      bodyWidth: null,
    }
  },
  created() {
    this.deviceWidth = CommonMixin.methods.getDeviceWidth();
    this.bodyWidth = CommonMixin.methods.getContentWidth();
  }
}

</script>

<style scoped>

</style>