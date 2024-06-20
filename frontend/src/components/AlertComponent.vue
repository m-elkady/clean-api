<template>
    <v-alert :type="type" :value="visible" @input="updateVisibility" :text="text" :dismissible="dismissible">
    </v-alert>
  </template>
  
  <script>
  export default {
    name: 'ReusableAlert',
    props: {
      type: {
        type: String,
        default: 'info', // default type can be 'info', 'error', 'success', 'warning'
      },
      message: {
        type: String,
        default: '',
      },
      visible: {
        type: Boolean,
        default: true,
      },
      dismissible: {
        type: Boolean,
        default: true,
      },
    },
    methods: {
      updateVisibility(value) {
        this.$emit('update:visible', value);
      },
    },
  };
  </script>