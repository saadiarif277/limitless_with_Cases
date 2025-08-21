/**
 * Register globally available components into this file.
 */

import { defineAsyncComponent } from "vue";
import { Head as InertiaHead } from "@inertiajs/vue3";
const AppLogo = defineAsyncComponent(() => import("./components/application/app-logo.vue"));
const AppModelTable = defineAsyncComponent(() => import("./components/application/app-model-table.vue"));
const Avatar = defineAsyncComponent(() => import("./components/avatar.vue"));
const Button = defineAsyncComponent(() => import("./components/button.vue"));
const ButtonWidget = defineAsyncComponent(() => import("./components/button-widget.vue"));
const Card = defineAsyncComponent(() => import("./components/card.vue"));
const ChartBar = defineAsyncComponent(() => import("./components/charts/chart-bar.vue"));
const ChartDoughnut = defineAsyncComponent(() => import("./components/charts/chart-doughnut.vue"));
const ContentBody = defineAsyncComponent(() => import("./components/content-body.vue"));
const ContentFoot = defineAsyncComponent(() => import("./components/content-foot.vue"));
const ContentHead = defineAsyncComponent(() => import("./components/content-head.vue"));
const FormCheckbox = defineAsyncComponent(() => import("./components/form/form-checkbox.vue"));
const FormDatePicker = defineAsyncComponent(() => import("./components/form/form-date-picker.vue"));
const FormFile = defineAsyncComponent(() => import("./components/form/form-file.vue"));
const FormError = defineAsyncComponent(() => import("./components/form/form-error.vue"));
const FormGroup = defineAsyncComponent(() => import("./components/form/form-group.vue"));
const FormInput = defineAsyncComponent(() => import("./components/form/form-input.vue"));
const FormLabel = defineAsyncComponent(() => import("./components/form/form-label.vue"));
const FormSelect = defineAsyncComponent(() => import("./components/form/form-select.vue"));
const FormTextarea = defineAsyncComponent(() => import("./components/form/form-textarea.vue"));
const Form = defineAsyncComponent(() => import("./components/form/form.vue"));
const Heading = defineAsyncComponent(() => import("./components/heading.vue"));
const HorizontalMenuItem = defineAsyncComponent(() => import("./components/horizontal-menu-item.vue"));
const HorizontalMenu = defineAsyncComponent(() => import("./components/horizontal-menu.vue"));
const Link = defineAsyncComponent(() => import("./components/link.vue"));
const Paragraph = defineAsyncComponent(() => import("./components/paragraph.vue"));
const SectionGroup = defineAsyncComponent(() => import("./components/section-group.vue"));
const SectionHeading = defineAsyncComponent(() => import("./components/section-heading.vue"));
const Section = defineAsyncComponent(() => import("./components/section.vue"));
const TableBody = defineAsyncComponent(() => import("./components/table-body.vue"));
const TableData = defineAsyncComponent(() => import("./components/table-data.vue"));
const TableFoot = defineAsyncComponent(() => import("./components/table-foot.vue"));
const TableHead = defineAsyncComponent(() => import("./components/table-head.vue"));
const TableHeader = defineAsyncComponent(() => import("./components/table-header.vue"));
const TableRow = defineAsyncComponent(() => import("./components/table-row.vue"));
const Table = defineAsyncComponent(() => import("./components/table.vue"));
const Tabs = defineAsyncComponent(() => import("./components/tabs.vue"));
const VerticalMenuItem = defineAsyncComponent(() => import("./components/vertical-menu-item.vue"));
const VerticalMenu = defineAsyncComponent(() => import("./components/vertical-menu.vue"));

const components = {
    "v-avatar": Avatar,
    "v-inertia-head": InertiaHead,
    "v-app-logo": AppLogo,
    "v-app-model-table": AppModelTable,
    "v-button": Button,
    "v-button-widget": ButtonWidget,
    "v-card": Card,
    "v-chart-bar": ChartBar,
    "v-chart-doughnut": ChartDoughnut,
    "v-content-body": ContentBody,
    "v-content-foot": ContentFoot,
    "v-content-head": ContentHead,
    "v-form-checkbox": FormCheckbox,
    "v-form-date-picker": FormDatePicker,
    "v-form-file": FormFile,
    "v-form-error": FormError,
    "v-form-group": FormGroup,
    "v-form-input": FormInput,
    "v-form-label": FormLabel,
    "v-form-select": FormSelect,
    "v-form-textarea": FormTextarea,
    "v-form": Form,
    "v-heading": Heading,
    "v-horizontal-menu-item": HorizontalMenuItem,
    "v-horizontal-menu": HorizontalMenu,
    "v-link": Link,
    "v-paragraph": Paragraph,
    "v-section-group": SectionGroup,
    "v-section-heading": SectionHeading,
    "v-section": Section,
    "v-table-body": TableBody,
    "v-table-foot": TableFoot,
    "v-table-data": TableData,
    "v-table-head": TableHead,
    "v-table-header": TableHeader,
    "v-table-row": TableRow,
    "v-table": Table,
    "v-tabs": Tabs,
    "v-vertical-menu-item": VerticalMenuItem,
    "v-vertical-menu": VerticalMenu,
};

const registercomponents = (app = null) => {
    Object.keys(components).forEach((key) => {
        app.component(key, components[key]);
    });
};

export default registercomponents;