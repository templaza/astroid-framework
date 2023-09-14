<script setup>
import Heading from "@/components/Heading.vue";
import Container from "./components/Container.vue";
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCircle, faArrowsLeftRight, faTrash } from "@fortawesome/free-solid-svg-icons";
library.add(faCircle, faArrowsLeftRight, faTrash);
const astroid_config = JSON.parse(document.getElementById("astroid-script-options").innerHTML);
</script>
<template>
  <Heading :config="astroid_config" />
  <Container :config="astroid_config" />
</template>