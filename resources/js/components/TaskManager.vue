<script>
import { ref } from 'vue'
import _ from 'lodash'
import axios from 'axios'
import draggable from 'vuedraggable'
import { Modal } from 'bootstrap'

let modalSaveTask = ref(null);
let saveTaskModalObj = null;
export default {
    name: "TaskManager",
    order: 0,
    components: {
        draggable
    },
    props: {
        projects: Array,
    },
    data() {
        return {
            enabled: true,
            projects_ref:_.cloneDeep(this.projects),
            list: [],
            selected_project_id: 0,
            task: {
                action: 'create',
                id: 0,
                name: '',
                priority: 1
            },
            dragging: false,
            saving: false,
            main_message: '',
            save_message: '',
        }
    },
    computed: {
        draggingInfo() {
            return this.dragging ? "under drag" : "";
        },
        newTaskPriority() {
            let priority = 1
            if (this.task.action==='create')
                priority = this.task.priority = this.list.length > 0
                    ? Math.max.apply(Math, this.list.map(function(o) { return o.priority; })) + 1
                    : 1
            else
                priority = this.list.length > 0
                    ? Math.max.apply(Math, this.list.map(function(o) { return o.priority; }))
                    : 1
            return priority
        }
    },
    methods: {
        init() {
            let vm = this
            saveTaskModalObj = new Modal('#saveTask')
            let default_project = vm.projects_ref[0]
            vm.selected_project_id = default_project.id
            vm.list = default_project.tasks
        },
        selectProjectTasks: function() {
            let vm = this
            let project = vm.projects_ref.find(p => p.id === vm.selected_project_id)
            if(project) {
                vm.list = project.tasks
            }
        },
        resetProject: function(prj) {
            let vm = this
            let project = vm.projects_ref.find(p => p.id === prj.id)
            if(project) {
                project = prj
                vm.list = prj.tasks
            }
        },
        getTask: function(id) {
            let vm = this
            return vm.list.find(t => t.id === id)
        },
        editProjectTask(id) {
            let vm = this
            let task = vm.getTask(id)
            if (task) {
                vm.task.action = 'update'
                vm.task.id = task.id
                vm.task.name = task.name
                vm.task.priority = task.priority
                saveTaskModalObj.show()
            }
        },
        addProjectTask: function() {
            let vm = this
            vm.task.action = 'create'
            vm.task.id = 0
            vm.task.name = ''
            saveTaskModalObj.show()
        },
        saveTask() {
            let vm = this
            if (vm.task.name.trim().length>0) {
                vm.saving = true
                axios.post('/save/task', {
                    'project_id': vm.selected_project_id,
                    'action': vm.task.action,
                    'id': vm.task.id,
                    'name': vm.task.name,
                    'priority': vm.task.priority
                }).then((res) => {
                    vm.save_message = res.data.message
                    if(res.data.success) {
                        vm.resetProject(res.data.project)
                        vm.resetAction()
                        setTimeout(() => {
                            saveTaskModalObj.hide()
                        }, 1500)
                    } else {
                        setTimeout(() => {
                            vm.save_message = ''
                        }, 3000)
                    }
                    vm.saving = false
                })
                .catch((error) => {
                    //console.log(error)
                });
            }
        },
        logChange: function(evt) {
            let vm = this
            axios.post('/task/priority', {
                'id': evt.moved.element.id,
                'current': evt.moved.oldIndex + 1,
                'future': evt.moved.newIndex + 1
            }).then((res) => {
                vm.main_message = res.data.message
                if(res.data.success) {
                    setTimeout(() => {
                        vm.main_message = ''
                    }, 1500)
                } else {
                    setTimeout(() => {
                        vm.save_message = ''
                    }, 3000)
                }
            })
            .catch((error) => {
                //console.log(error)
            });
        },
        deleteTask(id) {
            let vm = this
            axios.post('/delete/task', {
                'id': id
            }).then((res) => {
                vm.main_message = res.data.message
                if(res.data.success) {
                    vm.resetProject(res.data.project)
                    setTimeout(() => {
                        vm.main_message = ''
                    }, 1500)
                } else {
                    setTimeout(() => {
                        vm.main_message = ''
                    }, 3000)
                }
            })
            .catch((error) => {
                //console.log(error)
            });
        },
        resetAction() {
            let vm = this
            vm.task.action = 'create'
            vm.task.id = 0
            vm.task.name = ''
            vm.save_message = ''
        },
    },
    mounted() {
        this.init()
    }
}
</script>

<template>
    <div class="row w-100 justify-content-center">
        <div class="col-6">
            <select v-model="selected_project_id" @change="selectProjectTasks">
                <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{project.name}}
                </option>
            </select>
            <h3>Project Tasks</h3>
            <draggable
                tag="ul"
                :list="list"
                :disabled="!enabled"
                handle=".handle"
                item-key="name"
                class="list-group"
                ghost-class="ghost"
                :move="checkMove"
                @change="logChange"
                @start="dragging = true"
                @end="dragging = false">
                <template #item="{ element, index }">
                    <li class="list-group-item handle">
                        <i class="fas fa-grip-vertical handle"></i>

                        <span class="text">{{ element.name }} </span>

                        <i class="fas fa-times close" @click="deleteTask(element.id)"></i>
                        <i class="fas fa-edit edit" @click="editProjectTask(element.id)"></i>
                    </li>
                </template>
            </draggable>
            <h6 style="color: #0d6efd">{{main_message}}</h6>
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-primary" @click="addProjectTask">
                <i class="fas fa-plus">New Task</i>
            </button>

        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="saveTask" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="saveTask" aria-hidden="true" ref="modalSaveTask">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">{{task.action}} new task</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" v-model="task.name" placeholder="Task name" />
                    <label class="mt-4" for="newTaskPriority">Task Priority</label>&nbsp;
                    <select v-model="task.priority" id="newTaskPriority" name="newTaskPriority">
                        <option v-for="i in newTaskPriority" :key="i">
                            {{i}}
                        </option>
                    </select>
                    <h6 style="color: #0d6efd; margin-top: 5px;">{{save_message}}</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" :disabled="saving" @click="saveTask">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.handle {
    float: left;
    padding-top: 8px;
    padding-bottom: 8px;
}
.text {
    margin: 20px;
}
.edit {
    float: right;
    padding-top: 8px;
    padding-right: 4px;
    padding-bottom: 8px;
}
.close {
    float: right;
    padding-top: 8px;
    padding-bottom: 8px;
}
.list-group-item {
    cursor: move;
}

.list-group-item i {
    cursor: pointer;
}
</style>
