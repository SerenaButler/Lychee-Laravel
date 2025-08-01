<template>
	<Fieldset :legend="$t('settings.system.header')">
		<div class="flex flex-col gap-4 mb-8">
			<BoolField
				v-if="dark_mode_enabled !== undefined"
				:label="$t('settings.system.use_dark_mode')"
				:config="dark_mode_enabled"
				@filled="saveDarkMode"
			/>
			<SelectLang v-if="lang !== undefined" :label="$t('settings.system.language')" :config="lang" @filled="save" />
			<div class="flex flex-wrap justify-between">
				<label for="pp_dialog_nsfw_visible" class="text-muted-color-emphasis">{{ $t("settings.system.nsfw_album_visibility") }}</label>
				<ToggleSwitch id="pp_dialog_nsfw_visible" v-model="nsfwVisible" class="text-sm" @update:model-value="updateNSFW" />
				<p class="my-1.5 text-muted-color w-full" v-html="$t('settings.system.nsfw_album_explanation')"></p>
			</div>
			<BoolField
				v-if="cache_enabled !== undefined"
				:label="$t('settings.system.cache_enabled')"
				:config="cache_enabled"
				:details="$t('settings.system.cache_enabled_details')"
				@filled="save"
			/>
		</div>
	</Fieldset>
	<Fieldset v-if="!is_se_enabled && !is_se_info_hidden">
		<template #legend>
			<div class="font-bold" v-html="$t('settings.lychee_se.header')" />
		</template>
		<p class="mb-2" v-html="$t('settings.lychee_se.call4action')" />
		<div class="mb-8 flex items-start" v-if="!is_se_enabled">
			<div class="w-3/4">
				<FloatLabel variant="on">
					<InputText v-model="licenseKey" id="licenseKey" class="w-full" @update:model-value="licenseKeyIsInvValid = false" />
					<label for="licenseKey">{{ $t("dialogs.register.license_key") }}</label>
				</FloatLabel>
				<span class="inline-block mt-4 font-bold text-danger-600" v-if="licenseKey && licenseKeyIsInvValid">{{
					$t("dialogs.register.invalid_license")
				}}</span>
			</div>
			<Button
				class="w-1/4 border-none font-bold bg-primary-500/20 hover:bg-primary-500 hover:text-surface-0"
				v-if="!is_se_enabled"
				@click="register"
				severity="contrast"
				:disabled="!isValidRegistrationForm"
				>{{ $t("dialogs.register.register") }}
			</Button>
		</div>
		<p class="flex flex-wrap justify-between my-6">
			<label for="enable_se_preview">{{ $t("settings.lychee_se.preview") }}</label>
			<ToggleSwitch id="enable_se_preview" v-model="enable_se_preview" class="text-sm" @update:model-value="savePreview" />
		</p>
		<p class="flex flex-wrap justify-between">
			<label for="disable_se_call_for_actions">{{ $t("settings.lychee_se.hide_call4action") }}</label>
			<ToggleSwitch id="disable_se_call_for_actions" v-model="disable_se_call_for_actions" class="text-sm" @update:model-value="saveHideC4A" />
			<span class="mt-1 w-full text-muted-color"
				><i class="pi pi-exclamation-triangle text-orange-500 ltr:mr-2 rtl:ml-2" />{{ $t("settings.lychee_se.hide_warning") }}</span
			>
		</p>
	</Fieldset>
	<Fieldset
		:legend="$t('settings.dropbox.header')"
		class="border-b-0 ltr:border-r-0 ltr:rounded-r-none rtl:border-l-0 rtl:rounded-l-none rounded-b-none"
		:toggleable="true"
		:collapsed="true"
	>
		<p class="mb-4 text-muted-color">
			{{ $t("settings.dropbox.instruction") }}
			<a href="https://www.dropbox.com/developers/saver" class="pl-2 border-b border-dashed border-b-primary-500 text-primary-500">
				<i class="pi pi-link"></i>
			</a>
		</p>
		<div class="flex gap-4">
			<FloatLabel class="w-full grow" variant="on">
				<InputPassword id="api_key" type="text" v-model="dropbox_key" />
				<label for="api_key" class="text-muted-color">{{ $t("settings.dropbox.api_key") }}</label>
			</FloatLabel>
			<Button severity="contrast" class="w-full border-none bg-primary-500/20 hover:bg-primary-500" @click="saveDropboxKey">{{
				$t("settings.dropbox.set_key")
			}}</Button>
		</div>
	</Fieldset>
	<Fieldset :legend="$t('settings.gallery.header')">
		<div class="flex flex-col mb-6">
			<!-- ALBUM ORDER -->
			<SelectOptionsField
				v-if="photoSortingColumn !== undefined"
				:label="$t('settings.gallery.photo_order_column')"
				:config="photoSortingColumn"
				:options="photoSortingColumnsOptions"
				:mapper="SelectBuilders.buildPhotoSorting"
				@filled="save"
			/>
			<SelectOptionsField
				v-if="photoSortingOrder !== undefined"
				:label="$t('settings.gallery.photo_order_direction')"
				:config="photoSortingOrder"
				:options="sortingOrdersOptions"
				:mapper="SelectBuilders.buildSortingOrder"
				@filled="save"
			/>
			<SelectOptionsField
				v-if="albumSortingColumn !== undefined"
				:label="$t('settings.gallery.album_order_column')"
				:config="albumSortingColumn"
				:options="albumSortingColumnsOptions"
				:mapper="SelectBuilders.buildAlbumSorting"
				@filled="save"
			/>
			<div class="mb-4" />
			<SelectOptionsField
				v-if="albumSortingOrder !== undefined"
				:label="$t('settings.gallery.album_order_direction')"
				:config="albumSortingOrder"
				:options="sortingOrdersOptions"
				:mapper="SelectBuilders.buildSortingOrder"
				@filled="save"
			/>
			<SelectOptionsField
				v-if="aspectRatio !== undefined"
				:label="$t('settings.gallery.aspect_ratio')"
				:config="aspectRatio"
				:options="aspectRationOptions"
				:mapper="SelectBuilders.buildAspectRatio"
				@filled="save"
			/>
			<SelectOptionsField
				v-if="layout !== undefined"
				:label="$t('settings.gallery.photo_layout')"
				:config="layout"
				:options="photoLayoutOptions"
				:mapper="SelectBuilders.buildPhotoLayout"
				@filled="save"
			/>
			<div class="mb-4" />
			<SelectField
				v-if="album_decoration !== undefined"
				:label="$t('settings.gallery.album_decoration')"
				:config="album_decoration"
				@filled="save"
			/>
			<SelectField
				v-if="album_decoration_orientation !== undefined"
				:label="$t('settings.gallery.album_decoration_direction')"
				:config="album_decoration_orientation"
				@filled="save"
			/>
			<SelectOptionsField
				v-if="image_overlay_type !== undefined"
				:label="$t('settings.gallery.photo_overlay')"
				:config="image_overlay_type"
				:options="overlayOptions"
				:mapper="SelectBuilders.buildOverlay"
				@filled="save"
			/>
		</div>
		<!-- LICENSE -->
		<div class="flex flex-col">
			<SelectOptionsField
				v-if="default_license !== undefined"
				:label="$t('settings.gallery.license_default')"
				:config="default_license"
				:options="licenseOptions"
				:mapper="SelectBuilders.buildLicense"
				@filled="save"
			/>
			<div class="mb-4 text-muted-color">
				<p>
					{{ $t("settings.gallery.license_help") }}
					<a
						href="https://creativecommons.org/choose/"
						target="_blank"
						class="pl-2 border-b border-dashed border-b-primary-500 text-primary-500"
					>
						<i class="pi pi-link"></i>
					</a>
				</p>
			</div>
		</div>
	</Fieldset>
	<Fieldset :legend="$t('settings.geolocation.header')">
		<div class="flex flex-col gap-4">
			<BoolField :label="$t('settings.geolocation.map_display')" v-if="map_display !== undefined" :config="map_display" @filled="save" />
			<BoolField
				:label="$t('settings.geolocation.map_display_public')"
				v-if="map_display_public !== undefined"
				:config="map_display_public"
				@filled="save"
			/>
			<SelectOptionsField
				:label="$t('settings.geolocation.map_provider')"
				v-if="map_provider !== undefined"
				:config="map_provider"
				:options="mapProvidersOptions"
				:mapper="SelectBuilders.buildMapProvider"
				@filled="save"
			/>
			<BoolField
				:label="$t('settings.geolocation.map_include_subalbums')"
				v-if="map_include_subalbums !== undefined"
				:config="map_include_subalbums"
				@filled="save"
			/>
			<BoolField
				:label="$t('settings.geolocation.location_decoding')"
				v-if="location_decoding !== undefined"
				:config="location_decoding"
				@filled="save"
			/>
			<BoolField :label="$t('settings.geolocation.location_show')" v-if="location_show !== undefined" :config="location_show" @filled="save" />
			<BoolField
				:label="$t('settings.geolocation.location_show_public')"
				v-if="location_show_public !== undefined"
				:config="location_show_public"
				@filled="save"
			/>
		</div>
	</Fieldset>
</template>
<script setup lang="ts">
import { computed, ref } from "vue";
import FloatLabel from "primevue/floatlabel";
import Button from "primevue/button";
import ToggleSwitch from "primevue/toggleswitch";
import InputPassword from "@/components/forms/basic/InputPassword.vue";
import SettingsService from "@/services/settings-service";
import {
	photoSortingColumnsOptions,
	albumSortingColumnsOptions,
	sortingOrdersOptions,
	licenseOptions,
	aspectRationOptions,
	SelectBuilders,
	photoLayoutOptions,
	overlayOptions,
	mapProvidersOptions,
} from "@/config/constants";
import { trans } from "laravel-vue-i18n";
import SelectLang from "../forms/settings/SelectLang.vue";
import SelectOptionsField from "../forms/settings/SelectOptionsField.vue";
import SelectField from "../forms/settings/SelectField.vue";
import BoolField from "../forms/settings/BoolField.vue";
import { useToast } from "primevue/usetoast";
import { useLycheeStateStore } from "@/stores/LycheeState";
import { storeToRefs } from "pinia";
import InputText from "../forms/basic/InputText.vue";
import MaintenanceService from "@/services/maintenance-service";
import { onMounted, watch } from "vue";
import Fieldset from "@/components/forms/basic/Fieldset.vue";

const toast = useToast();

const props = defineProps<{
	hash: string;
	configs: App.Http.Resources.Models.ConfigCategoryResource[];
}>();
const emits = defineEmits<{ refresh: [] }>();

const dropbox_key = ref<string | undefined>(undefined);
const photoSortingColumn = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const photoSortingOrder = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const albumSortingColumn = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const albumSortingOrder = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const album_decoration = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const album_decoration_orientation = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const image_overlay_type = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const default_license = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const aspectRatio = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const lang = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const layout = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const nsfwVisible = ref<boolean | undefined>(undefined);

const dark_mode_enabled = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const cache_enabled = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);

const map_display = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const map_display_public = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const map_provider = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const map_include_subalbums = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const location_decoding = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const location_show = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);
const location_show_public = ref<App.Http.Resources.Models.ConfigResource | undefined>(undefined);

const disable_se_call_for_actions = ref<boolean | undefined>(undefined);
const enable_se_preview = ref<boolean | undefined>(undefined);
const licenseKey = ref<string | undefined>(undefined);
const licenseKeyIsInvValid = ref(false);
const isValidRegistrationForm = computed(() => {
	return licenseKey.value !== undefined && licenseKey.value !== "";
});

// Map stuff
const lycheeStore = useLycheeStateStore();
const { is_se_preview_enabled, is_se_enabled, is_se_info_hidden } = storeToRefs(lycheeStore);

function save(configKey: string, value: string) {
	SettingsService.setConfigs({
		configs: [
			{
				key: configKey,
				value: value,
			},
		],
	}).then(() => {
		toast.add({ severity: "success", summary: trans("settings.toasts.change_saved"), detail: trans("settings.toasts.details"), life: 3000 });
		emits("refresh");
	});
}

function load(configs: App.Http.Resources.Models.ConfigCategoryResource[]) {
	const configurations = [] as App.Http.Resources.Models.ConfigResource[];
	configs.forEach((config) => (config.configs as App.Http.Resources.Models.ConfigResource[]).forEach((value) => configurations.push(value)));

	lang.value = configurations.find((config) => config.key === "lang");
	dark_mode_enabled.value = configurations.find((config) => config.key === "dark_mode_enabled");
	nsfwVisible.value = configurations.find((config) => config.key === "nsfw_visible")?.value === "1";
	dropbox_key.value = configurations.find((config) => config.key === "dropbox_key")?.value ?? "";
	cache_enabled.value = configurations.find((config) => config.key === "cache_enabled");

	photoSortingColumn.value = configurations.find((config) => config.key === "sorting_photos_col");
	photoSortingOrder.value = configurations.find((config) => config.key === "sorting_photos_order");
	albumSortingColumn.value = configurations.find((config) => config.key === "sorting_albums_col");
	albumSortingOrder.value = configurations.find((config) => config.key === "sorting_albums_order");
	default_license.value = configurations.find((config) => config.key === "default_license");
	aspectRatio.value = configurations.find((config) => config.key === "default_album_thumb_aspect_ratio");
	layout.value = configurations.find((config) => config.key === "layout");
	album_decoration.value = configurations.find((config) => config.key === "album_decoration");
	album_decoration_orientation.value = configurations.find((config) => config.key === "album_decoration_orientation");
	image_overlay_type.value = configurations.find((config) => config.key === "image_overlay_type");

	map_display.value = configurations.find((config) => config.key === "map_display");
	map_display_public.value = configurations.find((config) => config.key === "map_display_public");
	map_provider.value = configurations.find((config) => config.key === "map_provider");
	map_include_subalbums.value = configurations.find((config) => config.key === "map_include_subalbums");
	location_decoding.value = configurations.find((config) => config.key === "location_decoding");
	location_show.value = configurations.find((config) => config.key === "location_show");
	location_show_public.value = configurations.find((config) => config.key === "location_show_public");

	disable_se_call_for_actions.value = configurations.find((config) => config.key === "disable_se_call_for_actions")?.value === "1";
	enable_se_preview.value = configurations.find((config) => config.key === "enable_se_preview")?.value === "1";
	licenseKey.value = configurations.find((config) => config.key === "license_key")?.value ?? "";
}

function updateNSFW() {
	save("nsfw_visible", nsfwVisible.value ? "1" : "0");
}

function saveDarkMode(_configKey: string, value: string) {
	if (value === "1") {
		document.body.classList.add("dark");
		save("dark_mode_enabled", "1");
	} else {
		document.body.classList.remove("dark");
		save("dark_mode_enabled", "0");
	}
}

function savePreview() {
	save("enable_se_preview", enable_se_preview.value ? "1" : "0");
}

function saveHideC4A() {
	save("disable_se_call_for_actions", disable_se_call_for_actions.value ? "1" : "0");
}

function register() {
	if (licenseKey.value === undefined || licenseKey.value === "") {
		return;
	}

	MaintenanceService.register(licenseKey.value)
		.then((response) => {
			if (response.data.success) {
				is_se_enabled.value = true;
				is_se_preview_enabled.value = false;
				is_se_info_hidden.value = false;
				toast.add({
					severity: "success",
					summary: trans("settings.toasts.thank_you"),
					detail: trans("settings.toasts.reload"),
					life: 5000,
				});
			} else {
				licenseKeyIsInvValid.value = true;
			}
		})
		.catch(() => {
			licenseKeyIsInvValid.value = true;
		});
}

function saveDropboxKey() {
	save("dropbox_key", dropbox_key.value ?? "");
}

onMounted(() => {
	load(props.configs);
});

watch(
	() => props.hash,
	() => {
		load(props.configs);
	},
);
</script>
