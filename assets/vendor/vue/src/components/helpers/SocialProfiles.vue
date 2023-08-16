<script setup>
import { computed, ref } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faBehance, faDribbble, faFacebookF, faFlickr, faGithub, faInstagram, faLinkedinIn, faFacebookMessenger, faPinterest, faReddit, faSkype, faSlack, faSoundcloud, faSpotify, faTwitter, faTelegram, faTumblr, faVk, faWhatsapp, faYoutube } from "@fortawesome/free-brands-svg-icons";
library.add(faBehance, faDribbble, faFacebookF, faFlickr, faGithub, faInstagram, faLinkedinIn, faFacebookMessenger, faPinterest, faReddit, faSkype, faSlack, faSoundcloud, faSpotify, faTwitter, faTelegram, faTumblr, faVk, faWhatsapp, faYoutube);

const emit = defineEmits(['update:modelValue']);
const props = defineProps(['modelValue', 'field']);
const socialSearch = ref('');
const filterSocial = computed(() => {
    let tmp = [];
    props.field.input.options.forEach(element => {
        if (socialSearch.value === '' || (element.title.toLowerCase().includes(socialSearch.value.toLowerCase()))) {
            tmp.push(element);
        }
    });
    return tmp;
})
</script>
<template>
    <textarea class="d-none" :id="props.field.input.id" :name="props.field.input.name" v-text="modelValue"></textarea>
    <div class="row">
        <div class="col-lg-9">

        </div>
        <div class="col-lg-3">
            <h3>{{ props.field.input.lang.social_brands }}</h3>
            <input type="text" class="form-control" :placeholder="props.field.input.lang.social_search" v-model="socialSearch">
            <div v-for="(social, key) in filterSocial" :key="key" class="border rounded mt-3 p-2">
                <font-awesome-icon :icon="social.icon" />
                {{ social.title }}
            </div>
        </div>
    </div>
</template>