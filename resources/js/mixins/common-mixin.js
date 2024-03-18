export default {
    name: "CommonMixin",
    data() {
        return {};
    },
    methods: {
        getDeviceWidth() {
            return screen.width;
        },
        getContentWidth() {
            return window.appContainerWidth - 20;
        }
    }
};