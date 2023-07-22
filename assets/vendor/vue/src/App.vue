<script setup>
import Heading from "@/components/Heading.vue";
import Container from "./components/Container.vue";
import { reactive, ref } from "vue";
const astroid_config = JSON.parse(document.getElementById("astroid-script-options").innerHTML);
</script>

<template>
  <Heading :config="astroid_config" />
  <Container :config="astroid_config" />
</template>