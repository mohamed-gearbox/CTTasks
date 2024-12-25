import { createApp } from 'vue'

import TaskManager from "./components/TaskManager.vue"

const app = createApp({})

app.component('task-manager', TaskManager)

app.mount('#vueapp')
